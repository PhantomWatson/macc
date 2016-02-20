<ul class="nav navbar-nav">
    <li>
        <a href="http://munciearts.org">
            Back to Main Site
        </a>
    </li>
    <li>
        <?= $this->Html->link(
            'Become a Member',
            [
                'controller' => 'MembershipLevels',
                'action' => 'index'
            ]
        ) ?>
    </li>
    <li>
        <?= $this->Html->link(
            'Members',
            [
                'controller' => 'Users',
                'action' => 'members'
            ]
        ) ?>
    </li>
    <li>
        <?= $this->Html->link(
            'Art Tags',
            [
                'controller' => 'Tags',
                'action' => 'index'
            ]
        ) ?>
    </li>
    <li>
        <?= $this->Html->link(
            'Donate',
            [
                'controller' => 'Payments',
                'action' => 'donate'
            ]
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
                    <?= $this->Html->link(
                        'View my profile',
                        [
                            'controller' => 'Users',
                            'action' => 'view',
                            $authUser['id'],
                            $authUser['slug']
                        ]
                    ) ?>
                </li>
                <li>
                    <?= $this->Html->link(
                        'Edit my profile',
                        [
                            'controller' => 'Users',
                            'action' => 'editProfile'
                        ]
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
                        <?= $this->Html->link(
                            'Manage Users',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Users',
                                'action' => 'index'
                            ]
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Membership Levels',
                            [
                                'prefix' => 'admin',
                                'controller' => 'MembershipLevels',
                                'action' => 'index'
                            ]
                        ) ?>
                    </li>
                    <li>
                        <?= $this->Html->link(
                            'Payment Records',
                            [
                                'prefix' => 'admin',
                                'controller' => 'Payments',
                                'action' => 'index'
                            ]
                        ) ?>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <li>
            <?= $this->Html->link(
                'Logout',
                [
                    'controller' => 'Users',
                    'action' => 'logout'
                ]
            ) ?>
        </li>
    <?php else: ?>
        <li>
            <?= $this->Html->link(
                'Register',
                [
                    'controller' => 'Users',
                    'action' => 'register'
                ]
            ) ?>
        </li>
        <li>
            <?= $this->Html->link(
                'Login',
                [
                    'controller' => 'Users',
                    'action' => 'login'
                ]
            ) ?>
        </li>
    <?php endif; ?>
</ul>
