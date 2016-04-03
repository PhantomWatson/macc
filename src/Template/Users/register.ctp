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
    echo $this->Form->input(
        'name',
        [
            'class' => 'form-control',
            'placeholder' => 'Name',
            'div' => [
                'class' => 'form-group'
            ]
        ]
    );
    echo $this->Form->input(
        'email',
        [
            'class' => 'form-control',
            'placeholder' => 'Email address',
            'div' => [
                'class' => 'form-group'
            ]
        ]
    );
    echo $this->Form->input(
        'new_password',
        [
            'label' => 'Password',
            'type' => 'password',
            'class' => 'form-control',
            'placeholder' => 'Password',
            'div' => [
                'class' => 'form-group'
            ]
        ]
    );
    echo $this->Form->input(
        'confirm_password',
        [
            'type' => 'password',
            'class' => 'form-control',
            'placeholder' => 'Confirm your password',
            'div' => [
                'class' => 'form-group'
            ]
        ]
    );
    echo $this->Form->input(
        'mailing_list',
        [
            'type' => 'checkbox',
            'label' => 'Sign up for the MACC mailing list'
        ]
    );
?>

<div class="input form-group">
    <label>
        Human?
    </label>
    <?= $this->Recaptcha->display() ?>
    <?php if (isset($recaptchaError)): ?>
        <div class="error-message">
            Invalid CAPTCHA response. Please try again.
        </div>
    <?php endif; ?>
</div>

<?php
    echo $this->Form->submit(
        'Register',
        ['class' => 'btn btn-primary']
    );
    echo $this->Form->end();
?>