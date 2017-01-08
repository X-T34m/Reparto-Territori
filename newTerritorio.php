<?php

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	$PER_TUTTI = 0;
	$PER_PIONIERI = 1;
	$COMMERCIALE = 2;

	require 'DAO.php';
	$dao = new DAO();

	$addTerritorio = true;
	$inArray = false;

	$territoriList = $dao->getTerritori();

	$exist = array();

	foreach ($territoriList as $territorio) {
		$exist[] = $territorio[ 'numero' ] . "-" . $territorio[ 'tipo' ];
	}

	if (!empty($_POST)) {
		if ($_POST[ 't' ] == $COMMERCIALE) {
			if (in_array( $_POST[ 'n' ] . "-" . $_POST[ 't' ], $exist )) {
				$addTerritorio = false;
				$inArray = true;
			} else {
				if ($dao->newTerritorio( $_POST[ 'n' ], $_POST[ 't' ] )) header( 'Location: territori.php' );
				else $addTerritorio = false;
			}
		} else {
			if (in_array( $_POST[ 'n' ] . "-" . $PER_TUTTI, $exist ) || in_array( $_POST[ 'n' ] . "-" . $PER_PIONIERI, $exist )) {
				$addTerritorio = false;
				$inArray = true;
			} else {
				if ($dao->newTerritorio( $_POST[ 'n' ], $_POST[ 't' ] )) header( 'Location: territori.php' );
				else $addTerritorio = false;
			}
		}
	}

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!$addTerritorio) echo '#errore{color:red;font-size: 25px;}'; ?>
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
				<a href="territori.php">
					<button>ANNULLA</button>
				</a>
			</td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
		<?php
			if (!$addTerritorio && $inArray) echo '<tr><td id="errore" colspan="2" align="center">QUESTO TERRITORIO GIA ESISTE</td></tr>';
			else if (!$addTerritorio) echo '<tr><td id="errore" colspan="2" align="center">ERRORE, CONTATTA RUBEN</td></tr>';
		?>
		<form method="post" action="newTerritorio.php">
			<tr>
				<td align="center" colspan="2">
					<input type="number" name="n" placeholder="NUMERO" required>
				</td>
			</tr>
			<tr>
				<td align="center" colspan="2">
					<select name="t">
						<option value="<?= $PER_TUTTI ?>">PER TUTTI</option>
						<option value="<?= $PER_PIONIERI ?>">PER PIONIERI</option>
						<option value="<?= $COMMERCIALE ?>">COMMERCIALE</option>
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

