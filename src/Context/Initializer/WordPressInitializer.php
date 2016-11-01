<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use VCCW\Behat\Mink\WordPressExtension\Context\WordPressContext;

class WordPressInitializer implements ContextInitializer
{
	private $parameters;

	/**
	 * Initializes initializer.
	 *
	 * @param array $parameters
	 */
	public function __construct( array $parameters )
	{
		$this->parameters = $parameters;
	}

	/**
	 * Initializes provided context.
	 *
	 * @param Context $context
	 */
	public function initializeContext( Context $context )
	{
		if ( $context instanceof WordPressContext ) {
			$context->set_params( $this->parameters );
		}
	}
}
