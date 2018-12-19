<?php
/**
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var \App\View\AppView $this
 * @var bool $autoRenew
 * @var string $expires
 * @var string $membershipLevelsUrl
 * @var string $myMembershipUrl
 * @var string $renewUrl
 * @var string $userName
 */
?>

<p>
    <?= $userName ?>,
</p>

<?php if ($autoRenew): ?>
    <p>
        Your Muncie Arts and Culture Council "<?= $membershipLevel->name ?>" level membership will be automatically
        renewed on <?= $expires ?> for $<?= $membershipLevel->cost ?>.
    </p>
    <p>
        If your credit or debit card information has changed since you last purchased a year of membership, you will
        need to <?= $this->Html->link('manually renew your membership', $renewUrl) ?> in order to continue
        receiving your membership benefits.
    </p>
    <p>
        If you do not wish to renew your membership, you can visit the
        <?= $this->Html->link('My Membership page', $myMembershipUrl) ?> and click 'Turn automatic renewal off'.
    </p>
<?php else: ?>
    <p>
        Your Muncie Arts and Culture Council membership will expire on <?= $expires ?>. If you would like to continue
        being a member at the "<?= $membershipLevel->name ?>" level, please
        <?= $this->Html->link('click here', $renewUrl) ?> to renew your membership for
        $<?= $membershipLevel->cost ?> and continue receiving your membership benefits.
    </p>
<?php endif; ?>
<p>
    If you would like to continue being a member but upgrade or downgrade your membership to a different level, you
    can <?= $this->Html->link('click here', $membershipLevelsUrl) ?> to view the options available.
</p>
