<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

use RdKafka\KafkaConsumer;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class KafkaReceiver
{
    public function __construct(private readonly array $options, private readonly SerializerInterface $serializer)
    {
    }

    public function get(array $queues = ['events']): iterable
    {
        $message = $this->getConsumer($queues)->consume($this->options['receiveTimeout']);

        switch ($message->err) {
            case \RD_KAFKA_RESP_ERR_NO_ERROR:
                $envelope = $this->serializer->decode([
                    'body' => $message->payload,
                    'headers' => $message->headers,
                    'key' => $message->key,
                    'offset' => $message->offset,
                    'timestamp' => $message->timestamp,
                ]);

                return [$envelope->with(new KafkaReceivedStamp($message))];
            case \RD_KAFKA_RESP_ERR__TRANSPORT:
            case \RD_KAFKA_RESP_ERR__TIMED_OUT:
            case \RD_KAFKA_RESP_ERR__PARTITION_EOF:
                break;
            default:
                throw new TransportException($message->errstr(), $message->err);
        }

        return [];
    }

    public function ack(Envelope $envelope): void
    {
        $stamp = $envelope->last(KafkaReceivedStamp::class);

        if (null === $stamp) {
            return;
        }

        $message = $stamp->message;

        if ($this->options['commitAsync']) {
            $this->getConsumer()->commitAsync($message);
        } else {
            $this->getConsumer()->commit($message);
        }
    }

    public function reject(Envelope $envelope): void
    {
    }

    private function getConsumer(array $queues = []): KafkaConsumer
    {
        static $consumer = null;

        if (null === $consumer) {
            $consumer = new KafkaConsumer(new Configuration($this->options['consumer']));
            $consumer->subscribe($queues);
        }

        return $consumer;
    }
}
