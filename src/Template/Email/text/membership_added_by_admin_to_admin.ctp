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

A membership has been granted to a user by an administrator:

Administrator: <?= $adminUserName ?>

Member: <?= $member->name ?>

Email: <?= $member->email ?>

Level: <?= $membershipLevel->name ?>

Profile: <?= $profileUrl ?>

Referrer: <?= $member->referrer ? $member->referrer : '(none)' ?>
