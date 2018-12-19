<?php
namespace App\Command;

use App\LocalTime\LocalTime;
use App\Model\Entity\Membership;
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
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
                LocalTime::getDate($membership->expires)
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
        $expirationDateTriggers = $this->getExpirationDateTriggers();
        $retval = [];
        foreach ($expirationDateTriggers as $boundaries) {
            $memberships = $membershipsTable->find()
                ->where([
                    'expires >=' => $boundaries['start'],
                    'expires <=' => $boundaries['end'],
                    function (QueryExpression $exp) {
                        return $exp->isNull('canceled');
                    },
                    function (QueryExpression $exp) {
                        return $exp->isNull('renewed');
                    }
                ])
                ->contain(['Users', 'MembershipLevels'])
                ->all();
            if ($memberships) {
                foreach ($memberships as $membership) {
                    $retval[] = $membership;
                }
            }
        }

        return $retval;
    }

    /**
     * Returns the number of seconds between the current UTC time and the current local (Muncie) time
     *
     * @return int
     */
    private function getOffsetFromUtc()
    {
        $localTimezone = new \DateTimeZone(Configure::read('localTimezone'));
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        return $localTimezone->getOffset($utcTime);
    }

    /**
     * Returns an array that contains the UTC times that correspond to the boundaries of local days
     *
     * e.g. for "tomorrow", the UTC times are returned that correspond to the local times that begin and end that day
     * This is used so that statements like "your membership will expire tomorrow" can be accurate despite the
     * complications of storing dates in UTC time
     *
     * @return array
     */
    private function getExpirationDateTriggers()
    {
        $offset = $this->getOffsetFromUtc();
        $nowLocal = Time::now()->addSeconds($offset);
        $todayStart = $nowLocal
            ->copy()
            ->hour(0)
            ->minute(0)
            ->second(0);
        $todayEnd = $nowLocal
            ->copy()
            ->hour(23)
            ->minute(59)
            ->second(59);

        $expirationDateTriggers = [];

        // Boundaries for the day one week from today
        $expirationDateTriggers[] = [
            'start' => $todayStart
                ->copy()
                ->addDays(7)
                ->subSeconds($offset),
            'end' => $todayEnd
                ->copy()
                ->addDays(7)
                ->subSeconds($offset)
        ];

        // Boundaries for tomorrow
        $expirationDateTriggers[] = [
            'start' => $todayStart
                ->copy()
                ->addDays(1)
                ->subSeconds($offset),
            'end' => $todayEnd
                ->copy()
                ->addDays(1)
                ->subSeconds($offset)
        ];

        return $expirationDateTriggers;
    }
}
