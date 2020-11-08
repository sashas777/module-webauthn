<?php
/**
 * @author     The S Group <support@sashas.org>
 * @copyright  2020  Sashas IT Support Inc. (https://www.sashas.org)
 * @license     http://opensource.org/licenses/GPL-3.0  GNU General Public License, version 3 (GPL-3.0)
 */

declare(strict_types=1);

namespace TheSGroup\WebAuthn\Api;

/**
 * Interface RegistrationInterface
 */
interface RegistrationInterface
{

    /**
     * @param int $customerId
     *
     * @return mixed
     */
    public function creationRequest(int $customerId);

    /**
     * @param int $customerId
     * @param mixed $credential
     *
     * @return mixed
     */
    public function responseVerification(int $customerId, $credential);
}
