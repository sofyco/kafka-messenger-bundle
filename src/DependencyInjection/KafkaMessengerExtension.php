<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\DependencyInjection;

use Sofyco\Bundle\KafkaMessengerBundle\Transport\KafkaTransportFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

final class KafkaMessengerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $transport = new Definition(KafkaTransportFactory::class);
        $transport->addTag('messenger.transport_factory');

        $container->setDefinition(KafkaTransportFactory::class, $transport);
    }
}
