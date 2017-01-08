<?php

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	$NORMALE = 0;
	$PIONIERE = 1;

	require 'DAO.php';
	$dao = new DAO();

	$addProclamatore = true;

	if (!empty($_POST)) {
		if ($dao->newProclamatore( trim( $_POST[ 'n' ] ), trim( $_POST[ 'c' ] ), trim( $_POST[ 't' ] ) )) header( 'Location: assign.php' );
		else $addProclamatore = false;
	}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!$addProclamatore) echo '#errore{color:red;font-size: 25px;}'; ?>
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 300px;
			}

			input, select, option {
				width: 100%;
				max-width: 300px;
				padding: 20px;
				font-size: 30px;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td>
				<a href="assign.php">
					<button>ANNULLA</button>
				</a>
			</td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
		<?php
			if (!$addProclamatore) echo '<tr><td id="errore" colspan="2" align="center">ERRORE, CONTATTA RUBEN</td></tr>';
		?>
		<form method="post" action="newProclamatore.php">
			<tr>
				<td align="center" colspan="2">
					<input type="text" name="n" placeholder="Nome" required>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="text" name="c" placeholder="Cognome" required>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<select name="t">
						<option value="<?= $NORMALE ?>">NORMALE</option>
						<option value="<?= $PIONIERE ?>">PIONIERE</option>
					</select>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="AGGIUNGI">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
