<?php

declare(strict_types=1);

namespace App\Scheduler;

use App\Message\FetchMastodonNotificationsMessage;
use App\Message\StartBatchCheckingMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
class DefaultScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        $schedule = new Schedule();

        foreach ($this->getScheduleList() as $recurringMessage) {
            $schedule->add($recurringMessage);
        }

        return $schedule;
    }

    /**
     * @return iterable<RecurringMessage>
     */
    private function getScheduleList(): iterable
    {
        yield RecurringMessage::every('10 minutes', new StartBatchCheckingMessage());
        yield RecurringMessage::every('10 minutes', new FetchMastodonNotificationsMessage());
    }
}
