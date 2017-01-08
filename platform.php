<?php

	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	error_reporting( -1 );

	session_start();
	if (!isset($_SESSION[ 'login' ])) {
		header( 'Location: index.php' );
	}

	$PIONIERE = 1;

	$PER_TUTTI = 0;
	$PER_PIONIERI = 1;
	$COMMERCIALE = 2;
	$CATTIVI = 1;

	$MESE_ALL = "all";
	$MESE_GENNAIO = "01-31";
	$MESE_FEBBRAIO = "02-29";
	$MESE_MARZO = "03-31";
	$MESE_APRILE = "04-30";
	$MESE_MAGGIO = "05-31";
	$MESE_GIUGNO = "06-30";
	$MESE_LUGLIO = "07-31";
	$MESE_AGOSTO = "08-31";
	$MESE_SETTEMBRE = "09-30";
	$MESE_OTTOBRE = "10-31";
	$MESE_NOVEMBRE = "11-30";
	$MESE_DICEMBRE = "12-31";

	$MESE = $MESE_ALL;

	$ANNO = "all";

	require 'DAO.php';

	$dao = new DAO();

	$userList = $dao->getProclamatori();

	$ORDINE_NUMERO = 0;
	$ORDINE_NOME = 1;
	$ORDINE_USCITO = 2;
	$ORDINE_RIENTRATO = 3;
	$ordine_crescente = "ASC";
	$ordine_decrescente = "DESC";

	$ORDINE = $ORDINE_USCITO;
	$ordine = $ordine_decrescente;

	$page = 1;

	$UID = "all";

	if (!empty($_POST)) {
		if (isset($_POST[ 'action' ])) {
			if (strcmp( $_POST[ 'action' ], "setEnd" ) == 0) $dao->setEndAssociazione( $_POST[ 'aid' ] );
			else if (strcmp( $_POST[ 'action' ], "delete" ) == 0) $dao->deleteAssociazione( $_POST[ 'aid' ] );
		}
		if (isset($_POST[ 'ORDINE' ])) $ORDINE = $_POST[ 'ORDINE' ];
		if (isset($_POST[ 'ordine' ])) $ordine = $_POST[ 'ordine' ];
		if (isset($_POST[ 'mese' ])) $MESE = $_POST[ 'mese' ];
		if (isset($_POST[ 'page' ])) $page = $_POST[ 'page' ];
		if (isset($_POST[ 'anno' ])) $ANNO = $_POST[ 'anno' ];
		if (isset($_POST[ 'nome' ])) $UID = $_POST[ 'nome' ];
	}

	$list = $dao->getAssociazioni( $ORDINE, $ordine, $MESE, $ANNO, $UID, ($page * 50) - 50 );
	$listWithoutLimit = $dao->getAssociazioni( $ORDINE, $ordine, $MESE, $ANNO, $UID, 0, 5000 );
	$nRecords = count( $listWithoutLimit );
	$npage = round( $nRecords / 50, 0, PHP_ROUND_HALF_DOWN );
	if ($npage == 0) $npage++;

