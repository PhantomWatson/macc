<?php if ($mainPicture['fullsize']): ?>
    <div id="main-profile-picture">
        <a href="/img/members/<?= $user->id ?>/<?= $mainPicture['fullsize'] ?>" title="Click to view full-sized picture">
            <img src="/img/members/<?= $user->id ?>/<?= $mainPicture['thumb'] ?>" alt="Main profile picture" />
        </a>
    </div>
<?php endif; ?>

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
            <?= $this->element('commonmark_parsed', ['input' => $user->profile]) ?>
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

<?php
    $nonMainPictures = [];
    foreach ($user->pictures as $picture) {
        if ($picture['id'] != $user['main_picture_id']) {
            $nonMainPictures[] = $picture;
        }
    }
?>

<?php if (! empty($nonMainPictures)): ?>
    <section id="profile-pictures">
        <h2>
            Pictures
        </h2>
        <ul>
            <?php foreach ($nonMainPictures as $picture): ?>
                <li>
                    <a href="/img/members/<?= $user->id ?>/<?= $picture['filename'] ?>" title="Click to view full-sized picture">
                        <img src="/img/members/<?= $user->id ?>/<?= $picture['thumbnail_filename'] ?>" alt="Profile picture" />
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php
        $this->Html->css('/magnific-popup/magnific-popup.css', ['block' => 'css']);
        $this->Html->script('/magnific-popup/jquery.magnific-popup.js', ['block' => 'script']);
    ?>
    <?php $this->append('buffered'); ?>
        memberProfile.init();
    <?php $this->end(); ?>

<?php endif; ?>
