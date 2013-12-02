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

            self::$container->register('global_service_factory', 'UniPress\GlobalServiceFactory');

            self::$container->register('wp_embed')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPEmbed')
                ->addArgument('wp_embed');

            self::$container->register('wp_rewrite')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\Rewrite')
                ->addArgument('wp_rewrite');

            self::$container->register('wp_widget_factory')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\Widget\Factory')
                ->addArgument('wp_widget_factory');

            self::$container->register('wp_roles')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPRoles')
                ->addArgument('wp_roles');

            self::$container->register('wp_locale')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPLocale')
                ->addArgument('wp_locale');

            self::$container->register('wp_object_cache')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPObjectCache')
                ->addArgument('wp_object_cache');

            self::$container->register('wp_query')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WP_Query')
                ->addArgument('wp_query');

            self::$container->register('wp_customize')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPCustomizeManager')
                ->addArgument('wp_customize');

            self::$container->register('wp_scripts')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPScripts')
                ->addArgument('wp_scripts');

            self::$container->register('wp_styles')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPStyles')
                ->addArgument('wp_styles');
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