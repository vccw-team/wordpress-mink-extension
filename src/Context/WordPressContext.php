<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\MinkContext;

/**
 * Features context.
 */
class WordPressContext extends MinkContext
{
	private $parameters; // parameters from the `behat.yml`.

	public function set_params( $params )
	{
		$this->parameters = $params;
	}

	public function get_params()
	{
		return $this->parameters;
	}

	/**
	 * Login with username and password.
	 * Example: Given I login as "admin" width password "admin"
	 *
	 * @param string $username The user name.
	 * @param string $password The password for the $username.
	 * @Given /^I login as "(?P<username>(?:[^"]|\\")*)" with password "(?P<password>(?:[^"]|\\")*)"$/
	 */
	public function login_as_user_password( $username, $password )
	{
		$this->_login( $username, $password );
	}

	/**
	 * Login as the role like "administrator", It should be defined in the `behat.yml`.
	 * Example: Given I login as the "([^"]*)" role
	 *
	 * @param string $role The role that is defined in `behat.yml`.
	 * @Given /^I login as the "(?P<role>[a-zA-Z]*)" role$/
	 */
	public function login_as_the_role( $role )
	{
		$p = $this->get_params();

		if ( empty( $p['roles'][ $role ] ) ) {
			throw new \InvalidArgumentException( sprintf(
				"Role '%s' is not defined in the `behat.yml`", $role
			) );
		} else {
			$this->_login(
				$p['roles'][ $role ]['username'],
				$p['roles'][ $role ]['password']
			);
		}
	}

	/**
	 * The mouseover over the specific element.
	 * Example: I hover over the ".site-title a" element
	 *
	 * @param string $selector The CSS selector.
	 * @When /^I hover over the "(?P<selector>[^"]*)" element$/
	 */
	public function hover_over_the_element( $selector )
	{
		$session = $this->getSession();
		$element = $session->getPage()->find( 'css', $selector );

		if ( null === $element ) {
			throw new \InvalidArgumentException( sprintf(
				'Could not evaluate CSS selector: "%s"', $selector
			) );
		}

		$element->mouseOver();
	}

	/**
	 * Wait for specific seconds.
	 * Example:
	 * * When I wait for 5 seconds
	 * * When I wait for a second
	 *
	 * @param int $second The seconds that wait for.
	 * @Given /^I wait for (?P<second>[0-9]+) seconds$/
	 * @Given /^I wait for a second$/
	 */
	public function wait_for_second( $second = 1 )
	{
		$this->getSession()->wait( $second * 1000 );
	}

	/**
	 * Wait the specific element will be loaded.
	 * Example: I wait the "#wpadminbar" element be loaded
	 *
	 * @Given /^I wait the "(?P<selector>[^"]*)" element be loaded$/
	 */
	public function wait_the_element_be_loaded( $selector )
	{
		$wait = 60;

		$page = $this->getSession()->getPage();
		$element = $page->find( 'css', $selector );

		for ( $i = 0; $i < $wait; $i++ ) {
			try {
				if ( $page->find( 'css', $selector ) ) {
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
	 * Change the screen size.
	 * Example: Given the screen size is 1440x900
	 *
	 * @param int $width The screen width.
	 * @param int $height The screen height.
	 * @Given /^the screen size is (?P<width>[0-9]+)x(?P<height>[0-9]+)/
	 */
	public function set_window_size( $width, $height )
	{
		$this->getSession()->getDriver()->resizeWindow( $width, $height, 'current' );
	}

	/**
	 * Logout from the WordPress.
	 * Example: When I logout
	 *
	 * @Given I logout
	 * @Given I am not logged in
	 */
	public function logout()
	{
		$this->_logout();
	}

	/**
	 * Take a screenshot of the current page and save it to the specific path.
	 * Example: Then take a screenshot and save it to "./path/to/image.png"
	 *
	 * @param string $path The path to the screenshot will be saved
	 * @Then /^take a screenshot and save it to "(.*)"/
	 */
	public function take_a_screenshot( $path )
	{
		$path = str_replace( "~", posix_getpwuid(posix_geteuid())['dir'], $path );
		$image = $this->getSession()->getDriver()->getScreenshot();
		$result = file_put_contents( $path, $image );

		if ( ! $result ) {
			throw new \Exception( 'Cannot take a screenshot.' );
		}
	}

	/**
	 * Log in into the WordPress
	 *
	 * @param string $user The user name.
	 * @param string $password The password.
	 */
	private function _login( $user, $password )
	{
		$this->_logout();

		$this->getSession()->visit( $this->locatePath( '/wp-login.php' ) );
		$element = $this->getSession()->getPage();
		$element->fillField( "user_login", $user );
		$element->fillField( "user_pass", $password );
		$submit = $element->findButton( "wp-submit" );
		if ( empty( $submit ) ) {
			throw new \Exception( sprintf(
				"No submit button at %s",
				$this->getSession()->getCurrentUrl()
			) );
		}

		$submit->click();

		$this->getSession()->visit( $this->locatePath( '/wp-admin/' ) );
		$current_url = $this->getSession()->getCurrentUrl();
		if ( "/wp-admin/" === substr( $current_url, 0 - strlen( "/wp-admin/" ) ) ) {
			return true;
		} else {
			throw new \Exception( 'Login failed.' );
		}
	}

	/**
	 * Log out from WordPress
	 *
	 * @param none
	 */
	private function _logout()
	{
		$this->getSession()->visit( $this->locatePath( '/wp-admin/' ) );
		$current_url = $this->getSession()->getCurrentUrl();
		if ( "/wp-admin/" !== substr( $current_url, 0 - strlen( "/wp-admin/" ) ) ) {
			return true; // user isn't login.
		}
		$page = $this->getSession()->getPage();
		$logout = $page->find( "css", "#wp-admin-bar-logout a" );
		if ( ! empty( $logout ) ) {
			$this->getSession()->visit( $this->locatePath( $logout->getAttribute( "href" ) ) );
		}
	}

	/**
	 * @AfterStep
	 */
	public function after_step( afterStepScope $scope )
	{
		// something to do
	}
}
