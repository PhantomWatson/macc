<?php
echo $this->Form->control(
    'name',
    [
        'class' => 'form-control',
        'placeholder' => 'Name',
        'div' => [
            'class' => 'form-group'
        ]
    ]
);
echo $this->Form->control(
    'email',
    [
        'class' => 'form-control',
        'placeholder' => 'Email address',
        'div' => [
            'class' => 'form-group'
        ]
    ]
);
echo $this->Form->control(
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
echo $this->Form->control(
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
echo $this->Form->control(
    'mailing_list',
    [
        'type' => 'checkbox',
        'label' => 'Email me about MACC news and upcoming events'
    ]
);
echo $this->Form->hidden('purchasingMemberLevel');
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
