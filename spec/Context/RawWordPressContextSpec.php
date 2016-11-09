<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Behat\Mink\Mink;

use Behat\MinkExtension\Context\RawMinkContext;

use VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext;

class RawWordPressContextSpec extends ObjectBehavior
{
	function it_is_raw_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext' );
	}

	function it_can_set_and_get_parameters()
	{
		$parameters = array(
            'one' => '1',
            'two' => '2',
        );
        $this->set_params( $parameters );
        $this->get_params()->shouldReturn( $parameters );
	}

	function it_can_replace_var()
	{
		// variable should be converted
		$this->set_variables( 'FOO', 'hello' );
		$this->replace_variables( '{FOO}' )->shouldReturn( 'hello' );

		// $str is not variable
		$this->replace_variables( 'FOO' )->shouldReturn( 'FOO' );

		// $str is look like variable, but it is undefined
		$this->replace_variables( '{HELLO}' )->shouldReturn( '{HELLO}' );
	}
}
