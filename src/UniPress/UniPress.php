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

            self::$container->register('wp_embed', 'WordPress\WPEmbed');
            self::$container->register('wp_rewrite', 'WordPress\Rewrite');
            self::$container->register('wp_widget_factory', 'WordPress\Widget\Factory');
            self::$container->register('wp_roles', 'WordPress\WPRoles');
            self::$container->register('wp_locale', 'WordPress\WPLocale');
            self::$container->register('wp_object_cache', 'WordPress\WPObjectCache');
            self::$container->register('wp_query', 'WP_Query');
            self::$container->register('wp_customize', 'WordPress\WPCustomizeManager');
            self::$container->register('wp_scripts', 'WordPress\WPScripts');
            self::$container->register('wp_styles', 'WordPress\WPStyles');

            foreach (self::$container->getServiceIds() as $id) {
                $GLOBALS[$id] = self::$container->get($id);
            }
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