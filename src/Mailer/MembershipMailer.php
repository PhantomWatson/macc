<?php
namespace App\Mailer;

use App\LocalTime\LocalTime;
use App\Model\Entity\Membership;
use App\Model\Entity\MembershipLevel;
use App\Model\Entity\User;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
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
     * @return void
     */
    public function newMember($recipientEmail, $membership)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->get($membership->user_id);
        $membershipLevelTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        $membershipLevel = $membershipLevelTable->get($membership->membership_level_id);

        $this
            ->setTo($recipientEmail)
            ->setSubject('MACC - New Member: ' . $user->name)
            ->setViewVars([
                'membership' => $membership,
                'membershipLevel' => $membershipLevel,
                'user' => $user,
                'profileUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'view',
                    $user->id,
                    $user->slug
                ], true)
            ])
            ->viewBuilder()
            ->setTemplate('new_member');
    }

    /**
     * Defines an email informing a user that their membership is about to expire
     *
     * @param Membership $membership Membership entity
     * @return void
     */
    public function expiringMembership($membership)
    {
        $expirationString = $this->getExpirationString($membership->expires);
        $autoRenew = (bool)$membership->auto_renew;
        $subject = sprintf(
            'Muncie Arts and Culture Council - Membership %s %s',
            $autoRenew ? 'automatically renewing' : 'expiring',
            $expirationString
        );

        $this
            ->setTo($membership->user->email)
            ->setSubject($subject)
            ->setViewVars([
                'userName' => $membership->user->name,
                'autoRenew' => $autoRenew,
                'expires' => LocalTime::get($membership->expires, 'MMMM d'),
                'membershipLevel' => $membership->membership_level,
                'renewUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membership->membership_level_id,
                    '?' => ['renewing' => 1]
                ], true),
                'membershipLevelsUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Memberships',
                    'action' => 'levels',
                    '?' => [
                        'renewing' => 1,
                        'mlid' => $membership->membership_level->id
                    ]
                ], true),
                'myMembershipUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Memberships',
                    'action' => 'myMembership'
                ], true)
            ])
            ->viewBuilder()
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
     * @return void
     */
    public function autoRenewFailedCardDeclined(Membership $membership)
    {
        $this
            ->setTo($membership->user->email)
            ->setSubject('Muncie Arts and Culture Council - Error renewing membership')
            ->setViewVars([
                'userName' => $membership->user->name,
                'renewUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membership->membership_level_id,
                    '?' => ['renewing' => 1]
                ], true)
            ])
            ->viewBuilder()
            ->setTemplate('card_declined');
    }

    /**
     * Defines an email that informs an admin that there was an error automatically renewing a membership
     * (other than a declined card)
     *
     * @param Membership $membership Membership entity
     * @param string $errorMsg Error message
     * @return void
     */
    public function errorRenewingMembership(Membership $membership, $errorMsg)
    {
        $this
            ->setTo(Configure::read('admin_email'))
            ->setSubject(sprintf(
                'Muncie Arts and Culture Council - Error renewing %s\'s membership',
                $membership->user->name
            ))
            ->setViewVars([
                'userName' => $membership->user->name,
                'errorMsg' => $errorMsg
            ])
            ->viewBuilder()
            ->setTemplate('error_renewing');
    }

    /**
     * Defines an email that informs a user that their membership was just auto-renewed
     *
     * @param Membership $membership Membership entity
     * @return void
     */
    public function membershipAutoRenewed(Membership $membership)
    {
        $this
            ->setTo($membership->user->email)
            ->setSubject('Muncie Arts and Culture Council - Membership automatically renewed')
            ->setViewVars([
                'userName' => $membership->user->name,
                'profileUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true)
            ])
            ->viewBuilder()
            ->setTemplate('membership_auto_renewed');
    }

    /**
     * Defines an email that informs a user that their account was added by an administrator
     *
     * @param User $user User entity
     * @param string $password Plain-text password
     * @return void
     */
    public function accountAddedByAdmin(User $user, $password)
    {
        $this
            ->setTo($user->email)
            ->setSubject('Muncie Arts and Culture Council - User account created')
            ->setViewVars([
                'userName' => $user->name,
                'profileUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true),
                'password' => $password,
                'loginUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'login'
                ], true),
                'userEmail' => $user->email
            ])
            ->viewBuilder()
            ->setTemplate('account_added_by_admin');
    }

    /**
     * Defines an email that informs a user that their membership was added by an administrator
     *
     * @param Membership $membership Membership entity
     * @return void
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

        $this
            ->setTo($user->email)
            ->setSubject('Muncie Arts and Culture Council - Membership added')
            ->setViewVars([
                'userName' => $user->name,
                'profileUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'myBio',
                    '?' => ['flow' => 1]
                ], true),
                'membershipLevelName' => $membershipLevel->name,
                'expires' => LocalTime::getDate($membership->expires)
            ])
            ->viewBuilder()
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
     * @return void
     */
    public function membershipAddedByAdminToAdmin($recipientEmail, $adminUserName, Membership $membership)
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $member = $usersTable->get($membership->user_id);
        $membershipLevelTable = TableRegistry::getTableLocator()->get('MembershipLevels');
        $membershipLevel = $membershipLevelTable->get($membership->membership_level_id);

        $this
            ->setTo($recipientEmail)
            ->setSubject('MACC - New Member: ' . $member->name)
            ->setViewVars([
                'membership' => $membership,
                'membershipLevel' => $membershipLevel,
                'member' => $member,
                'adminUserName' => $adminUserName,
                'profileUrl' => Router::url([
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'view',
                    $member->id,
                    $member->slug
                ], true)
            ])
            ->viewBuilder()
            ->setTemplate('membership_added_by_admin_to_admin');
    }
}
