<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Program[]|\Cake\Collection\CollectionInterface $programs
 */
?>

<p>
    <?= $this->Html->link(
        'Add New Program',
        [
            'prefix' => 'admin',
            'controller' => 'Programs',
            'action' => 'add'
        ],
        ['class' => 'btn btn-success']
    ) ?>
</p>

<p>
    When MACC introduces new <strong>programs that can receive donations</strong>, they can be added here so that they
    appear as options on the
    <?= $this->Html->link(
        'donation page',
        [
            'prefix' => false,
            'controller' => 'Donations',
            'action' => 'donate',
        ]
    ) ?>, where each program's name and description will be presented to potential donors. When a program concludes,
    please remove it from this list.
</p>

<?php if (!$programs->isEmpty()): ?>
    <table class="table">
        <thead>
            <tr>
                <th>
                    Name
                </th>
                <th>
                    Description
                </th>
                <th>
                    Actions
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($programs as $program): ?>
                <tr>
                    <td>
                        <?= $program->name ?>
                    </td>
                    <td>
                        <?= $program->description ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <?= $this->Html->link(
                                        'Edit',
                                        ['action' => 'edit', $program->id]
                                    ) ?>
                                </li>
                                <li>
                                    <?= $this->Form->postLink(
                                        'Delete',
                                        ['action' => 'delete', $program->id],
                                        [
                                            'confirm' => 'Are you sure you want to remove this program?'
                                        ]
                                    ) ?>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="alert alert-warning">
        No programs found
    </p>
<?php endif; ?>
