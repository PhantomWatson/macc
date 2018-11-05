<?php
/**
 * @var \App\View\AppView $this
 * @var \Cake\ORM\ResultSet $users
 */
?>

<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Memberships',
        [
            'prefix' => 'admin',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<p>
    The following are users who previously had MACC memberships but aren't members anymore. The membership levels
    listed are the most recent membership levels held by each user. These former members are listed in reverse
    chronological order of when their membership expired.
</p>

<?php if (empty($users)): ?>
    <p class="alert alert-info">
        No results were found.
    </p>
<?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Level
                </th>
                <th>
                    Expired
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= $user->name ?>
                        <br />
                        <a href="mailto:<?= $user->email ?>">
                            <?= $user->email ?>
                        </a>
                    </td>
                    <td>
                        <?= $user->memberships[0]->membership_level->name ?>
                    </td>
                    <td>
                        <?= \App\LocalTime\LocalTime::getDate($user->memberships[0]->expires) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

