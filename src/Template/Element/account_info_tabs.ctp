<?php
/**
 * @var \App\View\AppView $this
 */
    use Cake\Routing\Router;
    $tabs = [
        'Bio' => Router::url([
            'controller' => 'Users',
            'action' => 'myProfile'
        ]),
        'Tags' => Router::url([
            'controller' => 'Users',
            'action' => 'myTags'
        ]),
        'Pictures' => Router::url([
            'controller' => 'Users',
            'action' => 'myPictures'
        ])
    ];
?>
<ul class="nav nav-tabs" id="profile-tabs">
    <?php foreach ($tabs as $label => $url): ?>
        <?php $active = $url == $this->request->getRequestTarget(); ?>
        <li role="presentation" <?= $active ? 'class="active"' : '' ?>>
            <a href="<?= $url ?>" aria-controls="home">
                <?= $label ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
