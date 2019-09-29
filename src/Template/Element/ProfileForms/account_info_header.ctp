<?php
/**
 * @var AppView $this
 * @var array $authUser
 * @var Membership $authUserMembership
 * @var string $profileUnavailableMsg
 * @var User $user
 */

    use App\Model\Entity\Membership;
    use App\Model\Entity\User;
    use App\View\AppView;
    use Cake\Routing\Router;

    $isAdmin = $this->request->getParam('prefix') == 'admin';
    $prefix = $isAdmin ? 'admin' : false;
    $actionPrepend = $isAdmin ? 'update' : 'my';
    $queryStrings = $this->request->getQuery('flow') ? ['flow' => 1] : [];
    $userId = $isAdmin ? $user->id : null;
    $tabs = [
        'Bio' => Router::url([
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => $actionPrepend . 'Bio',
            $userId,
            '?' => $queryStrings
        ]),
        'Tags' => Router::url([
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => $actionPrepend . 'Tags',
            $userId,
            '?' => $queryStrings
        ]),
        'Pictures' => Router::url([
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => $actionPrepend . 'Pictures',
            $userId,
            '?' => $queryStrings
        ]),
        'Logo' => Router::url([
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => $actionPrepend . 'Logo',
            $userId,
            '?' => $queryStrings
        ]),
        'Contact' => Router::url([
            'prefix' => $prefix,
            'controller' => 'Users',
            'action' => $actionPrepend . 'Contact',
            $userId,
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
        <?php if ($isAdmin): ?>
            <?= $this->Html->link(
                '<span class="glyphicon glyphicon-arrow-left"></span> Back to Users',
                [
                    'prefix' => 'admin',
                    'controller' => 'Users',
                    'action' => 'index'
                ],
                [
                    'class' => 'btn btn-default',
                    'escape' => false
                ]
            ) ?>
        <?php endif; ?>
        <?php
            $url = Router::url([
                'prefix' => false,
                'controller' => 'Users',
                'action' => 'view',
                $user->id,
                $user->slug,
            ], true);
        ?>
        <?= $this->Html->link(
            $isAdmin ? 'View profile' : 'View my profile',
            $url,
            ['class' => 'btn btn-default']
        ) ?>
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
