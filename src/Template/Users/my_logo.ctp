<?php
/**
 * @var \App\Model\Entity\Picture $picture
 * @var \App\Model\Entity\User $user
 * @var \App\View\AppView $this
 * @var string $memberLevelName
 * @var string|null $logoPath
 */
    use Cake\Core\Configure;
?>

<?= $this->element('account_info_header') ?>

<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <p>
        If you or your organization has a logo, you are invited to upload it so that we can display it in the footer
        of our website to recognize your
        membership<?php if ($memberLevelName): ?> at the <?= $memberLevelName ?> level<?php endif; ?>.
    </p>

    <?= $this->element('img_upload_notes') ?>

    <div style="text-align: center;">
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
    </div>

    <?php
        echo $this->Form->end();
        echo $this->element('jquery_ui');
    ?>

    <div class="text-right">
        <?php if ($logoPath): ?>
            <?= $this->Form->postLink(
                'Remove logo',
                [
                    'prefix' => false,
                    'action' => 'removeLogo'
                ],
                [
                    'class' => 'btn btn-danger',
                    'confirm' => "Are you sure you want to remove this logo?"
                ]
            ) ?>
        <?php endif; ?>

        <?php if ($this->request->getQuery('flow')): ?>
            <?= $this->Html->link(
                'Next',
                [
                    'controller' => 'Users',
                    'action' => 'myContact',
                    '?' => ['flow' => 1]
                ],
                ['class' => 'btn btn-primary']
            ) ?>
        <?php endif; ?>
    </div>
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
