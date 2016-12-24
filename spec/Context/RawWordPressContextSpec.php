<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;

class RawWordPressContextSpec extends ObjectBehavior
{
	function it_is_raw_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext' );
	}

	function it_can_set_and_get_parameters()
	{
		$params = json_decode( '{
			"roles": {
				"administrator": {
					"username": "admin",
					"password": "admin"
				}
			},
			"admin_url": "/wp-admin"
		}', true );

        $this->set_params( $params );
        $this->get_params()->shouldReturn( $params );
	}


	function it_can_replace_var()
	{
		// variable should be converted.
		$this->set_variables( 'FOO', 'hello' );
		$this->replace_variables( '{FOO}' )->shouldReturn( 'hello' );

		// $str is not variable
		$this->replace_variables( 'FOO' )->shouldReturn( 'FOO' );

		// $str looks like variable, but it is undefined.
		$this->replace_variables( '{HELLO}' )->shouldReturn( '{HELLO}' );
	}

	function it_can_get_admin_url()
	{
		$params = json_decode( '{
			"roles": {
				"administrator": {
					"username": "admin",
					"password": "admin"
				}
			},
			"admin_url": "/wp-admin"
		}', true );

		$this->set_params( $params );
		$this->get_admin_url()->shouldReturn( '/wp-admin' );
	}
}
