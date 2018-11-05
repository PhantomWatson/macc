<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[]|\Cake\Collection\CollectionInterface $users
 */
?>
<div id="admin-users-index">
    <p>
        <?= $this->Html->link(
            'Add User',
            [
                'prefix' => 'admin',
                'action' => 'add'
            ],
            ['class' => 'btn btn-success']
        ) ?>
    </p>

    <?= $this->element('pagination') ?>

    <table class="table">
        <thead>
            <tr>
                <th>
                    <?= $this->Paginator->sort('name', 'User') ?>
                </th>
                <th>
                    <?= $this->Paginator->sort('role') ?>
                </th>
                <th>
                    <?= $this->Paginator->sort('created', 'Added') ?>
                </th>
                <th class="actions">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= h($user['name']) ?>
                        <br />
                        <a href="mailto:<?= h($user['email']) ?>">
                            <?= h($user['email']) ?>
                        </a>
                    </td>
                    <td>
                        <?= ucwords($user['role']) ?>
                    </td>
                    <td>
                        <?= \App\LocalTime\LocalTime::getDate($user->created) ?>
                    </td>
                    <td class="actions btn-group">
                        <div class="btn-group">
                            <?= $this->Html->link(
                                'Edit',
                                [
                                    'prefix' => 'admin',
                                    'action' => 'edit',
                                    $user['id']
                                ],
                                ['class' => 'btn btn-default']
                            ) ?>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle dropdown</span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->Html->link(
                                        'Edit basic account info',
                                        [
                                            'prefix' => 'admin',
                                            'action' => 'edit',
                                            $user['id']
                                        ]
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->Html->link(
                                        'Edit profile',
                                        [
                                            'prefix' => 'admin',
                                            'action' => 'editProfile',
                                            $user['id']
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                        <?= $this->Form->postLink(
                            'Delete',
                            [
                                'prefix' => 'admin',
                                'action' => 'delete',
                                $user['id']
                            ],
                            [
                                'class' => 'btn btn-default',
                                'confirm' => "Are you sure you want to delete {$user['name']}'s account?"
                            ]
                        ) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $this->element('pagination') ?>
</div>
