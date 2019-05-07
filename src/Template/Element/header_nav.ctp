<?php
/**
 * @var AppView $this
 */

use App\View\AppView;
use Cake\Routing\Router;
use Cake\Core\Configure;
use Cake\Utility\Hash;

if (! function_exists('navLink')) {
    /**
     * @param string $label Link label
     * @param array $url Link URL
     * @param AppView $view
     * @return mixed
     */
    function navLink($label, $url, $view) {
        // Ignore SSL option for the purposes of comparing this URL to the one the user is currently viewing
        $urlForComparison = $url;
        if (isset($urlForComparison['_ssl'])) {
            unset($urlForComparison['_ssl']);
        }

        $here = $view->getRequest()->getAttribute('here');
        $class =  $here == Router::url($urlForComparison) ? 'current' : '';
        return $view->Html->link(
            $label,
            Router::url($url),
            ['class' => $class]
        );
    }

    /**
     * @param string $label Group label
     * @param array $links Array of links
     * @param AppView $view AppView object
     * @param array $relatedUrls Array of related URLs
     * @return null
     */
    function navLinkGroup($label, $links, $view, $relatedUrls = []) {
        $isCurrent = false;
        $here = $view->getRequest()->getAttribute('here');
        $urls = Hash::extract($links, '{n}.url');
        $urls = array_merge($urls, $relatedUrls);
        foreach ($urls as $url) {
            // Ignore SSL option for the purposes of comparing this URL to the one the user is currently viewing
            if (isset($url['_ssl'])) {
                unset($url['_ssl']);
            }

            if ($here == Router::url($url)) {
                $isCurrent = true;
            }
            //echo "<li>$here vs. " . Router::url($url) . '</li>';
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
            $this,
            [
                [
                    'controller' => 'Users',
                    'action' => 'myTags'
                ],
                [
                    'controller' => 'Users',
                    'action' => 'myPictures'
                ],
                [
                    'controller' => 'Users',
                    'action' => 'myLogo'
                ],
                [
                    'controller' => 'Users',
                    'action' => 'myContact'
                ]
            ]
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
                    [
                        'label' => 'Programs',
                        'url' => [
                            'prefix' => 'admin',
                            'controller' => 'Programs',
                            'action' => 'index'
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
