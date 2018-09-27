<?php
namespace App\Integrations;

use App\Model\Entity\Membership;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Client\Response;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;

/**
 * Class LglIntegration
 *
 * This handles sending constituent info updates to MACC's LGL (Little Green Light) account
 *
 * @package App\Integrations
 * @property Client $client
 */
class LglIntegration
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Sends an update request to LGL creating a constituent record
     *
     * @param User $user User entity
     * @return bool
     */
    public function addUser($user)
    {
        $url = Configure::read('lglIntegrationListeners.addUser');

        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'macc_user_id' => $user->id
        ];
        $response = $this->client->post($url, $data);

        if ($response->isOk()) {
            $this->logSuccess($url, $data, $response);

            return true;
        }

        $this->logError($url, $data, $response);

        return false;
    }

    /**
     * Sends an update request to LGL updating a constituent's membership info
     *
     * @param User $user User entity
     * @param Membership|null $membership Membership entity
     * @return bool
     */
    public function addMembership($user, $membership = null)
    {
        $url = Configure::read('lglIntegrationListeners.addMembership');
        $membershipLevel = TableRegistry::getTableLocator()
            ->get('MembershipLevels')
            ->get($membership->membership_level_id);
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'macc_user_id' => $user->id,
            'membership_level' => $membershipLevel->name,
            'membership_start' => $membership->created->format('M j, Y'),
            'membership_end' => $membership->expires->format('M j, Y')
        ];
        $response = $this->client->post($url, $data);

        if ($response->isOk()) {
            $this->logSuccess($url, $data, $response);

            return true;
        }

        $this->logError($url, $data, $response);

        return false;
    }

    /**
     * Sends an update request to LGL updating a constituent's contact info
     *
     * @param User $user User entity
     * @return bool
     */
    public function updateContact($user)
    {
        $url = Configure::read('lglIntegrationListeners.updateContact');
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'address' => $user->address,
            'city' => $user->city,
            'state' => $user->state,
            'zipcode' => $user->zipcode,
            'macc_user_id' => $user->id
        ];
        $response = $this->client->post($url, $data);

        if ($response->isOk()) {
            $this->logSuccess($url, $data, $response);

            return true;
        }

        $this->logError($url, $data, $response);

        return false;
    }

    /**
     * Sends an update request to LGL updating a constituent's name
     *
     * @param User $user User entity
     * @return bool
     */
    public function updateName($user)
    {
        $url = Configure::read('lglIntegrationListeners.updateName');
        $data = [
            'name' => $user->name,
            'email' => $user->email,
            'macc_user_id' => $user->id
        ];
        $response = $this->client->post($url, $data);

        if ($response->isOk()) {
            $this->logSuccess($url, $data, $response);

            return true;
        }

        $this->logError($url, $data, $response);

        return false;
    }

    /**
     * Logs an error message
     *
     * @param string $url URL of request
     * @param array $data POST data sent
     * @param Response $response Response to request
     * @return void
     */
    private function logError($url, $data, $response)
    {
        $errorMsg = sprintf(
            "LGL integration returned status code %s.\nURL: %s\nPOST data:%s\nResponse: %s",
            $response->getStatusCode(),
            $url,
            print_r($data, true),
            $response->getBody()
        );
        Log::write('error', $errorMsg);
    }

    /**
     * Logs information about a successful response
     *
     * @param string $url URL of request
     * @param array $data POST data sent
     * @param Response $response Response to request
     * @return void
     */
    private function logSuccess($url, array $data, Response $response)
    {
        // Only write to log in debug mode
        if (!Configure::read('debug')) {
            return;
        }

        $successMsg = sprintf(
            "LGL integration returned status code %s.\nURL: %s\nPOST data:%s\nResponse: %s",
            $response->getStatusCode(),
            $url,
            print_r($data, true),
            $response->getBody()
        );
        Log::write('debug', $successMsg);
    }
}
