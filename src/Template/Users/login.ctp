<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<p>
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
    echo $this->Form->control(
        'email',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->control(
        'password',
        [
            'class' => 'form-control',
            'div' => ['class' => 'form-group']
        ]
    );
    echo $this->Form->control(
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
?>

<p>
    <?= $this->Html->link(
        'I forgot my password',
        [
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'forgotPassword'
        ]
    ) ?>
</p>
