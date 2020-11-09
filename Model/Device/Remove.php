<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model\Device;

use Magento\Framework\Exception\LocalizedException;
use TheSGroup\WebAuthn\Api\RemoveInterface;
use TheSGroup\WebAuthn\Api\CredentialSourceRepositoryInterface;

/**
 * Class Remove
 * Remove customer's credential
 */
class Remove implements RemoveInterface
{
    /**
     * @var CredentialSourceRepositoryInterface
     */
    private $credentialSourceRepository;

    /**
     * Remove constructor.
     *
     * @param CredentialSourceRepositoryInterface $credentialSourceRepository
     */
    public function __construct(
        CredentialSourceRepositoryInterface $credentialSourceRepository
    ) {
        $this->credentialSourceRepository = $credentialSourceRepository;
    }

    /**
     * @param int $customerId
     * @param int $entityId
     *
     * @return mixed|void
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function removeDevice(int $customerId, int $entityId)
    {
        $credential = $this->credentialSourceRepository->get($entityId);
        if ($credential->getCustomerId() != $customerId) {
            throw new LocalizedException(__('User not authorized.'));
        }
        $this->credentialSourceRepository->deleteById($entityId);
    }
}
