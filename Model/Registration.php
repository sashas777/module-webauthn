<?php
/**
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use TheSGroup\WebAuthn\Api\CredentialSourceRepositoryInterface;
use TheSGroup\WebAuthn\Api\RegistrationInterface;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialRpEntityFactory;
use Webauthn\PublicKeyCredentialUserEntityFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Nyholm\Psr7\Factory\Psr17FactoryFactory;
use Nyholm\Psr7Server\ServerRequestCreatorFactory;
use Webauthn\ServerFactory;
use Webauthn\Server;
use Webauthn\PublicKeyCredentialSource;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Laminas\Uri\Http;

class Registration implements RegistrationInterface
{
    private $rpEntityFactory;
    private $credentialUserEntityFactory;

    private $serverFactory;
    private $server = null;

    private $serializer;
    private $customerRepository;
    private $credentialSourceRepository;
    private $credentialSourceInterfaceFactory;
    private $serverRequestCreatorFactory;
    private $psr17FactoryFactory;
    private $storeManager;
    private $http;

    public function __construct(
        PublicKeyCredentialRpEntityFactory $rpEntityFactory,
        PublicKeyCredentialUserEntityFactory $credentialUserEntityFactory,
        ServerFactory $serverFactory,
        SerializerInterface $serializer,
        CustomerRepositoryInterface $customerRepository,
        CredentialSourceRepositoryInterface $credentialSourceRepository,
        CredentialSourceInterfaceFactory $credentialSourceInterfaceFactory,
        ServerRequestCreatorFactory $serverRequestCreatorFactory,
        Psr17FactoryFactory $psr17FactoryFactory,
        StoreManagerInterface $storeManager,
        Http $http
    ) {
        $this->rpEntityFactory = $rpEntityFactory;
        $this->credentialUserEntityFactory = $credentialUserEntityFactory;
        $this->serverFactory = $serverFactory;
        $this->credentialSourceRepository = $credentialSourceRepository;
        $this->serializer = $serializer;
        $this->customerRepository = $customerRepository;
        $this->credentialSourceInterfaceFactory = $credentialSourceInterfaceFactory;
        $this->serverRequestCreatorFactory = $serverRequestCreatorFactory;
        $this->psr17FactoryFactory = $psr17FactoryFactory;
        $this->storeManager = $storeManager;
        $this->http = $http;
    }

    private function getServer(): ?Server
    {
        if ($this->server === null) {

            //@todo icon
            $websiteUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
            $url = $this->http->parse($websiteUrl);

            /** @var \Webauthn\PublicKeyCredentialRpEntity $rpEntity */
            $rpEntity = $this->rpEntityFactory->create(
                [
                    'name' => $this->storeManager->getStore()->getName(),
                    'id' => $url->getHost(),
                    'icon' => null
                ]
            );
            /** @var \Webauthn\Server $server */
            $this->server = $this->serverFactory->create([
                'relyingParty' => $rpEntity,
                'publicKeyCredentialSourceRepository' => $this->credentialSourceRepository,
                'metadataStatementRepository' => null
            ]);
        }
        return $this->server;
    }

    public function creationRequest(int $customerId, string $deviceName)
    {

        $customer = $this->customerRepository->getById($customerId);
        $name = $customer->getFirstname().' '.$customer->getLastname();
        $email = $customer->getEmail();
        $customerId = (string) $customer->getId();

        /** @var \Webauthn\PublicKeyCredentialUserEntity $userEntity */
        $userEntity = $this->credentialUserEntityFactory->create(
            [
                'name'=>$email,
                'id' => $customerId,
                'displayName' => $name
            ]
        );
        $credentialSources = $this->credentialSourceRepository->findAllForUserEntity($userEntity);
        // Convert the Credential Sources into Public Key Credential Descriptors
        $excludeCredentials = array_map(function (PublicKeyCredentialSource $credential) {
            return $credential->getPublicKeyCredentialDescriptor();
        }, $credentialSources);

        $server = $this->getServer();

        $publicKeyCredentialCreationOptions = $server->generatePublicKeyCredentialCreationOptions(
            $userEntity,
            PublicKeyCredentialCreationOptions::ATTESTATION_CONVEYANCE_PREFERENCE_NONE,
            $excludeCredentials
        );

        $credentialCreationOptions = $this->serializer->serialize($publicKeyCredentialCreationOptions);

        /** @var \TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface $credentialSource */
        $credentialSource = $this->credentialSourceRepository->getCredentialCreationOptions(
            (int) $customerId,
            (int) $customer->getStoreId()
        );
        if (!$credentialSource->getEntityId()) {
            $credentialSource = $this->credentialSourceInterfaceFactory->create();
        }
        $credentialSource->setCustomerId($customerId);
        $credentialSource->setStoreId((string)$customer->getStoreId());
        $credentialSource->setCredentialCreationOptions($credentialCreationOptions);
        $credentialSource->setDeviceName($deviceName);
        $this->credentialSourceRepository->save($credentialSource);

        return $credentialCreationOptions;
    }

    public function responseVerification(int $customerId, $credential)
    {
        $customer = $this->customerRepository->getById($customerId);
        $credentialJson = $this->serializer->serialize($credential);

        $credentialCreationOptions = $this->credentialSourceRepository->getCredentialCreationOptions(
            (int) $customer->getId(),
            (int) $customer->getStoreId()
        );
        if (!$credentialCreationOptions->getEntityId()) {
            throw new LocalizedException(__('There is no creation option request for this customer'));
        }
        $credentialCreationOptionsObject = PublicKeyCredentialCreationOptions::createFromString(
            $credentialCreationOptions->getCredentialCreationOptions()
        );

        /** @var \Nyholm\Psr7\Factory\Psr17Factory $psr17Factory */
        $psr17Factory = $this->psr17FactoryFactory->create();
        /** @var \Nyholm\Psr7Server\ServerRequestCreator $creator */
        $creator = $this->serverRequestCreatorFactory->create(
            [
                'serverRequestFactory' => $psr17Factory,
                'uriFactory' => $psr17Factory,
                'uploadedFileFactory' => $psr17Factory,
                'streamFactory' => $psr17Factory,
            ]
        );

        $serverRequest = $creator->fromGlobals();

        try {
            $server = $this->getServer();
            $publicKeyCredentialSource = $server->loadAndCheckAttestationResponse(
                $credentialJson,
                $credentialCreationOptionsObject,
                $serverRequest
            );

            $credentialCreationOptions->setPublicCredential($this->serializer->serialize($publicKeyCredentialSource));
            $credentialCreationOptions->setPublicCredentialId(
                base64_encode($publicKeyCredentialSource->getPublicKeyCredentialId())
            );
            $this->credentialSourceRepository->save($credentialCreationOptions);

        } catch(\Exception $exception) {
            var_dump('excepton');
            var_dump($exception->getMessage());
            var_dump($exception->getTraceAsString());
            // Something went wrong!
        }
    }
}
