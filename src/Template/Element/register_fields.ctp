<div id="register-sections">
    <section>
        <h2>
            Your Info
        </h2>
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
                    ],
                    'value' => ''
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
                    ],
                    'value' => ''
                ]
            );
            echo $this->Form->control(
                'referrer',
                [
                    'class' => 'form-control',
                    'label' => '(Optional) How did you find out about how to become a member of MACC?',
                    'placeholder' => 'Examples: The name of someone who referred you, the URL of a website, etc.'
                ]
            );
        ?>
    </section>

    <section>
        <h2>
            Mailing List
        </h2>
        <?= $this->Form->control(
            'mailing_list',
            [
                'type' => 'checkbox',
                'label' => 'Email me about MACC news and upcoming events'
            ]
        ) ?>
    </section>

    <?= $this->Form->hidden('purchasingMemberLevel') ?>

    <section>
        <h2>
            Spam Protection
        </h2>
        <div class="input form-group">
            <?= $this->Recaptcha->display() ?>
            <?php if (isset($recaptchaError)): ?>
                <div class="error-message">
                    Invalid CAPTCHA response. Please try again.
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>
