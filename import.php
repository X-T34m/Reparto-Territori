<?php

	ini_set( 'display_errors', 1 );
	ini_set( 'display_startup_errors', 1 );
	error_reporting( -1 );

	require 'DAO.php';
	require './PHPExcel/PHPExcel/IOFactory.php';

	class Import {

		function execute ($import) {

			return;
			if (strcmp( $import, "user" ) == 0) $this->importUser();
			if (strcmp( $import, "storico" ) == 0) $this->importStorico();
			if (strcmp( $import, "territori" ) == 0) $this->importTerritori();

		}

		protected function importUser () {

			$dao = new Import_DAO();

			$handle = fopen( "proclamatori.txt", "r" );
			if ($handle) {
				while (($line = fgets( $handle )) !== false) {
					echo '<br>' . $line . " -> " . $dao->createUser( $line );
				}

				fclose( $handle );
			} else {

			}
		}

		protected function importStorico () {

			$dao = new Import_DAO();

			$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
			$objReader->setReadDataOnly( true );
			$objPHPExcel = $objReader->load( "storico.xlsx" );

			$objWorksheet = $objPHPExcel->getActiveSheet();

			$userID = array();
			$res = $dao->getAllUserId();
			foreach ($res as $user) {
				$userID[ trim( $user[ 'cognome' ] . " " . $user[ 'nome' ] ) ] = $user[ 'id' ];
			}

			$territoriID = array();
			$RES = $dao->getAllTerritoriId();
			foreach ($RES as $terr) {
				$territoriID[ $terr[ 'numero' ] . "-" . $terr[ 'tipo' ] ] = $terr[ 'id' ];
			}

			//print_r( $userID );
			//print_r( $territoriID );

			echo '<table>';
			$R = 1;
			foreach ($objWorksheet->getRowIterator() as $row) {
				echo '<tr style="border-bottom: 1px solid black;">';
				$cellIterator = $row->getCellIterator();
				$cellIterator->setIterateOnlyExistingCells( false );

				$numero = 0;
				$nome = "";
				$uscito = "";
				$rientrato = "";

				echo '<td>';
				$i = 0;
				foreach ($cellIterator as $cell) {
					//echo '<td>' . $cell->getValue() . '</td>';
					if ($i == 0) $numero = trim( $cell->getValue() );
					elseif ($i == 1) $nome = trim( $cell->getValue() );
					elseif ($i == 2) {
						$uscito = trim( $cell->getValue() );
						if (!empty($uscito)) {
							$uscito = date( $format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP( $uscito ) );
							$uscito .= " 00:00:00";
						}
					} elseif ($i == 3) {
						$rientrato = trim( $cell->getValue() );
						if (empty($uscito)) {

							//echo '<span style="color:red;">Riga ' . $R . ': manca data di uscita, la imposto manualmente.</span>';

							$uscito = date( $format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP( $rientrato ) );
							$date = new DateTime( $uscito );
							$date->sub( new DateInterval( 'P3M' ) );
							$uscito = date( $format = "Y-m-d", $date->getTimestamp() );
							$uscito .= " 00:00:00";
							//echo '<p>Rientrato: ' . $rientrato . ' -- Uscito (-90 giorni): ' . $uscito . '</p>';
						}
						if (!empty($rientrato)) {
							$rientrato = date( $format = "Y-m-d", PHPExcel_Shared_Date::ExcelToPHP( $rientrato ) );
							$rientrato .= " 00:00:00";
						}
					}
					$i++;
				}

				$stampa = array_key_exists( trim( $nome ), $userID ) ? "trovato il proclamatore" : "<span style='color:limegreen;'>non ho trovato il proclamatore</span>";
				echo '<p>Riga ' . $R . ': ' . $stampa . '. ' . $nome . '</p>';

				if (empty($rientrato)) $rientrato = "0000:00:00 00:00:00";
				echo '<p>Rientrato: ' . $rientrato . ' // Uscito: ' . $uscito . '</p>';

				if (array_key_exists( trim( $nome ), $userID )) {

					$idP = $userID[ trim( $nome ) ];
					$veroNumero = preg_replace( "/[^0-9]/", "", $numero );
					$tTIPO = 0;

					$PER_TUTTI = 0;
					$PER_PIONIERI = 1;
					$COMMERCIALE = 2;

					if (strpos( $numero, 'P' ) !== false) {
						//echo '<p>' . $veroNumero . ' PIONIERE</p>';
						$tTIPO = $PER_PIONIERI;
					} elseif (strpos( $numero, 'Comm.' ) !== false) {
						//echo '<p>' . $veroNumero . ' COMM</p>';
						$tTIPO = $COMMERCIALE;
					} else {
						//echo '<p>' . $veroNumero . ' NORMALE</p>';
						$tTIPO = $PER_TUTTI;
					}

					$idT = $territoriID[ trim( $veroNumero . "-" . $tTIPO ) ];

					echo '<p>N:' . $veroNumero . ';T:' . $tTIPO . ';UID:' . $idP . ';TID:' . $idT . ';</p>';
					if ($veroNumero < 140) {
						$res = $dao->newAssociazione( $idT, $idP, $uscito, $rientrato );
						echo '<p><b>IMPORT: ' . $res . '</b></p>';
					}

				}

				echo '<hr></td></tr>';
				$R++;
			}
			echo '</table>';

		}

		protected
		function importTerritori () {

			$dao = new Import_DAO();

			$objReader = PHPExcel_IOFactory::createReader( 'Excel2007' );
			$objReader->setReadDataOnly( true );
			$objPHPExcel = $objReader->load( "territori.xlsx" );

			$objWorksheet = $objPHPExcel->getActiveSheet();

			echo '<table>';
			$i = 0;
			foreach ($objWorksheet->getRowIterator() as $row) {
				if ($i > 2) {
					echo '<tr>';
					$cellIterator = $row->getCellIterator();
					$cellIterator->setIterateOnlyExistingCells( false ); //Controlla celle vuote

					$numero = 0;
					$TIPO = "";
					$type = 0;
					foreach ($cellIterator as $cell) {
						if ($type == 0) $numero = $cell->getValue();
						else $TIPO = $cell->getValue();
						//echo '<td>' . $cell->getValue() . '</td>';
						$type++;
					}

					if (!empty($numero) && !empty($TIPO)) echo '<td>' . $dao->createTerritorio( $numero, $TIPO ) . '</td>';

					echo '</tr>';
				}
				$i++;
			}
			echo '</table>';

		}
	}

	$imp = new Import();
	$imp->execute( $_GET[ 'type' ] );