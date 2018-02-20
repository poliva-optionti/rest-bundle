<?php

namespace MNC\RestBundle;

use MNC\RestBundle\DependencyInjection\Compiler\ExtensionCompilerPass;
use MNC\RestBundle\DependencyInjection\Compiler\TransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MNCRestBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TransformerCompilerPass());
        $container->addCompilerPass(new ExtensionCompilerPass());
        parent::build($container);
    }
}
