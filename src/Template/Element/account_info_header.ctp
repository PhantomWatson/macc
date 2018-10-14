<?php
/**
 * @var \App\Model\Entity\Membership $authUserMembership
 * @var \App\View\AppView $this
 * @var array $authUser
 * @var bool $qualifiesForLogo
 * @var string $profileUnavailableMsg
 */
    use Cake\Routing\Router;

    $queryStrings = $this->request->getQuery('flow') ? ['flow' => 1] : [];
    $tabs = [
        'Bio' => Router::url([
            'controller' => 'Users',
            'action' => 'myBio',
            '?' => $queryStrings
        ]),
        'Tags' => Router::url([
            'controller' => 'Users',
            'action' => 'myTags',
            '?' => $queryStrings
        ]),
        'Pictures' => Router::url([
            'controller' => 'Users',
            'action' => 'myPictures',
            '?' => $queryStrings
        ]),
        'Logo' => Router::url([
            'controller' => 'Users',
            'action' => 'myLogo',
            '?' => $queryStrings
        ]),
        'Contact' => Router::url([
            'controller' => 'Users',
            'action' => 'myContact',
            '?' => $queryStrings
        ])
    ];
?>

<?php if (isset($profileUnavailableMsg)): ?>
    <p class="alert alert-warning">
        <?= $profileUnavailableMsg ?>
    </p>
<?php elseif (!$this->request->getQuery('flow')): ?>
    <p>
        <?php
            $url = Router::url([
                'controller' => 'Users',
                'action' => 'view',
                $authUser['id'],
                $authUser['slug']
            ], true);
        ?>
        <?= $this->Html->link('View my profile', $url, ['class' => 'btn btn-default']) ?>
    </p>
<?php endif; ?>

<ul class="nav nav-tabs" id="profile-tabs">
    <?php foreach ($tabs as $label => $url): ?>
        <?php $active = explode('?', $url)[0] == explode('?', $this->request->getRequestTarget())[0]; ?>
        <li role="presentation" <?= $active ? 'class="active"' : '' ?>>
            <a href="<?= $url ?>" aria-controls="home">
                <?= $label ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>
