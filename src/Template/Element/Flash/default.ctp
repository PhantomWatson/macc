<?php
    if (! isset($class)) {
        $class = empty($params['class']) ? 'info' : $params['class'];
    }
?>

<?php if (isset($message) && ! empty($message)): ?>
    <div class="alert alert-<?= h($class) ?> alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <?= $message ?>
    </div>
<?php endif; ?>
