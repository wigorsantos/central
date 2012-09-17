<?php

class Views{
	
	public static function countPendencias($user){
		$pre_aprovar = DB::$solicitacao->count(array("pre_aprovador" => $_SESSION['user'], "status" => 1, "pre_aprovado" => false));
		$analisar = DB::$solicitacao->count(array("analisador" => $_SESSION['user'], "status" => 6));
		$revisar = DB::$solicitacao->count(array("solicitante" => $_SESSION['user'], "status" => 4));

		$it_aprova = DB::$tipo->find(array("aprovador" => $_SESSION['user']),array('_id'));
		$t_aprova = array();
		foreach ($it_aprova as $t) {
			$t_aprova[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_cota = DB::$tipo->find(array("cotador" => $_SESSION['user']),array('_id'));
		$t_cota = array();
		foreach ($it_cota as $t) {
			$t_cota[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_executa = DB::$tipo->find(array("executor" => $_SESSION['user']),array('_id'));
		$t_executa = array();
		foreach ($it_executa as $t) {
			$t_executa[] = MongoDBRef::create('tipo',$t['_id']);
		}

		$aprovar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_aprova), "status" => 1, "pre_aprovado" => true));
		$cotar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_cota), "status" => 5));
		$executar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_executa), "status" => 2, "executado" => false));

		return $pre_aprovar + $aprovar + $analisar + $cotar + $executar + $revisar;
	}

	/**************************************************************************************************************************************/

	/**
	 * GET|POST /
	 */
	public function home_all(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		header("Location: " . SITE_BASE . "todas");
	}

	/**
	 * GET /logout
	 */
	public function logout_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		session_destroy();
		header("Location: " . SITE_BASE . "login");
	}

	/**
	 * GET /login
	 */
	public function login_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if(HAS_USER){
			if($_SESSION['IS_FIRST_ACCESS']){
				$url = isset($_REQUEST['u']) ? $_REQUEST['u'] : SITE_BASE . 'todas';
				header("Location: " . SITE_BASE . "bemvindo?u=" . $url);
			}else{
				$url = isset($_REQUEST['u']) ? urldecode($_REQUEST['u']) : SITE_BASE . 'todas';
				header("Location: " . $url);
			}
		}else{
			include 'templates/login.php';
		}
	}

	/**
	 * POST /login
	 */
	public function login_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$username = strtolower($_REQUEST['user']);
		$password = $_REQUEST['pass'];
		try {
			$authUser = AD::$ad->user()->authenticate($username, $password);
			if($authUser == true){
				$_SESSION['user'] = $username;
				# Checa se é o primeiro acesso
				$is_first_access = DB::$access_log->findOne(array('user' => $username),array('_id')) == null;
				# Checa se é o primeiro acesso nessa versão
				$is_first_on_new = DB::$db->newversion->findOne(array('user' => $username, 'version' => CENTRAL_CURRENT_VERSION),array('_id')) == null;
				$_SESSION['IS_FIRST_ACCESS'] = $is_first_access;
				$_SESSION['IS_FIRST_ON_NEW'] = $is_first_on_new;
				if($is_first_access){
					$url = isset($_REQUEST['u']) ? $_REQUEST['u'] : 'todas';
					header("Location: " . SITE_BASE . "bemvindo?u=" . $url);
				} else {
					DB::$access_log->save(array(
						"user" => $username,
						"data" => new MongoDate(),
						"ip" => $_SERVER['REMOTE_ADDR']
					));
					# A cada 100 acessos perguntar ao usuário como está experiência dele
					$acessos = DB::$access_log->count(array("user"=>$username));
					if($acessos % 100 == 0){
						# Enviar um e-mail
						$from = array('email' => ORG_TIMAIL, 'nome' => 'Tecnologia da informação');
						$to = $username . ADLDAP_ACCOUNT_SUFFIX;
						$cc = null;
						$subject = "Como está a sua experiência com a central de solicitações?";
						$uinfo = AD::info($username,array('displayname'));
						$body = "<html><style>div,span,td,th,strong,p{font-family: Helvetica, Arial, sans-serif;font-size:12px;}hr {border: none;border-bottom: solid 1px #000000;margin-bottom: 10px;}</style><body>";
						$body .= "<p>Olá " . $uinfo[0]['displayname'][0] . "!</p><p></p>";
						$body .= "<p>Para que a central de solicitações se torne mais eficiente é necessário saber a sua opinião sobre ela. Por favor, acesse <a href='http://" . SERVER_NAME . SITE_BASE ."feedback'>http://" . SERVER_NAME . SITE_BASE ."feedback</a>, deixe o seu recado e avalie a sua satisfação quando quiser.</p><p></p>";
						$body .= "<p>Atenciosamente,<br>A Equipe de Tecnologia da Informação<br><b>" . ORG_NAME . "</b></p>";
						$body .= "</body></html>";
						EMAIL::send($from,$to,$subject,$body,$cc);
					}
					if($is_first_on_new) {
						header("Location: " . SITE_BASE . "novidades");
					} else {
						$url = isset($_REQUEST['u']) ? urldecode($_REQUEST['u']) : 'todas';
						header("Location: " . $url);
					}
				}
			} else {
				throw new Exception("Login falhou", 1);
			}
		} catch (Exception $e) {
			$_SESSION['flash'] = LOGIN_FAILED;
			$_SESSION['login_error'] = true;
			header('Location: ' . SITE_BASE . "login");
		}
	}

	/**
	 * GET /pendencias
	 */
	public function pendencias_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$_TITLE = "Minhas pendências";

		$it_aprova = DB::$tipo->find(array("aprovador" => $_SESSION['user']),array('_id'));
		$t_aprova = array();
		foreach ($it_aprova as $t) {
			$t_aprova[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_cota = DB::$tipo->find(array("cotador" => $_SESSION['user']),array('_id'));
		$t_cota = array();
		foreach ($it_cota as $t) {
			$t_cota[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_executa = DB::$tipo->find(array("executor" => $_SESSION['user']),array('_id'));
		$t_executa = array();
		foreach ($it_executa as $t) {
			$t_executa[] = MongoDBRef::create('tipo',$t['_id']);
		}

		// Paginação
		$default_page_size = 10;
		function makePageUrl($list, $number, $page_size = false){
			$qry = array();

			$page_aprovar = isset($_GET['aprovar_page']) ? $_GET['aprovar_page'] : false;
			$page_aprovar_size =isset($_GET['aprovar_page_size']) ? $_GET['aprovar_page_size'] : false;
			if($list == "aprovar"){
				$page_aprovar = $number;
				if($page_size && !empty($page_size)){
					$page_aprovar_size = $page_size;
				}
			}
			if($page_aprovar) $qry['aprovar_page'] = $page_aprovar;
			if($page_aprovar_size) $qry['aprovar_page_size'] = $page_aprovar_size;

			$page_executar = isset($_GET['executar_page']) ? $_GET['executar_page'] : false;
			$page_executar_size =isset($_GET['executar_page_size']) ? $_GET['executar_page_size'] : false;
			if($list == "executar"){
				$page_executar = $number;
				if($page_size && !empty($page_size)){
					$page_executar_size = $page_size;
				}
			}
			if($page_executar) $qry['executar_page'] = $page_executar;
			if($page_executar_size) $qry['executar_page_size'] = $page_executar_size;

			$page_cotar = isset($_GET['cotar_page']) ? $_GET['cotar_page'] : false;
			$page_cotar_size =isset($_GET['cotar_page_size']) ? $_GET['cotar_page_size'] : false;
			if($list == "cotar"){
				$page_cotar = $number;
				if($page_size && !empty($page_size)){
					$page_cotar_size = $page_size;
				}
			}
			if($page_cotar) $qry['cotar_page'] = $page_cotar;
			if($page_cotar_size) $qry['cotar_page_size'] = $page_cotar_size;

			$page_analisar = isset($_GET['analisar_page']) ? $_GET['analisar_page'] : false;
			$page_analisar_size =isset($_GET['analisar_page_size']) ? $_GET['analisar_page_size'] : false;
			if($list == "analisar"){
				$page_analisar = $number;
				if($page_size && !empty($page_size)){
					$page_analisar_size = $page_size;
				}
			}
			if($page_analisar) $qry['analisar_page'] = $page_analisar;
			if($page_analisar_size) $qry['analisar_page_size'] = $page_analisar_size;

			$page_revisar = isset($_GET['revisar_page']) ? $_GET['revisar_page'] : false;
			$page_revisar_size =isset($_GET['revisar_page_size']) ? $_GET['revisar_page_size'] : false;
			if($list == "revisar"){
				$page_revisar = $number;
				if($page_size && !empty($page_size)){
					$page_revisar_size = $page_size;
				}
			}
			if($page_revisar) $qry['revisar_page'] = $page_revisar;
			if($page_revisar_size) $qry['revisar_page_size'] = $page_revisar_size;

			return http_build_query($qry);
		}
		
		$aprovar_page = isset($_GET['aprovar_page']) ? $_GET['aprovar_page'] : 1;
		$aprovar_limit = isset($_GET['aprovar_page_size']) ? $_GET['aprovar_page_size'] : $default_page_size;
		$aprovar_skip = ($aprovar_page - 1) * $aprovar_limit;
		$qry_pre_aprovar = array("pre_aprovador" => $_SESSION['user'], "status" => 1, "pre_aprovado" => false);
		$qry_aprovar = array("tipo" => array('$in' => $t_aprova), "status" => 1, "aprovado" => false, "pre_aprovado" => true);
		$total_aprovar = DB::$solicitacao->count(array('$or' => array($qry_pre_aprovar,$qry_aprovar)));
		$aprovar_pages = ceil($total_aprovar/$aprovar_limit);
		if($aprovar_pages > 1){
			$aprovar_is_first = $aprovar_page == 1;
			$aprovar_is_last = $aprovar_page == $aprovar_pages;
			$aprovar_first_url = $aprovar_is_first ? "#" : "?" . makePageUrl("aprovar",$aprovar_page - 1);
			$aprovar_last_url = $aprovar_is_last ? "#" : "?" . makePageUrl("aprovar",$aprovar_page + 1);
		}

		$executar_page = isset($_GET['executar_page']) ? $_GET['executar_page'] : 1;
		$executar_limit = isset($_GET['executar_page_size']) ? $_GET['executar_page_size'] : $default_page_size;
		$executar_skip = ($executar_page - 1) * $executar_limit;
		$total_executar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_executa), "status" => 2, "executado" => false));
		$executar_pages = ceil($total_executar/$executar_limit);
		if($executar_pages > 1){
			$executar_is_first = $executar_page == 1;
			$executar_is_last = $executar_page == $executar_pages;
			$executar_first_url = $executar_is_first ? "#" : "?" . makePageUrl("executar",$executar_page - 1);
			$executar_last_url = $executar_is_last ? "#" : "?" . makePageUrl("executar",$executar_page + 1);
		}

		$cotar_page = isset($_GET['cotar_page']) ? $_GET['cotar_page'] : 1;
		$cotar_limit = isset($_GET['cotar_page_size']) ? $_GET['cotar_page_size'] : $default_page_size;
		$cotar_skip = ($cotar_page - 1) * $cotar_limit;
		$total_cotar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_cota), "status" => 5));
		$cotar_pages = ceil($total_cotar/$cotar_limit);
		if($cotar_pages > 1){
			$cotar_is_first = $cotar_page == 1;
			$cotar_is_last = $cotar_page == $cotar_pages;
			$cotar_first_url = $cotar_is_first ? "#" : "?" . makePageUrl("cotar",$cotar_page - 1);
			$cotar_last_url = $cotar_is_last ? "#" : "?" . makePageUrl("cotar",$cotar_page + 1);
		}

		$analisar_page = isset($_GET['analisar_page']) ? $_GET['analisar_page'] : 1;
		$analisar_limit = isset($_GET['analisar_page_size']) ? $_GET['analisar_page_size'] : $default_page_size;
		$analisar_skip = ($analisar_page - 1) * $analisar_limit;
		$total_analisar = DB::$solicitacao->count(array("analisador" => $_SESSION['user'], "status" => 6));
		$analisar_pages = ceil($total_analisar/$analisar_limit);
		if($analisar_pages > 1){
			$analisar_is_first = $analisar_page == 1;
			$analisar_is_last = $analisar_page == $analisar_pages;
			$analisar_first_url = $analisar_is_first ? "#" : "?" . makePageUrl("analisar",$analisar_page - 1);
			$analisar_last_url = $analisar_is_last ? "#" : "?" . makePageUrl("analisar",$analisar_page + 1);
		}

		$revisar_page = isset($_GET['revisar_page']) ? $_GET['revisar_page'] : 1;
		$revisar_limit = isset($_GET['revisar_page_size']) ? $_GET['revisar_page_size'] : $default_page_size;
		$revisar_skip = ($revisar_page - 1) * $revisar_limit;
		$total_revisar = DB::$solicitacao->count(array("solicitante" => $_SESSION['user'], "status" => 4));
		$revisar_pages = ceil($total_revisar/$revisar_limit);
		if($revisar_pages > 1){
			$revisar_is_first = $revisar_page == 1;
			$revisar_is_last = $revisar_page == $revisar_pages;
			$revisar_first_url = $revisar_is_first ? "#" : "?" . makePageUrl("revisar",$revisar_page - 1);
			$revisar_last_url = $revisar_is_last ? "#" : "?" . makePageUrl("revisar",$revisar_page + 1);
		}
		
		$aprovar = DB::$solicitacao->find(array('$or' => array($qry_aprovar,$qry_pre_aprovar)),array('_id'))->sort(array('data' => -1))->limit($aprovar_limit)->skip($aprovar_skip);
		$executar = DB::$solicitacao->find(array("tipo" => array('$in' => $t_executa), "status" => 2, "executado" => false),array('_id'))->sort(array('data' => -1))->limit($executar_limit)->skip($executar_skip);
		$cotar = DB::$solicitacao->find(array("tipo" => array('$in' => $t_cota), "status" => 5),array('_id'))->sort(array('data' => -1))->limit($cotar_limit)->skip($cotar_skip);
		$analisar = DB::$solicitacao->find(array("analisador" => $_SESSION['user'], "status" => 6),array('_id'))->sort(array('data' => -1))->limit($analisar_limit)->skip($analisar_skip);
		$revisar = DB::$solicitacao->find(array("solicitante" => $_SESSION['user'], "status" => 4),array('_id'))->sort(array('data' => -1))->limit($revisar_limit)->skip($revisar_skip);

		include 'templates/pendencias.php';
	}

	/**
	 * GET /participacoes
	 */
	public function participacoes_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$_TITLE = "Minhas participações";
		$it_executa = DB::$tipo->find(array("executor" => $_SESSION['user']),array('_id'));
		$t_executa = array();
		foreach ($it_executa as $t) {
			$t_executa[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_informado = DB::$tipo->find(array("informar" => $_SESSION['user']),array('_id'));
		$t_informado = array();
		foreach ($it_informado as $t) {
			$t_informado[] = MongoDBRef::create('tipo',$t['_id']);
		}

		// Paginação
		$default_page_size = 10;
		function makePageUrl($list, $number, $page_size=false){
			$qry = array();

			$page_aprovar = isset($_GET['aprovar_page']) ? $_GET['aprovar_page'] : false;
			$page_aprovar_size =isset($_GET['aprovar_page_size']) ? $_GET['aprovar_page_size'] : false;
			if($list == "aprovar"){
				$page_aprovar = $number;
				if($page_size && !empty($page_size)){
					$page_aprovar_size = $page_size;
				}
			}
			if($page_aprovar) $qry['aprovar_page'] = $page_aprovar;
			if($page_aprovar_size) $qry['aprovar_page_size'] = $page_aprovar_size;

			$page_executar = isset($_GET['executar_page']) ? $_GET['executar_page'] : false;
			$page_executar_size =isset($_GET['executar_page_size']) ? $_GET['executar_page_size'] : false;
			if($list == "executar"){
				$page_executar = $number;
				if($page_size && !empty($page_size)){
					$page_executar_size = $page_size;
				}
			}
			if($page_executar) $qry['executar_page'] = $page_executar;
			if($page_executar_size) $qry['executar_page_size'] = $page_executar_size;

			$page_cotar = isset($_GET['cotar_page']) ? $_GET['cotar_page'] : false;
			$page_cotar_size =isset($_GET['cotar_page_size']) ? $_GET['cotar_page_size'] : false;
			if($list == "cotar"){
				$page_cotar = $number;
				if($page_size && !empty($page_size)){
					$page_cotar_size = $page_size;
				}
			}
			if($page_cotar) $qry['cotar_page'] = $page_cotar;
			if($page_cotar_size) $qry['cotar_page_size'] = $page_cotar_size;

			$page_analisar = isset($_GET['analisar_page']) ? $_GET['analisar_page'] : false;
			$page_analisar_size =isset($_GET['analisar_page_size']) ? $_GET['analisar_page_size'] : false;
			if($list == "analisar"){
				$page_analisar = $number;
				if($page_size && !empty($page_size)){
					$page_analisar_size = $page_size;
				}
			}
			if($page_analisar) $qry['analisar_page'] = $page_analisar;
			if($page_analisar_size) $qry['analisar_page_size'] = $page_analisar_size;

			$page_informado = isset($_GET['informado_page']) ? $_GET['informado_page'] : false;
			$page_informado_size =isset($_GET['informado_page_size']) ? $_GET['informado_page_size'] : false;
			if($list == "informado"){
				$page_informado = $number;
				if($page_size && !empty($page_size)){
					$page_informado_size = $page_size;
				}
			}
			if($page_informado) $qry['informado_page'] = $page_informado;
			if($page_informado_size) $qry['informado_page_size'] = $page_informado_size;

			return http_build_query($qry);
		}

		$aprovar_page = isset($_GET['aprovar_page']) ? $_GET['aprovar_page'] : 1;
		$aprovar_limit = isset($_GET['aprovar_page_size']) ? $_GET['aprovar_page_size'] : $default_page_size;
		$aprovar_skip = ($aprovar_page - 1) * $aprovar_limit;
		$qry_pre_aprovar = array("pre_aprovador" => $_SESSION['user'], "pre_aprovado" => true);
		$qry_aprovar = array("aprovador" => $_SESSION['user'], "aprovado" => true);
		$total_aprovar = DB::$solicitacao->count(array('$or' => array($qry_pre_aprovar, $qry_aprovar)));
		$aprovar_pages = ceil($total_aprovar/$aprovar_limit);
		if($aprovar_pages > 1){
			$aprovar_is_first = $aprovar_page == 1;
			$aprovar_is_last = $aprovar_page == $aprovar_pages;
			$aprovar_first_url = $aprovar_is_first ? "#" : "?" . makePageUrl("aprovar",$aprovar_page - 1);
			$aprovar_last_url = $aprovar_is_last ? "#" : "?" . makePageUrl("aprovar",$aprovar_page + 1);
		}

		$executar_page = isset($_GET['executar_page']) ? $_GET['executar_page'] : 1;
		$executar_limit = isset($_GET['executar_page_size']) ? $_GET['executar_page_size'] : $default_page_size;
		$executar_skip = ($executar_page - 1) * $executar_limit;
		$total_executar = DB::$solicitacao->count(array("executor" => $_SESSION['user'], 'status' => 7, 'executado' => true));
		$executar_pages = ceil($total_executar/$executar_limit);
		if($executar_pages > 1){
			$executar_is_first = $executar_page == 1;
			$executar_is_last = $executar_page == $executar_pages;
			$executar_first_url = $executar_is_first ? "#" : "?" . makePageUrl("executar",$executar_page - 1);
			$executar_last_url = $executar_is_last ? "#" : "?" . makePageUrl("executar",$executar_page + 1);
		}

		$cotar_page = isset($_GET['cotar_page']) ? $_GET['cotar_page'] : 1;
		$cotar_limit = isset($_GET['cotar_page_size']) ? $_GET['cotar_page_size'] : $default_page_size;
		$cotar_skip = ($cotar_page - 1) * $cotar_limit;
		$total_cotar = DB::$solicitacao->count(array("cotador" => $_SESSION['user'], "cotado" => true));
		$cotar_pages = ceil($total_cotar/$cotar_limit);
		if($cotar_pages > 1){
			$cotar_is_first = $cotar_page == 1;
			$cotar_is_last = $cotar_page == $cotar_pages;
			$cotar_first_url = $cotar_is_first ? "#" : "?" . makePageUrl("cotar",$cotar_page - 1);
			$cotar_last_url = $cotar_is_last ? "#" : "?" . makePageUrl("cotar",$cotar_page + 1);
		}

		$analisar_page = isset($_GET['analisar_page']) ? $_GET['analisar_page'] : 1;
		$analisar_limit = isset($_GET['analisar_page_size']) ? $_GET['analisar_page_size'] : $default_page_size;
		$analisar_skip = ($analisar_page - 1) * $analisar_limit;
		$total_analisar = DB::$solicitacao->count(array("analisador" => $_SESSION['user'], "analisado" => true));
		$analisar_pages = ceil($total_analisar/$analisar_limit);
		if($analisar_pages > 1){
			$analisar_is_first = $analisar_page == 1;
			$analisar_is_last = $analisar_page == $analisar_pages;
			$analisar_first_url = $analisar_is_first ? "#" : "?" . makePageUrl("analisar",$analisar_page - 1);
			$analisar_last_url = $analisar_is_last ? "#" : "?" . makePageUrl("analisar",$analisar_page + 1);
		}

		$informado_page = isset($_GET['informado_page']) ? $_GET['informado_page'] : 1;
		$informado_limit = isset($_GET['informado_page_size']) ? $_GET['informado_page_size'] : $default_page_size;
		$informado_skip = ($informado_page - 1) * $informado_limit;
		$total_informado = DB::$solicitacao->count(array("tipo" => array('$in' => $t_informado), "status" => array('$gt' => 0)));
		$informado_pages = ceil($total_informado/$informado_limit);
		if($informado_pages > 1){
			$informado_is_first = $informado_page == 1;
			$informado_is_last = $informado_page == $informado_pages;
			$informado_first_url = $informado_is_first ? "#" : "?" . makePageUrl("informado",$informado_page - 1);
			$informado_last_url = $informado_is_last ? "#" : "?" . makePageUrl("informado",$informado_page + 1);
		}

		$aprovar = DB::$solicitacao->find(array('$or' => array($qry_pre_aprovar, $qry_aprovar)),array('_id'))->sort(array('data' => -1))->limit($aprovar_limit)->skip($aprovar_skip);
		$executar = DB::$solicitacao->find(array("executor" => $_SESSION['user'], 'status' => 7, 'executado' => true),array('_id'))->sort(array('data' => -1))->limit($executar_limit)->skip($executar_skip); # Antiga condição "status" => array('$in', array(2,7))
		$cotar = DB::$solicitacao->find(array("cotador" => $_SESSION['user'], "cotado" => true),array('_id'))->sort(array('data' => -1))->limit($cotar_limit)->skip($cotar_skip);
		$analisar = DB::$solicitacao->find(array("analisador" => $_SESSION['user'], "analisado" => true),array('_id'))->sort(array('data' => -1))->limit($analisar_limit)->skip($analisar_skip);
		$informado = DB::$solicitacao->find(array("tipo" => array('$in' => $t_informado), "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit($informado_limit)->skip($informado_skip);
		include 'templates/participacao.php';
	}

	/**
	 * GET /nova
	 */
	public function nova_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if(isset($_REQUEST['detailOf'])){
			$t_id = new MongoId($_REQUEST['detailOf']);
			$t = DB::$tipo->findOne(array('_id' => $t_id));
			include 'templates/novo.detailOf.php';
		}else{
			$_TITLE = "Nova solicitação";
			include 'templates/novo.php';
		}
	}

	/**
	 * POST /nova
	 */
	public function nova_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		# Salvar a solicitação como rascunho e redirecionar para a visualização
		if(!isset($_POST) ||
			(!isset($_POST['tipo']) && !empty($_POST['tipo'])) || 
			(!isset($_POST['descricao']) && !empty($_POST['descricao'])) || 
			(!isset($_POST['centro']) && !empty($_POST['centro'])) || 
			(!isset($_POST['item']) && !empty($_POST['item']))){
			$_SESSION['flash'] = "Preencha as informações básicas da solicitação e adicione pelo menos 1 detalhe.";
			header("Location: " . SITE_BASE ."nova");
		}else{
			$items = array();
			foreach($_POST['item'] as $item) {			
				$item_empty = true;
				foreach($item as $col => $v){
					if(!is_null($v) && (is_numeric($v) || !empty($v))){
						$item_empty = false;
					}
				}
				if(!$item_empty){
					$items[] = $item;
				}
			}
			if(count($items) == 0){
				$_SESSION['flash'] = "Adicione pelo menos 1 detalhe.";
				header("Location: " . SITE_BASE ."nova");
			} else {
				$centro = DB::$centro->findOne(array('apelido' => (int)$_POST['centro']),array('_id'));
				if(!$centro){
					$_SESSION['flash'] = "O centro de custos informado selecionado não é válido.";
					header("Location: " . SITE_BASE ."nova");
				}else{
					$s = Solicitacao::create($_SESSION['user'],$_POST['tipo'],$_POST['descricao'],$_POST['prazo'],(string)$centro['_id']);
					foreach($items as $item) {
						$s->saveDetail($item);
					}
					header("Location: " . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET|POST /hub
	 */
	public function hub_all(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$oinc = intval($_REQUEST['oinc']);
		$act = isset($_REQUEST['act']) ? '/' . $_REQUEST['act'] : '';

		$s = DB::$solicitacao->findOne(array('numero' => $oinc),array('_id'));
		header('Location: ' . SITE_BASE . $s['_id'] . $act);
	}

	/**
	 * GET /[ID]
	 */
	public function solicitacao_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canView($_SESSION['user'])){
				$_CAN_VIEW = false;
			}else{
				$_CAN_VIEW = true;
				if(isset($_REQUEST['a']) && $_REQUEST['a'] == 'delete'){
					$s->removeFile(new MongoId($_REQUEST['fid']), $_SESSION['user']);
					$_SESSION['flash'] = "Arquivo excluído!";
					header("Location: " . SITE_BASE . $s->id);
				}else{
					$_TITLE = $s->tipo['nome'] . " #" . $s->numero;
				}
			}
			include 'templates/solicitacao.php';
		}		
	}

	/**
	 * POST /[ID]
	 */
	public function solicitacao_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if($_REQUEST['a'] == 'attach'){
				if($s->canAttach($_SESSION['user'])){
					if ($_FILES['file']['error'] != 0) {
						$_SESSION['flash'] = 'Selecione um arquivo para anexar';
					} else {
						$s->attachFile('file',$_SESSION['user']);
						$_SESSION['flash'] = "Arquivo anexado!";
					}
					header("Location: " . SITE_BASE . $s->id);
				}
			}elseif($_REQUEST['a'] == 'observe'){
				if($s->canObserve($_SESSION['user'])){
					if(!isset($_POST['text']) || empty($_POST['text'])){
						$_SESSION['flash'] = "A observação estava em branco, portanto não foi adicionada";
					}else{
						$s->observe($_SESSION['user'],$_POST['text']);
						$_SESSION['flash'] = "Observação adicionada!";
					}
					header("Location: " . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID]/editar
	 */
	public function editar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canEdit($_SESSION['user'])){
				$_SESSION['flash'] = 'Você não pode editar essa solicitação';
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if(isset($_GET['remove'])){
					$item_id = new MongoId($_GET['remove']);
					$s->removeDetail($item_id);
					header('Location: ' . SITE_BASE . $s->id . '/editar');
				}else{
					$_TITLE = "Editar " . $s->tipo['nome'] . " #" . $s->numero;
					include 'templates/editar.php';
				}
			}
		}
	}

	/**
	 * POST /[ID]/editar
	 */
	public function editar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!isset($_POST) ||
				(!isset($_POST['descricao']) && !empty($_POST['descricao'])) || 
				(!isset($_POST['item']) && !empty($_POST['item']))){
				$_SESSION['flash'] = "Preencha as informações básicas da solicitação e adicione pelo menos 1 detalhe.";
				header("Location: " . SITE_BASE ."nova");
			}else{
				# Salvar as alterações na solicitação
				$descricao = $_POST['descricao'];
				$prazo = $_POST['prazo'];

				$s->descricao = $descricao;
				$s->prazo = $prazo;
				$s->save();

				# Salvar os itens da solicitação
				$items = array();
				foreach($_POST['item'] as $item) {
					$item_empty = true;
					foreach($item as $col => $v){
						if(!is_null($v) && (is_numeric($v) || !empty($v))){
							$item_empty = false;
						}
					}
					if(!$item_empty){
						$items[] = $item;
					}
				}
				$c_items = count($items);
				foreach ($items as $item) {
					if(isset($item['id'])){
						if(isset($item['remove'])){
							if($c_items > 1){
								DB::$item->remove(array('_id' => new MongoId($item['id'])));
								$c_items--;
							}else{
								$_SESSION['flash'] = 'É necessário pelo menos um detalhe na solicitação';
							}
						}else{
							$o_item = DB::$item->findOne(array('_id' => new MongoId($item['id'])));
							foreach ($item as $column => $value) {
								if($column != '_id'){
									$o_item[$column] = $value;
								}
							}
							DB::$item->save($o_item);
						}
					}else{
						$item_empty = true;
						foreach($item as $col => $v){
							if(!is_null($v) && (is_numeric($v) || !empty($v))){
								$item_empty = false;
							}
						}
						if(!$item_empty){
							$s->saveDetail($item);
						}
					}
				}
				if($c_items == 0){
					$_SESSION['flash'] = 'É necessário pelo menos um detalhe na solicitação';
				}
				if(!isset($_SESSION['flash'])){
					$_SESSION['flash'] = "Alterações salvas!";
				}
				# Avisar sobre a edição
				if($s->status[0] == 4){
					if(!isset($_SESSION['flash'])){
						$_SESSION['flash'] = "Revisão salva!";
					}
					$s->update();
				}
				header('Location: ' . SITE_BASE . $s->id);
			}
		}
	}

	/**
	 * GET /[ID]/enviar
	 */
	public function enviar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isSolicitante($_SESSION['user'])){
				$_SESSION['flash'] = 'Você não pode enviar essa solicitação';
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = "Enviar " . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/enviar.php';
			}
		}
	}

	/**
	 * POST /[ID]/enviar
	 */
	public function enviar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isSolicitante($_SESSION['user'])){
				$_SESSION['flash'] = 'Você não pode enviar essa solicitação';
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$s->send();
				$_SESSION['flash'] = 'A sua solicitação foi enviada!';
				header('Location: ' . SITE_BASE . $s->id);
			}
		}
	}

	/**
	 * GET /[ID]/cotar
	 */
	public function cotar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isCotador($_SESSION['user']) || $s->status[0] != 5){
				$_SESSION['flash'] = "Você não pode cotar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = 'Cotar a ' . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/cotar.php';
			}
		}
	}

	/**
	 * POST /[ID]/cotar
	 */
	public function cotar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isCotador($_SESSION['user']) || $s->status[0] != 5){
				$_SESSION['flash'] = "Você não pode cotar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if ($_FILES['file']['error'] != 0) {
					$_SESSION['flash'] = 'Selecione um arquivo para anexar';
					header('Location: ' . SITE_BASE . $s->id . '/cotar');
				}else{
					if(isset($_POST['text']) && !empty($_POST['text'])){
						$s->observe($_SESSION['user'],$_POST['text']);
					}	
					$s->quote('file',$_SESSION['user']);		
					$_SESSION['flash'] = 'A cotação foi anexada!';
					header('Location: ' . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID]/analisar
	 */
	public function analisar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isAnalisador($_SESSION['user']) || $s->status[0] != 6){
				$_SESSION['flash'] = "Você não pode analisar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = 'Analisar a ' . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/analisar.php';
			}
		}
	}

	/**
	 * POST /[ID]/analisar
	 */
	public function analisar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->isAnalisador($_SESSION['user']) || $s->status[0] != 6){
				$_SESSION['flash'] = "Você não pode analisar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if(!isset($_POST['resposta']) || empty($_POST['resposta']) || !in_array($_POST['resposta'], array("yes","no")) ){
					$_SESSION['flash'] = 'Escolha uma das opções de resposta';
					header('Location: ' . SITE_BASE . $s->id . '/analisar');
				}else{
					if(!isset($_POST['text']) || empty($_POST['text'])){
						$_SESSION['flash'] = 'Complemente a sua resposta';
						header('Location: ' . SITE_BASE . $s->id . '/analisar');
					}else{
						if(!in_array($_POST['resposta'], array('yes','no'))) {
							$_SESSION['flash'] = 'Escolha uma das opções de resposta';
							header('Location: ' . SITE_BASE . $s->id . '/analisar');
						}else{
							if($_POST['resposta'] == 'yes'){
								$problem = true;
							} elseif ($_POST['resposta'] == 'no') {
								$problem = false;
							}
							$s->analyze($problem, $_POST['text']);
							$_SESSION['flash'] = 'A sua análise foi registrada!';
							header('Location: ' . SITE_BASE . $s->id);
						}
					}
				}
			}
		}
	}

	/**
	 * GET /[ID]/decidir
	 */
	public function decidir_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canDecide($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode tomar um decisão para essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = "Decidir sobre a " . $s->tipo['nome'] . " #" . $s->numero;
				$a = "decidir";
				if(isset($_REQUEST['a']) && !empty($_REQUEST['a'])){
					$a = $_REQUEST['a'];
					switch ($_REQUEST['a']) {
						case 'aprovar':
							if($s->pre_aprovado && $s->isAprovador($_SESSION['user'])){
								$s->approve($_SESSION['user']);
							}else{
								$s->preApprove($_SESSION['user']);
							}
							$_TITLE = $s->tipo['nome'] . " #" . $s->numero . " aprovada!";
							$_SESSION['flash'] = $_TITLE;
							break;
						case 'recusar':
							$s->refuse($_SESSION['user']);
							$_TITLE = $s->tipo['nome'] . " #" . $s->numero . " recusada!";
							$_SESSION['flash'] = $_TITLE;
							break;
						case 'devolver':
							$s->devolve($_SESSION['user']);
							$_TITLE = $s->tipo['nome'] . " #" . $s->numero . " devolvida para revisão!";
							$_SESSION['flash'] = $_TITLE;
							break;
						case 'analisar':
							$_TITLE = "Enviar para análise: " . $s->tipo['nome'] . " #" . $s->numero;
							break;
						default: $a = "decidir"; break;
					}
				}
				include 'templates/decidir.php';
			}
		}
	}

	/**
	 * POST /[ID]/decidir
	 */
	public function decidir_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canDecide($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode tomar um decisão para essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$a = $_REQUEST['a'];
				if($a == 'analisar'){
					if(isset($_POST['especialista']) && !empty($_POST['especialista'])){
						if(isset($_REQUEST['text']) && !empty($_REQUEST['text'])){
							$s->observe($_SESSION['user'],$_REQUEST['text']);
						}						
						$s->sendToAnalysis($_POST['especialista'],$_SESSION['user']);
						$_TITLE = $s->tipo['nome'] . " #" . $s->numero . " enviada para análise!";
						include 'templates/decidir.php';						
					}else{
						$_SESSION['flash'] = "Escolha um especialista";
						header('Location: ' . SITE_BASE . $s->id . '/decidir?a=analisar');
					}
				}elseif($a == 'observe'){
					# Adicionar observação
					if(isset($_REQUEST['text']) && !empty($_REQUEST['text'])){
						$s->observe($_SESSION['user'],$_REQUEST['text']);
						if(!isset($_SESSION['flash'])){
							$_SESSION['flash'] = "O";
						}else{
							$_SESSION['flash'] .= "<br>E uma o";
						}
						$_SESSION['flash'] .= "bservação adicionada!";	
					}
					header("Location: " . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID]/encaminhar
	 */
	public function encaminhar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canRefer($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode encaminhar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = 'Encaminhar a solicitação';
				switch ($s->status[0]) {
					case 1: $acao = "aprovação";break;
					case 5: $acao = "cotação";break;
					case 6: $acao = "análise";break;
					case 2: $acao = "execução";break;
				}
				include 'templates/forward.php';
			}
		}
	}

	/**
	 * POST /[ID]/encaminhar
	 */
	public function encaminhar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canRefer($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode encaminhar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if(!isset($_POST['to']) || empty($_POST['to'])){
					$_SESSION['flash'] = "Digite o usuário para quem vai encaminhar a solicitação";
					header('Location: ' . SITE_BASE . $s->id . '/encaminhar');
				}else{
					$s->refer(trim($_POST['to']),(!isset($_POST['text']) || empty($_POST['text']) ? false : $_POST['text']),$_SESSION['user']);
					$_SESSION['flash'] = "A solicitação foi encaminhada!";
					header('Location: ' . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID]/excluir
	 */
	public function excluir_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canDelete($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode excluir essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = "Excluir " . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/excluir.php';
			}
		}
	}

	/**
	 * POST /[ID]/excluir
	 */
	public function excluir_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canDelete($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode excluir essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$ok = $_REQUEST['ok'];
				if($ok == 0){
					$s->delete();
					$_SESSION['flash'] = "A solicitação foi excluída";
					header('Location: ' . SITE_BASE . 'todas');
				}else{
					header('Location: ' . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID]/imprimir
	 */
	public function imprimir_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canView($_SESSION['user'])){
				$_SESSION['flash'] = 'Você não pode imprimir a solicitação';
				header('Location: ' . SITE_BASE . 'todas');
			}else{
				$_TITLE = "Imprimir " . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/print.php';
			}
		}
	}

	/**
	 * GET /[ID]/executar
	 */
	public function executar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->status[0] == 2 || !$s->isExecutor($_SESSION['user']  || $s->executado)){
				$_SESSION['flash'] = "Você não pode executar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = 'Executar a solicitação';
				include 'templates/run.php';
			}
		}
	}

	/**
	 * POST /[ID]/executar
	 */
	public function executar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->status[0] == 2 || !$s->isExecutor($_SESSION['user']  || $s->executado)){
				$_SESSION['flash'] = "Você não pode executar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if(isset($_POST['text']) && !empty($_POST['text'])){
					$s->observe($_SESSION['user'],$_POST['text']);
				}
				$s->run($_SESSION['user']);
				$_SESSION['flash'] = "A execução foi confirmada!";
				header('Location: ' . SITE_BASE . $s->id);
			}
		}
	}

	/**
	 * GET /[ID]/cancelar
	 */
	public function cancelar_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canCancel($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode cancelar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				$_TITLE = 'Cancelar a ' . $s->tipo['nome'] . " #" . $s->numero;
				include 'templates/cancel.php';
			}
		}
	}

	/**
	 * POST /[ID]/cancelar
	 */
	public function cancelar_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			if(!$s->canCancel($_SESSION['user'])){
				$_SESSION['flash'] = "Você não pode cancelar essa solicitação";
				header('Location: ' . SITE_BASE . $s->id);
			}else{
				if(!isset($_POST['text']) || empty($_POST['text'])){
					$_SESSION['flash'] = "Informe porque a solicitação está sendo cancelada";
					header('Location: ' . SITE_BASE . $s->id . '/cancelar');
				}else{
					$s->cancel($_SESSION['user'], $_POST['text']);

					$_SESSION['flash'] = 'A solicitação foi cancelada.';
					header('Location: ' . SITE_BASE . $s->id);
				}
			}
		}
	}

	/**
	 * GET /[ID].qr
	 */
	public function qr_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$s = new Solicitacao($_REQUEST['oid']);
		if(!$s){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			include 'qrcode/qrlib.php';
			$url = SITE_PATH . $s->id;
			QRcode::png($url,false,5,2,true);
		}
	}

	/**
	 * GET /bemvindo
	 */
	public function bemvindo_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if(!$_SESSION['IS_FIRST_ACCESS'] && !$_SESSION['IS_FIRST_ON_NEW']){
			header("Location: " . SITE_BASE ."perfil");
		}else{
			$_TITLE = "Bem vindo!";
			$info = AD::info($_SESSION['user'],array('displayname','manager','department','telephonenumber','mobile','title'));
			$manager = $info[0]['manager'][0];
			$virg_pos = stripos($manager, ",");
			$manager = substr($manager, 3, $virg_pos-3);
			$manager = AD::$ad->user()->all(false,$manager);
			$manager = $manager[0];
			$uinfo = array(
				"displayname" => $info[0]['displayname'][0],
				"manager" => $manager,
				"department" => $info[0]['department'][0],
				"telephone" => $info[0]['telephonenumber'][0],
				"mobile" => $info[0]['mobile'][0],
				"title" => $info[0]['title'][0]
			);
			include 'templates/welcome.php';
		}
	}

	/**
	 * POST /bemvindo
	 */
	public function bemvindo_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if(!$_SESSION['IS_FIRST_ACCESS'] && !$_SESSION['IS_FIRST_ON_NEW']){
			header("Location: " . SITE_BASE ."perfil");
		}else{
			if(empty($_REQUEST['gerente']) || empty($_REQUEST['departamento']) || empty($_REQUEST['telefone']) || empty($_REQUEST['celular']) || empty($_REQUEST['cargo'])){
				$_SESSION['flash'] = "Todas essas informações são necessárias para continuar";
				header('Location: ' . $_SERVER['REQUEST_URI']);
			}else{
				//$authUser = AD::$ad->user()->authenticate(ADMIN_USERNAME, ADMIN_PASSWORD);
				$user_info = AD::info($_SESSION['user'],array('manager','department','telephone','mobile','title'));
				$user_manager = $user_info[0]['manager'][0];
				$user_department = $user_info[0]['department'][0];
				$user_telephone = $user_info[0]['telephone'][0];
				$user_mobile = $user_info[0]['mobile'][0];
				$user_title =  $user_info[0]['title'][0];

				$manager_info = AD::info($_REQUEST['gerente'],array('distinguishedname'));
				$manager = $manager_info[0]['distinguishedname'][0];
				$department = $_REQUEST['departamento'];
				$telephone = $_REQUEST['telefone'];
				$mobile = $_REQUEST['celular'];
				$title = $_REQUEST['cargo'];

				$modify = array();
				if($manager != $user_manager){
					$modify['manager'] = $manager;
				}
				if($department != $user_department){
					$modify['department'] = $department;
				}
				if($telephone != $user_telephone){
					$modify['telephone'] = $telephone;
				}
				if($mobile != $user_mobile){
					$modify['mobile'] = $mobile;
				}
				if($title != $user_title){
					$modify['title'] = $title;
				}
				if(count($modify)>0){
					AD::$ad->user()->modify($_SESSION['user'],$modify);
					DB::$access_log->save(array(
						"user" => $_SESSION['user'],
						"data" => new MongoDate(),
						"ip" => $_SERVER['REMOTE_ADDR']
					));
					$url = isset($_REQUEST['u']) ? urldecode($_REQUEST['u']) : 'todas';
					$_SESSION['IS_FIRST_ACCESS'] = false;
					header("Location: " . $url);
				}
			}
		}
	}

	/**
	 * GET /perfil
	 */
	public function perfil_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if(isset($_SESSION['IS_FIRST_ACCESS']) && $_SESSION['IS_FIRST_ACCESS']) {
			header("Location: " . SITE_BASE ."welcome");
		}else{
			$_TITLE = "Perfil";
			$user = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : $_SESSION['user'];
			$info = AD::info($user,array('displayname','manager','department','telephonenumber','mobile','title'));
			$manager = $info[0]['manager'][0];
			$virg_pos = stripos($manager, ",");
			$manager = substr($manager, 3, $virg_pos-3);
			$manager = AD::$ad->user()->all(false,$manager);
			$manager = $manager[0];
			$uinfo = array(
				"displayname" => $info[0]['displayname'][0],
				"manager" => $manager,
				"department" => $info[0]['department'][0],
				"telephone" => $info[0]['telephonenumber'][0],
				"mobile" => $info[0]['mobile'][0],
				"title" => $info[0]['title'][0]
			);
			/* ESTATÍSTICAS DO USUÁRIO */

			# Solicitações
			$solicitacoes = array();
			$solicitacoes["rascunho"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 0));
			$solicitacoes["pendente"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 1));
			$solicitacoes["aprovada"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 2));
			$solicitacoes["recusada"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 3));
			$solicitacoes["retornada"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 4));
			$solicitacoes["cotando"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 5));
			$solicitacoes["analise"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 6));
			$solicitacoes["executada"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 7));
			$solicitacoes["cancelada"] = DB::$solicitacao->count(array("solicitante" => $user, "status" => 8));
			$solicitacoes["total"] = $solicitacoes["rascunho"] + $solicitacoes["pendente"] + $solicitacoes["aprovada"] + $solicitacoes["recusada"] + $solicitacoes["retornada"] + $solicitacoes["cotando"] + $solicitacoes["analise"] + $solicitacoes["executada"] + $solicitacoes["cancelada"];

			# Acessos
			$acessos = DB::$access_log->count(array("user" => $user));
			$dt_0 = strtotime("today");		# Hoje
			$dt_1 = strtotime("yesterday");	# Ontem
			$dt_2 = strtotime("2 day ago");	# Dois dias atrás
			$dt_3 = strtotime("3 day ago");	# Três dias atrás
			$dt_4 = strtotime("4 day ago");	# Quatro dias atrás
			$dt_5 = strtotime("5 day ago");	# Cinco dias atrás
			$dt_6 = strtotime("6 day ago");	# Seis dias atrás
			$dt_7 = strtotime("tomorrow");	# Amanhã

			$acessos_0 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_0), '$lt' => new MongoDate($dt_7))));
			$acessos_1 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_1), '$lt' => new MongoDate($dt_0))));
			$acessos_2 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_2), '$lt' => new MongoDate($dt_1))));
			$acessos_3 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_3), '$lt' => new MongoDate($dt_2))));
			$acessos_4 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_4), '$lt' => new MongoDate($dt_3))));
			$acessos_5 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_5), '$lt' => new MongoDate($dt_4))));
			$acessos_6 = DB::$access_log->count(array("user" => $user, "data" => array('$gte' => new MongoDate($dt_6), '$lt' => new MongoDate($dt_5))));

			$ips = DB::$db->command(array('distinct' => 'access_log', 'key' => 'ip', 'query' => array("user" => $user)));
			$total_ips = array();
			foreach ($ips['values'] as $ip) {
				$total_ips[$ip] = DB::$access_log->count(array('ip' => $ip,"user" => $user));
			}

			# Satisfação
			$feedbacks = DB::$feedback->find(array("user" => $user));
			$feedback_counts = array(
				"error" => 0,
				"suggestion" => 0,
				"complaint" => 0,
				"compliment" => 0,
				"other" => 0
			);
			$rating_count = 0; $rating_total = 0; $rating_avg = 0;
			foreach ($feedbacks as $f) {
				if(isset($f['rating'])){
					$rating_count ++;
					$rating_total += $f['rating'];
				}
				
				if(!isset($f['type'])) {
					$f['type'] = 'other';
				}

				switch ($f['type']) {
					case 'error': 
						$feedback_counts['error'] += 1; 
						break;
					case 'suggestion': 
						$feedback_counts['suggestion'] += 1;
						break;
					case 'complaint': 
						$feedback_counts['complaint'] += 1;
						break;
					case 'compliment': 
						$feedback_counts['compliment'] += 1;
						break;
					case 'other': 
						$feedback_counts['other'] += 1;
						break;
				}
			}
			$rating_avg = $rating_count > 0 ? round($rating_total/$rating_count,1) : 0;
			#$rating_avg = rand(-20,20)/10; # Aleatório para testar
			if ($rating_avg > 1) {
				$rating = 2;
			} elseif ($rating_avg > 0) {
				$rating = 1;
			} elseif ($rating_avg > -1) {
				$rating = 0;
			} elseif ($rating_avg <= -1) {
				$rating = -2;
			} else {
				$rating = -1;
			}
			$rating_map = array(
				2 => array("title" => "Muito feliz.", "desc" => "Está facilitando muito o meu trabalho."),
				1 => array("title" => "Feliz.", "desc" => "Está ajudando um pouco no meu trabalho."),
				0 => array("title" => "Indiferente.", "desc" => "Não mudou nada para mim."),
				-1 => array("title" => "Triste.", "desc" => "Está me atrapalhando um pouco."),
				-2 => array("title" => "Muito triste.", "desc" => "Está me atrapalhando muito, ou até me impedindo de trabalhar.")
			);
			include 'templates/profile.php';
		}
	}

	/**
	 * POST /perfil
	 */
	public function perfil_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if($_SESSION['IS_FIRST_ACCESS']){
			header("Location: " . SITE_BASE ."welcome");
		}else{
			$user = isset($_REQUEST['user']) && !empty($_REQUEST['user']) ? $_REQUEST['user'] : $_SESSION['user'];
			if(empty($_REQUEST['gerente']) || empty($_REQUEST['departamento']) || empty($_REQUEST['telefone']) || empty($_REQUEST['celular']) || empty($_REQUEST['cargo'])){
				$_SESSION['flash'] = "Todas essas informações são necessárias para continuar";
				header('Location: ' . $_SERVER['REQUEST_URI']);
			}else{
				//$authUser = AD::$ad->user()->authenticate(ADMIN_USERNAME, ADMIN_PASSWORD);
				$user_info = AD::info($user,array('manager','department','telephone','mobile','title'));
				$user_manager = $user_info[0]['manager'][0];
				$user_department = $user_info[0]['department'][0];
				$user_telephone = $user_info[0]['telephone'][0];
				$user_mobile = $user_info[0]['mobile'][0];
				$user_title =  $user_info[0]['title'][0];

				$manager_info = AD::info($_REQUEST['gerente'],array('distinguishedname'));
				$manager = $manager_info[0]['distinguishedname'][0];
				$department = $_REQUEST['departamento'];
				$telephone = $_REQUEST['telefone'];
				$mobile = $_REQUEST['celular'];
				$title = $_REQUEST['cargo'];

				$modify = array();
				if($manager != $user_manager){
					$modify['manager'] = $manager;
				}
				if($department != $user_department){
					$modify['department'] = $department;
				}
				if($telephone != $user_telephone){
					$modify['telephone'] = $telephone;
				}
				if($mobile != $user_mobile){
					$modify['mobile'] = $mobile;
				}
				if($title != $user_title){
					$modify['title'] = $title;
				}
				if(count($modify)>0){
					AD::$ad->user()->modify($user,$modify);
					$url = isset($_REQUEST['u']) ? urldecode($_REQUEST['u']) : 'todas';
					header("Location: " . $url);
				}
			}
		}
	}

	/**
	 * GET /feedback
	 */
	public function feedback_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$_TITLE = "Como está a sua experiência?";
		include 'templates/feedback.php';
	}

	/**
	 * POST /feedback
	 */
	public function feedback_post(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		if((!isset($_POST['message']) || empty($_POST['message'])) && (!isset($_POST['rating']) || empty($_POST['rating']))){
			$_SESSION['flash'] = "Escreva uma mensagem ou escolha a sua satisfação com a ferramenta";
			header('Location: ' . SITE_BASE . 'feedback');
		}else{
			$fb = array("user" => $_SESSION['user'], "data" => new MongoDate());
			if(isset($_POST['message']) || !empty($_POST['message'])){
				$fb['message'] = $_POST['message'];
			}
			if(isset($_POST['message-type']) || !empty($_POST['message-type'])){
				$fb['type'] = $_POST['message-type'];
			}
			if(isset($_POST['rating']) || !empty($_POST['rating'])){
				$fb['rating'] = $_POST['rating'];
			}
			# Registrar o feedback
			DB::$feedback->save($fb);
			# Avisar o(s) administrador(es)
			$uinfo = AD::info($_SESSION['user'],array('displayname'));
			$from = array('email' => $_SESSION['user'] . ADLDAP_ACCOUNT_SUFFIX, 'name' => $uinfo[0]['displayname'][0]);
			
			$admins = DB::$roles->find(array("role" => array('$gt' => 0)));
			$to = array();
			foreach ($admins as $u) {
				$to[] = $u['user'] . ADLDAP_ACCOUNT_SUFFIX;
			}
			$types = array(
				'error' => array('name' => 'erro', 'subject' => 'reportou um erro'),
				'suggestion' => array('name' => 'sugestão', 'subject' => 'enviou uma sugestão'),
				'complaint' => array('name' => 'reclamação', 'subject' => 'fez uma reclamação'),
				'compliment' => array('name' => 'elogio', 'subject' => 'fez um elogio'),
				'other' => array('name' => 'outro', 'subject' => 'enviou um feedback')
			);
			$subject = "[FEEDBACK] " . $uinfo[0]['displayname'][0] . " " . $types[$fb['type']]['subject'];
			
			$body = "<html><style>div,span,td,th,strong,p{font-family: Helvetica, Arial, sans-serif;font-size:12px;}hr {border: none;border-bottom: solid 1px #000000;margin-bottom: 10px;}</style><body><h2>Feedback da central de solicitações</h2><p>" . $uinfo[0]['displayname'][0] . " enviou um feedback do tipo '" . $types[$fb['type']]['name'] . "'!</p>";
			if(isset($fb['message'])){
				$body .= "<p><b>Mensagem:</b><blockquote>" . $fb['message'] . "</blockquote></p>";
			}
			if(isset($fb['rating'])){
				$rating_textual = array(
						2 => '<span style="font-size:18px;font-weight:bold;color:#0000FF">Muito feliz</span>',
						1 => '<span style="font-size:18px;font-weight:bold;color:#0096FF">Feliz</span>',
						0 => '<span style="font-size:18px;font-weight:bold;color:#000000">Indiferente</span>',
						-1 => '<span style="font-size:18px;font-weight:bold;color:#FF6400">Triste</span>',
						-2 => '<span style="font-size:18px;font-weight:bold;color:#FF0000">Muito triste</span>'
				);
				$body .= "<p><b>Avaliação da experiência:</b> " . $rating_textual[$fb['rating']] . ".</p>";
			}
			
			$cc = false;
			
			EMAIL::send($from, $to, $subject, $body, $cc);

			$_TITLE = "Obrigado!";
			include 'templates/feedback.php';
		}
	}

	/**
	 * GET /solicitacoes
	 */
	public function solicitacoes_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$status = $_REQUEST['status'];
		$s = -1;
		$query = null;
		switch ($status) {
			case 'all':
				$_TITLE = "Minhas solicitações";
				$query = array('solicitante' => $_SESSION['user']);
				break;
			case 'rascunhos':
				$_TITLE = "Meus rascunhos de solicitação";
				$s = 0;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 0);
				break;
			case 'pendentes':
				$_TITLE = "Minhas solicitações pendentes";
				$s = 1;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 1);
				break;
			case 'cotando':
				$_TITLE = "Minhas solicitações em cotação";
				$s = 5;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 5);
				break;
			case 'analisando':
				$_TITLE = "Minhas solicitações em análise";
				$s = 6;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 6);
				break;
			case 'retornadas':
				$_TITLE = "Minhas solicitações retornadas";
				$s = 4;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 4);
				break;
			case 'encaminhadas':
				$_TITLE = "Minhas solicitações encaminhadas";
				$s = 7;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 7);
				break;
			case 'aprovadas':
				$_TITLE = "Minhas solicitações aprovadas";
				$s = 2;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 2, 'executado' => false);
				break;
			case 'recusadas':
				$_TITLE = "Minhas solicitações recusadas";
				$s = 3;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 3);
				break;
			case 'executadas':
				$_TITLE = "Minhas solicitações executadas";
				$s = 7;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 7, 'executado' => true);
				break;
			case 'canceladas':
				$_TITLE = "Minhas solicitações canceladas";
				$s = 8;
				$query = array('solicitante' => $_SESSION['user'], 'status' => 8);
				break;
			
			default:
				$_TITLE = "Minhas solicitações";
				$query = array('solicitante' => $_SESSION['user']);
				break;
		}
		$page = isset($_GET['p']) ? $_GET['p'] : 1;
		$limit = isset($_GET['page_size']) ? $_GET['page_size'] : 20;
		$skip = ($page - 1) * $limit;
		$total = DB::$solicitacao->count($query);
		$pages = ceil($total/$limit);
		$list = DB::$solicitacao->find($query,array('_id'))->sort(array('data' => -1))->limit($limit)->skip($skip);
		include 'templates/solicitacoes.php';
	}

	/**
	 * GET /d/[ID]
	 */
	public function download_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$file = DB::$grid->findOne(array('_id' => new MongoId($_REQUEST['fid'])));
		if(!$file){
			header(':', true, 404);
			include 'templates/404.php';
		}else{
			header('Content-Type: ' . $file->file['contentType']);
			header('Content-Disposition: attachment; filename='.$file->getFilename()); 
			header('Content-Transfer-Encoding: binary');
			echo $file->getBytes();
		}
	}

	/**
	 * GET /novidades
	 */
	public function novidades_get(){
		global $section;
		global $status_mapping;
		global $verbal_status;
		$_TITLE = "Novidades";
		# Gravar o primeiro acesso na nova versão
		if($_SESSION['IS_FIRST_ON_NEW']){
			$is_first_on_new = DB::$db->newversion->save(array('user' => $_SESSION['user'], 'version' => CENTRAL_CURRENT_VERSION));
			$_SESSION['IS_FIRST_ON_NEW'] = false;
		}
		include 'templates/novidades.php';
	}

	/**
	 * GET|POST /admin
	 */
	public function admin_all(){
		global $section;
		global $status_mapping;
		global $verbal_status;

		$role = DB::$roles->findOne(array("user" => $_SESSION['user']));
		if(!$role){
			$_SESSION['flash'] = "Você não tem permissão para acessar a administração";
			header('Location: ' . SITE_BASE . 'todas');
		} else {
			# Incluindo as ṽiews de administração
			require_once 'Views.admin.php';
			$rolesmap = array(
				3 => array("description" => "super usuário", "access_description" => "total à central de solicitações e suas configurações"),
				1 => array("description" => "desenvolvedor", "access_description" => "aos logs, feedbacks e configurações de SMTP e Active Directory"),
				0 => array("description" => "gerente", "access_description" => "às estatísicas e relatórios"),
				2 => array("description" => "administrador", "access_description" => "a todas as opções de administração, exceto a edição manual de solicitações"),
			);
			$ratings_map = array(
				2 => array("class" => "rat2", "title" => "Muito Feliz"),
				1 => array("class" => "rat1", "title" => "Feliz"),
				0 => array("class" => "rat0", "title" => "Indiferente"),
				-1 => array("class" => "rat-1", "title" => "Triste"),
				-2 => array("class" => "rat-2", "title" => "Muito Triste")
			);
			$titles_map = array(
				"dashboard" => "Dashboard",

				"tipos" => "Tipos de solicitação",
				"especialistas" => "Especialistas",
				"centros" => "Centros de custo",
				"relatorios" => "Relatórios",
				"feedbacks" => "Feedbacks",
				"logs" => "Logs de acesso",
				"all" => "Todas as solicitações",
				
				"org" => "Dados da organização",
				"env" => "Ambiente do sistema",
				"smtp" => "Email SMTP",
				"ldap" => "Active Directory&trade;",
				"mongo" => "Banco de dados MongoDB&trade;",
				"regras" => "Regras de usuários",
				"backup" => "Backup e restauração",
				"status" => "Alterar status da solicitação",
				"editar" => "Editar uma solicitação",
				"apps" => "Aplicativos OAuth"
			);
			
			$action = $_REQUEST['admin_action'];
			$_TITLE = $titles_map[$action] . ' - Administração';
			$D = array();

			$admin_views = new AdminViews();
			if(IS_POST){
				$append = "_post";
			}else{
				$append = "_get";
			}
			$action_name = $action . $append;
			if(!method_exists($admin_views, $action_name)){
				$action_name = $action . "_all";
			}
			if(!method_exists($admin_views, $action_name)){
				$_SESSION['IS_ERROR'] = true;
				header(':', true, 404);
				require_once(dirname(__FILE__) . '/templates/404.php');
			}else{
				$D = $admin_views->$action_name();
				/*echo '<pre>';
				print_r($D);
				echo '</pre>';*/
				include 'templates/admin/' . $action . '.php';
			}
		}
	}

}
?>
