<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?php if (empty($members)): ?>
    <p class="alert alert-info">
        No current or expired memberships were found.
    </p>
<?php else: ?>

    <p>
        Every 24 hours, memberships that are set to auto-renew and are about to expire will be automatically renewed.
        In case there's a problem with this happening automatically, this can also be done manually.
        <br />
        <?= $this->Html->link(
            'Process Auto-Renewals',
            [
                'prefix' => false,
                'controller' => 'Memberships',
                'action' => 'processRecurring'
            ],
            [
                'id' => 'auto-renew',
                'class' => 'btn btn-default'
            ]
        ) ?>
        <?= $this->Html->link(
            'View Auto-Renewal Logs',
            [
                'prefix' => 'admin',
                'controller' => 'Memberships',
                'action' => 'autoRenewalLogs'
            ],
            [
                'id' => 'auto-renew',
                'class' => 'btn btn-default'
            ]
        ) ?>
    </p>

    <div id="auto-renew-results" class="alert">
    </div>

    <hr />

    <?= $this->element('pagination') ?>

    <table class="table" id="admin-memberships">
        <thead>
            <tr>
                <th>
                    <?= $this->Paginator->sort('Users.name', 'Name') ?>
                </th>
                <th>
                    <?= $this->Paginator->sort('Memberships.membership_level_id', 'Membership Status') ?>
                </th>
                <th>
                    <?= $this->Paginator->sort('Memberships.expires', 'Expiration') ?>
                </th>
                <th>
                    <?= $this->Paginator->sort('Memberships.auto_renew', 'Auto Renew?') ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td>
                        <?= $member->name ?>
                    </td>

                    <?php
                        /** @var \Cake\I18n\Time $expires */
                        $expires = $member->memberships[0]['expires'];
                        $expired = date('Y-m-d') > $expires->format('Y-m-d');
                    ?>
                    <?php if ($expired): ?>
                        <td class="expired">
                            Expired
                        </td>
                        <td class="expired">
                            <?= $expires->format('F j, Y') ?>
                        </td>
                    <?php else: ?>
                        <td>
                            <?= $member->memberships[0]['membership_level']['name'] ?>
                        </td>
                        <td>
                            <?= $expires->format('F j, Y') ?>
                        </td>
                    <?php endif; ?>

                    <?php if ($member->memberships[0]['auto_renew']): ?>
                        <td>
                            Yes
                        </td>
                    <?php else: ?>
                        <td class="no-auto-renew">
                            No
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $this->element('pagination') ?>

<?php endif; ?>

<?php $this->append('buffered'); ?>
    membershipsList.init();
<?php $this->end(); ?>
