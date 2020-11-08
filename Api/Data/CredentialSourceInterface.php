<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface CredentialSourceInterface
 * Credential Source Interface
 */
interface CredentialSourceInterface extends ExtensibleDataInterface
{
    const CUSTOMER_ID = 'customer_id';
    const ENTITY_ID = 'entity_id';
    const PUBLIC_CREDENTIAL_ID = 'public_credential_id';
    const DEVICE_NAME = 'device_name';
    const CREATED_AT = 'created_at';
    const STORE_ID = 'store_id';
    const PUBLIC_CREDENTIAL = 'public_credential';
    const CREDENTIAL_CREATION_OPTIONS = 'credential_creation_options';

    /**
     * Get entity_id
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param int $entityId
     *
     * @return CredentialSourceInterface
     */
    public function setEntityId($entityId);

    /**
     * Get customer_id
     * @return string
     */
    public function getCustomerId(): string;

    /**
     * Set customer_id
     *
     * @param string $customerId
     *
     * @return CredentialSourceInterface
     */
    public function setCustomerId(string $customerId): CredentialSourceInterface;

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \TheSGroup\WebAuthn\Api\Data\CredentialSourceExtensionInterface $extensionAttributes
    );

    /**
     * Get public_credential_id
     * @return string|null
     */
    public function getPublicCredentialId(): ?string;

    /**
     * Set public_credential_id
     *
     * @param string|null $publicCredentialId
     *
     * @return CredentialSourceInterface
     */
    public function setPublicCredentialId(?string $publicCredentialId): CredentialSourceInterface;

    /**
     * Get device_name
     * @return string|null
     */
    public function getDeviceName(): ?string;

    /**
     * Set device_name
     *
     * @param string|null $deviceName
     *
     * @return CredentialSourceInterface
     */
    public function setDeviceName(?string $deviceName): CredentialSourceInterface;

    /**
     * Get public_credential
     * @return string|null
     */
    public function getPublicCredential(): ?string;

    /**
     * Set public_credential
     *
     * @param string|null $publicCredential
     *
     * @return CredentialSourceInterface
     */
    public function setPublicCredential(?string $publicCredential): CredentialSourceInterface;

    /**
     * Get credential_creation_options
     * @return string|null
     */
    public function getCredentialCreationOptions(): ?string;

    /**
     * Set credential_creation_options
     *
     * @param string|null $credentialOptions
     *
     * @return CredentialSourceInterface
     */
    public function setCredentialCreationOptions(?string $credentialOptions): CredentialSourceInterface;

    /**
     * Get store_id
     * @return string
     */
    public function getStoreId(): string;

    /**
     * Set store_id
     *
     * @param string $storeId
     *
     * @return CredentialSourceInterface
     */
    public function setStoreId(string $storeId): CredentialSourceInterface;

    /**
     * Get created_at
     * @return string|null
     */
    public function getCreatedAt(): ?string;

    /**
     * Set created_at
     *
     * @param string $createdAt
     *
     * @return CredentialSourceInterface
     */
    public function setCreatedAt(string $createdAt): CredentialSourceInterface;
}

