<?php if (empty($members)): ?>
    <p class="alert alert-info">
        Sorry, but we couldn't find any current members to display.
    </p>
<?php else: ?>
    <p>
        These are the current members of the Muncie Arts and Culture Council.
        Want to become a member?
        <?= $this->Html->link(
            'Learn about the membership options available.',
            [
                'controller' => 'MembershipLevels',
                'action' => 'index'
            ]
        ) ?>
    </p>
    <ul>
        <?php foreach ($members as $member): ?>
            <li>
                <?= $this->Html->link(
                    $member->name,
                    [
                        'controller' => 'Users',
                        'action' => 'view',
                        $member->id,
                        $member->slug
                    ]
                ) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
