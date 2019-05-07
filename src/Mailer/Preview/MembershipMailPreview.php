<?php
namespace App\Mailer\Preview;

use App\Mailer\MembershipMailer;
use App\Model\Entity\Membership;
use App\Model\Entity\User;
use Cake\ORM\TableRegistry;
use DebugKit\Mailer\MailPreview;

class MembershipMailPreview extends MailPreview
{
    /**
     * Previews the 'expiring membership' email for a user with auto-renew on
     *
     * @return MembershipMailer
     */
    public function expiringMembershipAutoRenew()
    {
        $membership = $this->getArbitraryMembership();
        $membership->auto_renew = true;

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $mailer->expiringMembership($membership);

        return $mailer;
    }

    /**
     * Previews the 'expiring membership' email for a user with auto-renew off
     *
     * @return MembershipMailer
     */
    public function expiringMembershipManualRenew()
    {
        $membership = $this->getArbitraryMembership();
        $membership->auto_renew = false;

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $mailer->expiringMembership($membership);

        return $mailer;
    }

    /**
     * Previews the 'your card was declined' email
     *
     * @return MembershipMailer
     */
    public function autoRenewFailedCardDeclined()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $mailer->autoRenewFailedCardDeclined($membership);

        return $mailer;
    }

    /**
     * Previews the 'a membership could not be automatically renewed' email
     *
     * @return MembershipMailer
     */
    public function errorRenewingMembership()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $mailer->errorRenewingMembership($membership, 'Error details go here');

        return $mailer;
    }

    /**
     * Previews the 'your membership was automatically renewed' email
     *
     * @return MembershipMailer
     */
    public function membershipAutoRenewed()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $mailer->membershipAutoRenewed($membership);

        return $mailer;
    }

    /**
     * Returns an arbitrarily chosen membership record for email-previewing
     *
     * @return Membership
     */
    private function getArbitraryMembership()
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');

        return $membershipsTable
            ->find()
            ->contain(['Users', 'MembershipLevels'])
            ->orderDesc('expires')
            ->first();
    }

    /**
     * Previews the 'an account was created for you by an admin' email
     *
     * @return MembershipMailer
     */
    public function accountAddedByAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        /** @var User $user */
        $user = TableRegistry::getTableLocator()->get('Users')->find()->first();
        $password = 'randomlyGeneratedPassword';
        $mailer->accountAddedByAdmin($user, $password);

        return $mailer;
    }

    /**
     * Previews the 'a membership was created for you by an admin' email
     *
     * @return MembershipMailer
     */
    public function membershipAddedByAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $membership = $this->getArbitraryMembership();
        $mailer->membershipAddedByAdmin($membership);

        return $mailer;
    }

    /**
     * Previews the 'hey there admin, some admin added a membership' email
     *
     * @return MembershipMailer
     */
    public function membershipAddedByAdminToAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $membership = $this->getArbitraryMembership();
        /** @var User $adminUser */
        $adminUser = TableRegistry::getTableLocator()->get('Users')->find()->first();
        $recipientEmail = 'recipient@example.com';
        $mailer->membershipAddedByAdminToAdmin($recipientEmail, $adminUser->name, $membership);

        return $mailer;
    }
}
