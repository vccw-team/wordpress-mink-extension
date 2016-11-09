<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context\Initializer;

use PhpSpec\ObjectBehavior;

class WordPressInitializerSpec extends ObjectBehavior
{
	function let()
	{
		$params = json_decode( '{
			"roles": {
				"administrator": {
					"username": "admin",
					"password": "admin"
				},
				"editor": {
					"username": "editor",
					"password": "editor"
				}
			},
			"admin_url": "/wp-admin"
		}', true );

		$this->beConstructedWith( $params );
	}

	function it_is_a_context_initializer_extension()
	{
		$this->shouldHaveType( 'Behat\Behat\Context\Initializer\ContextInitializer' );
	}
}
