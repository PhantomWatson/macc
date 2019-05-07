<?php
/**
 * @var AppView $this
 * @var User $user
 * @var string $resetUrl
 */

use App\Model\Entity\User;
use App\View\AppView;
?>
<?= $user->name ?>,

We received your request to reset your password. If you visit the following URL in the next 24 hours, you will be prompted to enter a new password.

<?= $resetUrl ?>
