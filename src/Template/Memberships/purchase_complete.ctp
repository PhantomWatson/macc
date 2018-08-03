<?php
/**
 * @var \App\View\AppView $this
 * @var bool $isFirstMembershipPurchase
 */
?>
<p>
    Thank you for your purchase of a Muncie Arts and Culture Council membership! Now, it's time to
    <?php if ($isFirstMembershipPurchase): ?>
        create your member profile so people can learn about you and your unique connection to the Muncie arts
        community.
    <?php else: ?>
        review your member profile to see if any information needs to be updated.
    <?php endif; ?>
</p>
<p class="text-center">
    <?= $this->Html->link(
        $isFirstMembershipPurchase ? 'Create Member Profile' : 'Update Member Profile',
        [
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'myBio',
            '?' => ['flow' => 1]
        ],
        ['class' => 'btn btn-primary btn-lg']
    ) ?>
</p>
