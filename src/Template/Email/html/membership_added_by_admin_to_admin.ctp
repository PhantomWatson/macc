<?php
/**
 * @var \App\Model\Entity\Membership $membership
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var \App\Model\Entity\User $member
 * @var \App\View\AppView $this
 * @var string $adminUserName
 * @var string $profileUrl
 */
?>

<p>
    A membership has been granted to a user by an administrator:
</p>

<ul>
    <li>
        <strong>Administrator:</strong> <?= $adminUserName ?>
    </li>
    <li>
        <strong>Member:</strong> <?= $member->name ?>
    </li>
    <li>
        <strong>Email:</strong> <a href="mailto:<?= $member->email ?>"><?= $member->email ?></a>
    </li>
    <li>
        <strong>Level:</strong> <?= $membershipLevel->name ?>
    </li>
    <li>
        <strong>Profile:</strong> <a href="<?= $profileUrl ?>"><?= $profileUrl ?></a>
    </li>
</ul>
