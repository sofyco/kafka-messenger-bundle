<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Tests\App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

final class Kernel extends \Symfony\Component\HttpKernel\Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new \Symfony\Bundle\FrameworkBundle\FrameworkBundle();
        yield new \Sofyco\Bundle\KafkaMessengerBundle\KafkaMessengerBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', ['test' => true]);
    }
}
