<?php
/**
 * @var \App\View\AppView $this
 */
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    $this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
    $email = $authUser ? $authUser['email'] : null;
?>

<p>
    Donations to the Muncie Arts and Culture Council are tax-deductible and a great way to support your art community.
</p>

<form>
    <div class="form-group">
        <label for="donation-amount">
            Donation Amount (in dollars)
        </label>
        <input type="number" class="form-control" id="donation-amount" min="1" required="required" />
    </div>
    <button type="submit" class="btn btn-primary" id="donation-button">
        Enter payment information
    </button>
</form>

<?php $this->append('buffered'); ?>
    donation.init(<?= json_encode([
        'buttonSelector' => '#donation-amount',
        'email' => $authUser ? $authUser['email'] : null,
        'key' => Configure::read('Stripe.Public'),
        'postData' => [
            'userId' => $authUser ? $authUser['id'] : null
        ],
        'postUrl' => Router::url([
            'controller' => 'Payments',
            'action' => 'completeDonation'
        ], true),
        'redirectUrl' => Router::url([
            'controller' => 'Payments',
            'action' => 'donationComplete'
        ], true)
    ]) ?>);
<?php $this->end(); ?>
