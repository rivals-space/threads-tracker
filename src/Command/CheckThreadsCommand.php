<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\StartBatchCheckingMessage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:check-threads',
    description: 'Force check threads accounts',
)]
class CheckThreadsCommand extends Command
{
    public function __construct(
        readonly private MessageBusInterface $bus,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bus->dispatch(new StartBatchCheckingMessage());

        return Command::SUCCESS;
    }
}
