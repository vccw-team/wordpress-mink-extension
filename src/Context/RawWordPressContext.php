<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Features context.
 */
class RawWordPressContext extends RawMinkContext
{
	protected $timeout = 60;

	protected $parameters; // parameters from the `behat.yml`.

	public function set_params( $params )
	{
		$this->parameters = $params;
	}

	public function get_params()
	{
		return $this->parameters;
	}

	/**
	 * Log in into the WordPress
	 *
	 * @param string $user The user name.
	 * @param string $password The password.
	 */
	protected function login( $user, $password )
	{
		$this->logout();

		$this->getSession()->visit( $this->locatePath( '/wp-login.php' ) );
		$this->wait_the_element( "#loginform" );

		$element = $this->getSession()->getPage();
		$element->fillField( "user_login", $user );
		$element->fillField( "user_pass", $password );

		$submit = $element->findButton( "wp-submit" );
		$submit->click();

		for ( $i = 0; $i < $this->timeout; $i++ ) {
			try {
				$page = $this->getSession()->getPage();
				if ( $page->find( 'css', "body.wp-core-ui" ) ) {
					return true;
				}
			} catch ( \Exception $e ) {
				// do nothing
			}

			sleep( 1 );
		}

		throw new \Exception( 'Login timeout' );
	}

	/**
	 * Log out from WordPress
	 *
	 * @param none
	 */
	protected function logout()
	{
		$this->getSession()->visit( $this->locatePath( '/wp-admin/' ) );
		if ( ! $this->is_logged_in() ) {
			return true; // user isn't login.
		}
		$page = $this->getSession()->getPage();
		$logout = $page->find( "css", "#wp-admin-bar-logout a" );
		if ( ! empty( $logout ) ) {
			$this->getSession()->visit( $this->locatePath( $logout->getAttribute( "href" ) ) );

			for ( $i = 0; $i < $this->timeout; $i++ ) {
				try {
					$url = $this->getSession()->getCurrentUrl();
					if ( strpos( $url, "loggedout=true" ) ) {
						return true;
					}
				} catch ( \Exception $e ) {
					// do nothing
				}

				sleep( 1 );
			}

			throw new \Exception( 'Logout timeout' );
		}
	}

	/**
	 * Determine if the a user is already logged in.
	 *
	 * @return boolean
	 *   Returns TRUE if a user is logged in for this session.
	 */
	protected function is_logged_in() {
		$session = $this->getSession();
		$url = $session->getCurrentUrl();

		// go to the /wp-admin/
		$session->visit( $this->locatePath( '/wp-admin/' ) );

		// if user doesn't login, it should be /wp-login.php
		$current_url = $session->getCurrentUrl();
		if ( "/wp-admin/" === substr( $current_url, 0 - strlen( "/wp-admin/" ) ) ) {
			$session->visit( $url );
			return true; // user isn't login.
		} else {
			$session->visit( $url );
			return false;
		}
	}

	/**
	 * Wait the $selector to be loaded
	 *
	 * @param string $selector The CSS selector.
	 * @return boolean
	 */
	protected function wait_the_element( $selector )
	{
		$page = $this->getSession()->getPage();
		$element = $page->find( 'css', $selector );

		for ( $i = 0; $i < $this->timeout; $i++ ) {
			try {
				if ( $page->find( 'css', $selector ) ) {
					sleep( 1 );
					return true;
				}
			} catch ( \Exception $e ) {
				// do nothing
			}

			sleep( 1 );
		}

		throw new \Exception( "No html element found for the selector ('$selector')" );
	}

	/**
	 * Get the WordPress version from meta.
	 *
	 * @return string WordPress versin number.
	 */
	protected function get_wp_version()
	{
		$this->getSession()->visit( $this->locatePath( '/' ) );
		$page = $this->getSession()->getPage();
		$meta = $page->find( 'css', "meta[name=generator]" );
		if ( $meta ) {
			$version = $meta->getAttribute( "content" );
			if ( $version ) {
				return str_replace( "WordPress ", "", $version );
			}
		}

		throw new \Exception( "No version number found" );
	}

	protected function replace_variables( $str ) {
		return preg_replace_callback( '/\{([A-Z_]+)\}/', array( $this, '_replace_var' ), $str );
	}

	private function _replace_var( $matches ) {
		$cmd = $matches[0];
		foreach ( array_slice( $matches, 1 ) as $key ) {
			$cmd = str_replace( '{' . $key . '}', $this->variables[ $key ], $cmd );
		}
		return $cmd;
	}
}
