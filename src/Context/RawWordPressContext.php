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
	private $guzzle;

	public function __construct()
	{
		$this->guzzle = new \GuzzleHttp\Client();
	}

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
	 * Get http status code from the current page.
	 *
	 * @return int HTTP status code.
	 */
	protected function get_http_status()
	{
		$session = $this->getSession();
		return intval( $session->getStatusCode() );
	}

	/**
	 * Get http response headers from the current page.
	 *
	 * @return array HTTP response headers.
	 */
	protected function get_http_headers()
	{
		$session = $this->getSession();
		return $session->getResponseHeaders();
	}

	/**
	 * Get contents from $url.
	 *
	 * @param string $url The URL.
	 * @param string $method The request method.
	 * @param array $params An array of the http request.
	 * @return string The contents.
	 */
	protected function get_contents( $url, $method = 'GET', $params = array() )
	{
		$defaults = array(
			'verify' => false,
			'version' => '1.1'
		);

		$response = $this->guzzle->request( $method, $url, array_merge(
			$defaults, $params
		) );

		return $response->getBody();
	}

	/**
	 * Log in into the WordPress.
	 *
	 * @param string $user The user name.
	 * @param string $password The password.
	 * @return bool
	 * @throws \Exception If the page returns something wrong.
	 */
	protected function login( $user, $password )
	{
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
	 * @throws \Exception If the page returns something wrong.
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
	 * Log out from WordPress.
	 *
	 * @param none
	 * @return bool
	 * @throws \Exception If the page returns something wrong.
	 */
	protected function logout()
	{
		if ( ! $this->is_logged_in() ) {
			return; // user isn't login.
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
		$page = $this->getSession()->getPage();
		if ( $page->find( "css", ".logged-in" ) ) {
			return true;
		} elseif ( $page->find( "css", ".wp-admin" ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Wait the $selector to be loaded
	 *
	 * @param string $selector The CSS selector.
	 * @return bool
	 * @throws \Exception If the page returns something wrong.
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
	 * @throws \Exception
	 */
	protected function get_plugins()
	{
		if ( ! $this->is_logged_in() ) {
			throw new \Exception( "You are not logged in" );
		}

		$session = $this->getSession();
		$session->visit( $this->locatePath( $this->get_admin_url() . '/plugins.php' ) );
		$page = $session->getPage();
		$e = $page->findAll( 'css', "#the-list tr" );
		if ( ! count( $e ) ) {
			throw new \Exception( "Maybe you don't have permission to get plugins." );
		}

		$plugins = array();
		foreach ( $e as $plugin ) {
			$slug = $plugin->getAttribute( "data-slug" );
			$classes = preg_split( "/\s+/", $plugin->getAttribute( "class" ) );
			if ( in_array( "active", $classes ) ) {
				$status = "active";
			} else {
				$status = "inactive";
			}

			$plugins[ $slug ] = array(
				"status" => $status,
			);
		}

		return $plugins;
	}

	/**
	 * Get the current theme
	 *
	 * @return string The slug of the current theme.
	 * @throws \Exception
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
			$classes = preg_split( "/\s+/", trim( $e->getAttribute( "aria-describedby" ) ) );
			$theme = preg_replace( "/\-(name|action)$/", "", $classes[0] );
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
	 * @throws \Exception
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
	public function get_admin_url()
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
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	protected function assertTrue( $condition, $message = '' )
	{
		\PHPUnit_Framework_Assert::assertTrue( $condition, $message = '' );
	}

	/**
	 * Asserts that a condition is false.
	 *
	 * @param bool   $condition
	 * @param string $message
	 *
	 * @throws \PHPUnit_Framework_AssertionFailedError
	 */
	protected function assertFalse( $condition, $message = '' )
	{
		\PHPUnit_Framework_Assert::assertFalse( $condition, $message = '' );
	}
}
