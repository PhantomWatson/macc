<?php
namespace App\Event;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;

class EmailListener implements EventListenerInterface
{
    /**
     * implementedEvents() method
     *
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Model.Membership.afterFirstPurchase' => 'sendNewMembershipEmail',
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

    }
}
