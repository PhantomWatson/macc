<?php
/**
 * @var \App\View\AppView $this
 * @var string $profileUrl
 * @var string $userName
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    Your Muncie Arts and Culture Council membership has been automatically renewed for another year. Thank you for
    your continued support of MACC and its mission to build community among local artists and arts organizations and
    to serve as a resource to enable artistic growth and opportunity.
</p>

<p>
    Now's a good time to <?= $this->Html->link('review your profile information', $profileUrl) ?> to make sure that
    it's up-to-date. Telling us about yourself and your relationship with the Muncie arts community helps us connect
    people with artists and organizations.
</p>
