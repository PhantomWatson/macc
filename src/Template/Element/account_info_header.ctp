<?php
/**
 * @var \App\View\AppView $this
 * @var array $authUser
 * @var string $profileUnavailableMsg
 */
    use Cake\Routing\Router;
    $tabs = [
        'Bio' => Router::url([
            'controller' => 'Users',
            'action' => 'myBio'
        ]),
        'Tags' => Router::url([
            'controller' => 'Users',
            'action' => 'myTags'
        ]),
        'Pictures' => Router::url([
            'controller' => 'Users',
            'action' => 'myPictures'
        ]),
        'Contact' => Router::url([
            'controller' => 'Users',
            'action' => 'myContact'
        ])
    ];
?>

<?php if (isset($profileUnavailableMsg)): ?>
    <p class="alert alert-warning">
        <?= $profileUnavailableMsg ?>
    </p>
<?php else: ?>
    <p class="alert alert-info">
        <?php
            $url = Router::url([
                'controller' => 'Users',
                'action' => 'view',
                $authUser['id'],
                $authUser['slug']
            ], true);
        ?>
        My member profile: <?= $this->Html->link($url, $url) ?>
    </p>
<?php endif; ?>

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
