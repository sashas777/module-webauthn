<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */
declare(strict_types=1);

namespace TheSGroup\WebAuthn\Model;

use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface;
use TheSGroup\WebAuthn\Api\Data\CredentialSourceInterfaceFactory;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use TheSGroup\WebAuthn\Model\ResourceModel\CredentialSource as CredentialSourceResource;
use TheSGroup\WebAuthn\Model\ResourceModel\CredentialSource\Collection;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class CredentialSource
 * Data model for Credential Source
 */
class CredentialSource extends AbstractModel
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'tsg_customer_credential_source';

    /**
     * @var CredentialSourceInterfaceFactory
     */
    protected $credentialSourceDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * CredentialSource constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CredentialSourceInterfaceFactory $credentialSourceDataFactory
     * @param CredentialSourceResource $resource
     * @param Collection $resourceCollection
     * @param DataObjectHelper $dataObjectHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CredentialSourceInterfaceFactory $credentialSourceDataFactory,
        CredentialSourceResource $resource,
        Collection $resourceCollection,
        DataObjectHelper $dataObjectHelper,
        array $data = []
    ) {
        $this->credentialSourceDataFactory = $credentialSourceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve credentialSource model with credentialSource data
     * @return CredentialSourceInterface
     */
    public function getDataModel()
    {
        $credentialSourceData = $this->getData();

        $credentialSourceDataObject = $this->credentialSourceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $credentialSourceDataObject,
            $credentialSourceData,
            CredentialSourceInterface::class
        );

        return $credentialSourceDataObject;
    }
}

