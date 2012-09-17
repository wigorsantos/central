<?php
/*
 * API para os dados auxiliares
 */
class UtilAPI {

	/**
	 * Lista todos os tipos de solicitação
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function tipos($params){
		$_tipos = DB::$tipo->find();
		$tipos = array();
		foreach ($_tipos as $tipo) {
			$tipos[] = array(
				'id' => (string) $tipo['_id'],
				'nome' => $tipo['nome'],
				'descricao' => $tipo['descricao'],
				'aprovador' => $tipo['aprovador'],
				'cotador' => $tipo['cotador'],
				'executor' => $tipo['executor'],
				'cotar' => $tipo['cotar'],
				'detalhe' => $tipo['detalhe']
			);
		}
		return $tipos;
	}

	/**
	 * Lista todos os especialistas
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function especialistas($params){
		$_especialistas = DB::$especialista->find();
		$especialistas = array();
		foreach ($_especialistas as $especialista) {
			$especialistas[] = array(
				'id' => (string) $especialista['_id'],
				'usuario' => $especialista['usuario'],
				'especialidade' => $especialista['especialidade']
			);
		}
		return $especialistas;
	}

	/**
	 * Lista todos os centros de custo
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function centros($params){
		$_centros = DB::$centro->find();
		$centros = array();
		foreach ($_centros as $centro) {
			$centros[] = array(
				'id' => (string) $centro['_id'],
				'apelido' => $centro['apelido'],
				'descricao' => $centro['descricao']
			);
		}
		return $centros;
	}

	/**
	 * Retorna informações do usuário
	 * @param array $params Parâmetros para execução
	 * @return array com os dados solicitados
	 **/
	public function perfil($params){
		//return $params;
		$info = AD::info($params["current_user"],array('displayname','manager','department','telephonenumber','mobile','title'));
		$manager = $info[0]['manager'][0];
		$virg_pos = stripos($manager, ",");
		$manager = substr($manager, 3, $virg_pos-3);
		$manager = AD::$ad->user()->all(false,$manager);
		$manager = $manager[0];
		$manager_info = AD::info($manager,array('displayname','department','telephonenumber','mobile','title'));
		$uinfo = array(
			"user" => $params['current_user'],
			"displayname" => $info[0]['displayname'][0],
			"manager" => array(
				"user" => $manager,
				"displayname" => $manager_info[0]['displayname'][0],
				"department" => $manager_info[0]['department'][0],
				"telephone" => $manager_info[0]['telephonenumber'][0],
				"mobile" => $manager_info[0]['mobile'][0],
				"title" => $manager_info[0]['title'][0]
			),
			"department" => $info[0]['department'][0],
			"telephone" => $info[0]['telephonenumber'][0],
			"mobile" => $info[0]['mobile'][0],
			"title" => $info[0]['title'][0]
		);
		return $uinfo;
	}

}