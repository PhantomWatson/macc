<?php if (empty($members)): ?>
    <p class="alert alert-info">
        Sorry, but we couldn't find any current members to display.
    </p>
<?php else: ?>
    <p>
        These are the current members of the Muncie Arts and Culture Council.
        Click on a member's name to view their profile.
        Want to become a member?
        <?= $this->Html->link(
            'Learn about the membership options available.',
            [
                'controller' => 'Memberships',
                'action' => 'levels'
            ]
        ) ?>
    </p>

    <?= $this->element('pagination') ?>

    <table id="members-table" class="table">
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr>
                    <td class="member-name">
                        <?= $this->Html->link(
                            $member->name,
                            [
                                'controller' => 'Users',
                                'action' => 'view',
                                $member->id,
                                $member->slug
                            ]
                        ) ?>
                    </td>

                    <?php if ($member->main_picture_thumbnail): ?>
                        <td class="picture">
                            <a href="/img/members/<?= $member->id ?>/<?= $member->main_picture_fullsize ?>" title="Click to view full-sized picture" class="popup-img">
                                <img src="/img/members/<?= $member->id ?>/<?= $member->main_picture_thumbnail ?>" alt="Profile picture for <?= $member->name ?>" />
                            </a>
                        </td>
                    <?php else: ?>
                        <td class="no-picture"></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?= $this->element('pagination') ?>

<?php endif; ?>

<?php
    $this->Html->css('/magnific-popup/magnific-popup.css', ['block' => 'css']);
    $this->Html->script('/magnific-popup/jquery.magnific-popup.js', ['block' => 'script']);
?>
<?php $this->append('buffered'); ?>
    membersList.init();
<?php $this->end(); ?>