?>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta name=viewport content="width=device-width, initial-scale=1">
		<link href='https://fonts.googleapis.com/css?family=Roboto+Condensed' rel='stylesheet' type='text/css'>
		<style type="text/css">
			<?php if (!empty($list)) echo '#emptyList{display:none;}'; ?>
			* {
				font-family: 'Roboto Condensed', sans-serif;
				line-height: 200%;
				font-weight: 400;
			}

			form {
				display: table-cell;
			}

			.danger {
				height: 20px;
				width: 20px;
				display: inline-block;
				background: url("danger.png") no-repeat;
			}

			button {
				padding: 10px;
				font-size: 20px;
				width: 100%;
				max-width: 200px;
			}

			select, option {
				width: 100%;
				max-width: 300px;
				padding: 10px;
				font-size: 20px;
			}

			#emptyList {
				font-size: 28px;
			}

			.item1 {
				background-color: aliceblue;
			}

			.red {
				background-color: rgba(244, 67, 54, 0.54) !important;
			}

			.yellow {
				background-color: rgba(244, 255, 0, 0.41) !important;
			}
		</style>
	</head>
	<body>
	<table width="100%">
		<tr>
			<td width="33%" align="left">
				<a href="logout.php">
					<button>ESCI</button>
				</a>
			</td>
			<td width="33%" align="center">
				<a href="assign.php">
					<button>ASSEGNA</button>
				</a>
			</td>
			<td width="33%" align="right">
				<a href="territori.php">
					<button>TERRITORI</button>
				</a>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td width="33%">
				<form id="formFiltroORDINE" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="nome" value="<?= $UID ?>">
					<label for="ORDINE">Ordina per</label>
					<select name="ORDINE" onchange="document.getElementById('formFiltroORDINE').submit();">
						<?php

							$nu = $ORDINE == $ORDINE_NUMERO ? "selected" : "";
							$no = $ORDINE == $ORDINE_NOME ? "selected" : "";
							$u = $ORDINE == $ORDINE_USCITO ? "selected" : "";
							$r = $ORDINE == $ORDINE_RIENTRATO ? "selected" : "";

						?>
						<option <?= $nu ?> value="<?= $ORDINE_NUMERO ?>">NUMERO</option>
						<option <?= $no ?> value="<?= $ORDINE_NOME ?>">NOME</option>
						<option <?= $u ?> value="<?= $ORDINE_USCITO ?>">DATA USCITA</option>
						<option <?= $r ?> value="<?= $ORDINE_RIENTRATO ?>">DATA RICONSEGNA</option>
					</select>
				</form>
			</td>
			<td width="33%" align="center">
				<form id="formFiltroordine" method="post" action="platform.php">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="nome" value="<?= $UID ?>">
					<label for="ordine">Ordina in modo</label>
					<select name="ordine" onchange="document.getElementById('formFiltroordine').submit();">
						<?php

							$c = $ordine == $ordine_crescente ? "selected" : "";
							$d = $ordine == $ordine_decrescente ? "selected" : "";

						?>
						<option <?= $c ?> value="<?= $ordine_crescente ?>">CRESCENTE</option>
						<option <?= $d ?> value="<?= $ordine_decrescente ?>">DECRESCENTE</option>
					</select>
				</form>
			</td>
			<td width="33%" align="right">
				<form id="formFiltroMese" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<input type="hidden" name="nome" value="<?= $UID ?>">
					<label for="mese">Mesi</label>
					<select name="mese" onchange="document.getElementById('formFiltroMese').submit();">
						<?php

							$tutti = $MESE == $MESE_ALL ? "selected" : "";
							$m_1 = $MESE == $MESE_GENNAIO ? "selected" : "";
							$m_2 = $MESE == $MESE_FEBBRAIO ? "selected" : "";
							$m_3 = $MESE == $MESE_MARZO ? "selected" : "";
							$m_4 = $MESE == $MESE_APRILE ? "selected" : "";
							$m_5 = $MESE == $MESE_MAGGIO ? "selected" : "";
							$m_6 = $MESE == $MESE_GIUGNO ? "selected" : "";
							$m_7 = $MESE == $MESE_LUGLIO ? "selected" : "";
							$m_8 = $MESE == $MESE_AGOSTO ? "selected" : "";
							$m_9 = $MESE == $MESE_SETTEMBRE ? "selected" : "";
							$m_10 = $MESE == $MESE_OTTOBRE ? "selected" : "";
							$m_11 = $MESE == $MESE_NOVEMBRE ? "selected" : "";
							$m_12 = $MESE == $MESE_DICEMBRE ? "selected" : "";

						?>
						<option <?= $tutti ?> value="<?= $MESE_ALL ?>">TUTTI</option>
						<option <?= $m_1 ?> value="<?= $MESE_GENNAIO ?>">GENNAIO</option>
						<option <?= $m_2 ?> value="<?= $MESE_FEBBRAIO ?>">FEBBRAIO</option>
						<option <?= $m_3 ?> value="<?= $MESE_MARZO ?>">MARZO</option>
						<option <?= $m_4 ?> value="<?= $MESE_APRILE ?>">APRILE</option>
						<option <?= $m_5 ?> value="<?= $MESE_MAGGIO ?>">MAGGIO</option>
						<option <?= $m_6 ?> value="<?= $MESE_GIUGNO ?>">GIUGNO</option>
						<option <?= $m_7 ?> value="<?= $MESE_LUGLIO ?>">LUGLIO</option>
						<option <?= $m_8 ?> value="<?= $MESE_AGOSTO ?>">AGOSTO</option>
						<option <?= $m_9 ?> value="<?= $MESE_SETTEMBRE ?>">SETTEMBRE</option>
						<option <?= $m_10 ?> value="<?= $MESE_OTTOBRE ?>">OTTOBRE</option>
						<option <?= $m_11 ?> value="<?= $MESE_NOVEMBRE ?>">NOVEMBRE</option>
						<option <?= $m_12 ?> value="<?= $MESE_DICEMBRE ?>">DICEMBRE</option>
					</select>
				</form>
			</td>
		</tr>
		<tr>
			<td width="33%" align="left">
				<form id="formFiltroNome" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="anno" value="<?= $ANNO ?>">
					<label for="nome">Proclamatori</label>
					<select name="nome" onchange="document.getElementById('formFiltroNome').submit();">
						<?php

							$a = $UID == "all" ? "selected" : "";

						?>
						<option <?= $a ?> value="all">TUTTI</option>
						<?php

							foreach ($userList as $user) {

								$selected = $UID == $user[ "id" ] ? "selected" : "";

								echo '<option ' . $selected . ' value="' . $user[ "id" ] . '">' . $dao->formatName( $user[ 'nome' ], $user[ 'cognome' ], true ) . '</option>';
							}

						?>
					</select>
				</form>
			</td>
			<td></td>
			<td width="33%" align="right">
				<form id="formFiltroAnno" method="post" action="platform.php">
					<input type="hidden" name="ordine" value="<?= $ordine ?>">
					<input type="hidden" name="ORDINE" value="<?= $ORDINE ?>">
					<input type="hidden" name="mese" value="<?= $MESE ?>">
					<input type="hidden" name="nome" value="<?= $UID ?>">
					<label for="anno">Anno</label>
					<select name="anno" onchange="document.getElementById('formFiltroAnno').submit();">
						<?php

							$a = strcmp( $ANNO, "all" ) == 0 ? "selected" : "";
							$a14 = strcmp( $ANNO, "2014" ) == 0 ? "selected" : "";
							$a15 = strcmp( $ANNO, "2015" ) == 0 ? "selected" : "";
							$a16 = strcmp( $ANNO, "2016" ) == 0 ? "selected" : "";
							$a17 = strcmp( $ANNO, "2017" ) == 0 ? "selected" : "";

						?>
						<option <?= $a ?> value="all">TUTTI</option>
						<option <?= $a14 ?> value="2014">2014</option>
						<option <?= $a15 ?> value="2015">2015</option>
						<option <?= $a16 ?> value="2016">2016</option>
						<option <?= $a17 ?> value="2017">2017</option>
					</select>
				</form>
			</td>
		</tr>
	</table>
	<table width="100%">
		<tr>
			<td id="emptyList" align="center">Nessun territorio assegnato</td>
		</tr>
		<?php

			if (!empty($list)) echo '<tr><td><br></td></tr><th>TERRITORIO</th><th>PROCLAMATORE</th><th>USCITO</th><th>RIENTRATO</th><tr><td colspan="5"><hr></td></tr>';

			$i = 0;
			foreach ($list as $assegnazione) {
				$class = "item" . $i;

				if (strcmp( $assegnazione[ 'end' ], "0000-00-00 00:00:00" ) == 0) {
					$date1 = new DateTime( $assegnazione[ 'start' ] );
					$date2 = new DateTime( date( "Y-m-d" ) );

					$date1->add( new DateInterval( 'P90D' ) );

					$interval = date_diff( $date1, $date2 );
					//echo '<br>interval:' . $interval->format( '%R%a days' );

					if (intval( $interval->format( '%R%a' ) ) >= 0) $class .= " red";
					else {
						if ($assegnazione[ 'tTIPO' ] == $PER_PIONIERI && $assegnazione[ 'pTIPO' ] != $PIONIERE) $class .= " yellow";
					}
				}

				/**
				 * NUMERO TERRITORIO
				 */
				echo '<tr class="' . $class . '"><td align="center">';

				if ($assegnazione[ 'numero' ] == null) echo "RIMOSSO";
				else {

					$nome = $assegnazione[ 'numero' ] . " ";

					if ($assegnazione[ 'tTIPO' ] == $PER_PIONIERI) $nome .= "<span style='font-weight: 900'>(Pioniere)</span>";
					if ($assegnazione[ 'tTIPO' ] == $COMMERCIALE) $nome .= "<span style='font-weight: 900'>(Commerciale)</span>";
					if ($assegnazione[ 'cattivi' ] == $CATTIVI) $nome .= "<span class='danger'></span>";

					echo '<p>' . $nome . '</p>';
				}

				/**
				 * NOME PROCLAMATORE
				 */
				echo '</td><td align="center">';
				echo $assegnazione[ 'nome' ] == null ? "RIMOSSO" : $dao->formatName( $assegnazione[ 'nome' ], $assegnazione[ 'cognome' ], true );

				/**
				 * DATA ASSEGNAZIONE
				 */
				echo '</td><td align="center">';

				$start = explode( "-", explode( " ", $assegnazione[ 'start' ] )[ 0 ] );

				echo $start[ 2 ] . "." . $start[ 1 ] . "." . $start[ 0 ];

				/**
				 * DATA RIENTRO
				 */
				echo '</td><td align="center">';

				$end = explode( "-", explode( " ", $assegnazione[ 'end' ] )[ 0 ] );
				$setEnd = "<form method='post' action='platform.php' onsubmit='return confirm(\"Sei sicuro di voler impostare la data di rientro?\\nQuesta operazione &egrave; irreversibile.\");'>";
				$setEnd .= '<input type = "hidden" name = "ordine" value = "' . $ordine . '" >';
				$setEnd .= '<input type = "hidden" name = "ORDINE" value = "' . $ORDINE . '" >';
				$setEnd .= '<input type = "hidden" name = "mese" value = "' . $MESE . '" >';
				$setEnd .= '<input type = "hidden" name = "anno" value = "' . $ANNO . '" >';
				$setEnd .= '<input type = "hidden" name = "page" value = "' . $page . '" >';
				$setEnd .= '<input type = "hidden" name = "nome" value = "' . $UID . '" >';
				$setEnd .= "<input type='hidden' name='action' value='setEnd'>";
				$setEnd .= "<input type='hidden' name='aid' value='" . $assegnazione[ 'id' ] . "'>";
				$setEnd .= "<input type='submit' value='RIENTRATO' /></form>";

				echo strcmp( $assegnazione[ 'end' ], "0000-00-00 00:00:00" ) == 0 ? $setEnd : $end[ 2 ] . "." . $end[ 1 ] . "." . $end[ 0 ];

				/**
				 * RIMUOVI
				 */
				echo '</td><td align="center">';
				$delete = "<form method='post' action='platform.php' onsubmit='return confirm(\"Sei sicuro di voler cancellare questa assegnazione?\\nQuesta operazione &egrave; irreversibile.\");'>";
				$delete .= '<input type = "hidden" name = "ordine" value = "' . $ordine . '" >';
				$delete .= '<input type = "hidden" name = "ORDINE" value = "' . $ORDINE . '" >';
				$delete .= '<input type = "hidden" name = "mese" value = "' . $MESE . '" >';
				$delete .= '<input type = "hidden" name = "anno" value = "' . $ANNO . '" >';
				$delete .= '<input type = "hidden" name = "page" value = "' . $page . '" >';
				$delete .= '<input type = "hidden" name = "nome" value = "' . $UID . '" >';
				$delete .= "<input type='hidden' name='action' value='delete'>";
				$delete .= "<input type='hidden' name='aid' value='" . $assegnazione[ 'id' ] . "'>";
				$delete .= "<input type='submit' value='ELIMINA' /></form>";
				echo $delete;
				echo '</td></tr>';
				$i = $i == 0 ? 1 : 0;
			}

		?>
	</table>
	<br>
	<table width="100%">
		<tr align="center">
			<td>
				Pagina:
				<?php

					for ($i = 1; $i <= $npage; $i++) {

						$font = $page == $i ? "23px" : "15px";
						$disabled = $page == $i ? " onsubmit='return false;'" : "";

						echo '<form action="platform.php" method="post" ' . $disabled . '>';
						echo '<input type="hidden" name="page" value="' . $i . '">';
						echo '<input type="hidden" name="ORDINE" value="' . $ORDINE . '">';
						echo '<input type="hidden" name="ordine" value="' . $ordine . '">';
						echo '<input type="hidden" name="mese" value="' . $MESE . '">';
						echo '<input type="hidden" name="anno" value="' . $ANNO . '">';
						echo '<input type="hidden" name="nome" value="' . $UID . '">';
						echo '<input type="submit" value="' . $i . '" style="font-size:' . $font . '">';
						echo '</form>';

					}

				?>
			</td>
		</tr>
	</table>
	</body>
</html>
