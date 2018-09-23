<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Picture $picture
 * @var array $tags
 */
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

    <p class="text-right">
        <?= $this->Form->button(
            $this->request->getQuery('flow') ? 'Next' : 'Submit',
            ['class' => 'btn btn-primary']
        ) ?>
    </p>

    <?= $this->Form->end() ?>
</div>
