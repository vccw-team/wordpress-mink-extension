<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context\Initializer;

use PhpSpec\ObjectBehavior;

use Behat\Behat\Context\Context;
use VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext;

class WordPressInitializerSpec extends ObjectBehavior
{
	private $params = array();

	public function let()
	{
		$this->beConstructedWith( $this->get_mock_params() );
	}

	public function it_is_a_context_initializer_extension()
	{
		$this->shouldHaveType( 'Behat\Behat\Context\Initializer\ContextInitializer' );
	}

	public function it_does_nothing_for_basic_contexts( Context $context )
	{
		$this->initializeContext( $context );
	}

	public function it_injects_mink_and_parameters_in_mink_aware_contexts( RawWordPressContext $context )
	{
		$context->set_params( $this->get_mock_params() )->shouldBeCalled();
		$this->initializeContext( $context );
	}

	/**
	 * The parameters for the mockup
	 *
	 * @return array The parameters for the initializer.
	 */
	private function get_mock_params()
	{
		return json_decode( '{
			"roles": {
				"administrator": {
					"username": "admin",
					"password": "admin"
				},
				"editor": {
					"username": "editor",
					"password": "xxxx"
				}
			},
			"admin_url": "/wp-admin"
		}', true );
	}
}
