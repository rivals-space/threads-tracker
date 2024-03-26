<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\PostMastodonPrivateMentionMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Notifier\Bridge\Mastodon\MastodonOptions;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Exception\TransportExceptionInterface;
use Symfony\Component\Notifier\Message\ChatMessage;

#[AsMessageHandler]
final readonly class PostMastodonPrivateMentionMessageHandler
{
    public function __construct(
        private ChatterInterface $chatter,
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(PostMastodonPrivateMentionMessage $message): void
    {
        $chatMessage = new ChatMessage(sprintf('%s%s@%s', $message->message, PHP_EOL.PHP_EOL, $message->acct));
        $chatMessage
            ->transport('mastodon')
            ->options(new MastodonOptions([
                'visibility' => 'direct',
            ]))
        ;

        $this->chatter->send($chatMessage);
    }
}
