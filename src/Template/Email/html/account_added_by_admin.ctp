<?php
/**
 * @var \App\View\AppView $this
 * @var string $loginUrl
 * @var string $password
 * @var string $profileUrl
 * @var string $userEmail
 * @var string $userName
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    An account has been created for you on the Muncie Arts and Culture Council membership website.
    You can <a href="<?= $loginUrl ?>">log in</a> to your new account with the following credentials:
</p>

<ul>
    <li>
        Email: <?= $userEmail ?>
    </li>
    <li>
        Password: <?= $password ?>
    </li>
</ul>

<p>
    It's recommended that you change your password at your earliest convenience. Once you're logged in, you can
    <?= $this->Html->link('add your profile information', $profileUrl) ?>. Telling us about yourself and your
    relationship with the Muncie arts community helps us connect people with artists and organizations.
</p>
