<?php if (empty($tags)): ?>
    <p class="alert alert-info">
        Sorry, but we couldn't find any art tags associated with any current members.
    </p>
<?php else: ?>
    <p>
        Click on any of the following art tags to see related
        members of the Muncie Arts and Culture Council.
    </p>
    <ul>
        <?php foreach ($tags as $tag): ?>
            <li>
                <?= $this->Html->link(
                    $tag->name,
                    [
                        'controller' => 'Tags',
                        'action' => 'view',
                        $tag->slug
                    ]
                ) ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
