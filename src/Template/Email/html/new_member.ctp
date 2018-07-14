<?php
/**
 * @var \App\Model\Entity\Membership $membership
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var \App\Model\Entity\User $user
 * @var string $profileUrl
 * @var \App\View\AppView $this
 */
?>

<p>
    A new member has joined the Muncie Arts and Culture Council:
</p>

<ul>
    <li>
        <strong>Name:</strong> <?= $user->name ?>
    </li>
    <li>
        <strong>Email:</strong> <a href="mailto:<?= $user->email ?>"><?= $user->email ?></a>
    </li>
    <li>
        <strong>Level:</strong> <?= $membershipLevel->name ?>
    </li>
    <li>
        <strong>Paid:</strong> $<?= $membershipLevel->cost ?>
    </li>
    <li>
        <strong>Auto-renew?</strong> <?= $membership->auto_renew ? 'Yes' : 'No' ?>
    </li>
    <li>
        <strong>Profile:</strong> <a href="<?= $profileUrl ?>"><?= $profileUrl ?></a>
    </li>
</ul>
