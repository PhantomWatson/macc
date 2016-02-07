<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $payment->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $payment->id)]
            )
        ?></li>
        <li><?= $this->Html->link(__('List Payments'), ['action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Membership Levels'), ['controller' => 'MembershipLevels', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Membership Level'), ['controller' => 'MembershipLevels', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Membership Levels Users'), ['controller' => 'MembershipLevelsUsers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Membership Levels User'), ['controller' => 'MembershipLevelsUsers', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="payments form large-9 medium-8 columns content">
    <?= $this->Form->create($payment) ?>
    <fieldset>
        <legend><?= __('Edit Payment') ?></legend>
        <?php
            echo $this->Form->input('user_id', ['options' => $users]);
            echo $this->Form->input('membership_level_id', ['options' => $membershipLevels]);
            echo $this->Form->input('postback');
            echo $this->Form->input('admin_adder_id');
            echo $this->Form->input('notes');
            echo $this->Form->input('refunded_date');
            echo $this->Form->input('refunder_id');
        ?>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>
</div>
