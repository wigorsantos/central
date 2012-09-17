<?php 
	/**
	 * Classe estática para o acesso ao MongoDB
	 * @author Luiz Fernando da Silva <lfsilva@sccorinthians.com.br>
	 *
	 */
	class DB {
		public static $mongo;
		public static $db;
		public static $solicitacao;
		public static $tipo;
		public static $item;
		public static $aprovador;
		public static $executor;
		public static $especialista;
		public static $centro;
		public static $historico;
		public static $observacao;
		public static $access_log;
		public static $grid;
		public static $counters;
		public static $roles;
		public static $feedback;
		public static $application;
		public static $authorization;

		public static function initialize(){
			// Instanciar as variaveis estáticas
			if(MONGODB_AUTH){
				$connection = "mongodb://" . MONGODB_USER . ":" .MONGODB_PASSWORD . "@";
			} else {
				$connection = "mongodb://";
			}
			$connection  .= MONGODB_SERVER;
			self::$mongo = new Mongo($connection);
			self::$db = self::$mongo->selectDB(MONGODB_DATABASE);
			self::$solicitacao = self::$db->solicitacao;
			self::$tipo = self::$db->tipo;
			self::$item = self::$db->item;
			self::$aprovador = self::$db->aprovador;
			self::$executor = self::$db->executor;
			self::$especialista = self::$db->especialista;
			self::$centro = self::$db->centro;
			self::$historico = self::$db->historico;
			self::$observacao = self::$db->observacao;
			self::$access_log = self::$db->access_log;
			self::$grid = self::$db->getGridFS();
			self::$counters = self::$db->counters;
			self::$roles = self::$db->roles;
			self::$feedback = self::$db->feedback;
			self::$application = self::$db->application;
			self::$authorization = self::$db->authorization;
			// Criar os índices
			#self::$solicitacao->ensureIndex('numero'); # Indice no numero sequencial
			#self::$solicitacao->ensureIndex('data'); # Indice das datas
			#self::$solicitacao->ensureIndex('status'); # Indice dos status
			#self::$solicitacao->ensureIndex(array(
			#	'solicitante' => 1,
			#	'pre_aprovador' => 1,
			#	'aprovador' => 1,
			#	'analisador' => 1,
			#	'cotador' => 1,
			#)); # Indice dos usuarios relacionados
			self::$solicitacao->ensureIndex(array(
				'data' => -1, 
				'numero' => 1,
				'solicitante' => 1,
				'status' => 1
			));
		}

	}
?>