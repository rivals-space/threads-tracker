<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\FetchMastodonNotificationsMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:check-notifications',
    description: 'Force check mastodon notifications',
)]
class CheckNotificationsCommand extends Command
{
    public function __construct(
        readonly private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new FetchMastodonNotificationsMessage());

        return Command::SUCCESS;
    }
}
