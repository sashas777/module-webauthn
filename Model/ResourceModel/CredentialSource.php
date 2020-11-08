<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class CustomerCredentialSource
 */
class CredentialSource extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('tsg_customer_credential_source', CredentialSourceInterface::ENTITY_ID);
    }

    public function getAllCredentialsByCustomerId(int $customerId): array
    {
        $fieldCustomerId = $this->getConnection()
                                ->quoteIdentifier(
                                    sprintf('%s.%s', $this->getMainTable(), CredentialSourceInterface::CUSTOMER_ID)
                                );
        $fieldPublicCredential = $this->getConnection()
                                      ->quoteIdentifier(
                                          sprintf(
                                              '%s.%s',
                                              $this->getMainTable(),
                                              CredentialSourceInterface::PUBLIC_CREDENTIAL
                                          )
                                      );
        $isNotNullCondition = new \Zend_Db_Expr(' IS NOT NULL');
        $select = $this->getConnection()
                       ->select()
                       ->from($this->getMainTable(), [CredentialSourceInterface::PUBLIC_CREDENTIAL])
                       ->where($fieldCustomerId . '=?', $customerId)
                       ->where($fieldPublicCredential.$isNotNullCondition);

        return $this->getConnection()->fetchAll($select);
    }

    public function getCredentialCreationOptionsByCustomerId(
        int $customerId,
        int $storeId,
        AbstractModel $object
    ) {
        $fieldCustomerId = $this->getConnection()
                                ->quoteIdentifier(
                                    sprintf('%s.%s', $this->getMainTable(), CredentialSourceInterface::CUSTOMER_ID)
                                );
        $fieldStoreId = $this->getConnection()->quoteIdentifier(
            sprintf('%s.%s', $this->getMainTable(), CredentialSourceInterface::STORE_ID)
        );
        $fieldPublicCredential = $this->getConnection()
                                      ->quoteIdentifier(
                                          sprintf(
                                              '%s.%s',
                                              $this->getMainTable(),
                                              CredentialSourceInterface::PUBLIC_CREDENTIAL
                                          )
                                      );
        $fieldCredentialOptions = $this->getConnection()
                                       ->quoteIdentifier(
                                           sprintf(
                                               '%s.%s',
                                               $this->getMainTable(),
                                               CredentialSourceInterface::CREDENTIAL_CREATION_OPTIONS
                                           )
                                       );
        $isNullCondition = new \Zend_Db_Expr(' IS NULL');
        $isNotNullCondition = new \Zend_Db_Expr(' IS NOT NULL');
        $select = $this->getConnection()
                       ->select()
                       ->from($this->getMainTable())
                       ->where($fieldCustomerId . '=?', $customerId)
                       ->where($fieldStoreId . '=?', $storeId)
                       ->where($fieldPublicCredential.$isNullCondition)
                       ->where($fieldCredentialOptions.$isNotNullCondition)
                       ->limit(1);
        $data = $this->getConnection()->fetchRow($select);

        if ($data) {
            $object->setData($data);
        }
        return $this;
    }
}

