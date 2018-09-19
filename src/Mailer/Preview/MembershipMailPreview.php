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
     * Previews the 'expiring membership' email
     *
     * @return \Cake\Mailer\Email
     */
    public function expiringMembership()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->expiringMembership($membership);
    }

    /**
     * Previews the 'your card was declined' email
     *
     * @return \Cake\Mailer\Email
     */
    public function autoRenewFailedCardDeclined()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->autoRenewFailedCardDeclined($membership);
    }

    /**
     * Previews the 'a membership could not be automatically renewed' email
     *
     * @return \Cake\Mailer\Email
     */
    public function errorRenewingMembership()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->errorRenewingMembership($membership, 'Error details go here');
    }

    /**
     * Previews the 'your membership was automatically renewed' email
     *
     * @return \Cake\Mailer\Email
     */
    public function membershipAutoRenewed()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->membershipAutoRenewed($membership);
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
            ->contain(['Users'])
            ->orderDesc('expires')
            ->first();
    }

    /**
     * Previews the 'an account was created for you by an admin' email
     *
     * @return \Cake\Mailer\Email
     */
    public function accountAddedByAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        /** @var User $user */
        $user = TableRegistry::getTableLocator()->get('Users')->find()->first();
        $password = 'randomlyGeneratedPassword';

        return $mailer->accountAddedByAdmin($user, $password);
    }

    /**
     * Previews the 'a membership was created for you by an admin' email
     *
     * @return \Cake\Mailer\Email
     */
    public function membershipAddedByAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $membership = $this->getArbitraryMembership();

        return $mailer->membershipAddedByAdmin($membership);
    }

    /**
     * Previews the 'hey there admin, some admin added a membership' email
     *
     * @return \Cake\Mailer\Email
     */
    public function membershipAddedByAdminToAdmin()
    {
        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');
        $membership = $this->getArbitraryMembership();
        /** @var User $adminUser */
        $adminUser = TableRegistry::getTableLocator()->get('Users')->find()->first();
        $recipientEmail = 'recipient@example.com';

        return $mailer->membershipAddedByAdminToAdmin($recipientEmail, $adminUser->name, $membership);
    }
}
