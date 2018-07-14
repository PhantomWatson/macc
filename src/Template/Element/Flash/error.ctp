<?php
/**
 * @var \App\View\AppView $this
 */
?>
<?= $this->element('Flash'.DS.'default', [
    'class' => 'danger',
    'message' => $message
]); ?>
