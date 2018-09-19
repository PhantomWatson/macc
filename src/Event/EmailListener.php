<?php
namespace App\Event;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Mailer\MailerAwareTrait;

class EmailListener implements EventListenerInterface
{
    use MailerAwareTrait;

    /**
     * implementedEvents() method
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Model.Membership.afterFirstPurchase' => 'sendNewMembershipEmail',
            'Model.Membership.afterAdminGranted' => 'sendNewAdminGrantedMembershipEmail'
        ];
    }

    /**
     * Sends emails informing newMemberAlertRecipients that someone has purchased a new MACC membership
     *
     * @param \Cake\Event\Event $event Event
     * @param array $meta Array of metadata (userId, etc.)
     * @return void
     */
    public function sendNewMembershipEmail(Event $event, array $meta = [])
    {
        $recipientEmails = Configure::read('newMemberAlertRecipients');
        if (!is_array($recipientEmails) || empty($recipientEmails)) {
            return;
        }

        foreach ($recipientEmails as $email) {
            $this->getMailer('Membership')->send('newMember', [
                $email,
                $meta['membership']
            ]);
        }
    }

    /**
     * Sends emails informing newMemberAlertRecipients that someone has been granted a membership by an admin
     *
     * @param \Cake\Event\Event $event Event
     * @param array $meta Array of metadata (userId, etc.)
     * @return void
     */
    public function sendNewAdminGrantedMembershipEmail(Event $event, array $meta = [])
    {
        $recipientEmails = Configure::read('newMemberAlertRecipients');
        if (!is_array($recipientEmails) || empty($recipientEmails)) {
            return;
        }

        foreach ($recipientEmails as $email) {
            $this->getMailer('Membership')->send('membershipAddedByAdminToAdmin', [
                $email,
                $meta['adminUserName'],
                $meta['membership']
            ]);
        }
    }
}
