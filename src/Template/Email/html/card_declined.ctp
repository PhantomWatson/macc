<?php
/**
 * @var \App\View\AppView $this
 * @var string $renewUrl
 * @var string $userName
 */
?>

<p>
    <?= $userName ?>,
</p>

<p>
    Unfortunately, your credit or debit card was declined when we attempted to automatically renew your Muncie
    Arts and Culture Council membership. If your payment information has changed in the past year, such as a credit
    card expiring and being replaced, and you wish to continue being a member of MACC, please
    <?= $this->Html->link('manually renew your membership', $renewUrl) ?> with your new payment method at your
    earliest convenience.
</p>
