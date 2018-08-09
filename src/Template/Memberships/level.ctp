<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\MembershipLevel $membershipLevel
 * @var array $authUser
 * @var App\Model\Entity\User $user
 * @var bool $renewing
 */
?>

<p>
    <strong>
        Annual Cost:
    </strong>
    $<?= $membershipLevel->cost ?>
</p>

<strong>
    Membership Benefits at the <?= $membershipLevel->name ?> level:
</strong>
<div class="well">
    <?= $this->element('commonmark_parsed', ['input' => $membershipLevel->description]) ?>
</div>

<?= $this->Form->create($user) ?>
<?php if (!$authUser): ?>
    <h2>
        Create Your MACC Website Account
    </h2>
    <p>
        Already have an account?
        <?= $this->Html->link(
            'Log in',
            [
                'controller' => 'Users',
                'action' => 'login',
                '?' => [
                    'redirect' => $this->request->getRequestTarget()
                ]
            ]
        ) ?>
    </p>
    <?= $this->element('register_fields') ?>
<?php endif; ?>

<h2>
    Automatic Renewal
</h2>
<div class="radio">
    <label>
        <input type="radio" name="renewal" value="automatic" checked>
        Automatically renew my membership every year
    </label>
</div>
<div class="radio">
    <label>
        <input type="radio" name="renewal" value="manual">
        Only purchase one year of membership
    </label>
</div>
<?= $this->Form->submit(
    'Next',
    ['class' => 'btn btn-primary']
) ?>
<?= $this->Form->end() ?>
