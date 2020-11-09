<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\ViewModel\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use TheSGroup\WebAuthn\Api\CredentialSourceRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class Devices implements ArgumentInterface
{
    private $customerSession;

    private $credentialSourceRepository;

    private $searchCriteriaBuilder;
    private $timezone;

    public function __construct(
        Session $customerSession,
        CredentialSourceRepositoryInterface $credentialSourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TimezoneInterface $timezone
    ) {
        $this->customerSession  = $customerSession;
        $this->credentialSourceRepository  = $credentialSourceRepository;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
        $this->timezone  = $timezone;
    }

    public function getDevices(): SearchResultsInterface
    {
        $customerId = $this->customerSession->getCustomerId();
        $storeId = $this->customerSession->getCustomer()->getStoreId();

        $searchCriteria = $this->searchCriteriaBuilder->addFilter(CredentialSourceInterface::CUSTOMER_ID, $customerId)
            ->addFilter(CredentialSourceInterface::STORE_ID, $storeId)
            ->addFilter(CredentialSourceInterface::PUBLIC_CREDENTIAL_ID, true, 'notnull')
            ->create();
        $searchResult = $this->credentialSourceRepository->getList($searchCriteria);

        return $this->credentialSourceRepository->getList($searchCriteria);
    }

    public function formatDate(string $time): string
    {
        return $this->timezone->formatDateTime($time);
    }
}
