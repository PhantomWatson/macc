<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Payment $payment
 */
?>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Payment Records',
        [
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<p>
    You will need to manually add a payment record if someone pays for membership
    by a method <em>other than</em> through the MACC website, such as with a check. Please include
    information about how this payment was received in the <strong>notes</strong> section.
</p>

<p>
    Adding a payment record will grant the selected user one year of membership at the specified level.
</p>

<p>
    <strong>Does the user not have a website account?</strong>
    <?= $this->Html->link(
        'Create a new user account',
        [
            'prefix' => 'admin',
            'controller' => 'Users',
            'action' => 'add'
        ]
    ) ?>
    first, and select the "Grant this user a MACC membership" checkbox at the bottom of the form.
</p>

<?= $this->Form->create($payment) ?>

<?= $this->Form->control('user_id') ?>

<?= $this->Form->control('membership_level_id') ?>

<?= $this->Form->control('notes') ?>

<?= $this->Form->button(
    'Add Payment Record',
    ['class' => 'btn btn-primary']
) ?>

<?= $this->Form->end() ?>