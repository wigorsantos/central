<?php
class AdminViews {
	/*
	 * GET /admin/
	 */
	public function dashboard_get(){
		$D = array();

		## Quantidade de solicitações
		# Por status
		$D['by_status'] = array(
			'pendentes' => DB::$solicitacao->count(array('status' => 1)),
			'aprovadas' => DB::$solicitacao->count(array('status' => 2)),
			'recusadas' => DB::$solicitacao->count(array('status' => 3)),
			'retornadas' => DB::$solicitacao->count(array('status' => 4)),
			'cotando' => DB::$solicitacao->count(array('status' => 5)),
			'analise' => DB::$solicitacao->count(array('status' => 6)),
			'executadas' => DB::$solicitacao->count(array('status' => 7)),
			'canceladas' => DB::$solicitacao->count(array('status' => 8))
 		);
 		# Por tipo
 		$tipos = DB::$tipo->find(array(),array('_id','nome'));
 		$D['by_type'] = array();
 		foreach ($tipos as $tipo) {
 			$D['by_type'][$tipo['nome']] = DB::$solicitacao->count(array('tipo.$id' => $tipo['_id'], 'status' => array('$gt' => 0)));
 		}
 		# Por centro de custo
 		$centros = DB::$centro->find(array(),array('_id','apelido'));
 		$D['by_centro'] = array();
 		foreach ($centros as $centro) {
 			$D['by_centro'][$centro['apelido']] = DB::$solicitacao->count(array('centro.$id' => $centro['_id'], 'status' => array('$gt' => 0)));
 		}

 		## Acesso ao sistema
 		# Histórico
 		$dt_0 = strtotime("today");		# Hoje
 		$D['history']['dt'][0] = $dt_0;
		$dt_1 = strtotime("yesterday");	# Ontem
		$D['history']['dt'][1] = $dt_1;
		$dt_2 = strtotime("2 day ago");	# Dois dias atrás
		$D['history']['dt'][2] = $dt_2;
		$dt_3 = strtotime("3 day ago");	# Três dias atrás
		$D['history']['dt'][3] = $dt_3;
		$dt_4 = strtotime("4 day ago");	# Quatro dias atrás
		$D['history']['dt'][4] = $dt_4;
		$dt_5 = strtotime("5 day ago");	# Cinco dias atrás
		$D['history']['dt'][5] = $dt_5;
		$dt_6 = strtotime("6 day ago");	# Seis dias atrás
		$D['history']['dt'][6] = $dt_6;
		$dt_7 = strtotime("tomorrow");	# Amanhã
		$D['history']['dt'][7] = $dt_7;
		$D['history']['qt'][0] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_0), '$lt' => new MongoDate($dt_7))));
		$D['history']['qt'][1] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_1), '$lt' => new MongoDate($dt_0))));
		$D['history']['qt'][2] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_2), '$lt' => new MongoDate($dt_1))));
		$D['history']['qt'][3] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_3), '$lt' => new MongoDate($dt_2))));
		$D['history']['qt'][4] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_4), '$lt' => new MongoDate($dt_3))));
		$D['history']['qt'][5] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_5), '$lt' => new MongoDate($dt_4))));
		$D['history']['qt'][6] = DB::$access_log->count(array("data" => array('$gte' => new MongoDate($dt_6), '$lt' => new MongoDate($dt_5))));
 		# Acumulado por IP
		$ips = DB::$db->command(array('distinct' => 'access_log', 'key' => 'ip'));
		foreach ($ips['values'] as $ip) {
			$D['by_ip'][$ip] = DB::$access_log->count(array('ip' => $ip));
		}
 		# Acumulado por usuário
		$usuarios = DB::$db->command(array('distinct' => 'access_log', 'key' => 'user'));
		foreach ($usuarios['values'] as $u) {
			$D['by_user'][$u] = DB::$access_log->count(array('user' => $u));
		}

		## Feedbacks
		$feedbacks = DB::$feedback->find();
		$rating_count = 0; $rating_total = 0; $rating_avg = 0;
		$D['by_error']['error'] = 0; 
		$D['by_error']['suggestion'] = 0;
		$D['by_error']['complaint'] = 0;
		$D['by_error']['compliment'] = 0;
		$D['by_error']['other'] = 0;
		# Tipos de feedback
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
					$D['by_error']['error'] += 1; 
					break;
				case 'suggestion': 
					$D['by_error']['suggestion'] += 1;
					break;
				case 'complaint': 
					$D['by_error']['complaint'] += 1;
					break;
				case 'compliment': 
					$D['by_error']['compliment'] += 1;
					break;
				case 'other': 
					$D['by_error']['other'] += 1;
					break;
			}
		}
		# Satisfação dos usuários
		$rating_avg = round($rating_total/$rating_count,1);
		$D['rating_avg'] = $rating_avg;
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
		$D['rating'] = $rating;

		## Banco de dados
		$D['stats'] = DB::$db->command(array('dbStats' => 1, 'scale' => 1048576));
		
		return $D;
	}

	/***
	 *  BÁSICO
	 ***/

	/*
	 * GET /admin/tipos
	 */
	public function tipos_get(){
		$form = isset($_REQUEST['form']) ? $_REQUEST['form'] : false;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$mode = $id ? 'edit' : 'new';
		$D['id'] = $id;
		$D['mode'] = $mode;
		if($mode == 'edit'){	
			$D['tipo'] = DB::$tipo->findOne(array('_id' => new MongoId($id)));
		} elseif(isset($_SESSION['tipo'])) {
			$D['tipo'] = $_SESSION['tipo'];
		}
		if (!$form) {
			$D['tipos'] = DB::$tipo->find()->sort(array("nome" => 1));
		}
		
		return $D;
	}

	/*
	 * POST /admin/tipos
	 */
	public function tipos_post(){
		$form = isset($_REQUEST['form']) ? $_REQUEST['form'] : false;
		$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : false;
		$mode = $id ? 'edit' : 'new';
		if($mode == 'edit'){	
			$t = DB::$tipo->findOne(array('_id' => new MongoId($id)));
		}
		switch ($form) {
			case 'basic':
				$nome = !empty($_REQUEST['nome']) ? $_REQUEST['nome'] : false;
				$descricao = !empty($_REQUEST['descricao']) ? $_REQUEST['descricao'] : false;
				$cotar = isset($_REQUEST['cotar']) && $_REQUEST['cotar'] == 'on';
				if(!$nome || !$descricao){
					$_SESSION['flash'] = "O nome e a descrição do tipo de solicitação são obrigatórios.";
					header('Location: ' . SITE_BASE . 'admin/tipos?form=basic' . ($mode == 'edit' ? '&id=' . $id : ''));
				} else {
					if($mode == 'edit'){
						if ($t['nome'] != $nome) $t['nome'] = $nome;
						if ($t['descricao'] != $nome) $t['descricao'] = $descricao;
						$t['cotar'] = $cotar;
						DB::$tipo->save($t);
						$_SESSION['flash'] = "Alterações salvas!";
						header('Location: ' . SITE_BASE . 'admin/tipos');
					} else {
						$t = array(
							'nome' => $nome,
							'descricao' => $descricao,
							'cotar' => $cotar
						);
						$_SESSION['tipo'] = $t;
						header('Location: ' . SITE_BASE . 'admin/tipos?form=people');
					}
				}
				break;
			case 'people':
				$aprovador = !empty($_REQUEST['aprovador']) ? $_REQUEST['aprovador'] : false;
				$cotador = !empty($_REQUEST['cotador']) ? $_REQUEST['cotador'] : false;
				$executor = !empty($_REQUEST['executor']) ? $_REQUEST['executor'] : false;
				$informar = !empty($_REQUEST['informar']) ? $_REQUEST['informar'] : false;
				if(!$aprovador || !$executor){
					$_SESSION['flash'] = "É obrigatório informar o(s) aprovador(es) e exececutor(es) do tipo de solicitação.";
					header('Location: ' . SITE_BASE . 'admin/tipos?form=people' . ($mode == 'edit' ? '&id=' . $id : ''));
				} else {
					$_aprovador = explode(";", $aprovador);
					$aprovador = array();
					$_executor = explode(";", $executor);
					$executor = array();
					if($cotador){
						$_cotador = explode(";", $cotador);
						$cotador = array();
					}
					if($informar){
						$_informar = explode(";", $informar);
						$informar = array();
					}
					foreach ($_aprovador as $pessoa) { if (!empty($pessoa)) { $aprovador[] = trim($pessoa); } }
					foreach ($_cotador as $pessoa) { if (!empty($pessoa)) { $cotador[] = trim($pessoa); } }
					foreach ($_executor as $pessoa) { if (!empty($pessoa)) { $executor[] = trim($pessoa); } }
					foreach ($_informar as $pessoa) { if (!empty($pessoa)) { $informar[] = trim($pessoa); } }
					if($mode == 'edit'){
						$t['aprovador'] = $aprovador;
						$t['cotador'] = $cotador;
						$t['executor'] = $executor;
						$t['informar'] = $informar;
						DB::$tipo->save($t);
						$_SESSION['flash'] = "Alterações salvas!";
						header('Location: ' . SITE_BASE . 'admin/tipos');
					} else {
						$_SESSION['tipo']['aprovador']	= $aprovador;
						$_SESSION['tipo']['cotador'] 	= $cotador;
						$_SESSION['tipo']['executor'] 	= $executor;
						$_SESSION['tipo']['informar'] 	= $informar;
						header('Location: ' . SITE_BASE . 'admin/tipos?form=detail');
					}
				}
				break;
			case 'detail':
				$detalhes = !empty($_REQUEST['detalhe']) || count($_REQUEST['detalhe'] > 0) ? $_REQUEST['detalhe'] : false;
				if(!$detalhes){
					$_SESSION['flash'] = "É obrigatório informar pelo menos uma coluna para o tipo de solicitação.";
					header('Location: ' . SITE_BASE . 'admin/tipos?form=detail' . ($mode == 'edit' ? '&id=' . $id : ''));
				} else {
					$detalhe = array();
					foreach ($detalhes as $d) {
						if(
							(isset($d['nome_unico']) && !empty($d['nome_unico'])) &&
							(isset($d['nome']) && !empty($d['nome'])) &&
							(isset($d['tipo']) && !empty($d['tipo'])) &&
							(isset($d['dica']) && !empty($d['dica']))
						){
							$detalhe[] = array(
								'nome_unico' => $d['nome_unico'], 
								'nome' => $d['nome'], 
								'tipo' => $d['tipo'], 
								'dica' => $d['dica']
							);
						}
					}
					if($mode == 'edit'){
						$t['detalhe'] = $detalhe;
						DB::$tipo->save($t);
						$_SESSION['flash'] = "Alterações salvas!";
						header('Location: ' . SITE_BASE . 'admin/tipos');
					} else {
						$_SESSION['tipo']['detalhe'] = $detalhe;
						DB::$tipo->save($_SESSION['tipo']);
						unset($_SESSION['tipo']);
						$_SESSION['flash'] = "Tipo de solicitação criado!";
						header('Location: ' . SITE_BASE . 'admin/tipos');
					}
				}
				break;
			case 'delete':
				$solicitacoes = DB::$solicitacao->find(array('tipo.$id' => $t['_id']));
				# Arquiva as solicitações desse tipo
				$i = 0;
				foreach ($solicitacoes as $s) {
					$s['tipo'] = 'arquivada';
					DB::$solicitacao->save($s);
					$i++;
				}
				DB::$tipo->remove(array('_id' => $t['_id']));
				$_SESSION['flash'] = "O tipo de solicitação foi removido e " . ($i == 0 ? "nenhuma solicitação foi arquivada" : ($i > 1 ? $i . " solicitações foram arquivadas" : "uma solicitação foi arquivada")) . ".";
				header('Location: ' . SITE_BASE . 'admin/tipos');
				break;
		}
	}

	/*
	 * GET /admin/especialistas
	 */
	public function especialistas_get(){
		if(isset($_REQUEST['a']) && $_REQUEST['a'] == 'excluir' && isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
			DB::$especialista->remove(array('_id' => new MongoId($_REQUEST['id'])));
			$_SESSION['flash'] = 'Especialista removido!';
			header('Location: ' . SITE_BASE . 'admin/especialistas');
		}
		$D['especialistas'] = DB::$especialista->find();

		return $D;
	}

	/*
	 * POST /admin/especialistas
	 */
	public function especialistas_post(){
		foreach($_REQUEST['e'] as $e){
			if((isset($e['usuario']) && !empty($e['usuario'])) && (isset($e['especialidade']) && !empty($e['especialidade']))){
				if(isset($e['id']) && !empty($e['id'])){
					$id = new MongoId($e['id']);
					$tmp_e = DB::$especialista->findOne(array('_id'=>$id));
					unset($e['id']);
					$tmp_e['usuario'] = $e['usuario'];
					$tmp_e['especialidade'] = $e['especialidade'];
					DB::$especialista->save($tmp_e);
				}else{
					DB::$especialista->save($e);
				}
			}
		}
		$_SESSION['flash'] = 'Alterações salvas!';
		header('Location: ' . SITE_BASE . 'admin/especialistas');
	}

	/*
	 * GET /admin/centros
	 */
	public function centros_get(){
		if(isset($_REQUEST['a']) && $_REQUEST['a'] == 'excluir' && isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
			DB::$centro->remove(array('_id' => new MongoId($_REQUEST['id'])));
			$_SESSION['flash'] = 'Centro de custo removido!';
			header('Location: ' . SITE_BASE . 'admin/centros');
		}
		$D['centros'] = DB::$centro->find();

		return $D;
	}

	/*
	 * POST /admin/centros
	 */
	public function centros_post(){
		foreach($_REQUEST['c'] as $c){
			if((isset($c['apelido']) && !empty($c['apelido'])) && (isset($c['descricao']) && !empty($c['descricao']))){
				if(isset($c['id']) && !empty($c['id'])){
					$id = new MongoId($c['id']);
					$tmp_c = DB::$centro->findOne(array('_id'=>$id));
					unset($c['id']);
					$tmp_c['apelido'] = intval($c['apelido']);
					$tmp_c['descricao'] = $c['descricao'];
					DB::$centro->save($tmp_c);
				}else{
					DB::$centro->save($c);
				}
			}
		}
		$_SESSION['flash'] = 'Alterações salvas!';
		header('Location: ' . SITE_BASE . 'admin/centros');
	}

	/*
	 * GET /admin/relatorios
	 */
	public function relatorios_get(){
		$D = array();
		return $D;
	}

	/*
	 * GET /admin/feedbacks
	 */
	public function feedbacks_get(){
		$page = isset($_GET['p']) ? $_GET['p'] : 1;
		$limit = isset($_GET['page_size']) ? $_GET['page_size'] : 25;
		$skip = ($page - 1) * $limit;
		$total = DB::$feedback->count();
		$pages = ceil($total/$limit);
		$list = DB::$feedback->find()->sort(array('data' => -1))->limit($limit)->skip($skip);
		$D = array(
			'total' => $total,
			'pages' => $pages,
			'page' => $page,
			'list' => $list
		);
		return $D;
	}

	/*
	 * GET /admin/logs
	 */
	public function logs_get(){
		$page = isset($_GET['p']) ? $_GET['p'] : 1;
		$limit = isset($_GET['page_size']) ? $_GET['page_size'] : 30;
		$skip = ($page - 1) * $limit;
		$total = DB::$access_log->count();
		$pages = ceil($total/$limit);
		$list = DB::$access_log->find()->sort(array('data' => -1))->limit($limit)->skip($skip);
		$D = array(
			'total' => $total,
			'pages' => $pages,
			'page' => $page,
			'list' => $list
		);
		return $D;
	}

	/*
	 * GET /admin/all
	 */
	public function all_get(){
		$status = $_REQUEST['status'];
		$s = -1;
		$query = null;
		switch ($status) {
			case 'all':
				$title = "Todas as solicitações";
				$query = array();
				break;
			case 'rascunhos':
				$title = "Rascunhos de solicitação";
				$s = 0;
				$query = array('status' => 0);
				break;
			case 'pendentes':
				$title = "Solicitações pendentes";
				$s = 1;
				$query = array('status' => 1);
				break;
			case 'cotando':
				$title = "Solicitações em cotação";
				$s = 5;
				$query = array('status' => 5);
				break;
			case 'analisando':
				$title = "Solicitações em análise";
				$s = 6;
				$query = array('status' => 6);
				break;
			case 'retornadas':
				$title = "Solicitações retornadas";
				$s = 4;
				$query = array('status' => 4);
				break;
			case 'encaminhadas':
				$title = "Solicitações encaminhadas";
				$s = 7;
				$query = array('status' => 7);
				break;
			case 'aprovadas':
				$title = "Solicitações aprovadas";
				$s = 2;
				$query = array('status' => 2, 'executado' => false);
				break;
			case 'recusadas':
				$title = "Solicitações recusadas";
				$s = 3;
				$query = array('status' => 3);
				break;
			case 'executadas':
				$title = "Solicitações executadas";
				$s = 7;
				$query = array('status' => 7, 'executado' => true);
				break;
			case 'canceladas':
				$title = "Solicitações canceladas";
				$s = 8;
				$query = array('status' => 8);
				break;
			
			default:
				$title = "Todas as solicitações";
				$query = array();
				break;
		}
		$page = isset($_GET['p']) ? $_GET['p'] : 1;
		$limit = isset($_GET['page_size']) ? $_GET['page_size'] : 30;
		$skip = ($page - 1) * $limit;
		$total = DB::$solicitacao->count($query);
		$pages = ceil($total/$limit);
		$list = DB::$solicitacao->find($query,array('_id'))->sort(array('data.sec',-1))->limit($limit)->skip($skip);
		$D = array(
			's' => $s,
			'title' => $title,
			'total' => $total,
			'pages' => $pages,
			'page' => $page,
			'list' => $list
		);
		return $D;
	}

	/***
	 *  AVANÇADO
	 ***/

	/*
	 * GET /admin/org
	 */
	public function org_get(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * POST /admin/org
	 */
	public function org_post(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * GET /admin/env
	 */
	public function env_get(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * POST /admin/env
	 */
	public function env_post(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}
	
	/*
	 * GET /admin/smtp
	 */
	public function smtp_get(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * POST /admin/smtp
	 */
	public function smtp_post(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}
	
	/*
	 * GET /admin/ldap
	 */
	public function ldap_get(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * POST /admin/ldap
	 */
	public function ldap_post(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}
	
	/*
	 * GET /admin/mongo
	 */
	public function mongo_get(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}

	/*
	 * POST /admin/mongo
	 */
	public function mongo_post(){
		# Não implementado por ser de baixa prioridade. Previsto para a próxima release.
	}
	
	/*
	 * GET /admin/regras
	 */
	public function regras_get(){
		$D = array();
		$gerente = DB::$roles->find(array('role' => 0));
		foreach ($gerente as $r) {
			$D['gerente'][] = $r['user'];
		}
		
		$desenvolvedor = DB::$roles->find(array('role' => 1));
		foreach ($desenvolvedor as $r) {
			$D['desenvolvedor'][] = $r['user'];
		}
		
		$administrador = DB::$roles->find(array('role' => 2));
		foreach ($administrador as $r) {
			$D['administrador'][] = $r['user'];
		}
		
		$super = DB::$roles->find(array('role' => 3));
		foreach ($super as $r) {
			$D['super'][] = $r['user'];
		}
		
		return $D;
	}

	/*
	 * POST /admin/regras
	 */
	public function regras_post(){
		$gerente = isset($_REQUEST['gerente']) && !empty($_REQUEST['gerente']) ? $_REQUEST['gerente'] : false;
		$desenvolvedor = isset($_REQUEST['desenvolvedor']) && !empty($_REQUEST['desenvolvedor']) ? $_REQUEST['desenvolvedor'] : false;
		$administrador = isset($_REQUEST['administrador']) && !empty($_REQUEST['administrador']) ? $_REQUEST['administrador'] : false;
		$super = isset($_REQUEST['super']) && !empty($_REQUEST['super']) ? $_REQUEST['super'] : false;

		DB::$roles->remove();

		if($gerente){
			$list = explode(";",$gerente);
			foreach ($list as $item) {
				if(!empty($item)){
					$u = trim($item);
					$role = DB::$roles->findOne(array('user' => $u));
					if(!$role){
						$role = array('user' => $u);
					}
					$role['role'] = 0;
					DB::$roles->save($role);
				}
			}
		}
		if($desenvolvedor){
			$list = explode(";",$desenvolvedor);
			foreach ($list as $item) {
				if(!empty($item)){
					$u = trim($item);
					$role = DB::$roles->findOne(array('user' => $u));
					if(!$role){
						$role = array('user' => $u);
					}
					$role['role'] = 1;
					DB::$roles->save($role);
				}
			}
		}
		if($administrador){
			$list = explode(";",$administrador);
			foreach ($list as $item) {
				if(!empty($item)){
					$u = trim($item);
					$role = DB::$roles->findOne(array('user' => $u));
					if(!$role){
						$role = array('user' => $u);
					}
					$role['role'] = 2;
					DB::$roles->save($role);
				}
			}
		}
		if($super){
			$list = explode(";",$super);
			foreach ($list as $item) {
				if(!empty($item)){
					$u = trim($item);
					$role = DB::$roles->findOne(array('user' => $u));
					if(!$role){
						$role = array('user' => $u);
					}
					$role['role'] = 3;
					DB::$roles->save($role);
				}
			}
		}
		$_SESSION['flash'] = "Alterações salvas!";
		header('Location: ' . SITE_BASE . "admin/regras");
	}
	
	/*
	 * GET /admin/backup
	 */
	public function backup_get(){
		# Implementação prevista para o próximo release
	}

	/*
	 * POST /admin/backup
	 */
	public function backup_post(){
		# Implementação prevista para o próximo release
	}
	
	/*
	 * GET /admin/status
	 */
	public function status_get(){
		# Não precisa retornar nada
	}

	/*
	 * POST /admin/status
	 */
	public function status_post(){
		$_s = DB::$solicitacao->findOne(array('_id' => new MongoId($_REQUEST['id'])));
		$s = new Solicitacao($_s['_id']);
		if($s){
			if(isset($_REQUEST['status']) && $_REQUEST['status'] != "" && $_REQUEST['status'] >= 0 && $_REQUEST['status'] < 9){
				$status = intval($_REQUEST['status']);
				$s->changeStatus($status,$_SESSION['user']);
				$_SESSION['flash'] = 'O status foi alterado!';
			}else{
				$_SESSION['flash'] = 'O status indicado não é válido';
			}
		}else{
			$_SESSION['flash'] = 'Essa solicitação não foi encontrada';
		}
		header('Location: ' . SITE_BASE . 'admin/status');
	}
	
	/*
	 * GET /admin/editar
	 */
	public function editar_get(){
		# Ainda não implementado por questões de segurança. Implementação prevista para o próximo release.
	}	

	/*
	 * POST /admin/editar
	 */
	public function editar_post(){
		# Ainda não implementado por questões de segurança. Implementação prevista para o próximo release.
	}
	
	/*
	 * GET /admin/apps
	 */
	public function apps_get(){
		$a = $_REQUEST['a'];
		if($a == 'remove' && isset($_REQUEST['id'])){
			$app = new Application($_REQUEST['id']);
			$app->remove();
			header('Location: ' . SITE_BASE . 'admin/apps');
		}else{
			$D['apps'] = DB::$application->find();
		}
		return $D;
	}

	/*
	 * POST /admin/apps
	 */
	public function apps_post(){
		$a = $_REQUEST['a'];
		if($a == 'new' && isset($_REQUEST['name'])){
			$app = new Application();
			$app->name = $_REQUEST['name'];
			$app->description = $_REQUEST['description'];
			$app->save();
			header('Location: ' . SITE_BASE . 'admin/apps');
		}
	}

}