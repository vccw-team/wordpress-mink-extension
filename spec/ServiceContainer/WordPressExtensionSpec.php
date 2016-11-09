<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\ServiceContainer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WordPressExtensionSpec extends ObjectBehavior
{
	function it_is_a_testwork_extension()
	{
		$this->shouldHaveType( 'Behat\Testwork\ServiceContainer\Extension' );
	}

	function it_is_named_wp()
	{
		$this->getConfigKey()->shouldReturn('wp');
	}
}
