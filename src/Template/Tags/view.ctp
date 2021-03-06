<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Tag $tag
 * @var \App\Model\Entity\User $user
 */
?>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Tags',
        [
            'controller' => 'Tags',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

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
