<?php
    /**
     * @var \App\View\AppView $this
     * @var array $results
     */
?>
<ul>
    <?php foreach ($results as $result): ?>
        <li>
            <?= $result ?>
        </li>
    <?php endforeach; ?>
</ul>
