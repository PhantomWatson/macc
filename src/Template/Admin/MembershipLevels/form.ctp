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
<?= $this->Form->input('name') ?>
<?= $this->Form->input('cost', [
    'label' => 'Cost, in whole dollars (e.g. 30)',
    'step' => '1'
]) ?>
<?= $this->Form->input('description') ?>
<p>
    The description of this membership level can be
    <?= $this->Html->link(
        'styled with markdown',
        [
            'prefix' => false,
            'controller' => 'Pages',
            'action' => 'styling'
        ],
        ['target' => '_blank']
    ) ?>, but HTML is not allowed.
</p>
<?= $this->Form->button('Submit', [
    'class' => 'btn btn-primary'
]) ?>
<?= $this->Form->end() ?>
