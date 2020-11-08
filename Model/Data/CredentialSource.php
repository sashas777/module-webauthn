<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model\Data;

use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use Magento\Framework\Model\AbstractExtensibleModel;

/**
 * Class CredentialSource
 * Implementation of Credential Source
 */
class CredentialSource extends AbstractExtensibleModel implements CredentialSourceInterface
{
    /**
     * Get customer_id
     * @return string
     */
    public function getCustomerId(): string
    {
        return $this->getData(static::CUSTOMER_ID);
    }

    /**
     * Set customer_id
     *
     * @param string $customerId
     *
     * @return CredentialSourceInterface
     */
    public function setCustomerId(string $customerId): CredentialSourceInterface
    {
        return $this->setData(static::CUSTOMER_ID, $customerId);
    }

    /**
     * Get public_credential_id
     * @return string|null
     */
    public function getPublicCredentialId(): ?string
    {
        return $this->getData(static::PUBLIC_CREDENTIAL_ID);
    }

    /**
     * Set public_credential_id
     *
     * @param string|null $publicCredentialId
     *
     * @return CredentialSourceInterface
     */
    public function setPublicCredentialId(?string $publicCredentialId): CredentialSourceInterface
    {
        return $this->setData(static::PUBLIC_CREDENTIAL_ID, $publicCredentialId);
    }

    /**
     * @return string|null
     */
    public function getDeviceName(): ?string
    {
        return $this->getData(static::DEVICE_NAME);
    }

    /**
     * @param string|null $deviceName
     *
     * @return CredentialSourceInterface
     */
    public function setDeviceName(?string $deviceName): CredentialSourceInterface
    {
        return $this->setData(static::DEVICE_NAME, $deviceName);
    }


    /**
     * Get public_credential
     * @return string|null
     */
    public function getPublicCredential(): ?string
    {
        return $this->getData(static::PUBLIC_CREDENTIAL);
    }

    /**
     * Set public_credential
     *
     * @param string|null $publicCredential
     *
     * @return CredentialSourceInterface
     */
    public function setPublicCredential(?string $publicCredential): CredentialSourceInterface
    {
        return $this->setData(static::PUBLIC_CREDENTIAL, $publicCredential);
    }

    /**
     * @return string|null
     */
    public function getCredentialCreationOptions(): ?string
    {
        return $this->getData(static::CREDENTIAL_CREATION_OPTIONS);
    }

    /**
     * @param string|null $credentialOptions
     *
     * @return CredentialSourceInterface
     */
    public function setCredentialCreationOptions(?string $credentialOptions): CredentialSourceInterface
    {
        return $this->setData(static::CREDENTIAL_CREATION_OPTIONS, $credentialOptions);
    }

    /**
     * Get store_id
     * @return string
     */
    public function getStoreId(): string
    {
        return $this->getData(static::STORE_ID);
    }

    /**
     * Set store_id
     *
     * @param string $storeId
     *
     * @return CredentialSourceInterface
     */
    public function setStoreId(string $storeId): CredentialSourceInterface
    {
        return $this->setData(static::STORE_ID, $storeId);
    }

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->getData(static::CREATED_AT);
    }

    /**
     * Set created_at
     *
     * @param string $createdAt
     *
     * @return CredentialSourceInterface
     */
    public function setCreatedAt(string $createdAt): CredentialSourceInterface
    {
        return $this->setData(static::CREATED_AT, $createdAt);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

