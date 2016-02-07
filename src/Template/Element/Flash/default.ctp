<?php
    $class = empty($params['class']) ? 'info' : $params['class'];
?>

<div class="alert alert-<?= h($class) ?> alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <?= h($message) ?>
</div>
