<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Receiver\QueueReceiverInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

final class KafkaTransport implements TransportInterface, QueueReceiverInterface
{
    private KafkaSender $sender;
    private KafkaReceiver $receiver;

    public function __construct(private readonly array $options, private readonly SerializerInterface $serializer)
    {
    }

    public function getFromQueues(array $queueNames): iterable
    {
        return $this->getReceiver()->get($queueNames);
    }

    public function get(): iterable
    {
        return $this->getReceiver()->get();
    }

    public function ack(Envelope $envelope): void
    {
        $this->getReceiver()->ack($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->getReceiver()->reject($envelope);
    }

    public function send(Envelope $envelope): Envelope
    {
        return $this->getSender()->send($envelope);
    }

    private function getSender(): KafkaSender
    {
        return $this->sender ??= new KafkaSender($this->options, $this->serializer);
    }

    private function getReceiver(): KafkaReceiver
    {
        return $this->receiver ??= new KafkaReceiver($this->options, $this->serializer);
    }
}
