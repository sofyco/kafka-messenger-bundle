<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Messenger\Transport\TransportFactoryInterface;
use Symfony\Component\Messenger\Transport\TransportInterface;

/**
 * @template-implements TransportFactoryInterface<TransportInterface>
 */
final class KafkaTransportFactory implements TransportFactoryInterface
{
    private const string PROTOCOL = 'kafka://';

    public function createTransport(string $dsn, array $options, SerializerInterface $serializer): TransportInterface
    {
        $dsn = \str_replace(self::PROTOCOL, '', $dsn);

        $options['consumer']['metadata.broker.list'] = $dsn;
        $options['producer']['metadata.broker.list'] = $dsn;

        return new KafkaTransport($options, $serializer);
    }

    public function supports(string $dsn, array $options): bool
    {
        return \str_starts_with($dsn, self::PROTOCOL);
    }
}
