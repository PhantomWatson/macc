<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 */
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $membershipLevel->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $membershipLevel->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Membership Levels'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Payments'), ['controller' => 'Payments', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Payment'), ['controller' => 'Payments', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="membershipLevels form large-9 medium-8 columns content">
    <?= $this->Form->create($membershipLevel) ?>
    <fieldset>
        <legend><?= __('Edit Membership Level') ?></legend>
        <?php
            echo $this->Form->input('name');
            echo $this->Form->input('cost');
            echo $this->Form->input('description');
            echo $this->Form->input('users._ids', ['options' => $users]);
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
