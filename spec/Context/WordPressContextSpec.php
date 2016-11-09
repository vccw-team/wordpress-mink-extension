<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
// 
// use Behat\MinkExtension\Context\RawMinkContext;

class WordPressContextSpec extends ObjectBehavior
{
	function it_is_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\WordPressContext' );
	}
}
