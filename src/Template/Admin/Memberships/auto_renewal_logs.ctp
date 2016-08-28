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
                    <?= $log->created->format('M j, Y g:ia') ?>
                </td>
                <td class="message">
                    <?= nl2br($log->message) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->element('pagination') ?>