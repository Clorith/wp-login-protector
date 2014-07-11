<?php
/**
 * Plugin Name: Login Protector
 * Plugin URI: http://www.clorith.net
 * Description: Lock down your admin by IP after failed login attempts
 * Version: 1.0
 * Author: Clorith
 * License: GPL2
 *
 * Copyright 2014 Marius Jensen (email : marius@jits.no)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// The amount of failed login attempts that needs exceeding before a user is blocked
$protected_login_fails = 3;

function login_protect_errors( $errors ) {
	global $protected_login_fails;

	// The error codes used to identify an invalid login
	$bad_modes = array(
		'incorrect_password',
		'invalid_username'
	);

	// Return the current error code from the $errors object
	$error = $errors->get_error_code();

	// Check if the current error (if any) exists in our list of bad states
	if ( in_array( $error, $bad_modes ) ) {
		// Check if the user has any registered failed logins already
		$logins = get_transient( 'protected_login_' . $_SERVER['REMOTE_ADDR'] );

		// If there are no failed logins registered, set a default value
		if ( false === $logins ) {
			$logins = 0;
		}
		// Increment the amount of login attempts
		$logins++;

		// Update/set the current set of failed logins for referencing on consecutive hits
		set_transient( 'protected_login_' . $_SERVER['REMOTE_ADDR'], $logins, HOUR_IN_SECONDS );

		// If the login attempts exceed the allowed treshold, set the value to block any more attempts at accessing the admin
		if ( $logins > $protected_login_fails ) {
			set_transient( 'blocked_login_' . $_SERVER['REMOTE_ADDR'], true, WEEK_IN_SECONDS );
		}
	}
	return $errors;
}
add_filter( 'wp_login_errors', 'login_protect_errors' );

function check_blocked_ip() {
	// Check if the page is an admin page, or is the login page
	if ( $GLOBALS['pagenow'] == 'wp-login.php' || is_admin() ) {
		// Check if the "block this user" value is defined
		if ( false !== get_transient( 'blocked_login_' . $_SERVER['REMOTE_ADDR'] ) ) {
			// Send a 404 error ,this should hopefully make intelligent brute force bots stop trying
			header( 'HTTP/1.0 404 Not Found' );

			// Stop processing anythign else if the user is blocked any way
			die();
		}
	}
}
add_action( 'init', 'check_blocked_ip', 1 );
