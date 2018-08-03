<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>

<?= $this->element('account_info_header') ?>

<div id="edit_account">
    <?= $this->Form->create($user) ?>

    <?= $this->Form->control('email') ?>
    <?= $this->Form->control('address', ['label' => 'Mailing address']) ?>
    <?= $this->Form->control('city') ?>
    <?= $this->Form->control('state', ['label' => 'State abbreviation']) ?>
    <?= $this->Form->control('zipcode') ?>

    <?= $this->Form->button(
        'Submit',
        ['class' => 'btn btn-primary']
    ) ?>

    <?= $this->Form->end() ?>
</div>
