<?php

namespace UniPress;

use Symfony\Component\DependencyInjection\ContainerBuilder;

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
}