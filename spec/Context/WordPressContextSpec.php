<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordPressContextSpec extends ObjectBehavior
{
	function it_is_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext' );
	}
}
