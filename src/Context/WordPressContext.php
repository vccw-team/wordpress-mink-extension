<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

// use Behat\Gherkin\Node\PyStringNode,
//     Behat\Gherkin\Node\TableNode;

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
	 * @param string $user The user name.
	 * @param string $password The password.
	 * @Given /^I login as "([a-zA-Z0-9_]+)" with password "([a-zA-Z0-9_]+)"$/
	 */
	public function login_as_user_password( $user, $password )
	{
		$this->_login( $user, $password );
	}

	/**
	 * @When /^I login as the "([^"]*)" role$/
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
	 * @When /^I hover over the "([^"]*)" element$/
	 */
	public function hover_over_the_element( $locator )
	{
		$session = $this->getSession();
		$element = $session->getPage()->find( 'css', $locator );

		if ( null === $element ) {
			throw new \InvalidArgumentException( sprintf(
				'Could not evaluate CSS selector: "%s"', $locator
			) );
		}

		$element->mouseOver();
	}

	/**
	 * @Given /^I wait for ([0-9]+) seconds$/
	 * @Given /^I wait for a second$/
	 */
	public function wait_for_second( $sec = 1 )
	{
		$this->getSession()->wait( $sec * 1000 );
	}

	/**
	 * @Given /^I wait the "(.*)" element be loaded$/
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
	 * @param int $width The screen width.
	 * @param int $height The screen height.
	 * @Given /^the screen size is ([0-9]+)x([0-9]+)/
	 */
	public function set_window_size( $width, $height )
	{
		$this->getSession()->getDriver()->resizeWindow( $width, $height, 'current' );
	}

	/**
	 * @Given /^I logout$/
	 */
	public function logout()
	{
		$this->_logout();
	}

	/**
	 * @param string $path The path to the screenshot will be saved
	 * @Given /^take a screenshot and save it to "(.*)"/
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
	}

	/**
	 * Log out from WordPress
	 *
	 * @param none
	 */
	private function _logout()
	{
		$page = $this->getSession()->getPage();
		$logout = $page->find( "css", "#wp-admin-bar-logout a" );
		if ( ! empty( $logout ) ) {
			$this->getSession()->visit( $this->locatePath( $logout->getAttribute( "href" ) ) );
		}
	}
}
