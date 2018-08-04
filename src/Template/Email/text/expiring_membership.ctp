<?php
/**
 * @var \App\Model\Entity\Membership $membership
 * @var string $renewUrl
 * @var \App\View\AppView $this
 */
?>
<?= $membership->user->name ?>,

Your Muncie Arts and Culture Council membership will expire on <?= $membership->expires->format('F jS') ?>.
Please visit <?= $renewUrl ?> to renew your membership and continue receiving your membership benefits.
