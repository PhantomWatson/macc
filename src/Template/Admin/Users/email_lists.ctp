<p class="well">
    Need to email a whole category of users? Use the following automatically-compiled email lists.
</p>

<?php foreach ($emailLists as $header => $list): ?>
    <h2>
        <?= $header ?>
    </h2>
    <a href="mailto:<?= implode(';', $list) ?>">
        <?= implode('<br />', $list) ?>
    </a>
<?php endforeach; ?>
