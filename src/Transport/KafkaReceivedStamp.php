<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

use RdKafka\Message;
use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

final readonly class KafkaReceivedStamp implements NonSendableStampInterface
{
    public function __construct(public Message $message)
    {
    }
}
