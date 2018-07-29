<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var array $authUser
 * @var bool $autoRenew
 */
$this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
?>

<p>
    You will now be prompted to enter payment information using your debit or credit card to purchase a MACC membership
    at the <?= $membershipLevel->name ?> level for $<?= $membershipLevel->cost ?>. Click the button below to proceed.
</p>
    <button type="submit" class="btn btn-primary" id="payment-button">
        Enter payment information
    </button>

<?php $this->append('buffered'); ?>
    membershipPurchase.init(<?= json_encode([
        'costDollars' => $membershipLevel->cost,
        'email' => isset($authUser['email']) ? $authUser['email'] : null,
        'key' => \Cake\Core\Configure::read('Stripe.Public'),
        'membershipLevelId' => $membershipLevel->id,
        'membershipLevelName' => $membershipLevel->name,
        'postUrl' => \Cake\Routing\Router::url([
            'controller' => 'Memberships',
            'action' => 'completePurchase'
        ], true),
        'redirectUrl' => \Cake\Routing\Router::url([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ], true),
        'userId' => isset($authUser['id']) ? $authUser['id'] : null,
        'autoRenew' => $autoRenew
    ]) ?>);
<?php $this->end(); ?>