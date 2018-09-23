<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User[] $members
 */
?>
<?php if (empty($members)): ?>
    <p class="alert alert-info">
        Sorry, but we couldn't find any current members to display.
    </p>
<?php else: ?>
    <p>
        These are the <?= count($members) ?> current members of the Muncie Arts and Culture Council.
        Click on a member's name to view their profile. Want to become a member?
        <?= $this->Html->link(
            'Learn about the membership options available.',
            [
                'controller' => 'Memberships',
                'action' => 'levels'
            ]
        ) ?>
    </p>

    <div id="filter-members-container" class="input-group">
        <label class="sr-only" for="filter-members">Filter members</label>
        <div class="input-group-addon">
            <span class="glyphicon glyphicon-search"></span>
        </div>
        <input type="text" class="form-control" id="filter-members" placeholder="Filter members by name">
    </div>

    <?php $this->append('members-pagination'); ?>
        <nav aria-label="Members pagination" class="members-pagination input-group">
            <div class="pagination input-group-btn">
                <button aria-label="Previous" class="btn btn-default">
                    <span aria-hidden="true">&laquo;</span>
                </button>
                <button class="btn btn-link pagination-loading" disabled="disabled">
                    <img src="/img/loading_small.gif" alt="Loading..." />
                </button>
                <button aria-label="Next" class="btn btn-default">
                    <span aria-hidden="true">&raquo;</span>
                </button>
            </div>
        </nav>
    <?php $this->end(); ?>
    <?= $this->fetch('members-pagination') ?>

    <table id="members-table" class="table">
        <tbody>
            <?php foreach ($members as $member): ?>
                <tr data-member-name="<?= strtolower($member->name) ?>">
                    <td class="member-name">
                        <?= $this->Html->link(
                            $member->name,
                            [
                                'controller' => 'Users',
                                'action' => 'view',
                                $member->id,
                                $member->slug,
                                '?' => ['back' => 'index']
                            ]
                        ) ?>
                        <p class="tag-list">
                            <?php if ($member->more_tags): ?>
                                <?= $member->tag_list ?>, and <?= $member->more_tags ?> more
                            <?php else: ?>
                                <?= $member->tag_list ?>
                            <?php endif; ?>
                        </p>
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

    <?= $this->fetch('members-pagination') ?>
<?php endif; ?>

<?php
    $this->Html->css('/magnific-popup/magnific-popup.css', ['block' => 'css']);
    $this->Html->script('/magnific-popup/jquery.magnific-popup.js', ['block' => 'script']);
    $this->Html->script('members_list.js', ['block' => 'script']);
?>
<?php $this->append('buffered'); ?>
    membersList.init();
<?php $this->end(); ?>
