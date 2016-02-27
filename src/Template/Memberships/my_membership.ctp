<?php if (empty($user['memberships'])): ?>
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
    <?php if ($user['memberships'][0]->expires->format('U') < time()): ?>
        <p>
            Your membership expired on
            <?= $user['memberships'][0]->expires->format('F j, Y') ?>.
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
            <?= $user['memberships'][0]->membership_level['name'] ?>
        </p>
        <p>
            <strong>
                Expires:
            </strong>
            <?= $user['memberships'][0]->expires->format('F j, Y') ?>
        </p>
        <p>
            <strong>
                Automatic renewal:
            </strong>
            <?= $user['memberships'][0]->recurring_billing ? 'On' : 'Off' ?>
        </p>
    <?php endif; ?>
<?php endif; ?>
