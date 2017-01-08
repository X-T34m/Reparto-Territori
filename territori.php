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

	$ordine = -1;

	if (!empty($_POST)) {
		if (isset($_POST[ 'ordine' ])) $ordine = $_POST[ 'ordine' ];
		if (isset($_POST[ 'action' ])) {
			$azione = $_POST[ 'action' ];
			if (strcmp( $azione, "rimuovi" ) == 0) $dao->removeTerritorio( $_POST[ 'id' ] );
			elseif (strcmp( $azione, "cattivi" ) == 0) $dao->setCattivoTerritorio( $_POST[ 'id' ], 1 );
			elseif (strcmp( $azione, "no_cattivi" ) == 0) $dao->setCattivoTerritorio( $_POST[ 'id' ], 0 );
		}
	}

	$territoriList = $dao->getTerritori( $ordine );
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
				font-size: 20px;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 300px;
			}

			form {
				display: table-cell;
			}

			select, option {
				width: 100%;
				max-width: 300px;
				padding: 20px;
				font-size: 30px;
			}

			.red {
				background-color: rgba(244, 67, 54, 0.54) !important;
			}

			#item1 {
				background-color: aliceblue;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td width="50%" align="left" colspan="2">
				<a href="platform.php">
					<button>INDIETRO</button>
				</a>
			</td>
			<td width="50%" align="right" colspan="2">
				<a href="newTerritorio.php">
					<button>CREA</button>
				</a>
			</td>
		</tr>
		<tr>
			<td colspan="4" align="right">
				<form id="formFiltro" method="post" action="territori.php">
					<select name="ordine" onchange="document.getElementById('formFiltro').submit();">
						<?php

							$d = $ordine == -1 ? "selected" : "";
							$t = $ordine == $PER_TUTTI ? "selected" : "";
							$p = $ordine == $PER_PIONIERI ? "selected" : "";
							$c = $ordine == $COMMERCIALE ? "selected" : "";

						?>
						<option <?= $d ?> value="-1">TUTTI</option>
						<option <?= $t ?> value="<?= $PER_TUTTI ?>">PER TUTTI</option>
						<option <?= $p ?> value="<?= $PER_PIONIERI ?>">PER PIONIERI</option>
						<option <?= $c ?> value="<?= $COMMERCIALE ?>">COMMERCIALE</option>
					</select>
				</form>
			</td>
		</tr>
		<tr>
			<td><br></td>
		</tr>
		<?php

			if (!empty($territoriList)) echo '<th>NUMERO</th><th>TIPO</th><tr><td colspan="4"><hr></td></tr>';
			else echo '<tr><td colspan="4" align="center">NON CI SONO TERRITORI</td></tr>';

			$i = 0;
			foreach ($territoriList as $territorio) {
				$class = $territorio[ "cattivi" ] == 1 ? "red" : "";

				/**
				 * NUMERO TERRITORIO
				 */
				echo '<tr id="item' . $i . '" class="' . $class . '""><td align="center">';
				echo $territorio[ 'numero' ];

				/**
				 * TIPO TERRITORIO
				 */
				echo '</td><td align="center">';
				switch ($territorio[ 'tipo' ]) {
					case $PER_TUTTI:
						echo 'PER TUTTI';
						break;
					case $PER_PIONIERI:
						echo 'PER PIONIERI';
						break;
					case $COMMERCIALE:
						echo 'COMMERCIALE';
						break;
				}

				/**
				 * CANCELLA
				 */
				echo '</td><td align="center"><form action="territori.php" method="post" onsubmit="return confirm(\'Sei sicuro di voler cancellare il territorio?\\nQuesta operazione &egrave; irreversibile.\');">';
				echo '<input type="hidden" name="ordine" value="' . $ordine . '">';
				echo '<input type="hidden" name="action" value="rimuovi"><input type="hidden" name="id" value="' . $territorio[ "id" ] . '">';
				echo '<input type = "submit" value = "RIMUOVI" ></form></td>';

				/**
				 * IMPOSTA GENTE CATTIVA
				 */
				echo '</td><td align="center"><form action="territori.php" method="post">';
				echo '<input type="hidden" name="ordine" value="' . $ordine . '">';
				$azione = $territorio[ "cattivi" ] == 1 ? "no_cattivi" : "cattivi";
				echo '<input type="hidden" name="action" value="' . $azione . '"><input type="hidden" name="id" value="' . $territorio[ "id" ] . '">';
				$cattivi = $territorio[ "cattivi" ] == 1 ? "NON HA LISTA NOMI" : "HA LISTA NOMI";
				echo '<input type = "submit" value = "' . $cattivi . '" ></form></td>';
				echo '</tr > ';
				$i = $i == 0 ? 1 : 0;
			}

		?>
	</body>
</html>