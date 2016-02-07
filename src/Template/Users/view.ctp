<nav class="large-3 medium-4 columns" id="actions-sidebar">
    <ul class="side-nav">
        <li class="heading"><?= __('Actions') ?></li>
        <li><?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id]) ?> </li>
        <li><?= $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id)]) ?> </li>
        <li><?= $this->Html->link(__('List Users'), ['action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New User'), ['action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Payments'), ['controller' => 'Payments', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Payment'), ['controller' => 'Payments', 'action' => 'add']) ?> </li>
        <li><?= $this->Html->link(__('List Membership Levels'), ['controller' => 'MembershipLevels', 'action' => 'index']) ?> </li>
        <li><?= $this->Html->link(__('New Membership Level'), ['controller' => 'MembershipLevels', 'action' => 'add']) ?> </li>
    </ul>
</nav>
<div class="users view large-9 medium-8 columns content">
    <h3><?= h($user->name) ?></h3>
    <table class="vertical-table">
        <tr>
            <th><?= __('Name') ?></th>
            <td><?= h($user->name) ?></td>
        </tr>
        <tr>
            <th><?= __('Email') ?></th>
            <td><?= h($user->email) ?></td>
        </tr>
        <tr>
            <th><?= __('Password') ?></th>
            <td><?= h($user->password) ?></td>
        </tr>
        <tr>
            <th><?= __('Role') ?></th>
            <td><?= h($user->role) ?></td>
        </tr>
        <tr>
            <th><?= __('Id') ?></th>
            <td><?= $this->Number->format($user->id) ?></td>
        </tr>
        <tr>
            <th><?= __('Created') ?></th>
            <td><?= h($user->created) ?></td>
        </tr>
        <tr>
            <th><?= __('Modified') ?></th>
            <td><?= h($user->modified) ?></td>
        </tr>
    </table>
    <div class="related">
        <h4><?= __('Related Payments') ?></h4>
        <?php if (!empty($user->payments)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('User Id') ?></th>
                <th><?= __('Membership Level Id') ?></th>
                <th><?= __('Postback') ?></th>
                <th><?= __('Admin Adder Id') ?></th>
                <th><?= __('Notes') ?></th>
                <th><?= __('Refunded Date') ?></th>
                <th><?= __('Refunder Id') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->payments as $payments): ?>
            <tr>
                <td><?= h($payments->id) ?></td>
                <td><?= h($payments->user_id) ?></td>
                <td><?= h($payments->membership_level_id) ?></td>
                <td><?= h($payments->postback) ?></td>
                <td><?= h($payments->admin_adder_id) ?></td>
                <td><?= h($payments->notes) ?></td>
                <td><?= h($payments->refunded_date) ?></td>
                <td><?= h($payments->refunder_id) ?></td>
                <td><?= h($payments->created) ?></td>
                <td><?= h($payments->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'Payments', 'action' => 'view', $payments->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'Payments', 'action' => 'edit', $payments->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'Payments', 'action' => 'delete', $payments->id], ['confirm' => __('Are you sure you want to delete # {0}?', $payments->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
    <div class="related">
        <h4><?= __('Related Membership Levels') ?></h4>
        <?php if (!empty($user->membership_levels)): ?>
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th><?= __('Id') ?></th>
                <th><?= __('Name') ?></th>
                <th><?= __('Cost') ?></th>
                <th><?= __('Description') ?></th>
                <th><?= __('Created') ?></th>
                <th><?= __('Modified') ?></th>
                <th class="actions"><?= __('Actions') ?></th>
            </tr>
            <?php foreach ($user->membership_levels as $membershipLevels): ?>
            <tr>
                <td><?= h($membershipLevels->id) ?></td>
                <td><?= h($membershipLevels->name) ?></td>
                <td><?= h($membershipLevels->cost) ?></td>
                <td><?= h($membershipLevels->description) ?></td>
                <td><?= h($membershipLevels->created) ?></td>
                <td><?= h($membershipLevels->modified) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__('View'), ['controller' => 'MembershipLevels', 'action' => 'view', $membershipLevels->id]) ?>

                    <?= $this->Html->link(__('Edit'), ['controller' => 'MembershipLevels', 'action' => 'edit', $membershipLevels->id]) ?>

                    <?= $this->Form->postLink(__('Delete'), ['controller' => 'MembershipLevels', 'action' => 'delete', $membershipLevels->id], ['confirm' => __('Are you sure you want to delete # {0}?', $membershipLevels->id)]) ?>

                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
    </div>
</div>
