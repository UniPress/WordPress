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
                ->setClass('WordPress\WPEmbed')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPEmbed')
                ->addArgument('wp_embed');

            self::$container->register('wp_rewrite')
                ->setClass('WordPress\Rewrite')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\Rewrite')
                ->addArgument('wp_rewrite');

            self::$container->register('wp_widget_factory')
                ->setClass('WordPress\Widget\Factory')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\Widget\Factory')
                ->addArgument('wp_widget_factory');

            self::$container->register('wp_roles')
                ->setClass('WordPress\WPRoles')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPRoles')
                ->addArgument('wp_roles');

            self::$container->register('wp_locale')
                ->setClass('WordPress\WPLocale')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPLocale')
                ->addArgument('wp_locale');

            self::$container->register('wp_object_cache')
                ->setClass('WordPress\WPObjectCache')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPObjectCache')
                ->addArgument('wp_object_cache');

            self::$container->register('wp_query')
                ->setClass('WP_Query')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WP_Query')
                ->addArgument('wp_query');

            self::$container->register('wp_customize')
                ->setClass('WordPress\WPCustomizeManager')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPCustomizeManager')
                ->addArgument('wp_customize');

            self::$container->register('wp_scripts')
                ->setClass('WordPress\WPScripts')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPScripts')
                ->addArgument('wp_scripts');

            self::$container->register('wp_styles')
                ->setClass('WordPress\WPStyles')
                ->setFactoryService('global_service_factory')
                ->setFactoryMethod('create')
                ->addArgument('WordPress\WPStyles')
                ->addArgument('wp_styles');

            self::$container->compile();
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