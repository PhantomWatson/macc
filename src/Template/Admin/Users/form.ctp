<?php
/**
 * @var \App\View\AppView $this
 */
?>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Users',
        [
            'prefix' => 'admin',
            'controller' => 'Users',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<?php
    echo $this->Form->create(
        $user,
        ['id' => 'UserForm']
    );
    echo $this->Form->control(
        'name',
        ['class' => 'form-control']
    );
    echo $this->Form->control(
        'email',
        ['class' => 'form-control']
    );
    echo $this->Form->control(
        'role',
        [
            'after' => '<span class="note">Admins automatically have access to all communities and site functions</span>',
            'class' => 'form-control',
            'options' => $roles
        ]
    );
    $passwordFields = $this->Form->control(
        'new_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'label' => $this->request->getParam('action') == 'add' ? 'Password' : 'New Password',
            'type' => 'password'
        ]
    );
    $passwordFields .= $this->Form->control(
        'confirm_password',
        [
            'class' => 'form-control',
            'label' => 'Confirm password',
            'type' => 'password'
        ]
    );
?>

<?php if ($this->request->prefix == 'admin' && $this->request->getParam('action') == 'edit'): ?>
    <div id="password-fields-button" class="form-group">
        <a href="#">
            Change password
        </a>
    </div>
    <div id="password-fields" style="display: none;">
        <?= $passwordFields ?>
    </div>
<?php elseif ($this->request->prefix == 'admin' && $this->request->getParam('action') == 'add'): ?>
    <?= $passwordFields ?>
<?php endif; ?>

<?php
    $label = ($this->request->getParam('action') == 'add') ? 'Add User' : 'Update';
    echo $this->Form->button(
        $label,
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
?>

<?php $this->append('buffered'); ?>
    $('#password-fields-button a').click(function (event) {
        event.preventDefault();
        $('#password-fields-button').slideUp(300);
        $('#password-fields').slideDown(300);
    });
<?php $this->end(); ?>