<?php
	/**
	 * SETUP DO SISTEMA
	 * 
	 * Esse arquivo carrega todas as configurações do sistema e prepara o ambiente para a execução
	 * NÃO ALTERE ESSE ARQUIVO, todas as opções estão disponíveis no arquivo config.php
	 */

	# Incluir as configurações do sistema (O arquivo config.php lê o arquivo config.ini)
	require_once(dirname(__FILE__) . '/config.php');	
	# Aplicar definições do DEBUG
	if(DEBUG_ENABLED){
		error_reporting(DEBUG_LEVEL);
		ini_set('display_errors',1);
	}

	# Definir a localização
	setlocale(LC_CTYPE, SYSTEM_LOCALE);
	
	# Definir o tempo limite da sessão
	ini_set('session.gc_maxlifetime', SESSION_TIMEOUT);
	ini_set('session_gc_probability',1);
	ini_set('session.gc_probability', 1);
	
	# Iniciar/carregar a sessão
	session_start();
	
	# Definir constantes da requisição
	define('IS_POST',$_SERVER["REQUEST_METHOD"] == "POST");
	define('HAS_USER',isset($_SESSION['user']) and !empty($_SESSION['user']));
	define('REQUEST_METHOD_NAME', $_REQUEST['request_method_name']);
	define('CENTRAL_CURRENT_VERSION', 1);
	$_SESSION['IS_ERROR'] = false;

	if(REQUEST_METHOD_NAME == 'admin'){
		define('IS_ADMIN',true);
	} else {
		define('IS_ADMIN',false);
	}

	# Redirecionar para a tela de login caso necessário
	if(!in_array(REQUEST_METHOD_NAME, array("login","qr","bemvindo","novidades"))) {
		if(!HAS_USER){
			$uri = urlencode($_SERVER['REQUEST_URI']);
			header("Location: " . SITE_BASE . "login?u=".$uri);
			exit();
		}
		if(!empty($_SESSION['IS_FIRST_ACCESS']) && $_SESSION['IS_FIRST_ACCESS'] && REQUEST_METHOD_NAME != "bemvindo"){
			$uri = urlencode($_SERVER['REQUEST_URI']);
			header("Location: " . SITE_BASE . "bemvindo?u=" . $uri);
			exit();
		} elseif(!empty($_SESSION['IS_FIRST_ON_NEW']) && $_SESSION['IS_FIRST_ON_NEW'] && REQUEST_METHOD_NAME != "novidades"){
			header("Location: " . SITE_BASE . "novidades");
			exit();
		}
	}

	# Em que seção da aplicação está?
	$uri = $_SERVER['REQUEST_URI'];
	/*if(stripos($uri, SITE_BASE) !== false){
		$uri = substr($uri, strlen(SITE_BASE)) . "/";
	}*/
	$uri = explode('/', $uri);
	$uri = explode('?', $uri[1]);
	switch ($uri[0]) {
		case 'pendencias':
			$section = 'pendencias';
			break;
		case 'participacoes':
			$section = 'participacoes';
			break;
		case 'nova':
			$section = 'nova';
			break;
		case 'admin':
			$section = 'admin';
			break;
		case 'perfil':
		case 'feedback':
		case 'novidades':
			$section = 'profile';
			break;
		default:
			$section = 'home';
			break;
	}

	# mapear os status e os códigos
	$status_mapping = array(
		0 => "rascunho",
		1 => "pendente",
		2 => "aprovada",
		3 => "recusada",
		4 => "retornada",
		5 => "cotando",
		6 => "analise",
		7 => "executada",
		8 => "cancelada",
		"rascunho" => 0,
		"pendente" => 1,
		"aprovada" => 2,
		"recusada" => 3,
		"retornada" => 4,
		"cotando" => 5,
		"analise" => 6,
		"executada" => 7,
		"cancelada" => 8
	);
	
	# Listar os status verbalmente
	$verbal_status = array(
		"Rascunho",
		"Pendente",
		"Aprovada",
		"Recusada",
		"Retornada",
		"Em cotação",
		"Em análise",
		"Executada",
		"Cancelada"
	);
	
	# Incluir bibliotecas, extensões e complementos
	require_once(dirname(__FILE__) . '/ldap/adLDAP.php'); # Biblioteca adLDAP <http://adldap.sourceforge.net/>
	require_once(dirname(__FILE__) . '/phpmailer/class.phpmailer.php'); # Biblioteca PHPMailer <http://phpmailer.worxware.com/> 
	require_once(dirname(__FILE__) . '/messages.php'); # Strings para utilizar no sistema
	require_once(dirname(__FILE__) . '/Db.class.php'); # Classe gerenciadora do banco de dados
	require_once(dirname(__FILE__) . '/Email.class.php'); # Classe gerenciadora de e-mails
	require_once(dirname(__FILE__) . '/Solicitacao.class.php'); # Classe wrapper da solicitação
	require_once(dirname(__FILE__) . '/OAuth.class.php'); # Classe wrapper para autenticação
	require_once(dirname(__FILE__) . '/Notify.class.php'); # Classe wrapper para notificações
	require_once(dirname(__FILE__) . '/Views.php'); # Classe das views do sistema
	
	# Inicializar o banco de dados
	DB::initialize();
	
	# Conectar ao servidor LDAP
	Class AD {
		public static $ad;
		public static function initialize(){
			self::$ad = new adLDAP(array(
					"account_suffix" => ADLDAP_ACCOUNT_SUFFIX,
					"base_dn" => ADLDAP_BASE_DN,
					"domain_controllers" => unserialize(ADLDAP_SERVER),
					"admin_username" => ADLDAP_ADMIN_USERNAME,
					"admin_password" => ADLDAP_ADMIN_PASSWORD
			));
		}
		/**
		 * Retorna informações sobre um usuário do AD
		 * @param string $username O nome do usuário a ser pesquisado
		 * @param array $info As informações a serem retornadas do AD
		 * @return array As informações encontradas sobre o usuário no AD
		 */
		public static function info($username,$info){
			return self::$ad->user()->info(strtolower($username),$info);
		}
	}
	AD::initialize();

	# Conversor de unidades para exibir os anexos
	class Size {
		public static function format($bytes){
			if ($bytes > 0){
				$unit = intval(log($bytes, 1024));
				$units = array('B', 'KB', 'MB', 'GB');

				if (array_key_exists($unit, $units) === true){
					return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
				}
			}

			return $bytes;
		}
	}

	$_SESSION['SUDO_MODE'] = false;
	if(isset($_REQUEST["sudo"]) && !empty($_REQUEST["sudo"])){
		$_role = DB::$roles->findOne(array("user" => $_SESSION['user']));
		if($_role && $_role['role'] == 3){
			$_SESSION['real_user'] = $_SESSION['user'];
			$_SESSION['user'] = $_REQUEST["sudo"];
			$_SESSION['SUDO_MODE'] = true;
		} else {
			$_SESSION['flash'] .= "<br>Você não pode usar este recurso";
		}
	} elseif (isset($_SESSION['real_user'])) {
		$_SESSION['user'] = $_SESSION['real_user'];
		unset($_SESSION['real_user']);
		$_SESSION['SUDO_MODE'] = false;
	}

	
	# Carrega a regra do usuário atual
	if(isset($_SESSION['user']) && !empty($_SESSION['user'])){
		$_role = DB::$roles->findOne(array("user" => $_SESSION['user']));
		if($_role) {
			$_SESSION['role'] = $_role['role'];
		}else{
			$_SESSION['role'] = -1;
		}
	}else{
		$_SESSION['role'] = -1;
	}

	$migration = DB::$db->migration;
	$migrado = $migration->findOne(array("migrado" => true));
	if(!$migrado){
		echo "<meta charset='utf-8' /><pre>";

		#Listar os tipos de solicitação
		$tipos = DB::$tipo->find();
		foreach($tipos as $tipo){
			$tipo['executor'] = array($tipo['executor']);
			$tipo['aprovador'] = array($tipo['aprovador']);
			$tipo['cotador'] = array($tipo['cotador']);
			$tipo['informar'] = array();
			print_r($tipo);
			DB::$tipo->save($tipo);
		}

		$migrado = array("migrado" => true);
		$migration->save($migrado);

		echo "<pre>";

		$_SESSION['flash'] = "Banco de dados migrado!";
		exit();
	}

?>
