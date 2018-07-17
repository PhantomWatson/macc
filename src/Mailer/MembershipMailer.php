<?php
namespace App\Mailer;

use App\Model\Entity\Membership;
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
}
