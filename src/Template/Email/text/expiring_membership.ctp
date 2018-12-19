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
<?= $userName ?>,

<?php if ($autoRenew): ?>
Your Muncie Arts and Culture Council "<?= $membershipLevel->name ?>" level membership will be automatically renewed on
<?= $expires ?> for $<?= $membershipLevel->cost ?>.

If your credit or debit card information has changed since you last purchased a year of membership, you will need to
visit <?= $renewUrl ?> to manually renew your membership in order to continue receiving your membership benefits.

If you do not wish to renew your membership, you can visit the My Membership page at <?= $myMembershipUrl ?> and click
'Turn automatic renewal off'.
<?php else: ?>
Your Muncie Arts and Culture Council membership will expire on <?= $expires ?>. If you would like to continue being a
member at the "<?= $membershipLevel->name ?>" level, please visit <?= $renewUrl ?> to renew your membership for
$<?= $membershipLevel->cost ?> and continue receiving your membership benefits.
<?php endif; ?>

If you would like to continue being a member but upgrade or downgrade your membership to a different level, you
can visit <?= $membershipLevelsUrl ?> to view the options available.
