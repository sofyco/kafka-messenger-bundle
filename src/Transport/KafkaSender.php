<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

use RdKafka\Producer;
use RdKafka\ProducerTopic;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\Sender\SenderInterface;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;

final class KafkaSender implements SenderInterface
{
    public function __construct(private readonly array $options, private readonly SerializerInterface $serializer)
    {
    }

    public function send(Envelope $envelope): Envelope
    {
        $code = 0;
        $payload = $this->serializer->encode($envelope);

        $this->getTopic($this->options['topicName'])->produce(\RD_KAFKA_PARTITION_UA, 0, $payload['body']);

        for ($i = 0; $i < $this->options['flushRetries']; ++$i) {
            if (\RD_KAFKA_RESP_ERR_NO_ERROR === $code = $this->getProducer()->flush($this->options['flushTimeout'])) {
                return $envelope;
            }
        }

        throw new TransportException(\sprintf('Kafka producer response error: %d', $code), $code);
    }

    private function getProducer(): Producer
    {
        static $producer = null;

        return $producer ??= new Producer(new Configuration($this->options['producer']));
    }

    private function getTopic(string $name): ProducerTopic
    {
        static $topics = [];

        return $topics[$name] ??= $this->getProducer()->newTopic($name);
    }
}
