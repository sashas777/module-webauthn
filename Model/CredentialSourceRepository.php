<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model;

use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\SerializerInterface;
use TheSGroup\WebAuthn\Api\CredentialSourceRepositoryInterface;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceSearchResultsInterfaceFactory;
use TheSGroup\WebAuthn\Model\ResourceModel\CredentialSource as ResourceCredentialSource;
use TheSGroup\WebAuthn\Model\ResourceModel\CredentialSource\CollectionFactory as CredentialSourceCollectionFactory;
use Magento\Framework\Api\SearchCriteriaInterface;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * Class CredentialSourceRepository
 */
class CredentialSourceRepository implements CredentialSourceRepositoryInterface
{
    /**
     * @var CredentialSourceCollectionFactory
     */
    protected $credentialSourceCollectionFactory;

    /**
     * @var ResourceCredentialSource
     */
    protected $resource;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var CredentialSourceFactory
     */
    protected $credentialSourceFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var CredentialSourceSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @param ResourceCredentialSource $resource
     * @param CredentialSourceFactory $credentialSourceFactory
     * @param CredentialSourceCollectionFactory $credentialSourceCollectionFactory
     * @param CredentialSourceSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCredentialSource $resource,
        CredentialSourceFactory $credentialSourceFactory,
        CredentialSourceCollectionFactory $credentialSourceCollectionFactory,
        CredentialSourceSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        SerializerInterface $serializer
    ) {
        $this->resource = $resource;
        $this->credentialSourceFactory = $credentialSourceFactory;
        $this->credentialSourceCollectionFactory = $credentialSourceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function save(CredentialSourceInterface $credentialSource)
    {
        $credentialSourceData = $this->extensibleDataObjectConverter->toNestedArray(
            $credentialSource,
            [],
            CredentialSourceInterface::class
        );

        $credentialSourceModel = $this->credentialSourceFactory->create()->setData($credentialSourceData);

        try {
            $this->resource->save($credentialSourceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the credential source: %1',
                $exception->getMessage()
            ));
        }
        return $credentialSourceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $entityId)
    {
        $credentialSource = $this->credentialSourceFactory->create();
        $this->resource->load($credentialSource, $entityId);
        if (!$credentialSource->getId()) {
            throw new NoSuchEntityException(__('Credential source with id "%1" does not exist.', $entityId));
        }
        return $credentialSource->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->credentialSourceCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            CredentialSourceInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(CredentialSourceInterface $credentialSource): bool
    {
        try {
            $credentialSourceModel = $this->credentialSourceFactory->create();
            $this->resource->load($credentialSourceModel, $credentialSource->getEntityId());
            $this->resource->delete($credentialSourceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the credential source: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $credentialSourceId): bool
    {
        return $this->delete($this->get($credentialSourceId));
    }

    public function getCredentialCreationOptions(int $customerId, int $storeId): CredentialSourceInterface
    {
        $credentialSource = $this->credentialSourceFactory->create();
        $this->resource->getCredentialCreationOptionsByCustomerId($customerId, $storeId, $credentialSource);
        return $credentialSource->getDataModel();
    }

    /**
     * @param string $publicKeyCredentialId
     *
     * @return PublicKeyCredentialSource|null
     * @throws NoSuchEntityException
     */
    public function findOneByCredentialId(string $publicKeyCredentialId): ?PublicKeyCredentialSource
    {
        /** @var \TheSGroup\WebAuthn\Model\CredentialSource $credentialSource */
        $credentialSource = $this->credentialSourceFactory->create();
        $publicKeyCredentialId = base64_encode($publicKeyCredentialId);

        $this->resource->load($credentialSource, $publicKeyCredentialId, 'public_credential_id');

        if ($credentialSource->getDataModel()->getPublicCredential()) {
            $credential = $this->serializer->unserialize(
                $credentialSource->getDataModel()->getPublicCredential()
            );
            return PublicKeyCredentialSource::createFromArray($credential[$publicKeyCredentialId]);
        }

        return null;
    }

    /**
     * @param PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity
     *
     * @return array
     */
    public function findAllForUserEntity(PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity): array
    {
        $sources = [];
        $customerId =  $publicKeyCredentialUserEntity->getId();
        $credentials = $this->resource->getAllCredentialsByCustomerId((int) $customerId);

        foreach($credentials as $credentialJson)
        {
            $credential = $this->serializer->unserialize($credentialJson[CredentialSourceInterface::PUBLIC_CREDENTIAL]);
            $source = PublicKeyCredentialSource::createFromArray($credential);
            if ($source->getUserHandle() === $publicKeyCredentialUserEntity->getId())
            {
                $sources[] = $source;
            }
        }
        return $sources;
    }

    /**
     * @param PublicKeyCredentialSource $publicKeyCredentialSource
     */
    public function saveCredentialSource(PublicKeyCredentialSource $publicKeyCredentialSource): void
    {
        var_dump($publicKeyCredentialSource->getPublicKeyCredentialId());
        var_dump($publicKeyCredentialSource->getUserHandle());
       die('asd');
    }


}

