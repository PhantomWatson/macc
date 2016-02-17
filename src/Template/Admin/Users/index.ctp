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
                        <?= $user->created->format('F j, Y') ?>
                    </td>
                    <td class="actions btn-group">
                        <?= $this->Html->link(
                            'Edit',
                            [
                                'prefix' => 'admin',
                                'action' => 'edit',
                                $user['id']
                            ],
                            ['class' => 'btn btn-default']
                        ) ?>
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
</div>