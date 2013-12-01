<?php

namespace UniPress;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class UniPress
{
    /** @var ContainerBuilder */
    private static $container;

    public static function getContainer()
    {
        if (!self::$container) {
            self::$container = new ContainerBuilder();
        }

        return self::$container;
    }

    public static function getService($serviceId)
    {
        $container = self::getContainer();
        if ($container->has($serviceId)) {
            return $container->get($serviceId);
        }

        throw new ServiceNotFoundException($serviceId);
    }
}