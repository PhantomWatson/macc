<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Program $program
 */
?>

<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Programs',
        [
            'prefix' => 'admin',
            'controller' => 'Programs',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<p>
    Program descriptions will be displayed to potential donors, and can include HTML, such as
    <code>&lt;a href="http://example.com"&gt;links&lt;/a&gt;</pre></code>.
</p>

<?= $this->Form->create($program) ?>

<?= $this->Form->control('name') ?>

<?= $this->Form->control('description') ?>

<?= $this->Form->button(
    'Add Program',
    ['class' => 'btn btn-primary']
) ?>

<?= $this->Form->end() ?>
