<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[] $users
 */
?>

<p>
    The following are all of the current members who have submitted their mailing addresses to us.
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
                        <?= $user->memberships[0]->membership_level->name ?>
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
