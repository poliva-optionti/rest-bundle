<?php

namespace MNC\RestBundle\Maker;

use Doctrine\Common\Util\Inflector;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class MakeTransformer
 * @author Matías Navarro Carter <mnavarro@option.cl>s
 */
final class MakeResource extends AbstractMaker
{
    public static function getCommandName(): string
    {
        return 'make:resource';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates multiple classes for a Resource')
            ->addArgument('resource-name', InputArgument::OPTIONAL, 'The resource name (e.g. <fg=yellow>post</>)')
            ->addArgument('custom-namespace', InputArgument::OPTIONAL, 'A root namespace where to create everything (e.g. <fg=yellow>App</>)')
            ->setHelp(file_get_contents(__DIR__.'/../Resources/help/MakeResource.txt'))
        ;
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $rootNamespace = $input->getArgument('custom-namespace');
        if ($rootNamespace !== 'App') {
            // Some reflection hooking for easing work with other Symfony Versions.
            $generatorRefl = new \ReflectionClass($generator);
            $reflProp = $generatorRefl->getProperty('namespacePrefix');
            $reflProp->setAccessible(true);
            $reflProp->setValue($generator, $rootNamespace);
            $reflProp->setAccessible(false);
        }

        $resourceName = strtolower($input->getArgument('resource-name'));
        $resourceNamePlural = Inflector::pluralize($resourceName);
        $baseName = Str::asClassName($resourceName);
        $qbAlias = $resourceName[0];

        $entityClassDetails = $generator->createClassNameDetails(
            $baseName,
        'Entity\\'
        );

        $repositoryClassDetails = $generator->createClassNameDetails(
            $baseName,
            'Repository\\',
            'Repository'
        );

        $formClassDetails = $generator->createClassNameDetails(
            $baseName,
            'Form\\',
                'Form'
        );

        $transformerClassDetails = $generator->createClassNameDetails(
            $baseName,
            'Transformer\\',
            'Transformer'
        );

        $managerClassDetails = $generator->createClassNameDetails(
            $baseName,
            'ResourceManager\\',
            'Manager'
        );

        $controllerClassDetails = $generator->createClassNameDetails(
            $baseName,
            'Controller\\',
            'Controller'
        );

        $fixtureClassDetails = $generator->createClassNameDetails(
            $baseName,
            'DataFixtures\\',
            'Fixture'
        );

        // Generating Entity
        $generator->generateClass(
            $entityClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Entity.tpl.php',
            [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'manager_full_class_name' => $managerClassDetails->getFullName(),
            ]
        );

        // Generating Repository
        $generator->generateClass(
            $repositoryClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Repository.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_alias' => $qbAlias
            ]
        );

        // Generating Manager
        $generator->generateClass(
            $managerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/ResourceManager.tpl.php',
            [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'entity_alias' => $qbAlias,
            ]
        );

        // Generating Transformer
        $generator->generateClass(
            $transformerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Transformer.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
                'resource_name' => $resourceName,
            ]
        );

        // Generating Form
        $generator->generateClass(
            $formClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Form.tpl.php',
            [
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        // Generating Controller
        $generator->generateClass(
            $controllerClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Controller.tpl.php',
            [
                'resource_name' => $resourceName,
                'resource_name_plural' => $resourceNamePlural,
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'form_full_class_name' => $formClassDetails->getFullName(),
                'transformer_full_class_name' => $transformerClassDetails->getFullName(),
            ]
        );

        // Generating Fixture
        $generator->generateClass(
            $fixtureClassDetails->getFullName(),
            __DIR__.'/../Resources/skeleton/rest/Fixture.tpl.php',
            [
                'resource_name' => $resourceName,
                'entity_full_class_name' => $entityClassDetails->getFullName(),
                'entity_class_name' => $entityClassDetails->getShortName(),
            ]
        );

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text([
            'Next: Write the logic for your application and start using it.',
        ]);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }
}