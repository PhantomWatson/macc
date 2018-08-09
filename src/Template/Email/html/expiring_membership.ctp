<?php
/**
 * @var \App\View\AppView $this
 * @var bool $autoRenew
 * @var string $expires
 * @var string $renewUrl
 * @var string $userName
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    <?php if ($autoRenew): ?>
        Your Muncie Arts and Culture Council membership will be automatically renewed on <?= $expires ?>. If your
        credit or debit card information has changed, you will need to
        <?= $this->Html->link('manually renew your membership', $renewUrl) ?> to continue receiving your membership
        benefits.
    <?php else: ?>
        Your Muncie Arts and Culture Council membership will expire on <?= $expires ?>. Please visit
        members.MuncieArts.org to <?= $this->Html->link('renew your membership', $renewUrl) ?> and continue
        receiving your membership benefits.
    <?php endif; ?>
</p>
