<?php
namespace App\Mailer;

use App\Model\Entity\Membership;
use App\Model\Entity\MembershipLevel;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

class MembershipMailer extends Mailer
{
    /**
     * Defines an email informing an admin that a user has purchased their first membership
     *
     * @param string $recipientEmail Email address of recipient
     * @param Membership $membership New membership entity
     * @return Email
     */
    public function newMember($recipientEmail, $membership)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($membership->user_id);
        $membershipLevelTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        $membershipLevel = $membershipLevelTable->get($membership->membership_level_id);

        return $this
            ->setTo($recipientEmail)
            ->setSubject('MACC - New Member: ' . $user->name)
            ->setViewVars([
                'membership' => $membership,
                'membershipLevel' => $membershipLevel,
                'user' => $user,
                'profileUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'view',
                    $user->id,
                    $user->slug
                ], true)
            ])
            ->setTemplate('new_member');
    }

    /**
     * Defines an email informing a user that their membership is about to expire
     *
     * @param Membership $membership Membership entity
     * @return Email
     */
    public function expiringMembership($membership)
    {
        $expirationString = $this->getExpirationString($membership->expires);

        return $this
            ->setTo($membership->user->email)
            ->setSubject('Muncie Arts and Culture Council - Membership expiring ' . $expirationString)
            ->setViewVars([
                'userName' => $membership->user->name,
                'autoRenew' => (bool)$membership->auto_renew,
                'expires' => $membership->expires->format('F jS'),
                'renewUrl' => Router::url([
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membership->membership_level_id,
                    '?' => ['renewing' => 1]
                ], true)
            ])
            ->setTemplate('expiring_membership');
    }

    /**
     * Returns a string describing when the user's membership expires
     *
     * @param Time|FrozenTime $expires Time that the user's membership expires
     * @return string
     */
    private function getExpirationString($expires)
    {
        $nextWeek = new Time('+1 week');
        if ($expires->format('F j, Y') == $nextWeek->format('F j, Y')) {
            return 'in one week';
        }

        $tomorrow = new Time('+1 day');
        if ($expires->format('F j, Y') == $tomorrow->format('F j, Y')) {
            return 'tomorrow';
        }

        return 'soon';
    }

    /**
     * Defines an email that informs a user that their membership could not be renewed because of a declined payment
     *
     * @param Membership $membership Membership entity
     * @return Email
     */
    public function autoRenewFailedCardDeclined(Membership $membership)
    {
        return $this
            ->setTo($membership->user->email)
            ->setSubject('Muncie Arts and Culture Council - Error renewing membership')
            ->setViewVars([
                'userName' => $membership->user->name,
                'renewUrl' => Router::url([
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membership->membership_level_id,
                    '?' => ['renewing' => 1]
                ], true)
            ])
            ->setTemplate('card_declined');
    }

    /**
     * Defines an email that informs an admin that there was an error automatically renewing a membership
     * (other than a declined card)
     *
     * @param Membership $membership Membership entity
     * @param string $errorMsg Error message
     * @return Email
     */
    public function errorRenewingMembership(Membership $membership, $errorMsg)
    {
        return $this
            ->setTo(Configure::read('admin_email'))
            ->setSubject(sprintf(
                'Muncie Arts and Culture Council - Error renewing %s\'s membership',
                $membership->user->name
            ))
            ->setViewVars([
                'userName' => $membership->user->name,
                'errorMsg' => $errorMsg
            ])
            ->setTemplate('error_renewing');
    }

    /**
     * Defines an email that informs a user that their membership was just auto-renewed
     *
     * @param Membership $membership Membership entity
     * @return Email
     */
    public function membershipAutoRenewed(Membership $membership)
    {
        return $this
            ->setTo($membership->user->email)
            ->setSubject('Muncie Arts and Culture Council - Membership automatically renewed')
            ->setViewVars([
                'userName' => $membership->user->name,
                'profileUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true)
            ])
            ->setTemplate('membership_auto_renewed');
    }

    /**
     * Defines an email that informs a user that their account was added by an administrator
     *
     * @param User $user User entity
     * @param string $password Plain-text password
     * @return Email
     */
    public function accountAddedByAdmin(User $user, $password)
    {
        return $this
            ->setTo($user->email)
            ->setSubject('Muncie Arts and Culture Council - User account created')
            ->setViewVars([
                'userName' => $user->name,
                'profileUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true),
                'password' => $password,
                'loginUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'login'
                ], true),
                'userEmail' => $user->email
            ])
            ->setTemplate('account_added_by_admin');
    }

    /**
     * Defines an email that informs a user that their membership was added by an administrator
     *
     * @param Membership $membership Membership entity
     * @return Email
     */
    public function membershipAddedByAdmin(Membership $membership)
    {
        $membershipLevelTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        /** @var MembershipLevel $membershipLevel */
        $membershipLevel = $membershipLevelTable->get($membership->membership_level_id);
        if (isset($membership->user)) {
            $user = $membership->user;
        } else {
            $user = TableRegistry::getTableLocator()->get('Users')->get($membership->user_id);
        }

        return $this
            ->setTo($user->email)
            ->setSubject('Muncie Arts and Culture Council - Membership added')
            ->setViewVars([
                'userName' => $user->name,
                'profileUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true),
                'membershipLevelName' => $membershipLevel->name,
                'expires' => $membership->expires->format('F j, Y')
            ])
            ->setTemplate('membership_added_by_admin');
    }

    /**
     * Defines an email to an admin informing them that a membership was added by an admin
     *
     * with an awkward method name
     *
     * @param string $recipientEmail Recipient email address
     * @param string $adminUserName Admin user entity name
     * @param Membership $membership Membership entity
     * @return Email
     */
    public function membershipAddedByAdminToAdmin($recipientEmail, $adminUserName, Membership $membership)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $member = $usersTable->get($membership->user_id);
        $membershipLevelTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        $membershipLevel = $membershipLevelTable->get($membership->membership_level_id);

        return $this
            ->setTo($recipientEmail)
            ->setSubject('MACC - New Member: ' . $member->name)
            ->setViewVars([
                'membership' => $membership,
                'membershipLevel' => $membershipLevel,
                'member' => $member,
                'adminUserName' => $adminUserName,
                'profileUrl' => Router::url([
                    'controller' => 'Users',
                    'action' => 'view',
                    $member->id,
                    $member->slug
                ], true)
            ])
            ->setTemplate('membership_added_by_admin_to_admin');
    }
}
