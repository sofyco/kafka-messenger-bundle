<?php declare(strict_types=1);

namespace Sofyco\Bundle\KafkaMessengerBundle\Transport;

final class Configuration extends \RdKafka\Conf
{
    public function __construct(array $options)
    {
        parent::__construct();

        foreach ($options as $name => $value) {
            $this->set($name, (string) $value);
        }
    }
}
