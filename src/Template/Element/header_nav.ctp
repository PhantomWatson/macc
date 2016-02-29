<?php
use Cake\Routing\Router;

function navLink($label, $url, $view) {
    $url = Router::url($url);
    $class = $view->request->here == $url ? 'current' : '';
    return $view->Html->link(
        $label,
        $url,
        ['class' => $class]
    );
}
?>

<ul class="nav navbar-nav">
    <li>
        <a href="http://munciearts.org">
            Back to Main Site
        </a>
    </li>
    <li>
        <?= navLink(
            'Become a Member',
            [
                'prefix' => false,
                'controller' => 'MembershipLevels',
                'action' => 'index'
            ],
            $this
        ) ?>
    </li>
    <li>
        <?= navLink(
            'Members',
            [
                'prefix' => false,
                'controller' => 'Users',
                'action' => 'members'
            ],
            $this
        ) ?>
    </li>
    <li>
        <?= navLink(
            'Art Tags',
            [
                'prefix' => false,
                'controller' => 'Tags',
                'action' => 'index'
            ],
            $this
        ) ?>
    </li>
    <li>
        <?= navLink(
            'Donate',
            [
                'prefix' => false,
                'controller' => 'Payments',
                'action' => 'donate'
            ],
            $this
        ) ?>
    </li>
    <?php if ($authUser): ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                My Account
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <?= navLink(
                        'View my public profile',
                        [
                            'prefix' => false,
                            'controller' => 'Users',
                            'action' => 'view',
                            $authUser['id'],
                            $authUser['slug']
                        ],
                        $this
                    ) ?>
                </li>
                <li>
                    <?= navLink(
                        'Edit my public profile',
                        [
                            'prefix' => false,
                            'controller' => 'Users',
                            'action' => 'editProfile'
                        ],
                        $this
                    ) ?>
                </li>
                <li>
                    <?= navLink(
                        'My membership',
                        [
                            'prefix' => false,
                            'controller' => 'Memberships',
                            'action' => 'myMembership'
                        ],
                        $this
                    ) ?>
                </li>
            </ul>
        </li>
        <?php if ($authUser['role'] == 'admin'): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    Admin
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <?= navLink(
                            'Manage Users',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Users',
                                'action' => 'index'
                            ],
                            $this
                        ) ?>
                    </li>
                    <li>
                        <?= navLink(
                            'Membership Levels',
                            [
                                'prefix' => 'admin',
                                'controller' => 'MembershipLevels',
                                'action' => 'index'
                            ],
                            $this
                        ) ?>
                    </li>
                    <li>
                        <?= navLink(
                            'Payment Records',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Payments',
                                'action' => 'index'
                            ],
                            $this
                        ) ?>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <li>
            <?= navLink(
                'Logout',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'logout'
                ],
                $this
            ) ?>
        </li>
    <?php else: ?>
        <li>
            <?= navLink(
                'Register',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'register'
                ],
                $this
            ) ?>
        </li>
        <li>
            <?= navLink(
                'Login',
                [
                    'prefix' => false,
                    'controller' => 'Users',
                    'action' => 'login'
                ],
                $this
            ) ?>
        </li>
    <?php endif; ?>
</ul>
