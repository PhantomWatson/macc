<p>
    Here, you can enter your personal bio. This is a great place to
    talk about your role in the community, the groups you associate
    with, and the work that you do.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->Form->input(
        'profile',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group'],
            'label' => 'Your Profile',
            'type' => 'textarea'
        ]
    );
    echo $this->Form->button(
        'Update Profile',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
