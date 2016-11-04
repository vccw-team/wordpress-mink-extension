<?php

namespace VCCW\Behat\Mink\WordPressExtension\Context;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Features context.
 */
class RawWordPressContext extends RawMinkContext
{
	protected $timeout = 30;

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
}
