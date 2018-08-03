<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Picture $picture
 */
    use Cake\Core\Configure;
    $this->Html->script('jquery.dirty.js', ['block' => 'script']);
?>

<?= $this->element('account_info_header') ?>

<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <?= $this->Form->control('name') ?>

    <p>
        Tell about your past and present role in the community,
        the groups you associate with, and the work that you do.
    </p>

    <div>
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#editProfile" aria-controls="editProfile" role="tab" data-toggle="tab">
                    Edit
                </a>
            </li>
            <li role="presentation">
                <a href="#previewProfile" id="previewProfileLink" aria-controls="previewProfile" role="tab" data-toggle="tab">
                    Preview
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="editProfile">
                <?= $this->Form->control(
                    'profile',
                    [
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'label' => false,
                        'type' => 'textarea'
                    ]
                ) ?>
            </div>
            <div role="tabpanel" class="tab-pane commonmark-preview" id="previewProfile">

            </div>
        </div>
    </div>

    <p class="footnote">
        If you need to style your bio, such as with links, lists, italics, or bold, please use our
        <?= $this->Html->link(
            'Markdown styling guide',
            [
                'prefix' => false,
                'controller' => 'Pages',
                'action' => 'styling'
            ],
            ['target' => '_blank']
        ) ?>, as HTML is not allowed.
    </p>

    <?= $this->Form->button(
        'Submit',
        ['class' => 'btn btn-primary']
    ) ?>

    <?= $this->Form->end() ?>
</div>

<?php
    $this->Html->script('commonmark', ['block' => 'script']);
    $this->Html->script('sanitize', ['block' => 'script']);
?>

<?php $this->append('buffered'); ?>
    commonmarkPreviewer.init('previewProfileLink', 'profile', 'previewProfile');
    $('#edit_profile > form').dirty({
        preventLeaving: true
    });
<?php $this->end(); ?>
