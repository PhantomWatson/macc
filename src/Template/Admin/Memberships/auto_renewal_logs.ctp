<?php
/**
 * @var AppView $this
 * @var array $logs
 */

use App\LocalTime\LocalTime;
use App\View\AppView;
?>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Memberships',
        [
            'prefix' => 'admin',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<?= $this->element('pagination') ?>
<table class="table" id="auto-renewal-logs">
    <thead>
        <tr>
            <th>
                Date
            </th>
            <th>
                Message
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
            <tr <?= $log->error ? 'class="error"' : '' ?>>
                <td>
                    <?= LocalTime::getDateTime($log->created) ?>
                </td>
                <td class="message">
                    <?php
                        if (mb_strpos($log->message, '$chargeParams:')) {
                            echo str_replace('$chargeParams:', '<br />$chargeParams:<pre>', $log->message);
                            echo '</pre>';
                        } else {
                            echo nl2br($log->message);
                        }
                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('pagination') ?>
