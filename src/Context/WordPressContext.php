<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
		Behat\Gherkin\Node\TableNode;
use Behat\Behat\Hook\Scope\AfterStepScope;

/**
 * Features context.
 */
class WordPressContext extends RawWordPressContext
{
	/**
	 * Save env to variable.
	 * Example: Given save env $WP_VERSION as {WP_VERSION}
	 *
	 * @Given /^save env \$(?P<env>[A-Z_]+) as \{(?P<var>[A-Z_]+)\}$/
	 */
	public function save_env_as_var( $env, $var )
	{
		$this->set_variables( $var, getenv( $env ) );
	}

	/**
	 * Check http status code.
	 * Example: the HTTP status should be 400
	 *
	 * @param string $expect The HTTP status code.
	 * @then /^the HTTP status should be (?P<expect>[0-9]+)$/
	 */
	public function the_http_status_should_be( $expect )
	{
		$status = $this->get_http_status();
		$this->assertSame( $status, intval( $expect ), sprintf(
			'The HTTP status is %1$s, but it should be %2$s',
			$status,
			$expect
		) );
	}

	/**
	 * Check HTTP response headers.
	 * Example:
	 * Then the HTTP headers should be:
     *   | header        | value                    |
     *   | Content-Type  | text/html; charset=UTF-8 |
     *   | Connection    | close                    |
     *   | Host          | 127.0.0.1:8080           |
	 *
	 * @then /^the HTTP headers should be:$/
	 * @param TableNode $table The TableNode object.
	 * @throws \Exception
	 */
	public function the_http_headers_should_be( TableNode $table )
	{
		$headers = $this->get_http_headers();

		foreach ( $table->getHash() as $row ) {
			if ( ! empty( $headers[ $row['header'] ] ) ) {
				$value = $headers[ $row['header'] ][0];
				$this->assertSame( $row['value'], $value, sprintf(
					'The value of "%1$s" header is "%1$s", but it should be "%3$s".',
					$row['header'],
					$value,
					$row['value']
				) );
			} else {
				throw new \Exception( sprintf(
					'The value of "%1$s" header is empty, but it should be "%2$s".',
					$row['header'],
					$row['value']
				) );
			}
		}
	}

	/**
	 * Check status of plugins
	 * Example:
	 * Then the plugins should be:
	 * | slug               | status   |
	 * | akismet            | inactive |
	 * | hello-dolly        | inactive |
	 * | wordpress-importer | active   |
	 *
	 * @then /^the plugins should be:$/
	 */
	public function the_plugins_should_be( TableNode $table )
	{
		$plugins = $this->get_plugins();

		foreach ( $table->getHash() as $row ) {
			if ( ! empty( $plugins[ $row['slug'] ] ) ) {
				$status = $plugins[ $row['slug'] ]['status'];
				$this->assertSame( $row['status'], $status, sprintf(
					"The %s plugin is installed, but it is not %s.",
					$row['slug'],
					$row['status']
				) );
			} else {
				throw new \Exception( sprintf(
					"The %s plugin is not installed.",
					$row['slug']
				) );
			}
		}
	}

	/**
	 * Check status of plugins.
	 *
	 * @then /^the "(?P<slug>[^"]*)" plugin should be (?P<expect>(installed|activated))$/
	 */
	public function the_plugin_should_be( $slug, $expect )
	{
		$plugins = $this->get_plugins();

		if ( empty( $plugins[ $slug ] ) ) {
			throw new \Exception( sprintf(
				"The %s plugin is not installed.",
				$slug
			) );
		}

		if ( "activated" === $expect ) {
			$status = $plugins[ $slug ]['status'];
			$this->assertSame( "active", $status, sprintf(
				"The %s plugin is installed, but it is not activated.",
				$slug
			) );
		}
	}

	/**
	 * Check status of plugins.
	 *
	 * @then /^the "(?P<slug>[^"]*)" plugin should not be (?P<expect>(installed|activated))$/
	 */
	public function the_plugin_should_not_be( $slug, $expect )
	{
		$plugins = $this->get_plugins();

		if ( "installed" === $expect && ! empty( $plugins[ $slug ] ) ) {
			throw new \Exception( sprintf(
				"The %s plugin should not be installed, but installed.",
				$slug
			) );
		}

		if ( "activated" === $expect ) {
			$status = $plugins[ $slug ]['status'];
			$this->assertTrue( ( "active" !== $status ), sprintf(
				"The %s plugin should not be activated, but activated.",
				$slug
			) );
		}
	}

	/**
	 * Check the theme is activated.
	 * Example: Given the "twentysixteen" theme should be activated
	 *
	 * @Then /^the "(?P<theme>[^"]*)" theme should be activated$/
	 */
	public function theme_should_be_activated( $theme )
	{
		$theme = $this->replace_variables( $theme );
		$current_theme = $this->get_current_theme();

		$this->assertSame( $theme, $current_theme, sprintf(
			"The current theme is %s, but it should be %s",
			$current_theme,
			$theme
		) );
	}

