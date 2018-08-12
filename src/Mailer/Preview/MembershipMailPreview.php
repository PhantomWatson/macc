<?php
namespace App\Mailer\Preview;

use App\Mailer\MembershipMailer;
use App\Model\Entity\Membership;
use Cake\ORM\TableRegistry;
use DebugKit\Mailer\MailPreview;

class MembershipMailPreview extends MailPreview
{
    /**
     * Previews the 'expiring membership' email with an arbitrary membership
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

    public function autoRenewFailedCardDeclined()
    {
        $membership = $this->getArbitraryMembership();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->autoRenewFailedCardDeclined($membership);
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
}
