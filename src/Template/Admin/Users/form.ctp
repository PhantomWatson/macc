<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var array $roles
 * @var string $randomPassword
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
            'after' => '<span class="note">' .
                'Admins automatically have access to all communities and site functions' .
                '</span>',
            'class' => 'form-control',
            'options' => $roles
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
        <?= $this->Form->control(
            'new_password',
            [
                'autocomplete' => 'off',
                'class' => 'form-control',
                'label' => 'New password',
                'type' => 'password'
            ]
        ) ?>
        <?= $this->Form->control(
            'confirm_password',
            [
                'class' => 'form-control',
                'label' => 'Confirm password',
                'type' => 'password'
            ]
        ) ?>
    </div>
<?php elseif ($this->request->prefix == 'admin' && $this->request->getParam('action') == 'add'): ?>
    <?= $this->Form->control(
        'password',
        [
            'autocomplete' => 'off',
            'class' => 'form-control',
            'label' => 'Password',
            'type' => 'text',
            'value' => $this->request->getData('password') ?? $randomPassword
        ]
    ) ?>
    <div class="checkbox">
        <label for="add-membership-checkbox">
            <?= $this->Form->checkbox('addMembership', [
                'id' => 'add-membership-checkbox'
            ]) ?>
            <strong>Grant this user a MACC membership</strong>
            (you'll specify which level on the next page)
        </label>
    </div>
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
