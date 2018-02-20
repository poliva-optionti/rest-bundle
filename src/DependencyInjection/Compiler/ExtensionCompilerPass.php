<?php

namespace MNC\RestBundle\DependencyInjection\Compiler;

use Limenius\Liform\Transformer\ExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class ExtensionCompilerPass implements CompilerPassInterface
{
    const EXTENSION_TAG = 'liform.extension';

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('Limenius\Liform\Liform')) {
            return;
        }

        $liform = $container->getDefinition('Limenius\Liform\Liform');

        foreach ($container->findTaggedServiceIds(self::EXTENSION_TAG) as $id => $attributes) {
            $extension = $container->getDefinition($id);

            if (!isset(class_implements($extension->getClass())[ExtensionInterface::class])) {
                throw new \InvalidArgumentException(sprintf(
                    "The service %s was tagged as a '%s' but does not implement the mandatory %s",
                    $id,
                    self::EXTENSION_TAG,
                    ExtensionInterface::class
                ));
            }

            $liform->addMethodCall('addExtension', [$extension]);
        }
    }
}
