<?php
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    $this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
?>

<form>
    <strong>
        Membership Benefits:
    </strong>
    <div class="well">
        <?= $this->element('commonmark_parsed', ['input' => $membershipLevel->description]) ?>
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
            'controller' => 'Memberships',
            'action' => 'completePurchase'
        ], true),
        'redirectUrl' => Router::url([
            'controller' => 'Memberships',
            'action' => 'purchaseComplete'
        ], true),
        'userId' => $authUser['id']
    ]) ?>);
<?php $this->end(); ?>