<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel[]|\Cake\Collection\CollectionInterface $membershipLevels
 */
    use League\CommonMark\CommonMarkConverter;
    $converter = new CommonMarkConverter();
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
                    'controller' => 'MembershipLevels',
                    'action' => 'view',
                    $membershipLevel->id
                ],
                [
                    'class' => 'btn btn-primary',
                    'id' => 'purchaseLevel'.$membershipLevel->id
                ]
            ) ?>
        </section>
    <?php endforeach; ?>
</div>
