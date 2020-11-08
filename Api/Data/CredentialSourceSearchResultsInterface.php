<?php
/*
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface CredentialSourceSearchResultsInterface
 * Credential Source Search Result Interface
 */
interface CredentialSourceSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get  Credential Source list.
     * @return \TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface[]
     */
    public function getItems();

    /**
     * Set Credential Source list.
     * @param \TheSGroup\WebAuthn\Api\Data\CredentialSourceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

