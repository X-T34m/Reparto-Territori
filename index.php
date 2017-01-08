<?php

	session_start();

	if (isset($_SESSION[ 'login' ])) header( 'Location: platform.php' );

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			<?php if (isset($_GET[ 'error' ])){

				echo 'td:first-child{color:red;font-size:30px;}';

			} ?>

			input {
				width: 100%;
				max-width: 200px;
				padding: 20px;
				font-size: 30px;
			}
		</style
	</head>
	<body>
	<table width="100%">
		<?php if (isset($_GET[ 'error' ])) echo '<tr><td align="center">DATI ERRATI</td></tr>'; ?>
		<form method="post" action="login.php">
			<tr>
				<td align="center">
					<input type="text" name="u" placeholder="UTENTE" required>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="password" name="p" placeholder="PASSWORD" required>
				</td>
			</tr>
			<tr>
				<td align="center">
					<input type="submit" value="ACCEDI">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
