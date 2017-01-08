<?php

	session_start();

	$user = strtolower( $_POST[ 'u' ] );
	$pass = strtolower( $_POST[ 'p' ] );

	if (isset($user) && isset($pass)) {
		if (checkLogin( $user, $pass )) {
			$_SESSION[ 'login' ] = "ok";
			header( 'Location: platform.php' );

			return;
		}
	}

	function checkLogin ($u, $p) {
		if (strcmp( $u, "pippo" ) == 0 && strcmp( $p, "pluto" ) == 0) return true;
		if (strcmp( $u, "r.morigi" ) == 0 && strcmp( $p, "fatina23" ) == 0) return true;
		if (strcmp( $u, "a.romani" ) == 0 && strcmp( $p, "kayak2016" ) == 0) return true;
		if (strcmp( $u, "m.sabia" ) == 0 && strcmp( $p, "michelino123" ) == 0) return true;

		return false;
	}

	header( 'Location: index.php?error=login' );