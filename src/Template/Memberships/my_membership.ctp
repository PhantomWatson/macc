<?php if (empty($membership)): ?>
    <p>
        You have not yet
        <?= $this->Html->link(
            'purchased a membership',
            [
                'prefix' => false,
                'controller' => 'MembershipLevels',
                'action' => 'index'
            ]
        ) ?>.
    </p>
<?php else: ?>
    <?php if ($membership->expires->format('U') < time()): ?>
        <p>
            Your membership expired on
            <?= $membership->expires->format('F j, Y') ?>.
            Would you like to
            <?= $this->Html->link(
                'renew your membership',
                [
                    'prefix' => false,
                    'controller' => 'MembershipLevels',
                    'action' => 'index'
                ]
            ) ?>?
        </p>
    <?php else: ?>
        <p>
            <strong>
                Membership Level:
            </strong>
            <?= $membership->membership_level['name'] ?>
        </p>
        <p>
            <strong>
                Expires:
            </strong>
            <?= $membership->expires->format('F j, Y') ?>
            <?= $this->Html->link(
                'Renew now',
                [
                    'controller' => 'MembershipLevels',
                    'action' => 'view',
                    $membership->membership_level['id']
                ],
                ['class' => 'btn btn-default']
            ) ?>
        </p>
        <p>
            <strong>
                Automatic renewal:
            </strong>
            <?php $autoRenewed = $membership->recurring_billing; ?>
            <?= $autoRenewed ? 'On' : 'Off' ?>
            <?= $this->Form->postLink(
                'Turn automatic renewal '.($autoRenewed ? 'off' : 'on'),
                [
                    'prefix' => false,
                    'controller' => 'Memberships',
                    'action' => 'toggleAutoRenewal',
                    ($autoRenewed ? 0 : 1)
                ],
                [
                    'class' => 'btn btn-default',
                    'confirm' => 'Are you sure you want to turn automatic membership renewal '.($autoRenewed ? 'off' : 'on').'?'
                ]
            ) ?>
        </p>
    <?php endif; ?>
<?php endif; ?>
