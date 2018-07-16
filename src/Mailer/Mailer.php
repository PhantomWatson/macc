<?php
namespace App\Mailer;

use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Security;

class Mailer
{
    /**
     * Sends an email with a link that can be used in the next
     * 24 hours to give the user access to the password-reset page
     *
     * @param int $userId
     * @return boolean
     */
    public static function sendPasswordResetEmail($userId)
    {
        $timestamp = time();
        $hash = Mailer::getPasswordResetHash($userId, $timestamp);
        $resetUrl = Router::url([
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'resetPassword',
            $userId,
            $timestamp,
            $hash
        ], true);
        $email = new Email();
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($userId);
        $email->setTemplate('reset_password')
            ->setSubject('MACC website password reset')
            ->setTo($user->email)
            ->setViewVars(compact(
                'user',
                'resetUrl'
            ));
        return $email->send();
    }

    /**
     * Returns a hash for use in the emailed link to the password-reset page
     *
     * @param int $userId
     * @param int $timestamp
     * @return string
     */
    public static function getPasswordResetHash($userId, $timestamp)
    {
        return Security::hash($userId.$timestamp, 'sha1', true);
    }
}
