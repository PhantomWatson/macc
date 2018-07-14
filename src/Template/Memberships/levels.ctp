<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel[]|\Cake\Collection\CollectionInterface $membershipLevels
 */
    use Cake\Core\Configure;
?>

<div id="membership-levels-index">
    <p>
        Becoming a member of the Muncie Arts and Culture Council is a great
        way to support your arts community. Membership can be purchased in
        one-year increments and is open to everyone, regardless of whether
        you're an artist yourself or simply want to contribute.
    </p>

    <p>
        Members can include a bio on their profile pages and upload pictures of themselves and their work in order to
        promote themselves, help other members of the community connect to them, and to be featured on MACC's website
        and social media accounts.
    </p>

    <?php if (! isset($authUser) || empty($authUser)): ?>
        <p class="alert alert-info">
            Be sure to
            <?= $this->Html->link(
                'register an account',
                [
                    'controller' => 'Users',
                    'action' => 'register'
                ]
            ) ?>
            and
            <?= $this->Html->link(
                'log in',
                [
                    'controller' => 'Users',
                    'action' => 'login'
                ]
            ) ?>
            <em>before</em> purchasing a membership.
        </p>
    <?php endif; ?>

    <?php foreach ($membershipLevels as $membershipLevel): ?>
        <section>
            <h2>
                <?= $membershipLevel->name ?>
                -
                $<?= number_format($membershipLevel->cost) ?>
            </h2>
            <p>
                <?= $this->element('commonmark_parsed', ['input' => $membershipLevel->description]) ?>
            </p>
            <?= $this->Html->link(
                'Purchase',
                [
                    'controller' => 'Memberships',
                    'action' => 'level',
                    $membershipLevel->id,
                    '_ssl' => Configure::read('forceSSL')
                ],
                [
                    'class' => 'btn btn-primary',
                    'id' => 'purchaseLevel'.$membershipLevel->id
                ]
            ) ?>
        </section>
    <?php endforeach; ?>
</div>
