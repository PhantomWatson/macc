<?php
/**
 * @var array $footerLogos
 */
?>

<?php foreach ($footerLogos as $level): ?>
    <?php
        if (!$level['hasLogo'] && !$level['noLogo']) {
            continue;
        }
    ?>

    <footer class="logos-footer">
        <p>
            MACC thanks the support of our <?= $level['levelName'] ?> members
        </p>

        <?php if ($level['hasLogo']): ?>
            <ul>
                <?php foreach ($level['hasLogo'] as $logo): ?>
                    <li>
                        <a href="<?= $logo['url'] ?>">
                            <img src="<?= $logo['logo'] ?>" alt="<?= $logo['name'] ?>" title="<?= $logo['name'] ?>" />
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($level['hasLogo'] && $level['noLogo']): ?>
            <hr />
        <?php endif; ?>

        <?php if ($level['noLogo']): ?>
            <ul class="no-logo">
                <?php foreach ($level['noLogo'] as $logo): ?>
                    <li>
                        <a href="<?= $logo['url'] ?>">
                            <?= $logo['name'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </footer>
<?php endforeach; ?>