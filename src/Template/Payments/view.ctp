<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit Payment'), ['action' => 'edit', $payment->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete Payment'), ['action' => 'delete', $payment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $payment->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Payments'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Membership Levels'), ['controller' => 'MembershipLevels', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Membership Level'), ['controller' => 'MembershipLevels', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Membership Levels Users'), ['controller' => 'MembershipLevelsUsers', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Membership Levels User'), ['controller' => 'MembershipLevelsUsers', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="payments view large-9 medium-8 columns content">
    <h3><?= h($payment->id) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('User') ?></th>
            <td><?= $payment->has('user') ? $this->Html->link($payment->user->name, ['controller' => 'Users', 'action' => 'view', $payment->user->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Membership Level') ?></th>
            <td><?= $payment->has('membership_level') ? $this->Html->link($payment->membership_level->name, ['controller' => 'MembershipLevels', 'action' => 'view', $payment->membership_level->id]) : '' ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($payment->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Admin Adder Id') ?></th>
            <td><?= $this->Number->format($payment->admin_adder_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Refunder Id') ?></th>
            <td><?= $this->Number->format($payment->refunder_id) ?></td>
        </tr>
        <tr>
            <th><?= __('Refunded Date') ?></th>
            <td><?= h($payment->refunded_date) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($payment->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($payment->modified) ?></td>
        </tr>
    </table>
    <div class="row">
        <h4><?= __('Postback') ?></h4>
        <?= $this->Text->autoParagraph(h($payment->postback)); ?>
    </div>
    <div class="row">
        <h4><?= __('Notes') ?></h4>
        <?= $this->Text->autoParagraph(h($payment->notes)); ?>
    </div>
    <div class="related">
        <h4><?= __('Related Membership Levels Users') ?></h4>
        <?php if (!empty($payment->membership_levels_users)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Membership Level Id') ?></th>
                <th><?= __('Payment Id') ?></th>
                <th><?= __('Recurring Billing') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th><?= __('Expires') ?></th>
                <th><?= __('Canceled') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($payment->membership_levels_users as $membershipLevelsUsers): ?>
            <tr>
                <td><?= h($membershipLevelsUsers->id) ?></td>
                <td><?= h($membershipLevelsUsers->user_id) ?></td>
                <td><?= h($membershipLevelsUsers->membership_level_id) ?></td>
                <td><?= h($membershipLevelsUsers->payment_id) ?></td>
                <td><?= h($membershipLevelsUsers->recurring_billing) ?></td>
                <td><?= h($membershipLevelsUsers->created) ?></td>
                <td><?= h($membershipLevelsUsers->modified) ?></td>
                <td><?= h($membershipLevelsUsers->expires) ?></td>
                <td><?= h($membershipLevelsUsers->canceled) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'MembershipLevelsUsers', 'action' => 'view', $membershipLevelsUsers->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'MembershipLevelsUsers', 'action' => 'edit', $membershipLevelsUsers->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'MembershipLevelsUsers', 'action' => 'delete', $membershipLevelsUsers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $membershipLevelsUsers->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
