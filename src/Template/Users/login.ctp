<p class="alert alert-info">
    Don't have an account yet?
    You can <?= $this->Html->link(
        'register a MACC website account',
        [
            'controller' => 'Users',
            'action' => 'register'
        ]
    ) ?>
    for free.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->Form->input(
        'email',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->input(
        'password',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->input(
        'auto_login',
        [
            'label' => 'Keep me logged in on this computer',
            'type' => 'checkbox'
        ]
    );
    echo $this->Form->button(
        'Login',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
