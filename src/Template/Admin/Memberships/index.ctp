<?php if (empty($members)): ?>
    <p class="alert alert-info">
        No current or expired memberships were found.
    </p>
<?php else: ?>
    <table class="table" id="admin-memberships">
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Membership Status
                </th>
                <th>
                    Expiration
                </th>
                <th>
                    Auto Renew?
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
<?php endif; ?>
