<?php
namespace App\Mailer;

use App\Model\Entity\Membership;
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
                'membership' => $membership,
                'renewUrl' => Router::url([
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membership->membership_level_id
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
}
