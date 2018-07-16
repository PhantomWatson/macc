<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 */
?>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Membership Levels',
        [
            'prefix' => 'admin',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<?= $this->Form->create($membershipLevel) ?>
<?= $this->Form->control('name') ?>
<?= $this->Form->control('cost', ['step' => '1']) ?>
<?= $this->Form->control('description') ?>
<?= $this->Form->button('Submit', [
    'class' => 'btn btn-primary'
]) ?>
<?= $this->Form->end() ?>
