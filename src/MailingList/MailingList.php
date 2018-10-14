<?php
namespace App\MailingList;

use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Log\Log;
use DrewM\MailChimp\MailChimp;

class MailingList
{
    /**
     * @return \DrewM\MailChimp\MailChimp
     * @throws \Exception
     */
    public static function getMailChimpObject()
    {
        $apiKey = Configure::read('mailChimpApiKey');
        $MailChimp = new MailChimp($apiKey);
        if (Configure::read('debug')) {
            $MailChimp->verify_ssl = false;
        }
        return $MailChimp;
    }

    /**
     * Adds a user to the MailChimp mailing list if user has not
     * already been added. Returns TRUE if it's confirmed that the
     * user is on the mailing list, FALSE if an attempt to add the
     * user fails
     *
     * @param User $user
     * @return boolean
     * @throws \Exception
     */
    public static function addToList($user)
    {
        $isSubscribed = MailingList::isSubscribed($user->email);
        if ($isSubscribed) {
            return true;
        }
        $MailChimp = MailingList::getMailChimpObject();
        $listId = Configure::read('mailChimpListId');
        $nameSplit = explode(' ', $user->name);
        $firstName = array_shift($nameSplit);
        $lastName = implode(' ', $nameSplit);
        $response = $MailChimp->post("lists/$listId/members", [
            'email_address' => $user->email,
            'merge_fields' => [
                'FNAME' => $firstName,
                'LNAME' => $lastName
            ],
            'status' => 'subscribed'
        ]);
        return isset($response['status']) && $response['status'] == 'subscribed';
    }

    /**
     * Updates a user's email address in MailChimp and returns a boolean indicating success
     *
     * @param string $oldEmail Previous email address
     * @param string $newEmail What the email address is being updated to
     * @return boolean
     * @throws \Exception
     */
    public static function updateEmailAddress($oldEmail, $newEmail)
    {
        $MailChimp = MailingList::getMailChimpObject();
        $listId = Configure::read('mailChimpListId');
        $subscriberHash = $MailChimp->subscriberHash($oldEmail);
        $response = $MailChimp->post(
            "lists/$listId/members/$subscriberHash",
            ['email_address' => $newEmail]
        );

        if (isset($response['status']) && $response['status'] == 'subscribed') {
            return true;
        }

        // Log error
        $msg = "Error updating email address from $oldEmail to $newEmail in MailChimp. Details: \n";
        $msg .= print_r($response, true);
        Log::write('error', $msg);

        return false;
    }

    /**
     * Returns TRUE if an email address is subscribed
     * to the MailChimp mailing list
     *
     * @param string $email
     * @return boolean
     * @throws \Exception
     */
    public static function isSubscribed($email)
    {
        $MailChimp = MailingList::getMailChimpObject();
        $listId = Configure::read('mailChimpListId');
        $subscriberHash = $MailChimp->subscriberHash($email);
        $response = $MailChimp->get("lists/$listId/members/$subscriberHash");
        return isset($response['status']) && $response['status'] != 404;
    }
}
