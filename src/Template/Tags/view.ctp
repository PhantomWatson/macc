<?php if (empty($tag->users)): ?>
    Sorry, but we couldn't find any current members that are associated with <?= $tag->name ?>.
<?php else: ?>
    Members associated with <?= $tag->name ?>:

    <ul>
        <?php foreach ($tag->users as $user): ?>
            <li>
                <?= $this->Html->link(
                    $user->name,
                    [
                        'controller' => 'Users',
                        'action' => 'view',
                        $user->id,
                        $user->slug
                    ]
                ) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>