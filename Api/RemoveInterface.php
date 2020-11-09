<?php
/**
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Api;

/**
 * Interface RemoveInterface
 */
interface RemoveInterface
{

    /**
     * @param int $customerId
     * @param int $entityId
     *
     * @return mixed
     */
    public function removeDevice(int $customerId, int $entityId);
}
