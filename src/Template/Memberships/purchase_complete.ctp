<?php
/**
 * @var \App\View\AppView $this
 */
?>
<p>
    Thank you for your purchase of a Muncie Arts and Culture Council membership.
    Now's a great time to
    <?= $this->Html->link(
        'create your member profile',
        [
            'prefix' => false,
            'controller' => 'Users',
            'action' => 'myProfile'
        ]
    ) ?>, or update it if you've just renewed an existing membership.
</p>
