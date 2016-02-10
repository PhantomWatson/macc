<div id="edit_profile">
    <p>
        Here, you can enter your personal bio. This is a great place to
        talk about your role in the community, the groups you associate
        with, and the work that you do.
    </p>

    <?= $this->Form->create($user) ?>

    <section>
        <h2>
            Personal Bio
        </h2>
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