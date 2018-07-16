<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div id="edit_account">
    <?= $this->Form->create($user) ?>

    <?= $this->Form->control('name') ?>
    <?= $this->Form->control('email') ?>

    <?= $this->Form->button(
        'Update',
        ['class' => 'btn btn-primary']
    ) ?>

    <?= $this->Form->end() ?>
</div>
