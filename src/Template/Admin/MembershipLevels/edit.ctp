<?php
/**
 * @var AppView $this
 * @var MembershipLevel $membershipLevel
 * @var array $users
 */

use App\Model\Entity\MembershipLevel;
use App\View\AppView;
?>
<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= 'Actions' ?></li>
        <li><?= $this->Form->postLink(
                'Delete',
                ['action' => 'delete', $membershipLevel->id],
                ['confirm' => 'Are you sure you want to delete # {0}?', $membershipLevel->id]
            )
        ?></li>
        <li><?= $this->Html->link('List Membership Levels', ['action' => 'index']) ?></li>
        <li><?= $this->Html->link('List Payments', ['controller' => 'Payments', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link('New Payment', ['controller' => 'Payments', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link('List Users', ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link('New User', ['controller' => 'Users', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="membershipLevels form large-9 medium-8 columns content">
    <?= $this->Form->create($membershipLevel) ?>
    <fieldset>
        <legend><?= 'Edit Membership Level' ?></legend>
        <?php
            echo $this->Form->control('name');
            echo $this->Form->control('cost');
            echo $this->Form->control('description');
            echo $this->Form->control('users._ids', ['options' => $users]);
        ?>
    </fieldset>
    <?= $this->Form->button('Submit') ?>
    <?= $this->Form->end() ?>
</div>
