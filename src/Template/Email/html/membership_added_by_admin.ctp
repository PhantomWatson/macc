<?php
/**
 * @var \App\View\AppView $this
 * @var string $expires
 * @var string $membershipLevelName
 * @var string $profileUrl
 * @var string $userName
 */
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
