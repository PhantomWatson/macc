<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
    echo $this->Form->create($user);
    echo $this->Form->control(
        'new_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'div' => ['class' => 'form-group'],
            'label' => 'Change password',
            'type' => 'password'
        ]
    );
    echo $this->Form->control(
        'confirm_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'div' => ['class' => 'form-group'],
            'label' => 'Repeat new password',
            'type' => 'password'
        ]
    );
    echo $this->Form->button(
        'Submit',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();