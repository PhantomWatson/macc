<?php
namespace App\Command;

use App\Model\Entity\Membership;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\SocketException;
use Cake\I18n\Time;
use Cake\Mailer\Exception\MissingActionException;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;

/**
 * Class AlertUpcomingExpirationsCommand
 * @package App\Command
 * @property ConsoleIo $io
 */
class AlertUpcomingExpirationsCommand extends Command
{
    use MailerAwareTrait;

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
     * Processes location info file and updates the database
     *
     * @param Arguments $args Arguments
     * @param ConsoleIo $io Console IO object
     * @return int|null|void
     * @throws \Exception
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $memberships = $this->getExpiringMemberships();

        if (empty($memberships)) {
            $io->out('No expiring memberships found');

            return;
        }

        $io->out('Expiring memberships found:');
        foreach ($memberships as $membership) {
            $io->out(sprintf(
                ' - %s (%s)',
                $membership->user->name,
                $membership->expires->format('F j, Y')
            ));
        }

        $io->out();
        $io->out('Sending emails...');
        foreach ($memberships as $membership) {
            $io->out(sprintf(
                ' - Sending to %s (%s)',
                $membership->user->name,
                $membership->user->email
            ));
            try {
                $this->getMailer('Membership')->send('expiringMembership', [$membership]);
            } catch (SocketException $exception) {
                $io->error('   SocketException: ' . $exception->getMessage());
                continue;
            } catch (MissingActionException $exception) {
                $io->error('   MissingActionException: ' . $exception->getMessage());
                continue;
            } catch (\BadMethodCallException $exception) {
                $io->error('   BadMethodCallException: ' . $exception->getMessage());
                continue;
            }
            $io->out('   Sent');
        }

        $io->out();
        $io->out('Done');
    }

    /**
     * Returns an array of memberships that will expire in one week or one day
     *
     * @return Membership[]
     */
    private function getExpiringMemberships()
    {
        $membershipsTable = TableRegistry::getTableLocator()->get('Memberships');
        $expirationDateTriggers = [
            new Time('+ 1 week'),
            new Time('+ 1 day')
        ];
        $retval = [];
        foreach ($expirationDateTriggers as $time) {
            /** @var Time $time */
            $memberships = $membershipsTable->find()
                ->where([
                    'expires >=' => $time->i18nFormat('yyyy-MM-dd') . ' 00:00:00',
                    'expires <=' => $time->i18nFormat('yyyy-MM-dd') . ' 23:59:59',
                    function (QueryExpression $exp) {
                        return $exp->isNull('canceled');
                    },
                    function (QueryExpression $exp) {
                        return $exp->isNull('renewed');
                    }
                ])
                ->contain(['Users'])
                ->all();
            if ($memberships) {
                foreach ($memberships as $membership) {
                    $retval[] = $membership;
                }
            }
        }

        return $retval;
    }
}
