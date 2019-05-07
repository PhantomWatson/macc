<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 */
?>
<?= $this->element('Flash'.DS.'default', [
    'class' => 'danger',
    'message' => $message
]); ?>
