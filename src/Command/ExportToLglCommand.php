<?php
namespace App\Command;

use App\Integrations\LglIntegration;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;

/**
 * Class ExportToLglCommand
 * @package App\Command
 * @property ConsoleIo $io
 */
class ExportToLglCommand extends Command
{

    /**
     * Initialization method
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * Exports existing user information to the Little Green Light service
     *
     * @param Arguments $args Arguments
     * @param ConsoleIo $io Console IO object
     * @return int|null|void
     * @throws \Exception
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('This function will export all existing user information to Little Green Light');
        $continue = $io->askChoice('Continue?', ['y', 'n'], 'y') == 'y';
        if (!$continue) {
            return;
        }

        $io->out();
        $io->out('Collecting users...');
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $users = $usersTable->find()->all();
        $count = $users->count();
        $io->out(sprintf(
            ' - %s %s found',
            $count,
            __n('user', 'users', $count)
        ));

        $io->out();
        $io->out('Sending user info to LGL...');

        $lgl = new LglIntegration();
        foreach ($users as $user) {
            $lgl->addUser($user);
        }
        $io->out(' - Done');

        $io->out();
        $io->out('Collecting current membership info...');
        $members = [];
        foreach ($users as $user) {
            if ($usersTable->isCurrentMember($user->id)) {
                $members[] = $user;
            }
        }

        $count = count($members);
        $io->out(sprintf(
            ' - %s current %s found',
            $count,
            __n('membership', 'memberships', $count)
        ));
        $io->out();

        $io->out('Sending membership info to LGL...');
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        foreach ($members as $member) {
            $membership = $membershipsTable->getCurrentMembership($member->id);
            $lgl->addMembership($member, $membership);
        }
        $io->out(' - Done');

        $io->out();
        $io->success('Export complete');
    }
}
