<?php
/**
 * @var \App\View\AppView $this
 * @var bool $autoRenew
 * @var string $expires
 * @var string $renewUrl
 * @var string $userName
 */
?>
<?= $userName ?>,

<?php if ($autoRenew): ?>
    Your Muncie Arts and Culture Council membership will be automatically renewed on <?= $expires ?>. If your
    credit or debit card information has changed, you will need to visit <?= $renewUrl ?> and manually renew your
    membership, to continue receiving your membership benefits.
<?php else: ?>
    Your Muncie Arts and Culture Council membership will expire on <?= $expires ?>. Please visit <?= $renewUrl ?> to
    renew your membership and continue receiving your membership benefits.
<?php endif; ?>
