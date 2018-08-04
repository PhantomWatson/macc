<?php
/**
 * @var \App\Model\Entity\Membership $membership
 * @var string $renewUrl
 * @var \App\View\AppView $this
 */
?>

<p>
    <?= $membership->user->name ?>,
</p>

<p>
    Your Muncie Arts and Culture Council membership will expire on <?= $membership->expires->format('F jS') ?>.
    Please visit members.MuncieArts.org to <?= $this->Html->link('renew your membership', $renewUrl) ?> and
    continue receiving your membership benefits.
</p>
