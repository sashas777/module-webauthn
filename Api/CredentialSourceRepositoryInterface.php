<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use Webauthn\PublicKeyCredentialSourceRepository as PublicKeyCredentialSourceRepositoryInterface;

/**
 * Interface CredentialSourceRepositoryInterface
 */
interface CredentialSourceRepositoryInterface extends PublicKeyCredentialSourceRepositoryInterface
{
    /**
     * Save Credential Source
     *
     * @param CredentialSourceInterface $credentialSource
     *
     * @return CredentialSourceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(CredentialSourceInterface $credentialSource);

    /**
     * Retrieve Credential Source
     *
     * @param int $entityId
     *
     * @return CredentialSourceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get(int $entityId);

    /**
     * Retrieve CredentialSource matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return \TheSGroup\WebAuthn\Api\Data\CredentialSourceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Credential Source
     *
     * @param CredentialSourceInterface $credentialSource
     *
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(CredentialSourceInterface $credentialSource): bool;

    /**
     * Delete Credential Source by ID
     * @param int $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;

    /**
     * @param int $customerId
     * @param int $storeId
     *
     * @return CredentialSourceInterface
     */
    public function getCredentialCreationOptions(int $customerId, int $storeId): CredentialSourceInterface;
}

