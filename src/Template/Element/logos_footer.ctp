<?php
/**
 * @var array $footerLogos
 */
?>

<footer id="logos-footer">
    <p>MACC thanks the support of our Ambassador and Arts Hero members</p>

    <?php if ($footerLogos['hasLogo']): ?>
        <ul>
            <?php foreach ($footerLogos['hasLogo'] as $logo): ?>
                <li>
                    <a href="<?= $logo['url'] ?>">
                        <img src="<?= $logo['logo'] ?>" alt="<?= $logo['name'] ?>" title="<?= $logo['name'] ?>" />
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <?php if ($footerLogos['hasLogo'] && $footerLogos['noLogo']): ?>
        <hr />
    <?php endif; ?>

    <?php if ($footerLogos['noLogo']): ?>
        <ul class="no-logo">
            <?php foreach ($footerLogos['noLogo'] as $logo): ?>
                <li>
                    <a href="<?= $logo['url'] ?>">
                        <?= $logo['name'] ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</footer>