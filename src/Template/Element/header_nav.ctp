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
        // Ignore SSL option for the purposes of comparing this URL to the one the user is currently viewing
        $urlForComparison = $url;
        if (isset($urlForComparison['_ssl'])) {
            unset($urlForComparison['_ssl']);
        }

        $here = $view->request->getAttribute('here');
        $class =  $here == Router::url($urlForComparison) ? 'current' : '';
        return $view->Html->link(
            $label,
            Router::url($url),
            ['class' => $class]
        );
    }

    function navLinkGroup($label, $links, $view) {
        $isCurrent = false;
        $here = $view->request->getAttribute('here');
        foreach ($links as $link) {
            // Ignore SSL option for the purposes of comparing this URL to the one the user is currently viewing
            $urlForComparison = $link['url'];
            if (isset($urlForComparison['_ssl'])) {
                unset($urlForComparison['_ssl']);
            }

            if ($here == Router::url($urlForComparison)) {
                $isCurrent = true;
            }
        }
        ?>
        <li class="dropdown">
            <a href="#" class="dropdown-toggle <?= $isCurrent ? 'current' : '' ?>" data-toggle="dropdown" role="button"
               aria-haspopup="true" aria-expanded="false">
                <?= $label ?>
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <?php foreach ($links as $link): ?>
                    <li>
                        <?= navLink($link['label'], $link['url'], $view) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php
        return null;
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
        <?= navLinkGroup(
            'My Account',
            [
                [
                    'label' => 'My account',
                    'url' => [
                        'prefix' => false,
                        'controller' => 'Users',
                        'action' => 'myBio'
                    ]
                ],
                [
                    'label' => 'My membership',
                    'url' => [
                        'prefix' => false,
                        'controller' => 'Memberships',
                        'action' => 'myMembership'
                    ]
                ],
                [
                    'label' => 'Change password',
                    'url' => [
                        'prefix' => false,
                        'controller' => 'Users',
                        'action' => 'changePassword'
                    ]
                ]
            ],
            $this
        ) ?>
        <?php if ($authUser['role'] == 'admin'): ?>
            <?= navLinkGroup(
                'Admin',
                [
                    [
                        'label' => 'Manage Users',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Users',
                            'action' => 'index'
                        ]
                    ],
                    [
                        'label' => 'Membership Levels',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'MembershipLevels',
                            'action' => 'index'
                        ]
                    ],
                    [
                        'label' => 'Memberships',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Memberships',
                            'action' => 'index'
                        ]
                    ],
                    [
                        'label' => 'Payment Records',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Payments',
                            'action' => 'index'
                        ]
                    ],
                    [
                        'label' => 'Email Lists',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Users',
                            'action' => 'emailLists'
                        ]
                    ],
                    [
                        'label' => 'Mailing Addresses',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Users',
                            'action' => 'addresses'
                        ]
                    ],
                ],
                $this
            ) ?>
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
