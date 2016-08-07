<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Payment Records',
        [
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<p>
    You will need to manually add a payment record if someone pays for membership
    by a method <em>other than</em> through the MACC website, such as with a check. Please include
    information about how this payment was received in the <strong>notes</strong> section.
</p>

<p>
    Adding a record will also automatically grant the selected user one year of membership.
</p>

<?= $this->Form->create($payment) ?>

<?= $this->Form->input('user_id') ?>

<?= $this->Form->input('membership_level_id') ?>

<?= $this->Form->input('notes') ?>

<?= $this->Form->button(
    'Add Payment Record',
    ['class' => 'btn btn-primary']
) ?>

<?= $this->Form->end() ?>