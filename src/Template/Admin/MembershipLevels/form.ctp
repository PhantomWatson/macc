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
<?= $this->Form->button('Submit', [
    'class' => 'btn btn-primary'
]) ?>
<?= $this->Form->end() ?>
