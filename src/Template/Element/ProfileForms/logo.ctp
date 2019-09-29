<?php
/**
 * @var AppView $this
 * @var bool $qualifiesForLogo
 * @var int $manualFilesizeLimit
 * @var Picture $picture
 * @var string $memberLevelName
 * @var string|null $logoPath
 * @var User $user
 */

use App\Model\Entity\Membership;
use App\Model\Entity\Picture;
use App\Model\Entity\User;
use App\View\AppView;
use Cake\Core\Configure;
?>

<?= $this->element('ProfileForms/account_info_header') ?>

<?php if ($qualifiesForLogo): ?>
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
                        'prefix' => $this->request->getParam('prefix') === 'admin' ? 'admin' : false,
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
        <?php
            $params = [
                'filesizeLimit' => $manualFilesizeLimit . 'B',
                'timestamp' => time(),
                'token' => md5(Configure::read('upload_verify_token') . time()),
                'userId' => $user['id']
            ];
            if ($this->request->getParam('prefix') === 'admin') {
                $params['admin'] = true;
            }
        ?>
        logoUploader.init(<?= json_encode($params) ?>);
    <?php $this->end(); ?>
<?php else: ?>
    <p class="alert alert-warning">
        Once you purchase a membership at the
        <?= $this->Html->link(
            'Ambassador',
            [
                'controller' => 'Memberships',
                'action' => 'level',
                Membership::AMBASSADOR_LEVEL,
                '_ssl' => Configure::read('forceSSL')
            ]
        ) ?>
        or
        <?= $this->Html->link(
            'Arts Hero',
            [
                'controller' => 'Memberships',
                'action' => 'level',
                Membership::ARTS_HERO_LEVEL,
                '_ssl' => Configure::read('forceSSL')
            ]
        ) ?>
        level, you'll be able to upload a logo to be displayed in the footer of this website.
    </p>
<?php endif; ?>
