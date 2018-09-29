<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Payment[]|\Cake\Collection\CollectionInterface $payments
 */
?>
<p>
    <?= $this->Html->link(
        'Add Membership Payment',
        [
            'prefix' => 'admin',
            'controller' => 'Payments',
            'action' => 'add'
        ],
        ['class' => 'btn btn-success']
    ) ?>
</p>

<p>
    This is a list of all MACC membership payment records, including payments made through the website and
    payment records added manually by administrators.
</p>

<p>
    <strong>Received a check or cash payment for a membership?</strong> Click 'Add Membership Payment' above to manually
    add a payment record and grant a user one year of membership.
</p>

<?php if (empty($payments)): ?>
    <p class="alert alert-info">
        No payment records found
    </p>
<?php else: ?>
    <p>
        <strong>Refunds:</strong> If a refund is issued, click the [Refund] button next to that payment to
        record the refund. Note that the [Refund] button does not actually issue a refund, only record that a refund has been issued.
    </p>
<?php endif; ?>

<?php if ($payments): ?>
    <?= $this->element('pagination') ?>

    <table class="table" id="payments_index">
        <thead>
            <tr>
                <th>
                    Date
                </th>
                <th>
                    User
                </th>
                <th>
                    Membership
                </th>
                <th>
                    Report Refund
                </th>
                <th>
                    Details
                </th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td>
                        <?= $payment->created->format('n/j/Y') ?>
                    </td>
                    <td>
                        <?= $payment->user['name'] ?>
                    </td>
                    <td>
                        <?php if ($payment->membership_level['name']): ?>
                            <?= $payment->membership_level['name'] ?>
                        <?php else: ?>
                            <span class="unknown">
                                Unknown membership level
                            </span>
                        <?php endif; ?>

                        <?php if ($payment->amount): ?>
                            ($<?= number_format($payment->amount) ?>)
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($payment->refunder): ?>
                            <a class="refunded btn btn-default btn-block" href="#">
                                Refunded
                            </a>
                        <?php else: ?>
                            <?= $this->Form->postLink(
                                'Refund',
                                [
                                    'prefix' => 'admin',
                                    'action' => 'refund',
                                    $payment->id
                                ],
                                [
                                    'class' => 'btn btn-default btn-block',
                                    'escape' => false,
                                    'confirm' => 'Are you sure you want to mark this payment as having been refunded?'
                                ]
                            ) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="#" class="details btn btn-default btn-block">
                            Details
                        </a>
                    </td>
                </tr>
                <tr class="details">
                    <td colspan="5">
                        <ul>
                            <li>
                                <?php if ($payment->admin_adder): ?>
                                    Purchase record added by admin <?= $payment->admin_adder['name'] ?>
                                <?php else: ?>
                                    Purchase made online by <?= $payment->user['name'] ?>
                                <?php endif; ?>
                            </li>
                            <?php if ($payment->refunder): ?>
                                <li>
                                    Marked refunded by
                                    <?php if ($payment->refunder['name']): ?>
                                        <?= $payment->refunder['name'] ?>
                                    <?php else: ?>
                                        an unknown user
                                    <?php endif; ?>
                                    on
                                    <?= $payment->refunded_date->format('F j, Y') ?>
                                </li>
                            <?php endif; ?>
                            <?php if ($payment->notes): ?>
                                <li>
                                    <?= nl2br($payment->notes) ?>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $this->element('pagination') ?>

    <?php $this->append('buffered'); ?>
        $('a.refunded, a.details').click(function (event) {
            event.preventDefault();
            $(this).closest('tr').next('tr.details').find('ul').slideToggle();
        });
    <?php $this->end(); ?>
<?php endif; ?>