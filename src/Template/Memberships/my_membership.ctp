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
        <table class="table" id="membership-details">
            <tbody>
                <tr>
                    <th>
                        Membership Level:
                    </th>
                    <td>
                        <?= $membership->membership_level['name'] ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <th>
                        Expires:
                    </th>
                    <td>
                        <?= $membership->expires->format('F j, Y') ?>
                    </td>
                    <td>
                        <?= $this->Html->link(
                            'Renew now',
                            [
                                'controller' => 'MembershipLevels',
                                'action' => 'view',
                                $membership->membership_level['id']
                            ],
                            ['class' => 'btn btn-default']
                        ) ?>
                    </td>
                </tr>
                <tr>
                    <th>
                        Automatic renewal:
                    </th>
                    <td>
                        <?php if ($membership->auto_renew): ?>
                            <span class="text-success">
                                On
                            </span>
                        <?php else: ?>
                            <span class="text-danger">
                                Off
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $this->Form->postLink(
                            'Turn automatic renewal '.($membership->auto_renew ? 'off' : 'on'),
                            [
                                'prefix' => false,
                                'controller' => 'Memberships',
                                'action' => 'toggleAutoRenewal',
                                ($membership->auto_renew ? 0 : 1)
                            ],
                            [
                                'class' => 'btn btn-default',
                                'confirm' => 'Are you sure you want to turn automatic membership renewal '.($membership->auto_renew ? 'off' : 'on').'?'
                            ]
                        ) ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>
