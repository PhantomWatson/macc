<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div id="edit_account">
    <?= $this->Form->create($user) ?>

    <?= $this->Form->input('name') ?>
    <?= $this->Form->input('email') ?>

    <?= $this->Form->button(
        'Update',
        ['class' => 'btn btn-primary']
    ) ?>

    <?= $this->Form->end() ?>
</div>
