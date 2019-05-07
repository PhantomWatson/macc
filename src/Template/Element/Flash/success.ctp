<?php
/**
 * @var AppView $this
 * @var string $message
 */
use App\View\AppView;
?>
<?= $this->element('Flash' . DS . 'default', [
    'class' => 'success',
    'message' => $message
]) ?>
