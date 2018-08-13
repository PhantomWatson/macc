<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 * @var \App\Model\Entity\Picture $picture
 * @var string|null $logoPath
 */
    use Cake\Core\Configure;
?>

<?= $this->element('account_info_header') ?>

<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <p>
        If you or your organization has a logo, you are invited to upload it so that we can display it in the footer
        of our website to recognize your contribution.
    </p>

    <?= $this->element('img_upload_notes') ?>

    <div id="my-logo">
        <?php if ($logoPath): ?>
            <img src="<?= $logoPath ?>" alt="Logo" />
        <?php endif; ?>
    </div>

    <div id="picture-upload-container">
        <button id="picture-upload">
            Select image
        </button>
    </div>

    <p id="upload-status"></p>

    <?php
        echo $this->Form->end();
        echo $this->element('jquery_ui');
    ?>

    <?php if ($this->request->getQuery('flow')): ?>
        <p class="text-right">
            <?= $this->Html->link(
                'Next',
                [
                    'controller' => 'Users',
                    'action' => 'myContact',
                    '?' => ['flow' => 1]
                ],
                ['class' => 'btn btn-primary']
            ) ?>
        </p>
    <?php endif; ?>
</div>

<?php
    $this->Html->script('logo-uploader.js', ['block' => 'script']);
    $this->Html->script('/uploadifive/jquery.uploadifive.min.js', ['block' => 'script']);
    $this->Html->css('/uploadifive/uploadifive.css', ['block' => 'css']);
?>
<?php $this->append('buffered'); ?>
    logoUploader.init(<?= json_encode([
        'filesizeLimit' => $manualFilesizeLimit . 'B',
        'timestamp' => time(),
        'token' => md5(Configure::read('upload_verify_token') . time()),
        'userId' => $user['id']
    ]) ?>);
<?php $this->end(); ?>
