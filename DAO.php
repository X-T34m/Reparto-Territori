<?php

	include_once 'dbCredentials.php';

	class DAO {

		private static $CONN = null;

		//protected $PROCLAMATORE = 0;
		protected $PIONIERE = 1;

		protected $PER_TUTTI = 0;
		protected $PER_PIONIERI = 1;
		protected $COMMERCIALE = 2;

		public function __construct() {

			$info = "mysql:host=" . constant("HOST") . ";dbname=" . constant("DB") . ";charset=utf8";

			try {
				$opts = array(
					PDO::ATTR_PERSISTENT         => true,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);

				self::$CONN = new PDO($info, constant("USER"), constant("PASS"), $opts);
			} catch(PDOException $e) {
				die("PDOException: $e");
			}

			$error = self::$CONN->errorInfo();
			if(!is_null($error[2])) {
				die($error[2]);
			}
		}

		public function getAllAssociazioni() {
			$query = self::$CONN->prepare("SELECT a.*, t.tipo AS tTIPO
											FROM assegnazioni AS a
											LEFT JOIN territori AS t ON a.tid = t.id");
			$query->execute();

			return $query->fetchAll();
		}

		public function getAssociazioni($filtro, $ordine = "DESC", $mese = "all", $anno = "all", $uid = "all", $limit = 0, $max = 50) {

			$where = "";

			if(strcmp($anno, "all") == 0 && !strcmp($mese, "all") == 0) {
				$m = explode("-", $mese)[0];
				$where = "WHERE (a.start >= '2017-" . $m . "-01' AND a.start <= '2017-" . $mese . "') OR
				(a.start >= '2016-" . $m . "-01' AND a.start <= '2016-" . $mese . "') OR
				(a.start >= '2015-" . $m . "-01' AND a.start <= '2015-" . $mese . "') OR
				(a.start >= '2014-" . $m . "-01' AND a.start <= '2014-" . $mese . "')";
			} elseif(!strcmp($anno, "all") == 0 && strcmp($mese, "all") == 0) {
				$where = "WHERE a.start >= '" . $anno . "-01-01' AND a.start <= '" . $anno . "-12-31'";
			} elseif(!strcmp($anno, "all") == 0 && !strcmp($mese, "all") == 0) {
				$m = explode("-", $mese)[0];
				$where = "WHERE a.start >= '" . $anno . "-" . $m . "-01' AND a.start <= '" . $anno . "-" . $mese . "'";
			}

			if(!strcmp($uid, "all") == 0) {
				if(!empty($where)) $where .= " AND ";
				else $where = "WHERE ";
				$where .= "a.uid = " . $uid;
			}

			$order = "a.start " . $ordine;

			$ORDINE_NUMERO = 0;
			$ORDINE_NOME = 1;
			$ORDINE_USCITO = 2;
			$ORDINE_RIENTRATO = 3;

			if($filtro == $ORDINE_NUMERO) $order = "t.numero " . $ordine;
			elseif($filtro == $ORDINE_NOME) $order = "p.cognome, p.nome " . $ordine;
			elseif($filtro == $ORDINE_USCITO) $order = "a.start " . $ordine;
			elseif($filtro == $ORDINE_RIENTRATO) $order = "a.end " . $ordine;

			$query = self::$CONN->prepare("SELECT a.*, t.numero, t.tipo AS tTIPO, t.cattivi, p.nome, p.cognome, p.tipo AS pTIPO
											FROM assegnazioni AS a
											LEFT JOIN territori AS t ON a.tid = t.id
											LEFT JOIN proclamatori AS p ON a.uid = p.id
											" . $where . "
											ORDER BY " . $order . " LIMIT " . $limit . ", " . $max);
			$query->execute();

			return $query->fetchAll();
		}

		public function getProclamatori() {
			$query = self::$CONN->prepare("SELECT * FROM proclamatori ORDER BY cognome, nome");
			$query->execute();

			return $query->fetchAll();
		}

		public function getTerritori($filtro = -1) {

			$where = "";
			$order = "tipo, numero";

			if($filtro == 99) $order = "numero";
			elseif($filtro != -1) $where = "WHERE tipo = " . $filtro;

			$query = self::$CONN->prepare("SELECT * FROM territori " . $where . " ORDER BY " . $order);
			$query->execute();

			return $query->fetchAll();
		}

		public function newTerritorio($numero, $tipo) {
			$query = self::$CONN->prepare("INSERT INTO territori (numero,tipo) VALUES (:n,:t)");
			$query->bindValue(":n", $numero, PDO::PARAM_INT);
			$query->bindValue(":t", $tipo, PDO::PARAM_INT);

			return $query->execute();
		}

		public function newProclamatore($nome, $cognome, $tipo) {
			$query = self::$CONN->prepare("INSERT INTO proclamatori (nome,cognome,tipo) VALUES (:n,:c,:t)");
			$query->bindValue(":n", ucfirst(strtolower($nome)), PDO::PARAM_STR);
			$query->bindValue(":c", ucfirst(strtolower($cognome)), PDO::PARAM_STR);
			$query->bindValue(":t", $tipo, PDO::PARAM_INT);

			return $query->execute();
		}

		public function removeTerritorio($id) {
			$query = self::$CONN->prepare("DELETE FROM territori WHERE id = :id");
			$query->bindValue(":id", $id, PDO::PARAM_INT);

			return $query->execute();
		}

		public function setCattivoTerritorio($id, $val) {
			$query = self::$CONN->prepare("UPDATE territori SET cattivi = :v WHERE id = :id");
			$query->bindValue(":id", $id, PDO::PARAM_INT);
			$query->bindValue(":v", $val, PDO::PARAM_INT);

			return $query->execute();
		}

		public function newAssociazione($territorio, $proclamatore, $start) {
			$query = self::$CONN->prepare("INSERT INTO assegnazioni (tid,uid,start) VALUES (:t,:p,:s)");
			$query->bindValue(":t", $territorio, PDO::PARAM_INT);
			$query->bindValue(":p", $proclamatore, PDO::PARAM_INT);
			$query->bindValue(":s", $start, PDO::PARAM_STR);

			return $query->execute();
		}

		public function setEndAssociazione($id) {
			$query = self::$CONN->prepare("UPDATE assegnazioni SET end = CURRENT_TIMESTAMP WHERE id = :id");
			$query->bindValue(":id", $id, PDO::PARAM_INT);

			return $query->execute();
		}

		public function deleteAssociazione($id) {
			$query = self::$CONN->prepare("DELETE FROM assegnazioni WHERE id = :id");
			$query->bindValue(":id", $id, PDO::PARAM_INT);

			return $query->execute();
		}

		public function formatName($nome, $cognome, $skip = false) {
			if($skip) return $cognome . " " . $nome;

			$nome = ucfirst(strtolower($nome));
			$cognome = ucfirst(strtolower($cognome));

			return $cognome . " " . substr($nome, 0, 1) . ".";
		}

	}

	class Import_DAO {

		private static $CONN = null;

		public function __construct() {
			$info = "mysql:host=" . constant("HOST") . ";dbname=" . constant("DB") . ";charset=utf8";

			try {
				$opts = array(
					PDO::ATTR_PERSISTENT         => true,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);

				self::$CONN = new PDO($info, constant("USER"), constant("PASS"), $opts);
			} catch(PDOException $e) {
				die("PDOException: $e");
			}

			$error = self::$CONN->errorInfo();
			if(!is_null($error[2])) {
				die($error[2]);
			}
		}

		function createUser($user) {

			$cognome = explode(" ", $user)[0];
			$nome = explode(" ", $user)[1];

			$query = self::$CONN->prepare("INSERT INTO proclamatori (nome,cognome) VALUES (:n,:c)");
			$query->bindValue(":n", $nome, PDO::PARAM_STR);
			$query->bindValue(":c", $cognome, PDO::PARAM_STR);

			return $query->execute();
		}

		public function createTerritorio($numero, $tipo) {
			$PER_TUTTI = 0;
			$PER_PIONIERI = 1;
			$COMMERCIALE = 2;

			if(strcmp($tipo, "Normale") == 0) $tipo = $PER_TUTTI;
			elseif(strcmp($tipo, "Pionieri") == 0) $tipo = $PER_PIONIERI;
			elseif(strcmp($tipo, "Comm.") == 0) $tipo = $COMMERCIALE;

			$query = self::$CONN->prepare("INSERT INTO territori (numero,tipo) VALUES (:n,:t)");
			$query->bindValue(":n", $numero, PDO::PARAM_INT);
			$query->bindValue(":t", $tipo, PDO::PARAM_INT);

			return $query->execute();
		}

		public function getAllUserId() {
			$query = self::$CONN->prepare("SELECT * FROM proclamatori");
			$query->execute();

			return $query->fetchAll();
		}

		public function getAllTerritoriId() {
			$query = self::$CONN->prepare("SELECT * FROM territori");
			$query->execute();

			return $query->fetchAll();
		}

		public function newAssociazione($territorio, $proclamatore, $start, $end) {
			$query = self::$CONN->prepare("INSERT INTO assegnazioni (tid,uid,start,end) VALUES (:t,:p,:s,:e)");
			$query->bindValue(":t", $territorio, PDO::PARAM_INT);
			$query->bindValue(":p", $proclamatore, PDO::PARAM_INT);
			$query->bindValue(":s", $start, PDO::PARAM_STR);
			$query->bindValue(":e", $end, PDO::PARAM_STR);

			return $query->execute();
		}
	}