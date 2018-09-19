<?php
/**
 * @var \App\View\AppView $this
 * @var array $emailLists
 */
?>
<div class="well">
    <p>
        Need to email a whole category of users? Use the following automatically-compiled email lists.
    </p>
    <p>
        You can copy each list and paste it into the <em>to</em> field of a new message, or you can click
        on a list to automatically begin composing an email in your default email client.
    </p>
</div>

<?php foreach ($emailLists as $header => $list): ?>
    <section class="email-list">
        <h2>
            <?= $header ?>
        </h2>
        <a href="mailto:<?= implode(';', $list) ?>">
            <?= implode('<br />', $list) ?>
        </a>
    </section>
<?php endforeach; ?>
