<?php

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	$PIONIERE = 1;

	$PER_TUTTI = 0;
	$PER_PIONIERI = 1;
	$COMMERCIALE = 2;
	$CATTIVI = 1;

	require 'DAO.php';

	$dao = new DAO();

	$territorioInUso = array();

	$associazioniList = $dao->getAllAssociazioni();
	foreach ($associazioniList as $associazione) {
		if (strcmp( $associazione[ 'end' ], "0000-00-00 00:00:00" ) == 0) $territorioInUso[] = $associazione[ 'tid' ] . "-" . $associazione[ 'tTIPO' ];
	}

	$addAssociazione = true;
	$inArray = false;

	if (!empty($_POST)) {
		if (in_array( $_POST[ 't' ], $territorioInUso )) {
			$addAssociazione = false;
			$inArray = true;
		} else {
			if ($dao->newAssociazione( $_POST[ 't' ], $_POST[ 'p' ], $_POST[ 'start' ] . " 00:00:00" )) header( 'Location: platform.php' );
			else $addAssociazione = false;
		}
	}

	$userList = $dao->getProclamatori();
	$territoriList = $dao->getTerritori( 99 );

?>

<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!$addAssociazione) echo '#errore{color:red;font-size: 25px;}'; ?>
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 200px;
			}

			input, select, option {
				width: 100%;
				max-width: 300px;
				padding: 20px;
				font-size: 30px;
			}

			.pioniere {
				background-color: bisque;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td align="left" width="50%">
				<a href="platform.php">
					<button>INDIETRO</button>
				</a>
			</td>
			<td align="right" width="50%">
				<a href="newProclamatore.php">
					<button>PROCLAMATORI</button>
				</a>
			</td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
		<?php
			if (!$addAssociazione && $inArray) echo '<tr><td id="errore" colspan="2" align="center">QUESTO TERRITORIO GIA E\' IN USO</td></tr>';
			else if (!$addAssociazione) echo '<tr><td id="errore" colspan="2" align="center">ERRORE, CONTATTA RUBEN</td></tr>';
		?>
		<form method="post" action="assign.php">
			<tr>
				<td align="center" colspan="2">
					<select name="t" required>
						<option selected value="" disabled>TERRITORIO</option>
						<?php

							foreach ($territoriList as $territorio) {

								$nome = $territorio[ 'numero' ] . " ";

								if ($territorio[ 'tipo' ] == $PER_PIONIERI) $nome .= "(Pionieri)";
								if ($territorio[ 'tipo' ] == $COMMERCIALE) $nome .= "(Commerciale)";
								if ($territorio[ 'cattivi' ] == $CATTIVI) $nome .= "(!!)";

								if (!in_array( $territorio[ "id" ] . "-" . $territorio[ 'tipo' ], $territorioInUso )) echo '<option value="' . $territorio[ "id" ] . '">' . $nome . '</option>';
								else echo '<option value="' . $territorio[ "id" ] . '" disabled>' . $nome . '</option>';
							}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<select name="p" required>
						<option selected value="" disabled>PROCLAMATORE</option>
						<?php

							// I NOMI NON POSSONO ESSERE ABBREVIATI PER NON SBAGLIARE AD
							// ASSEGNARE UN TERRITORIO A DUE PERSONE SIMILI DI NOME
							foreach ($userList as $user) {

								$class = $user[ "tipo" ] == $PIONIERE ? "pioniere" : "";

								echo '<option class="' . $class . '" value="' . $user[ "id" ] . '">' . $dao->formatName( $user[ 'nome' ], $user[ 'cognome' ], true ) . '</option>';
							}

						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="date" name="start" value="<?= date( "Y-m-d" ) ?>" min="2015-01-01"
						   max="<?= date( "Y-m-d" ) ?>">
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<input type="submit" value="ASSEGNA">
				</td>
			</tr>
		</form>
	</table>
	</body>
</html>
