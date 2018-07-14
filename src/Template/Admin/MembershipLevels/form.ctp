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
<?= $this->Form->input('name') ?>
<?= $this->Form->input('cost', [
    'label' => 'Cost, in whole dollars (e.g. 30)',
    'step' => '1'
]) ?>

<label for="description">Description</label>
<div>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#editDescription" aria-controls="editDescription" role="tab" data-toggle="tab">
                Edit
            </a>
        </li>
        <li role="presentation">
            <a href="#previewDescription" id="previewDescriptionLink" aria-controls="previewDescription" role="tab" data-toggle="tab">
                Preview
            </a>
        </li>
    </ul>
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="editDescription">
            <?= $this->Form->input('description', [
                'label' => false
            ]) ?>
        </div>
        <div role="tabpanel" class="tab-pane commonmark-preview" id="previewDescription">

        </div>
    </div>
</div>

<p class="footnote">
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

<?php $this->Html->script('commonmark', ['block' => 'script']); ?>
<?php $this->Html->script('sanitize', ['block' => 'script']); ?>
<?php $this->append('buffered'); ?>
    commonmarkPreviewer.init('previewDescriptionLink', 'description', 'previewDescription');
<?php $this->end(); ?>
