<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('New Payment'), ['action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Users'), ['controller' => 'Users', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New User'), ['controller' => 'Users', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Membership Levels'), ['controller' => 'MembershipLevels', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Membership Level'), ['controller' => 'MembershipLevels', 'action' => 'add']) ?></li>
        <li><?= $this->Html->link(__('List Membership Levels Users'), ['controller' => 'MembershipLevelsUsers', 'action' => 'index']) ?></li>
        <li><?= $this->Html->link(__('New Membership Levels User'), ['controller' => 'MembershipLevelsUsers', 'action' => 'add']) ?></li>
    </ul>
</nav>
<div class="payments index large-9 medium-8 columns content">
    <h3><?= __('Payments') ?></h3>
    <table cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('id') ?></th>
                <th><?= $this->Paginator->sort('user_id') ?></th>
                <th><?= $this->Paginator->sort('membership_level_id') ?></th>
                <th><?= $this->Paginator->sort('admin_adder_id') ?></th>
                <th><?= $this->Paginator->sort('refunded_date') ?></th>
                <th><?= $this->Paginator->sort('refunder_id') ?></th>
                <th><?= $this->Paginator->sort('created') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= $this->Number->format($payment->id) ?></td>
                <td><?= $payment->has('user') ? $this->Html->link($payment->user->name, ['controller' => 'Users', 'action' => 'view', $payment->user->id]) : '' ?></td>
                <td><?= $payment->has('membership_level') ? $this->Html->link($payment->membership_level->name, ['controller' => 'MembershipLevels', 'action' => 'view', $payment->membership_level->id]) : '' ?></td>
                <td><?= $this->Number->format($payment->admin_adder_id) ?></td>
                <td><?= h($payment->refunded_date) ?></td>
                <td><?= $this->Number->format($payment->refunder_id) ?></td>
                <td><?= h($payment->created) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['action' => 'view', $payment->id]) ?>
                    <?= $this->Html->link(__('Edit'), ['action' => 'edit', $payment->id]) ?>
                    <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $payment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $payment->id)]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
        </ul>
        <p><?= $this->Paginator->counter() ?></p>
    </div>
</div>
