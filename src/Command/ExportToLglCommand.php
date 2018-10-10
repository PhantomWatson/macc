<?php
namespace App\Command;

use App\Integrations\LglIntegration;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\ORM\TableRegistry;
use Cake\Shell\Helper\ProgressHelper;

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
        $users = TableRegistry::getTableLocator()
            ->get('Users')
            ->find()
            ->all();
        $count = count($users);
        $io->out(sprintf(
            ' - %s %s found',
            $count,
            __n('user', 'users', $count)
        ));

        $io->out();
        $io->out('Sending user info to LGL...');

        /** @var ProgressHelper $progress */
        $progress = $io->helper('Progress');
        $progress->init([
            'total' => $count,
            'width' => 20
        ]);
        $lgl = new LglIntegration();
        foreach ($users as $user) {
            $lgl->addUser($user);
            $progress->increment(1);
            $progress->draw();
        }

        $io->out();
        $io->success('Done');
    }
}
