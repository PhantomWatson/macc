<?php
namespace App\MailingList;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use DrewM\MailChimp\MailChimp;

class MailingList
{
    /**
     * @return DrewM\MailChimp\MailChimp
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
     * @param string $email
     * @return boolean
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
     * Returns TRUE if an email address is subscribed
     * to the MailChimp mailing list
     *
     * @param string $email
     * @return boolean
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
