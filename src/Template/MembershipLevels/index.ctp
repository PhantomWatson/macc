<?php
    use Cake\Core\Configure;
    use Cake\Routing\Router;
    use League\CommonMark\CommonMarkConverter;
    $converter = new CommonMarkConverter();
    $this->Html->script('https://checkout.stripe.com/checkout.js', ['block' => 'script']);
?>

<div id="membership-levels-index">
    <p>
        Becoming a member of the Muncie Arts and Culture Council is a great
        way to support your arts community. Membership can be purchased in
        one-year increments and is open to everyone, regardless of whether
        you're an artist yourself or simply want to contribute.
    </p>

    <?php foreach ($membershipLevels as $membershipLevel): ?>
        <section>
            <h2>
                <?= $membershipLevel->name ?>
                -
                $<?= number_format($membershipLevel->cost) ?>
            </h2>
            <p>
                <?= $converter->convertToHtml($membershipLevel->description) ?>
            </p>
            <?= $this->Html->link(
                'Purchase',
                [
                    'controller' => 'Memberships',
                    'action' => 'purchase',
                    $membershipLevel->id
                ],
                [
                    'class' => 'btn btn-primary',
                    'id' => 'purchaseLevel'.$membershipLevel->id
                ]
            ) ?>

            <?php $this->append('buffered'); ?>
                membershipPurchase.setupPurchaseButton({
                    button_selector: <?= json_encode('#purchaseLevel'.$membershipLevel->id) ?>,
                    confirmation_message: <?= json_encode('Confirm payment of $'.$membershipLevel->cost.' to purchase one year of membership?') ?>,
                    cost_dollars: <?= $membershipLevel->cost ?>,
                    description: <?= json_encode($membershipLevel->name.' ($'.$membershipLevel->cost.')') ?>,
                    key: '<?= Configure::read('Stripe.Public') ?>',
                    post_data: {
                        user_id: '<?= $authUser['id'] ?>',
                        membership_level_id: '<?= $membershipLevel->id ?>'
                    },
                    post_url: '<?= Router::url([
                        'controller' => 'Payments',
                        'action' => 'completePurchase'
                    ], true) ?>',
                    redirect_url: '<?= Router::url([
                        'controller' => 'MembershipLevels',
                        'action' => 'index'
                    ], true) ?>',
                    email: '<?= $authUser['email'] ?>'
                });
            <?php $this->end(); ?>
        </section>
    <?php endforeach; ?>
</div>
