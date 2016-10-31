<?php

namespace VCCW\Mink\WordPressExtension;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Features context.
 */
class Context extends RawMinkContext
{
	/**
	 * Initializes context.
	 * Every scenario gets it's own context object.
	 *
	 * @param array $parameters context parameters (set them up through behat.yml)
	 */
	public function __construct()
	{
		// Initialize your context here
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
	 * @Given I click the :arg1 element
	 */
	public function click_the_element($selector)
	{
		$page = $this->getSession()->getPage();
		$element = $page->find( 'css', $selector );

		if (empty($element)) {
			throw new Exception( "No html element found for the selector ('$selector')" );
		}

		$element->click();
	}

	/**
	 * @Given /^I wait for ([0-9]+) seconds?$/
	 */
	public function wait_for_second( $sec )
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
			} catch ( Exception $e ) {
				// do nothing
			}

			sleep( 1 );
		}

		throw new Exception( "No html element found for the selector ('$selector')" );
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
	 * @param string $user The user name.
	 * @param string $password The password.
	 * @Given /^I login as "([a-zA-Z0-9_]+)" with password "([a-zA-Z0-9_]+)"$/
	 */
	public function wp_login( $user, $password )
	{
		$this->wp_logout();

		$this->getSession()->visit( $this->locatePath( '/wp-login.php' ) );
		$element = $this->getSession()->getPage();
		$element->fillField( "user_login", $user );
		$element->fillField( "user_pass", $password );
		$submit = $element->findButton( "wp-submit" );
		if ( empty( $submit ) ) {
			throw new \Exception( sprintf(
				"No submit button at %s",
				$this->getSession()->getCurrentUrl()
			));
		}

		$submit->click();
	}

	/**
	 * @param string $path The path to the screenshot will be saved
	 * @Given /^I take a screenshot and save to the "(.*)" file$/
	 */
	public function take_a_screenshot( $path )
	{
		$path = str_replace( "~", posix_getpwuid(posix_geteuid())['dir'], $path );
		$image = $this->getSession()->getDriver()->getScreenshot();
		file_put_contents( $path, $image );
	}

	/**
	 * @Given /^I logout$/
	 */
	public function wp_logout()
	{
		$this->logout_from_wp();
	}

	private function logout_from_wp()
	{
		$page = $this->getSession()->getPage();
		$logout = $page->find( "css", "#wp-admin-bar-logout a" );
		if ( ! empty( $logout ) ) {
			$this->getSession()->visit( $this->locatePath( $logout->getAttribute( "href" ) ) );
		}
	}

	/**
	 * @AfterStep
	 */
	public function take_screenshot_after_fail(afterStepScope $scope)
	{
		if ( 99 === $scope->getTestResult()->getResultCode() ) {
			$driver = $this->getSession()->getDriver();
			if ( ! ( $driver instanceof Selenium2Driver ) ) {
				return;
			}

			$image = $this->getSession()->getDriver()->getScreenshot();
			file_put_contents('/tmp/test.png', $image );
		}
	}
}
