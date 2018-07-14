<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel[]|\Cake\Collection\CollectionInterface $membershipLevels
 */
?>
<p>
    <?= $this->Html->link(
        'Add a New Membership Level',
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
            <td>
                Membership Level
            </td>
            <td>
                Cost
            </td>
            <td>
                Actions
            </td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($membershipLevels as $membershipLevel): ?>
            <tr>
                <td>
                    <?= $membershipLevel->name ?>
                </td>
                <td>
                    $<?= number_format($membershipLevel->cost) ?>
                </td>
                <td>
                    <?= $this->Html->link(
                        'Edit',
                        [
                            'prefix' => 'admin',
                            'action' => 'edit',
                            $membershipLevel->id
                        ],
                        [
                            'class' => 'btn btn-default'
                        ]
                    ) ?>
                    <?= $this->Form->postLink(
                        'Delete',
                        [
                            'prefix' => 'admin',
                            'action' => 'delete',
                            $membershipLevel->id
                        ],
                        [
                            'class' => 'btn btn-default',
                            'confirm' => "Are you sure you want to delete this membership level?"
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
