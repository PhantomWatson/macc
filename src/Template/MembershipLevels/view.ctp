<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var array $authUser
 */
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    use League\CommonMark\CommonMarkConverter;
    $converter = new CommonMarkConverter();
    $this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
    $this->Html->script('payment_processor.js', ['block' => 'script']);
?>

<form>
    <strong>
        Membership Benefits:
    </strong>
    <div class="well">
        <?= $converter->convertToHtml($membershipLevel->description) ?>
    </div>

    <p>
        <strong>
            Membership Level:
        </strong>
        <?= $membershipLevel->name ?>
    </p>

    <p>
        <strong>
            Annual Cost:
        </strong>
        $<?= $membershipLevel->cost ?>
    </p>

    <div class="radio">
        <label>
            <input type="radio" name="renewal" value="automatic" checked>
            Automatically renew my membership every year
        </label>
    </div>
    <div class="radio">
        <label>
            <input type="radio" name="renewal" value="manual">
            Only purchase one year of membership
        </label>
    </div>

    <button type="submit" class="btn btn-primary" id="payment-button">
        Enter payment information
    </button>
</form>

<?php $this->append('buffered'); ?>
    membershipPurchase.init(<?= json_encode([
        'costDollars' => $membershipLevel->cost,
        'email' => $authUser['email'],
        'key' => Configure::read('Stripe.Public'),
        'membershipLevelId' => $membershipLevel->id,
        'membershipLevelName' => $membershipLevel->name,
        'postUrl' => Router::url([
            'controller' => 'Payments',
            'action' => 'completePurchase'
        ], true),
        'redirectUrl' => Router::url([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ], true),
        'userId' => $authUser['id']
    ]) ?>);
<?php $this->end(); ?>

