<?php

namespace VCCW\Behat\Mink\WordPressExtension\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class WordPressExtension implements ExtensionInterface
{
	/**
	 * {@inheritdoc}
	 */
	public function getConfigKey()
	{
		return 'wp';
	}

	/**
	 * {@inheritdoc}
	 */
	public function initialize( ExtensionManager $extensionManager )
	{
	}

	/**
	 * {@inheritdoc}
	 */
	public function configure( ArrayNodeDefinition $builder )
	{
		$builder
			->addDefaultsIfNotSet()
			->children()
				->arrayNode( 'roles' )
					->prototype('array')
						->children()
							->scalarNode('username')->end()
							->scalarNode('password')->end()
						->end()
					->end()
				->end()
			->end();
	}

	/**
	 * {@inheritdoc}
	 */
	public function load( ContainerBuilder $container, array $config )
	{
		$definition = new Definition(
			'VCCW\Behat\Mink\WordPressExtension\Context\Initializer\WordPressInitializer',
			array(
				$config,
			)
		);
		$definition->addTag( ContextExtension::INITIALIZER_TAG );
		$container->setDefinition( 'wp.context_initializer', $definition );
	}

	/**
	 * {@inheritdoc}
	 */
	public function process( ContainerBuilder $container )
	{
	}
}
