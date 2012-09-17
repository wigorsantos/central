<?php
/**
 * API das solicitações
 **/
class SolicitacaoAPI {

	/******** BASE ********/
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function all($params){
		$r = array();
		$query = array('solicitante' => $params['current_user']);
		$page = isset($params['page']) ? $params['page'] : 1;
		$limit = isset($params['page_size']) ? $params['page_size'] : 20;
		$skip = ($page - 1) * $limit;
		$total = DB::$solicitacao->count($query);
		$pages = ceil($total/$limit);
		$data = DB::$solicitacao->find($query,array('_id'))->sort(array('data' => -1))->limit($limit)->skip($skip);
		$list = array();
		foreach ($data as $d) {
			$s = new Solicitacao($d['_id']);
			$list[] = $s->serial();
		}
		$r['total'] = $total;
		$r['pages'] = $pages;
		$r['page'] = $page;
		$r['page_size'] = $limit;
		$r['solicitacoes'] = $list;
		return $r;
	}
	
	/**
	 * /solicitacao/novo
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function novo($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/pendencias
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function pendencias($params){
		$r = array();
		$it_aprova = DB::$tipo->find(array("aprovador" => $params['current_user']),array('_id'));
		$t_aprova = array();
		foreach ($it_aprova as $t) {
			$t_aprova[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_cota = DB::$tipo->find(array("cotador" => $params['current_user']),array('_id'));
		$t_cota = array();
		foreach ($it_cota as $t) {
			$t_cota[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$it_executa = DB::$tipo->find(array("executor" => $params['current_user']),array('_id'));
		$t_executa = array();
		foreach ($it_executa as $t) {
			$t_executa[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$default_page_size = 20;
		$aprovar_page = isset($params['aprovar_page']) ? $params['aprovar_page'] : 1;
		$aprovar_limit = isset($params['aprovar_page_size']) ? $params['aprovar_page_size'] : $default_page_size;
		$aprovar_skip = ($aprovar_page - 1) * $aprovar_limit;
		$total_pre_aprovar = DB::$solicitacao->count(array("pre_aprovador" => $params['current_user'], "status" => 1, "pre_aprovado" => false));
		$total_aprovar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_aprova), "status" => 1, "pre_aprovado" => true));
		$total_aprovar = $total_pre_aprovar + $total_aprovar;
		$aprovar_pages = ceil($total_aprovar/$aprovar_limit);

		$executar_page = isset($params['executar_page']) ? $params['executar_page'] : 1;
		$executar_limit = isset($params['executar_page_size']) ? $params['executar_page_size'] : $default_page_size;
		$executar_skip = ($executar_page - 1) * $executar_limit;
		$total_executar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_executa), "status" => 2, "executado" => false));
		$executar_pages = ceil($total_executar/$executar_limit);

		$cotar_page = isset($params['cotar_page']) ? $params['cotar_page'] : 1;
		$cotar_limit = isset($params['cotar_page_size']) ? $params['cotar_page_size'] : $default_page_size;
		$cotar_skip = ($cotar_page - 1) * $cotar_limit;
		$total_cotar = DB::$solicitacao->count(array("tipo" => array('$in' => $t_cota), "status" => 5));
		$cotar_pages = ceil($total_cotar/$cotar_limit);

		$analisar_page = isset($params['analisar_page']) ? $params['analisar_page'] : 1;
		$analisar_limit = isset($params['analisar_page_size']) ? $params['analisar_page_size'] : $default_page_size;
		$analisar_skip = ($analisar_page - 1) * $analisar_limit;
		$total_analisar = DB::$solicitacao->count(array("analisador" => $params['current_user'], "status" => 6));
		$analisar_pages = ceil($total_analisar/$analisar_limit);

		$revisar_page = isset($params['revisar_page']) ? $params['revisar_page'] : 1;
		$revisar_limit = isset($params['revisar_page_size']) ? $params['revisar_page_size'] : $default_page_size;
		$revisar_skip = ($revisar_page - 1) * $revisar_limit;
		$total_revisar = DB::$solicitacao->count(array("solicitante" => $params['current_user'], "status" => 4));
		$revisar_pages = ceil($total_revisar/$revisar_limit);
		
		$it_pre_aprovar = DB::$solicitacao->find(array("pre_aprovador" => $params['current_user'], "status" => 1, "pre_aprovado" => false),array('_id'))->sort(array('data' => -1))->limit(ceil($aprovar_limit / 2))->skip(ceil($aprovar_skip / 2));
		$pre_aprovar = array();
		foreach ($it_pre_aprovar as $s) {
			$s = new Solicitacao($s['_id']);
			$pre_aprovar[] = $s->serial();
		}
		$it_aprovar = DB::$solicitacao->find(array("tipo" => array('$in' => $t_aprova), "status" => 1, "pre_aprovado" => true),array('_id'))->sort(array('data' => -1))->limit(ceil($aprovar_limit / 2))->skip(ceil($aprovar_skip / 2));
		$aprovar = array();
		foreach ($it_aprovar as $s) {
			$s = new Solicitacao($s['_id']);
			$aprovar[] = $s->serial();
		}
		$aprovar = array_merge($pre_aprovar,$aprovar);
		$it_executar = DB::$solicitacao->find(array("tipo" => array('$in' => $t_executa), "status" => 2, "executado" => false),array('_id'))->sort(array('data' => -1))->limit($executar_limit)->skip($executar_skip);
		$executar = array();
		foreach ($it_executar as $s) {
			$s = new Solicitacao($s['_id']);
			$executar[] = $s->serial();
		}
		$it_cotar = DB::$solicitacao->find(array("tipo" => array('$in' => $t_cota), "status" => 5),array('_id'))->sort(array('data' => -1))->limit($cotar_limit)->skip($cotar_skip);
		$cotar = array();
		foreach ($it_cotar as $s) {
			$s = new Solicitacao($s['_id']);
			$cotar[] = $s->serial();
		}
		$it_analisar = DB::$solicitacao->find(array("analisador" => $params['current_user'], "status" => 6),array('_id'))->sort(array('data' => -1))->limit($analisar_limit)->skip($analisar_skip);
		$analisar = array();
		foreach ($it_analisar as $s) {
			$s = new Solicitacao($s['_id']);
			$analisar[] = $s->serial();
		}
		$it_revisar = DB::$solicitacao->find(array("solicitante" => $params['current_user'], "status" => 4),array('_id'))->sort(array('data' => -1))->limit($revisar_limit)->skip($revisar_skip);
		$revisar = array();
		foreach ($it_revisar as $s) {
			$s = new Solicitacao($s['_id']);
			$revisar[] = $s->serial();
		}

		$r['total_aprovar'] = $total_aprovar;
		$r['aprovar_pages'] = $aprovar_pages;
		$r['aprovar_page'] = $aprovar_page;
		$r['aprovar_page_size'] = $aprovar_limit;

		$r['total_executar'] = $total_executar;
		$r['executar_pages'] = $executar_pages;
		$r['executar_page'] = $executar_page;
		$r['executar_page_size'] = $executar_limit;

		$r['total_cotar'] = $total_cotar;
		$r['cotar_pages'] = $cotar_pages;
		$r['cotar_page'] = $cotar_page;
		$r['cotar_page_size'] = $cotar_limit;

		$r['total_analisar'] = $total_analisar;
		$r['analisar_pages'] = $analisar_pages;
		$r['analisar_page'] = $analisar_page;
		$r['analisar_page_size'] = $analisar_limit;

		$r['total_revisar'] = $total_revisar;
		$r['revisar_pages'] = $revisar_pages;
		$r['revisar_page'] = $revisar_page;
		$r['revisar_page_size'] = $revisar_limit;

		$r['aprovar'] = $aprovar;
		$r['executar'] = $executar;
		$r['cotar'] = $cotar;
		$r['analisar'] = $analisar;
		$r['revisar'] = $revisar;
		return $r;
	}
	
	/**
	 * /solicitacao/participacoes
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function participacoes($params){
		$r = array();
		$it_informado = DB::$tipo->find(array("informar" => $params['current_user']),array('_id'));
		$t_informado = array();
		foreach ($it_informado as $t) {
			$t_informado[] = MongoDBRef::create('tipo',$t['_id']);
		}
		$default_page_size = 20;
		$aprovar_page = isset($params['aprovar_page']) ? $params['aprovar_page'] : 1;
		$aprovar_limit = isset($params['aprovar_page_size']) ? $params['aprovar_page_size'] : $default_page_size;
		$aprovar_skip = ($aprovar_page - 1) * $aprovar_limit;
		$total_pre_aprovar = DB::$solicitacao->count(array("pre_aprovador" => $params['current_user'], "status" => array('$gt' => 0)));
		$total_aprovar = DB::$solicitacao->count(array("aprovador" => $params['current_user'], "status" => array('$gt' => 0)));
		$total_aprovar = $total_pre_aprovar + $total_aprovar;
		$aprovar_pages = ceil($total_aprovar/$aprovar_limit);

		$executar_page = isset($params['executar_page']) ? $params['executar_page'] : 1;
		$executar_limit = isset($params['executar_page_size']) ? $params['executar_page_size'] : $default_page_size;
		$executar_skip = ($executar_page - 1) * $executar_limit;
		$total_executar = DB::$solicitacao->count(array("executor" => $params['current_user'], "status" => array('$in', array(2,7))));
		$executar_pages = ceil($total_executar/$executar_limit);

		$cotar_page = isset($params['cotar_page']) ? $params['cotar_page'] : 1;
		$cotar_limit = isset($params['cotar_page_size']) ? $params['cotar_page_size'] : $default_page_size;
		$cotar_skip = ($cotar_page - 1) * $cotar_limit;
		$total_cotar = DB::$solicitacao->count(array("cotador" => $params['current_user'], "status" => array('$gt' => 0)));
		$cotar_pages = ceil($total_cotar/$cotar_limit);

		$analisar_page = isset($params['analisar_page']) ? $params['analisar_page'] : 1;
		$analisar_limit = isset($params['analisar_page_size']) ? $params['analisar_page_size'] : $default_page_size;
		$analisar_skip = ($analisar_page - 1) * $analisar_limit;
		$total_analisar = DB::$solicitacao->count(array("analisador" => $params['current_user'], "status" => array('$gt' => 0)));
		$analisar_pages = ceil($total_analisar/$analisar_limit);

		$informado_page = isset($params['informado_page']) ? $params['informado_page'] : 1;
		$informado_limit = isset($params['informado_page_size']) ? $params['informado_page_size'] : $default_page_size;
		$informado_skip = ($informado_page - 1) * $informado_limit;
		$total_informado = DB::$solicitacao->count(array("tipo" => array('$in' => $t_informado), "status" => array('$gt' => 0)));
		$informado_pages = ceil($total_informado/$informado_limit);

		$it_pre_aprovar = DB::$solicitacao->find(array("pre_aprovador" => $params['current_user'], "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit(ceil($aprovar_limit / 2))->skip(ceil($aprovar_skip / 2));
		$pre_aprovar = array();
		foreach($it_pre_aprovar as $s) {
			$s = new Solicitacao($s['_id']);
			$pre_aprovar[] = $s->serial();
		}
		$it_aprovar = DB::$solicitacao->find(array("aprovador" => $params['current_user'], "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit(ceil($aprovar_limit / 2))->skip(ceil($aprovar_skip / 2));
		$aprovar = array();
		foreach($it_aprovar as $s) {
			$s = new Solicitacao($s['_id']);
			$aprovar[] = $s->serial();
		}
		$aprovar = array_merge($pre_aprovar,$aprovar);
		$it_executar = DB::$solicitacao->find(array("executor" => $params['current_user'], "status" => array('$in', array(2,7))),array('_id'))->sort(array('data' => -1))->limit(ceil($executar_limit / 2))->skip(ceil($executar_skip / 2));
		$executar = array();
		foreach($it_executar as $s) {
			$s = new Solicitacao($s['_id']);
			$executar[] = $s->serial();
		}
		$it_cotar = DB::$solicitacao->find(array("cotador" => $params['current_user'], "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit(ceil($cotar_limit / 2))->skip(ceil($cotar_skip / 2));
		$cotar = array();
		foreach($it_cotar as $s) {
			$s = new Solicitacao($s['_id']);
			$cotar[] = $s->serial();
		}
		$it_analisar = DB::$solicitacao->find(array("analisador" => $params['current_user'], "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit(ceil($analisar_limit / 2))->skip(ceil($analisar_skip / 2));
		$analisar = array();
		foreach($it_analisar as $s) {
			$s = new Solicitacao($s['_id']);
			$analisar[] = $s->serial();
		}
		$it_informado = DB::$solicitacao->find(array("tipo" => array('$in' => $t_informado), "status" => array('$gt' => 0)),array('_id'))->sort(array('data' => -1))->limit(ceil($informado_limit / 2))->skip(ceil($informado_skip / 2));
		$informado = array();
		foreach($it_informado as $s) {
			$s = new Solicitacao($s['_id']);
			$informado[] = $s->serial();
		}
		
		$r['total_aprovar'] = $total_aprovar;
		$r['aprovar_pages'] = $aprovar_pages;
		$r['aprovar_page'] = $aprovar_page;
		$r['aprovar_page_size'] = $aprovar_limit;

		$r['total_executar'] = $total_executar;
		$r['executar_pages'] = $executar_pages;
		$r['executar_page'] = $executar_page;
		$r['executar_page_size'] = $executar_limit;

		$r['total_cotar'] = $total_cotar;
		$r['cotar_pages'] = $cotar_pages;
		$r['cotar_page'] = $cotar_page;
		$r['cotar_page_size'] = $cotar_limit;

		$r['total_analisar'] = $total_analisar;
		$r['analisar_pages'] = $analisar_pages;
		$r['analisar_page'] = $analisar_page;
		$r['analisar_page_size'] = $analisar_limit;

		$r['total_informado'] = $total_informado;
		$r['informado_pages'] = $informado_pages;
		$r['informado_page'] = $informado_page;
		$r['informado_page_size'] = $informado_limit;

		$r['aprovar'] = $aprovar;
		$r['executar'] = $executar;
		$r['cotar'] = $cotar;
		$r['analisar'] = $analisar;
		$r['informado'] = $informado;
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function getByStatus($params){
		$r = array();
		$status = $params['status'];
		$query = null;
		switch ($status) {
			case 'rascunhos':
				$query = array('solicitante' => $params['current_user'], 'status' => 0);
				break;
			case 'pendentes':
				$query = array('solicitante' => $params['current_user'], 'status' => 1);
				break;
			case 'cotando':
				$query = array('solicitante' => $params['current_user'], 'status' => 5);
				break;
			case 'analisando':
				$query = array('solicitante' => $params['current_user'], 'status' => 6);
				break;
			case 'retornadas':
				$query = array('solicitante' => $params['current_user'], 'status' => 4);
				break;
			case 'encaminhadas':
				$query = array('solicitante' => $params['current_user'], 'status' => 7);
				break;
			case 'aprovadas':
				$query = array('solicitante' => $params['current_user'], 'status' => 2, 'executado' => false);
				break;
			case 'recusadas':
				$query = array('solicitante' => $params['current_user'], 'status' => 3);
				break;
			case 'executadas':
				$query = array('solicitante' => $params['current_user'], 'status' => 7, 'executado' => true);
				break;
			case 'canceladas':
				$query = array('solicitante' => $params['current_user'], 'status' => 8);
				break;
			default:
				return $r;
				break;
		}
		$page = isset($params['p']) ? $params['p'] : 1;
		$limit = isset($params['page_size']) ? $params['page_size'] : 20;
		$skip = ($page - 1) * $limit;
		$total = DB::$solicitacao->count($query);
		$pages = ceil($total/$limit);
		$data = DB::$solicitacao->find($query,array('_id'))->sort(array('data' => -1))->limit($limit)->skip($skip);
		$list = array();
		foreach ($data as $d) {
			$s = new Solicitacao($d['_id']);
			$list[] = $s->serial();
		}
		$r['total'] = $total;
		$r['pages'] = $pages;
		$r['page'] = $page;
		$r['page_size'] = $limit;
		$r['solicitacoes'] = $list;
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function get($params){
		$r = array();
		$s = new Solicitacao($params['id']);
		if(!$s){
			throw new Exception("Solicitação não encontrada", 404);
		}
		if(!$s->canView($params['current_user'])){
			throw new Exception("Acesso negado a solicitação", 403);	
		}
		$r = $s->fullSerial();
		return $r;
	}

	/******** ID ********/
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function send($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function approve($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function devolve($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function update($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function observe($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function quote($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function send_to_analysis($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function analyze($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function refuse($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function run($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function refer($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function delete($params){
		$r = array();
		return $r;
	}
	
	/**
	 * /solicitacao/
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function cancel($params){
		$r = array();
		return $r;
	}
}