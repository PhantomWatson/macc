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
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');

        /** @var Membership $membership */
        $membership = $membershipsTable->find()->contain(['Users'])->orderDesc('expires')->first();

        /** @var MembershipMailer $mailer */
        $mailer = $this->getMailer('Membership');

        return $mailer->expiringMembership($membership);
    }
}
