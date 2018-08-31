<?php
/**
 * @var \App\View\AppView $this
 * @var array $authUser
 */
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    $this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
    $this->Html->script('payment_processor.js', ['block' => 'script']);
    $this->Html->script('donation.js', ['block' => 'script']);
    $email = isset($authUser['email']) ? $authUser['email'] : null;
?>

<p>
    Donations to the Muncie Arts and Culture Council are tax-deductible and a great way to support your art community.
</p>

<form>
    <div class="form-group">
        <label for="donation-amount">
            Donation Amount (in dollars)
        </label>
        <div class="input-group">
            <div class="input-group-addon">$</div>
            <input type="number" class="form-control" id="donation-amount" min="1" required="required" />
            <div class="input-group-addon">.00</div>
        </div>
    </div>
    <div class="form-group">
        <label for="recipient-program">Optional: What MACC program would you like your donation to go toward?</label>
        <input type="text" class="form-control" id="recipient-program" placeholder="" />
    </div>
    <button type="submit" class="btn btn-primary" id="donation-button">
        Enter payment information
    </button>
</form>

<?php $this->append('buffered'); ?>
    donation.init(<?= json_encode([
        'buttonSelector' => '#donation-amount',
        'email' => $email,
        'key' => Configure::read('Stripe.Public'),
        'postData' => [
            'userId' => isset($authUser['id']) ? $authUser['id'] : null
        ],
        'postUrl' => Router::url([
            'controller' => 'Donations',
            'action' => 'completeDonation'
        ], true),
        'redirectUrl' => Router::url([
            'controller' => 'Donations',
            'action' => 'donationComplete'
        ], true)
    ]) ?>);
<?php $this->end(); ?>
