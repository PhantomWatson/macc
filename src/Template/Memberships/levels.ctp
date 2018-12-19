<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel[]|\Cake\Collection\CollectionInterface $membershipLevels
 * @var bool $renewing
 * @var int|null $membershipLevelId
 */
    use Cake\Core\Configure;
?>

<div id="membership-levels-index">
    <p>
        <?php if ($renewing): ?>
            Renewing your Muncie Arts and Culture Council membership
        <?php else: ?>
            Becoming a member of the Muncie Arts and Culture Council
        <?php endif; ?>
        is a great way to support your arts community. Membership can be purchased in one-year increments and is open
        to everyone, regardless of whether you're an artist yourself or simply want to contribute.
    </p>

    <p>
        Members can include a bio on their profile pages and upload pictures of themselves and their work in order to
        promote themselves, help other members of the community connect to them, and to be featured on MACC's website
        and social media accounts.
    </p>

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
            <?php
                $label = ($renewing && $membershipLevelId)
                    ? (
                        $membershipLevel->id == $membershipLevelId
                        ? 'Renew Membership'
                        : 'Change Membership Level'
                    )
                    : 'Purchase';
                $class = ($renewing && $membershipLevel->id == $membershipLevelId)
                    ? 'btn btn-success'
                    : 'btn btn-primary';
                echo $this->Html->link(
                    $label,
                    [
                        'controller' => 'Memberships',
                        'action' => 'level',
                        $membershipLevel->id,
                        '_ssl' => Configure::read('forceSSL')
                    ],
                    [
                        'class' => $class,
                        'id' => 'purchaseLevel' . $membershipLevel->id
                    ]
                );
            ?>
        </section>
    <?php endforeach; ?>
</div>
