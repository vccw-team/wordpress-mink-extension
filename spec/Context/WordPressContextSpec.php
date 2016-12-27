<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;

use Behat\Mink\Mink,
	Behat\Mink\Session,
	Behat\Mink\Driver\GoutteDriver,
	Behat\Mink\Driver\Goutte\Client as GoutteClient,
	Behat\Mink\Driver\Selenium2Driver;

class WordPressContextSpec extends ObjectBehavior
{
	function it_is_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext' );
	}

	public function it_should_check_status()
	{
		$this->init_mink( 'goutte' );
		$session = $this->getSession();

		$session->visit( 'http://127.0.0.1:8080/' );
		$this->shouldThrow( new \Exception( 'The HTTP status is 200, but it should be 404' ) )->during(
			'the_http_status_should_be',
			array( 404 )
		);
	}

	private function init_mink( $driver )
	{
		$mink = new Mink( array(
			'goutte' => new Session( new GoutteDriver( new GoutteClient() ) ),
			'selenium2' => new Session( new Selenium2Driver() ),
		) );
		$mink->setDefaultSessionName( $driver );
		$this->setMink( $mink );
	}
}
