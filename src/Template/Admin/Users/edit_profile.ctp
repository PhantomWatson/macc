<?php
/**
 * @var AppView $this
 * @var User $user
 * @var Picture $picture
 * @var array $tags
 * @var int $picLimit
 */

use App\Model\Entity\Picture;
use App\Model\Entity\User;
use App\View\AppView;
use Cake\Core\Configure;
$this->Html->script('jquery.dirty.js', ['block' => 'script']);
?>

<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left"></span> Back to Users',
        [
            'prefix' => 'admin',
            'controller' => 'Users',
            'action' => 'index'
        ],
        [
            'class' => 'btn btn-default',
            'escape' => false
        ]
    ) ?>
</p>

<div id="edit_profile">
    <?= $this->Form->create($user) ?>

    <section>
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#editProfile" aria-controls="editProfile" role="tab" data-toggle="tab">
                        Edit
                    </a>
                </li>
                <li role="presentation">
                    <a href="#previewProfile" id="previewProfileLink" aria-controls="previewProfile" role="tab" data-toggle="tab">
                        Preview
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="editProfile">
                    <?= $this->Form->control(
                        'profile',
                        [
                            'class' => 'form-control',
                            'div' => ['class' => 'form-group'],
                            'label' => false,
                            'type' => 'textarea'
                        ]
                    ) ?>
                </div>
                <div role="tabpanel" class="tab-pane commonmark-preview" id="previewProfile">

                </div>
            </div>
        </div>

        <p class="footnote">
            If you need to style your bio, such as with links, lists, italics, or bold, please use our
            <?= $this->Html->link(
                'Markdown styling guide',
                [
                    'prefix' => false,
                    'controller' => 'Pages',
                    'action' => 'styling'
                ],
                ['target' => '_blank']
            ) ?>, as HTML is not allowed.
        </p>
    </section>

    <section>
        <h2>
            Tags
        </h2>
        <p>
            Select any tags that describe what you perform, produce, and do in the community.
        </p>
        <?= $this->element('Tags'.DS.'editor', [
            'availableTags' => $tags,
            'selectedTags' => $user['tags']
        ]) ?>

        <?= $this->Form->button(
            'Update Profile',
            ['class' => 'btn btn-primary']
        ) ?>
    </section>

    <section>
        <h2>
            Pictures
        </h2>
        <p>
            You can upload <strong>up to <?= $picLimit ?> <?= __n('picture', 'pictures', $picLimit) ?></strong> of yourself and/or the artwork that you create.
        </p>

        <?php
            $uploadMax = ini_get('upload_max_filesize');
            $postMax = ini_get('post_max_size');
            $serverFilesizeLimit = min($uploadMax, $postMax);
            $manualFilesizeLimit = min('10M', $serverFilesizeLimit);
        ?>
        <ul class="footnote">
            <li>
                Images must be .jpg, .jpeg, .gif, or .png and at least 200px by 200px
            </li>
            <li>
                Very large images (over 3,000px by 3,000px) may fail to upload
            </li>
            <li>
                Each file cannot exceed <?php echo $manualFilesizeLimit; ?>B
            </li>
            <li>
                By uploading an image, you affirm that you are not violating any copyrights
            </li>
            <li>
                To be considerate of our diverse audience, images must not include offensive language, nudity, or graphic violence
            </li>
        </ul>

        <p>
            Click <span class="glyphicon glyphicon-star"></span> to make a picture your <strong>main picture</strong>,
            which will be displayed next to your name on the list of members.
        </p>

        <div id="picture-upload-container">
            <button id="picture-upload">
                Select images
            </button>
        </div>

        <p id="limit-reached" class="alert alert-info">
            You have reached the limit of <?= $picLimit ?> <?= __n('picture', 'pictures', $picLimit) ?>.
        </p>

        <p id="upload-status"></p>

        <table id="pictures">
            <tbody>
                <?php foreach ($user->pictures as $picture): ?>
                    <tr data-picture-id="<?= $picture->id ?>">
                        <td>
                            <span class="glyphicon glyphicon-star is-main" title="Main picture"></span>
                            <div class="make-main-container">
                                <button class="btn btn-link make-main" title="Make main picture">
                                    <span class="glyphicon glyphicon-star-empty"></span>
                                </button>
                            </div>
                            <button class="btn btn-link remove" title="Remove">
                                <span class="glyphicon glyphicon-remove text-danger"></span>
                            </button>
                        </td>
                        <td>
                            <a href="/img/members/<?= $user->id ?>/<?= $picture->filename ?>" title="Click for full-size">
                                <img src="/img/members/<?= $user->id ?>/<?= $picture->thumbnail_filename ?>" />
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php $this->append('buffered'); ?>
            userPictureEditor.init(<?= json_encode([
                'filesizeLimit' => $manualFilesizeLimit.'B',
                'limit' => $picLimit,
                'mainPictureId' => $user['main_picture_id'],
                'timestamp' => time(),
                'token' => md5(Configure::read('upload_verify_token').time()),
                'userId' => $user['id']
            ]) ?>);
        <?php $this->end(); ?>

    </section>

    <?php
        echo $this->Form->end();
        echo $this->element('jquery_ui');
    ?>
</div>

<?php
    $this->Html->script('commonmark', ['block' => 'script']);
    $this->Html->script('sanitize', ['block' => 'script']);
    $this->Html->script('/uploadifive/jquery.uploadifive.min.js', ['block' => 'script']);
    $this->Html->css('/uploadifive/uploadifive.css', ['block' => 'css']);
    $this->Html->css('/magnific-popup/magnific-popup.css', ['block' => 'css']);
    $this->Html->script('/magnific-popup/jquery.magnific-popup.js', ['block' => 'script']);
?>

<?php $this->append('buffered'); ?>
    commonmarkPreviewer.init('previewProfileLink', 'profile', 'previewProfile');
    $('#edit_profile > form').dirty({
        preventLeaving: true
    });
<?php $this->end(); ?>
