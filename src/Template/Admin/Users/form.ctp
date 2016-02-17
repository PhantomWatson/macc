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
    echo $this->Form->input(
        'name',
        ['class' => 'form-control']
    );
    echo $this->Form->input(
        'email',
        ['class' => 'form-control']
    );
    echo $this->Form->input(
        'role',
        [
            'after' => '<span class="note">Admins automatically have access to all communities and site functions</span>',
            'class' => 'form-control',
            'options' => $roles
        ]
    );
    $passwordFields = $this->Form->input(
        'new_password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'label' => $this->request->action == 'add' ? 'Password' : 'New Password',
            'type' => 'password'
        ]
    );
    $passwordFields .= $this->Form->input(
        'confirm_password',
        [
            'class' => 'form-control',
            'label' => 'Confirm password',
            'type' => 'password'
        ]
    );
?>

<?php if ($this->request->prefix == 'admin' && $this->request->action == 'edit'): ?>
    <div id="password-fields-button" class="form-group">
        <a href="#">
            Change password
        </a>
    </div>
    <div id="password-fields" style="display: none;">
        <?= $passwordFields ?>
    </div>
<?php elseif ($this->request->prefix == 'admin' && $this->request->action == 'add'): ?>
    <?= $passwordFields ?>
<?php endif; ?>

<?php
    $label = ($this->request->action == 'add') ? 'Add User' : 'Update';
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