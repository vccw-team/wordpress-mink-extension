<?php

namespace spec\VCCW\Behat\Mink\WordPressExtension\Context;

use PhpSpec\ObjectBehavior;

use Behat\Mink\Mink,
    Behat\Mink\Session,
	Behat\Mink\Driver\GoutteDriver,
	Behat\Mink\Driver\Goutte\Client as GoutteClient,
	Behat\Mink\Driver\Selenium2Driver;

class RawWordPressContextSpec extends ObjectBehavior
{
	public function it_is_raw_wordpress_aware()
	{
		$this->shouldHaveType( 'VCCW\Behat\Mink\WordPressExtension\Context\RawWordPressContext' );
	}

	public function it_can_set_and_get_parameters()
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
	
	public function it_can_replace_var()
	{
		// variable should be converted.
		$this->set_variables( 'FOO', 'hello' );
		$this->replace_variables( '{FOO}' )->shouldReturn( 'hello' );

		// $str is not variable
		$this->replace_variables( 'FOO' )->shouldReturn( 'FOO' );

		// $str looks like variable, but it is undefined.
		$this->replace_variables( '{HELLO}' )->shouldReturn( '{HELLO}' );
	}

	public function it_can_get_admin_url()
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

	public function it_should_get_status()
	{
		$this->init_mink( 'goutte' );
		$session = $this->getSession();

		$session->visit( 'http://127.0.0.1:8080/' );
		$this->get_http_status()->shouldReturn( 200 );

		$session->visit( 'http://127.0.0.1:8080/the-page-not-found' );
		$this->get_http_status()->shouldReturn( 404 );
	}

	public function it_should_get_headers()
	{
		$this->init_mink( 'goutte' );
		$session = $this->getSession();

		$session->visit( 'http://127.0.0.1:8080/' );
		$this->get_http_headers()->shouldHaveKeyWithValue(
			'Content-Type',
			array( 'text/html; charset=UTF-8' )
		);

		$session->visit( 'http://127.0.0.1:8080/the-page-not-found' );
		$this->get_http_headers()->shouldHaveKeyWithValue(
			'Content-Type',
			array( 'text/html; charset=UTF-8' )
		);
	}

	public function it_can_login_with_goutte()
	{
		$this->init_mink( 'goutte' );
		$session = $this->getSession();

		$this->login( 'admin', 'admin' )->shouldReturn( true );
		$this->login( 'admin', 'xxxx' )->shouldReturn( false );
	}

	public function it_can_login_with_selenium2()
	{
		$this->init_mink( 'selenium2' );
		$session = $this->getSession();

		$this->login( 'admin', 'admin' )->shouldReturn( true );
		$this->login( 'admin', 'xxxx' )->shouldReturn( false );
	}

	private function init_mink( $driver )
	{
		$mink = new Mink( array(
			'goutte' => new Session( new GoutteDriver( new GoutteClient() ) ),
			'selenium2' => new Session( new Selenium2Driver() ),
		) );
		$mink->setDefaultSessionName( $driver );
		$this->setMink( $mink );
		$this->setMinkParameter( 'base_url', 'http://127.0.0.1:8080' );
	}
}
