<?php
/*
 * Exceção da API
 */
class APIException extends Exception{
	/*
	 * Transforma a execeção em um Array que pode ser enviado na resposta json
	 * @params void
	 * @return array A exceção
	 */
	public function toArray(){
		$err = array(
			'name' => $this->string,
			'message' => $this->getMessage(),
			'code' => $this->getCode()
		);
		if(DEBUG_ENABLED){
			$err['file'] = $this->getFile();
			$err['line'] = $this->getLine();
			$err['trace'] = $this->getTrace();
			$err['trace_string'] = $this->getTraceAsString();
		}
		return $err;
	}
}

/*
 * API da Central de solicitações
 */
class API {

	private $success, $error, $data, $has_more;
	private $auth, $action, $role, $params, $app;

	/*
	 * Interpreta e executa a requisição
	 * @params mixed $request A requisição
	 * @return void
	 */
	public function parse_and_run($request){
		try {
			$this->parse_request($request);
			$this->data = $this->run($this->action);
			$this->success = true;
		} catch (APIException $e) {
			$this->success = false;
			$this->error = $e->toArray();
		}
	}

	/*
	 * Prepara a resposta JSON
	 * @params void
	 * @return void
	 */
	public function response(){
		$out = array(
			'success' => $this->success,
			'data' => $this->data
		);
		if(!is_null($this->error)){
			$out['error'] = $this->error;
			header(':', true, $this->error['code']);
		}
		if(!is_null($this->has_more))
			$out['has_more'] = $this->has_more;

		header('Content-Type: application/json; charset=utf8');
	    header('Access-Control-Allow-Origin: ' . SITE_PATH);
	    header('Access-Control-Max-Age: 3628800');
	    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
	    print(json_encode($out));

	}

	/*
	 * Interpreta os parâmetros da requisição
	 * @params mixed $request A requisição
	 * @return void
	 */
	private function parse_request($request){
		if(!isset($request['a']) || empty($request['a']))
			throw new APIException("Nenhuma ação foi solicitada",400);
		
		if(!isset($request['token']) || empty($request['token']))
			throw new APIException("Acesso não autenticado",403);

		if(!isset($request['appid']) || empty($request['appid']))
			throw new APIException("Aplicativo desconhecido",404);

		$this->action = $request['a'];
		
		try {
			$this->auth = new Authorization($request['token']);
		} catch (Exception $e) {
			throw new APIException("Acesso não autorizado",401);
		}

		$this->app = $this->auth->application;
		if($this->app->id != $request['appid'])
			throw new APIException("Token inválido para esse aplicativo",403);

		$this->params = array();
		foreach ($request as $key => $value) {
			if(!in_array($key, array("a","token"))){
				$this->params[$key] = $value;
			}
		}
		$this->params['current_user'] = $this->auth->user;
	}

	/*
	 * Executa a ação requisitada
	 * @params string $action A ação a executar
	 * @return mixed O resultado da execução
	 */
	private function run($action){
		$action = explode(".", $action);
		$class_name = $action[0];
		$method_name = $action[1];
		if(!class_exists($class_name))
			throw new APIException("A ação $class_name solicitada não existe", 404);
		
		$class = new $class_name();

		if(!method_exists($class, $method_name))
			throw new APIException("A ação solicitada não existe", 404);

		return $class->$method_name($this->params);
		
	}
}

/*
 * Setup configs
 */
require_once(dirname(__FILE__) . '/../config.php');
# Aplicar definições do DEBUG
if(DEBUG_ENABLED){
	error_reporting(DEBUG_LEVEL);
	ini_set('display_errors',1);
}

# Definir constantes da requisição
define('IS_POST',$_SERVER["REQUEST_METHOD"] == "POST");
define('HAS_USER',isset($_SESSION['user']) and !empty($_SESSION['user']));

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
	"rascunho" => 0,
	"pendente" => 1,
	"aprovada" => 2,
	"recusada" => 3,
	"retornada" => 4,
	"cotando" => 5,
	"analise" => 6,
	"executada" => 7
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
	"Executada"
);

# Incluir bibliotecas, extensões e complementos
require_once(dirname(__FILE__) . '/../ldap/adLDAP.php'); # Biblioteca adLDAP <http://adldap.sourceforge.net/>
require_once(dirname(__FILE__) . '/../phpmailer/class.phpmailer.php'); # Biblioteca PHPMailer <http://phpmailer.worxware.com/> 
require_once(dirname(__FILE__) . '/../messages.php'); # Strings para utilizar no sistema
require_once(dirname(__FILE__) . '/../Db.class.php'); # Classe gerenciadora do banco de dados
require_once(dirname(__FILE__) . '/../Email.class.php'); # Classe gerenciadora de e-mails
require_once(dirname(__FILE__) . '/../Solicitacao.class.php'); # Classe wrapper da solicitação
require_once(dirname(__FILE__) . '/../OAuth.class.php'); # Classe wrapper para autenticação
require_once(dirname(__FILE__) . '/../Notify.class.php'); # Classe wrapper para notificações

require_once(dirname(__FILE__) . '/AuthAPI.class.php'); # Classe da API de autenticação
require_once(dirname(__FILE__) . '/SolicitacaoAPI.class.php'); # Classe da API da solicitação
require_once(dirname(__FILE__) . '/UtilAPI.class.php'); # Classe da API de dados complementares

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

$api = new API();
$api->parse_and_run($_REQUEST);
$api->response();

?>