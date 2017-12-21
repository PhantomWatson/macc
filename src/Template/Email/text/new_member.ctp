<?php
/**
 * @var \App\Model\Entity\Membership $membership
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var \App\Model\Entity\User $user
 * @var string $profileUrl
 */
?>

A new member has joined the Muncie Arts and Culture Council:

Name: <?= $user->name ?>

Email: <?= $user->email ?>

Level: <?= $membershipLevel->name ?>

Paid: $<?= $membershipLevel->cost ?>

Auto-renew? <?= $membership->auto_renew ? 'Yes' : 'No' ?>

Profile: <?= $profileUrl ?>
