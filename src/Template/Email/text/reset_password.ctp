<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<?= $user->name ?>,

We received your request to reset your password. If you visit the following URL in the next 24 hours, you will be prompted to enter a new password.

<?= $resetUrl ?>
