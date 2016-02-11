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
        <?= $this->Form->input(
            'profile',
            [
                'class' => 'form-control',
                'div' => ['class' => 'form-group'],
                'label' => false,
                'type' => 'textarea'
            ]
        ) ?>
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