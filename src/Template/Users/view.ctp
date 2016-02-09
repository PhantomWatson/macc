<?php
    use League\CommonMark\CommonMarkConverter;
    $converter = new CommonMarkConverter();
?>

<section>
    <h2>
        About
    </h2>
    <?php if (empty($user->profile)): ?>
        <p class="alert alert-info">
            Sorry, this person does not have any profile information yet.
        </p>
    <?php else: ?>
        <p>
            <?= $converter->convertToHtml($user->profile) ?>
        </p>
    <?php endif; ?>
</section>

<section>
    <?php if (! empty($user->tags)): ?>
        <h2>
            Tags
        </h2>
        <ul>
            <?php foreach ($user->tags as $tag): ?>
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
</section>