	/**
	 * Check WordPress version.
	 * Example: Given the WordPress version should be "4.6"
	 *
	 * @Given /^the WordPress version should be "(?P<version>[^"]*)"$/
	 */
	public function wordpress_version_should_be( $version )
	{
		$version = $this->replace_variables( $version );
		$latest = '';

		if ( "latest" === $version || "nightly" === $version ) {
			$api = $this->get_contents( "https://api.wordpress.org/core/version-check/1.7/" );
			$versions = json_decode( $api );
			$latest = $versions->offers[0]->current;
		}

		if ( "latest" === $version ) {
			$version = $latest;
		}

		$the_version = $this->get_wp_version();
		if ( 0 === strpos( $the_version, $version ) ) {
			return true;
		} elseif ( "nightly" === $version && version_compare( $the_version, $latest, ">=" ) ) {
			return true;
		} else {
			throw new \Exception( sprintf(
				"The WordPress version number is %s, but it should be %s",
				$the_version,
				$version
			) );
		}
	}

	/**
	 * Return exception if user is not logged-in.
	 * Example: Then I should be logged in
	 *
	 * @Then I should be logged in
	 */
	public function i_should_be_logged_in()
	{
		if ( ! $this->is_logged_in() ) {
			throw new \Exception( "You are not logged in" );
		}
	}

	/**
	 * Return exception if user is logged-in.
	 * Example: I should not be logged in
	 *
	 * @Then I should not be logged in
	 */
	public function i_should_not_be_logged_in()
	{
		if ( $this->is_logged_in() ) {
			throw new \Exception( "You are logged in" );
		}
	}

	/**
	 * Login with username and password.
	 * When you failed to login, it will throw `Exception`.
	 * Example: Given I login as "admin" width password "admin"
	 *
	 * @param string $username The user name.
	 * @param string $password The password for the $username.
	 * @Given /^I login as "(?P<username>(?:[^"]|\\")*)" with password "(?P<password>(?:[^"]|\\")*)"$/
	 * @throws \Exception
	 */
	public function login_as_user_password( $username, $password )
	{
		$username = $this->replace_variables( $username );
		$password = $this->replace_variables( $password );

		$result = $this->login( $username, $password );

		if ( ! $result ) {
			throw new \Exception( "Login failed." );
		}
	}

	/**
	 * Login with username and password.
	 * Example: Given I login as "admin" width password "admin"
	 *
	 * @param string $username The user name.
	 * @param string $password The password for the $username.
	 * @Given /^I try to login as "(?P<username>(?:[^"]|\\")*)" with password "(?P<password>(?:[^"]|\\")*)"$/
	 */
	public function try_to_login_as_user_password( $username, $password )
	{
		$username = $this->replace_variables( $username );
		$password = $this->replace_variables( $password );

		$this->login( $username, $password );
	}

	/**
	 * Login as the role like "administrator", It should be defined in the `behat.yml`.
	 * When you failed to login, it will throw `Exception`.
	 * Example: Given I login as the "([^"]*)" role
	 *
	 * @param string $role The role that is defined in `behat.yml`.
	 * @Given /^I login as the "(?P<role>[a-zA-Z]*)" role$/
	 * @throws \Exception
	 */
	public function login_as_the_role( $role )
	{
		$role = $this->replace_variables( $role );

		$params = $this->get_params();

		if ( empty( $params['roles'][ $role ] ) ) {
			throw new \InvalidArgumentException( sprintf(
				"Role '%s' is not defined in the `behat.yml`", $role
			) );
		} else {
			$result = $this->login(
				$params['roles'][ $role ]['username'],
				$params['roles'][ $role ]['password']
			);
			if ( ! $result ) {
				throw new \Exception( "Login failed." );
			}
		}
	}

	/**
	 * Login as the role like "administrator", It should be defined in the `behat.yml`.
	 * Example: Given I login as the "([^"]*)" role
	 *
	 * @param string $role The role that is defined in `behat.yml`.
	 * @Given /^I try to login as the "(?P<role>[a-zA-Z]*)" role$/
	 */
	public function try_to_login_as_the_role( $role )
	{
		$role = $this->replace_variables( $role );

		$params = $this->get_params();

		if ( empty( $params['roles'][ $role ] ) ) {
			throw new \InvalidArgumentException( sprintf(
				"Role '%s' is not defined in the `behat.yml`", $role
			) );
		} else {
			$result = $this->login(
				$params['roles'][ $role ]['username'],
				$params['roles'][ $role ]['password']
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
		$selector = $this->replace_variables( $selector );

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
		$second = $this->replace_variables( $second );
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
		$selector = $this->replace_variables( $selector );
		return $this->wait_the_element( $selector );
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
		$width = $this->replace_variables( $width );
		$height = $this->replace_variables( $height );

		$this->getSession()->getDriver()->resizeWindow( $width, $height, 'current' );
	}

	/**
	 * Logout from the WordPress.
	 * Example: When I logout
	 *
	 * @Given I logout
	 * @Given I am not logged in
	 */
	public function i_logout()
	{
		$this->logout();
	}

	/**
	 * Take a screenshot of the current page and save it to the specific path.
	 * Example: Then take a screenshot and save it to "./path/to/image.png"
	 *
	 * @param string $path The path to the screenshot will be saved
	 * @Then /^take a screenshot and save it to "(.*)"/
	 * @throws \Exception
	 */
	public function take_a_screenshot( $path )
	{
		$path = $this->replace_variables( $path );

		$path = str_replace( "~", posix_getpwuid(posix_geteuid())['dir'], $path );
		$image = $this->getSession()->getDriver()->getScreenshot();
		$result = file_put_contents( $path, $image );

		if ( ! $result ) {
			throw new \Exception( 'Cannot take a screenshot.' );
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
