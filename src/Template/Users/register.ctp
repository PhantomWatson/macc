<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<p>
    Already have an account?
    <?= $this->Html->link(
        'Log in',
        [
            'controller' => 'Users',
            'action' => 'login'
        ]
    ) ?>.
</p>

<?php
    echo $this->Form->create($user);
    echo $this->element('register_fields', ['user' => $user]);
    echo $this->Form->submit(
        'Register',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
?>