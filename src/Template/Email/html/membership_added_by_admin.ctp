<?php
/**
 * @var AppView $this
 * @var string $expires
 * @var string $forgotPasswordUrl
 * @var string $membershipLevelName
 * @var string $profileUrl
 * @var string $userName
 */

use App\View\AppView;
?>
<p>
    <?= $userName ?>,
</p>

<p>
    A one-year Muncie Arts and Culture Council membership at the <?= $membershipLevelName ?> level has been applied
    to your account. This membership will expire on <?= $expires ?>.
</p>

<p>
    If you haven't already, please <?= $this->Html->link('add your profile information', $profileUrl) ?>. Telling
    us about yourself and your relationship with the Muncie arts community helps us connect people with artists and
    organizations.
</p>

<p>
    If you've forgotten your password, or if your account was created by a MACC site administrator and you need to
    personalize your password, please visit <?= $this->Html->link($forgotPasswordUrl, $forgotPasswordUrl) ?>.
</p>
