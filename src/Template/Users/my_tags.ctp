<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Picture $picture
 */
    use Cake\Core\Configure;
    $this->Html->script('jquery.dirty.js', ['block' => 'script']);
?>

<?= $this->element('account_info_header') ?>

<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <p>
        Select any tags that describe what you perform, produce, and do in the community.
    </p>
    <?= $this->element('Tags'.DS.'editor', [
        'availableTags' => $tags,
        'selectedTags' => $user['tags']
    ]) ?>

    <?= $this->Form->button(
        'Submit',
        ['class' => 'btn btn-primary']
    ) ?>

    <?= $this->Form->end() ?>
</div>
