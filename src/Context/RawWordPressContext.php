<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Features context.
 */
class RawWordPressContext extends RawMinkContext
{
	protected $timeout = 60;
	private $parameters; // parameters from the `behat.yml`.
	private $variables = array();

	/**
	 * Set parameter form initializer
	 *
	 * @param array $params The parameter for this extension.
	 */
	public function set_params( $params )
	{
		// var_dump( $params );
		$this->parameters = $params;
	}

	/**
	 * Get parameters
	 *
	 * @return array The parameter for this extension.
	 */
	public function get_params()
	{
		return $this->parameters;
	}

	/**
	 * Set variables for the senario.
	 *
	 * @param string $key The parameter for this extension.
	 */
	public function set_variables( $key, $value )
	{
		$this->variables[ $key ] = $value;
	}

	/**
	 * Get variables for the senario.
	 *
	 * @param string $key The parameter for this extension.
	 * @return array The value of the variable.
	 */
	public function get_variables( $key )
	{
		if ( ! empty( $this->variables[ $key ] ) ) {
			return $this->variables[ $key ];
		} else {
			return null;
		}
	}

	/**
	 * Log in into the WordPress
	 *
	 * @param string $user The user name.
	 * @param string $password The password.
	 */
	public function login( $user, $password )
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
				$admin_url = $this->get_admin_url() . '/';
				if ( $this->is_current_url( $admin_url ) ) {
					return true;
				} else {
					return false;
				}
			} catch ( \Exception $e ) {
				// do nothing
			}

			sleep( 1 );
		}

		throw new \Exception( 'Login timeout' );
	}

	/**
	 * Retrun true when I am at `$url`.
	 *
	 * @param string $url The URL where I should be.
	 * @return bool Return true when I am at `$url`.
	 */
	protected function is_current_url( $url )
	{
		$current_url = $this->getSession()->getCurrentUrl();
		if ( $url === substr( $current_url, 0 - strlen( $url ) ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Log out from WordPress
	 *
	 * @param none
	 */
	protected function logout()
	{
		$this->getSession()->visit( $this->locatePath( $this->get_admin_url() . '/' ) );
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
	protected function is_logged_in()
	{
		$session = $this->getSession();
		$url = $session->getCurrentUrl();
		$admin_url = $this->get_admin_url() . '/';

		// go to the /wp-admin/
		$session->visit( $this->locatePath( $admin_url ) );

		if ( $this->is_current_url( $admin_url ) ) {
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
	 * Get the current theme
	 *
	 * @return string The slug of the current theme.
	 */
	protected function get_current_theme()
	{
		if ( ! $this->is_logged_in() ) {
			throw new \Exception( "You are not logged in" );
		}

		$this->getSession()->visit( $this->locatePath( $this->get_admin_url() . '/themes.php' ) );
		$page = $this->getSession()->getPage();
		$e = $page->find( 'css', ".theme.active" );
		if ( $e ) {
			$theme = $e->getAttribute( "data-slug" );
			if ( $theme ) {
				return $theme;
			}
		}

		throw new \Exception( "Maybe you don't have permission to get the current theme." );
	}

	/**
	 * Get the WordPress version from meta.
	 *
	 * @return string WordPress version number.
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

	/**
	 * Replace with variables
	 *
	 * @param string $str The str or {VARIABLE} format text.
	 * @return string The value of the variable.
	 */
	public function replace_variables( $str )
	{
		if ( preg_match( "/^\{([A-Z0-9_]+)\}$/", $str, $matches ) ) {
			$key = $matches[1];
			if ( $this->get_variables( $key ) ) {
				return $this->get_variables( $key );
			}
		}
		return $str;
	}

	/**
	 * Returns the admin_url from configuration
	 *
	 * @param none
	 * @return string Admin url like `/wp-admin`
	 */
	protected function get_admin_url()
	{
		$params = $this->get_params();
		return $params['admin_url'];
	}

	/**
	 * Asserts that two variables have the same type and value.
	 * Used on objects, it asserts that two variables reference
	 * the same object.
	 *
	 * @param mixed  $expected
	 * @param mixed  $actual
	 * @param string $message
	 */
	protected function assertSame( $expected, $actual, $message = '' )
	{
		\PHPUnit_Framework_Assert::assertSame( $expected, $actual, $message = '' );
	}

	/**
	 * Asserts that a condition is true.
	 *
	 * @param bool   $condition
	 * @param string $message
	 *
	 * @throws PHPUnit_Framework_AssertionFailedError
	 */
	protected function assertTrue( $condition, $message = '' )
	{
		\PHPUnit_Framework_Assert::assertTrue( $condition, $message = '' );
	}
}
