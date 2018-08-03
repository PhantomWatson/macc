<?php
/**
 * @var \App\View\AppView $this
 */
use Cake\Routing\Router;
use Cake\Core\Configure;

if (! function_exists('navLink')) {
    /**
     * @param string $label Link label
     * @param array $url Link URL
     * @param \App\View\AppView $view
     * @return mixed
     */
    function navLink($label, $url, $view) {
        $url = Router::url($url);
        $class = $view->request->getAttribute('here') == $url ? 'current' : '';
        return $view->Html->link(
            $label,
            $url,
            ['class' => $class]
        );
    }
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
                'controller' => 'Memberships',
                'action' => 'levels'
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
                'controller' => 'Donations',
                'action' => 'donate',
                '_ssl' => Configure::read('forceSSL')
            ],
            $this
        ) ?>
    </li>
    <?php if (isset($authUser)): ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                My Account
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li>
                    <?= navLink(
                        'My account',
                        [
                            'prefix' => false,
                            'controller' => 'Users',
                            'action' => 'myBio'
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
                <li>
                    <?= navLink(
                        'Change password',
                        [
                            'prefix' => false,
                            'controller' => 'Users',
                            'action' => 'changePassword'
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
                            'Memberships',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Memberships',
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
                    <li>
                        <?= navLink(
                            'Email Lists',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Users',
                                'action' => 'emailLists'
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
