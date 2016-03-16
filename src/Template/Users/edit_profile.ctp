<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <section>
        <h2>
            Personal Bio
        </h2>
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
                    <?= $this->Form->input(
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
    </section>

    <section>
        <h2>
            Tags
        </h2>
        <p>
            Select any tags that describe what you perform, produce, and do in the community.
        </p>
        <?= $this->element('Tags'.DS.'editor', [
            'availableTags' => $tags,
            'selectedTags' => $user['tags']
        ]) ?>

        <?= $this->Form->button(
            'Update Profile',
            ['class' => 'btn btn-primary']
        ) ?>
    </section>

    <?php
        echo $this->Form->end();
        echo $this->element('jquery_ui');
    ?>
</div>

<?php $this->Html->script('commonmark', ['block' => 'script']); ?>
<?php $this->append('buffered'); ?>
    commonmarkPreviewer.init('previewProfileLink', 'profile', 'previewProfile');
<?php $this->end(); ?>
