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

    <p class="text-right">
        <?= $this->Form->button(
            $this->request->getQuery('flow') ? 'Finish' : 'Submit',
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <?= $this->Form->end() ?>

    <p class="alert alert-info">
        <strong>Why do we collect mailing addresses?</strong>
        <br />
        We collect mailing addresses from our members because we want to stay in touch. Every now and then, we host special events and programs such as the Mayor's Arts Awards and our annual reception. We'll always send digital communications, but we like the opportunity to send snail mail announcements as well.
    </p>
</div>
