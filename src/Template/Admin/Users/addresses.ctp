<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[] $users
 */
?>

<p>
    <?php if ($this->request->getQuery('all')): ?>
        The following are <strong>all of the mailing addresses</strong> that have been submitted by users of the
        membership website.
        <?= $this->Html->link(
            'Only show current members.',
            [
                'prefix' => 'admin',
                'controller' => 'Users',
                'action' => 'addresses'
            ]
        ) ?>
    <?php else: ?>
        The following are all of the <strong>current members</strong> who have submitted their mailing addresses to us.
        <?= $this->Html->link(
            'Show mailing addresses for non-members too.',
            [
                'prefix' => 'admin',
                'controller' => 'Users',
                'action' => 'addresses',
                '?' => ['all' => 1]
            ]
        ) ?>
    <?php endif; ?>
</p>

<?php if ($users): ?>
    <table class="table">
        <thead>
        <tr>
            <th>
                Member
            </th>
            <th>
                Level
            </th>
            <th>
                Address
            </th>
        </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= $user->name ?>
                    </td>
                    <td>
                        <?php if(isset($user->memberships[0])): ?>
                            <?= $user->memberships[0]->membership_level->name ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?= $user->address ?>
                        <br />
                        <?= $user->city ?>, <?= $user->state ?> <?= $user->zipcode ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-warning">
        Well, this is awkward. It looks like no current members have submitted their mailing addresses.
    </p>
<?php endif; ?>
