<?php

	class Solicitacao {

		private $obj;
		private $status_mapping;
		private $ad;
		private $notifier;

		/********************************************************************
		 * FUNÇÔES MÁGICAS DO PHP
		 ********************************************************************/

		public function __construct($id){
			if($id){
				if(gettype($id) == "string"){
					$id = new MongoId($id);
				}
				$this->obj = DB::$solicitacao->findOne(array('_id'=>$id));
				if(!$this->obj || !isset($this->obj['solicitante']) || !isset($this->obj['tipo'])) {
					throw new Exception("Solicitação inválida", 1);
					
				}
				$this->ad = AD::$ad;
				$this->status_mapping = array(
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
				$this->notifier = new Notify();
			}
			# CORREÇÃO TEMPORÁRIA PARA MUDANÇA DA ESTRUTURA DE DADOS
			if(!isset($this->obj['prazo'])){
				$this->obj['prazo'] = strftime("%d/%m/%Y",$this->obj['prazo_time']);
				DB::$solicitacao->save($this->obj);
			}
		}

		public function __set($name, $value){
			$settable = array("descricao",
				"departamento",
				"prazo",
				"centro",
				"pre_aprovado",
				"aprovado",
				"cotado",
				"analisado",
				"executado",
				"pre_aprovador",
				"aprovador",
				"cotador",
				"analisador",
				"executor");
			if(in_array($name, $settable)){
				$this->obj[$name] = $value;
			}
		}

		public function __get($name){
			$references = array("tipo","centro");
			$ad_users = array("solicitante","pre_aprovador","aprovador","cotador","analisador","executor","informar");
			if($name == "id"){
				return $this->obj['_id'];
			} elseif ($name == "status"){
				if($this->obj['executado']){
					return array(7,$this->status_mapping[7]);
				}
				return array($this->obj['status'],$this->status_mapping[$this->obj['status']]);
			} elseif ($name == "numero"){
				$n = $this->obj['numero'];
				if($n<100000){
					if($n<10000){
						if($n<1000){
							if($n<100){
								if($n<10){
									return "00000" . $n;
								}
								return "0000" . $n;
							}
							return "000" . $n;
						}
						return "00" . $n;
					}
					return "0" . $n;
				}
				return "" . $n;
			} elseif (in_array($name, $references)){
				return DB::$db->getDBRef($this->obj[$name]);
			} elseif (in_array($name, $ad_users)) {
				if($this->obj[$name] == null){
					return false;
				}
				$info = AD::info($this->obj[$name],array('displayname','manager','department','telephonenumber','mobile', 'title','description'));
				$manager = $info[0]['manager'][0];
				$virg_pos = stripos($manager, ",");
				$manager = substr($manager, 3, $virg_pos-3);
				$manager = AD::$ad->user()->all(false,$manager);
				$manager = $manager[0];
				$manager_info = AD::info($manager,array('displayname','department','telephonenumber','mobile','title'));
				return array(
					"user" => $this->obj[$name],
					"role" => $name,
					"displayname" => $info[0]['displayname'][0],
					"title" => $info[0]['title'][0],
					"description" => $info[0]['description'][0],
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
					"mobile" => $info[0]['mobile'][0]
				);
			} elseif ($name == "detalhes") {
				return $this->getDetails();
			} elseif ($name == "historico") {
				return $this->getHistory();
			} elseif ($name == "anexos") {
				return $this->getAttachments();
			} elseif ($name == "observacoes") {
				return $this->getObservations();
			} elseif ($name == "prazo_textual") {
				if($this->prazo == null){
					$prazo_count = "Sem";
					$prazo_interval = "prazo";
					return $prazo_count . " " . $prazo_interval;
				}
				$agora = date_create();
				$prazo = date_create_from_format("d/m/Y H:i",$this->prazo);
				if(!$prazo) $prazo = date_create_from_format("d/m/Y",$this->prazo);
				$interval = date_diff($agora,$prazo);
				if ($interval->days > 30) {
					$prazo_count = round($interval->days/30);
					$prazo_interval = "meses";
				} elseif ($interval->days > 7) {
					$prazo_count = round($interval->days / 7);
					$prazo_interval = "semanas";
				} elseif ($interval->days == 1) {
					$prazo_count = "Amanhã";
					$prazo_interval = "";
				} elseif ($interval->days == 0) {
					$prazo_count = "Hoje";
					$prazo_interval = "";
				} elseif ($interval->days == -1 ) {
					$prazo_count = "Ontem";
					$prazo_interval = "";
				} elseif ($interval->days > 1) {
					$prazo_count = $interval->days;
					$prazo_interval = "dias";
				} elseif ($interval->days < -1) {
					$prazo_count = $interval->days*-1;
					$prazo_interval = "dias atrás";
				} elseif ($interval->days < -7) {
					$prazo_count = round(($interval->days*-1) / 7);
					$prazo_interval = "semanas atrás";
				} elseif ($interval->days < -30) {
					$prazo_count = round(($interval->days*-1)/30);
					$prazo_interval = "meses atrás";
				} elseif ($interval->days >= 3650){
					$prazo_count = "Futuro";
					$prazo_interval = "";
				} elseif ($interval->days <= -3650){
					$prazo_count = "Vencido";
					$prazo_interval = "";
				}
				return $prazo_count . " " . $prazo_interval;
			} else {
				return $this->obj[$name];
			}
		}

		public function __call($name,$args){
			if(strpos($name,"can") !== false){
				$check = strtolower(substr($name, 3));
				$username = $args[0];
				$is_superuser = DB::$roles->findOne(array('user'=>$username,'role'=>3)) ? true : false;
				if($is_superuser) return true;
				switch($check){
					case "view": #Se o usuário pode ver e/ou imprimir a solicitação
						if ($this->status[0] == 0 && $username == $this->obj['solicitante']){ # Só o solicitante ve o rascunho
							return true;
						} elseif (
							$this->isSolicitante($username) || 
							$this->isPreAprovador($username) || 
							$this->isAprovador($username) || 
							$this->isAnalisador($username) || 
							$this->isCotador($username) || 
							$this->isExecutor($username) || 
							$this->isInformado($username)
						){
							return true;
						}
						return false;
						break;
					case "observe": # Se o usuário pode adicionar uma observação na solicitação
						return $this->isSolicitante($username) || $this->isPreAprovador($username) || $this->isAprovador($username) || $this->isAnalisador($username) || $this->isCotador($username) || $this->isExecutor($username);
						break;
					case "delete": #Se o usuário pode deletar a solicitação
						if($username == $this->obj['solicitante']){
							if((in_array($this->obj['status'],array(2,7)) && $this->executado) || in_array($this->obj['status'],array(0,3))){
								return true;
							}
						}
						return false;
						break;
					case "edit": # Se o usuário pode editar a solicitação
						return $this->isSolicitante($username) && in_array($this->status[0],array(0,4));
						break;
					case "attach": # Se o usuário pode anexar um arquivo na solicitação
						return $this->isSolicitante($username) || $this->isAnalisador($username) || $this->isCotador($username);
						break;
					case "decide": # Se o usuário pode ver a caixa "Decidir" na tela da solicitação
						return $this->isPreAprovador($username) || $this->isAprovador($username);
						break;
					case "send": # Se o usuário pode enviar a solicitação
							return $this->isSolicitante($username) && $this->status[0] == 0;
						break;
					case "refer": # Se o usuário pode encaminhar a solicitação
						if($this->obj['status'] == 1){
							return ($this->pre_aprovado && $this->isAprovador($username)) || (!$this->pre_aprovado && $this->isPreAprovador($username));
						} elseif ($this->obj['status'] == 5) {
							return $this->isCotador($username);
						} elseif ($this->obj['status'] == 6) {
							return $this->isAnalisador($username);
						} elseif ($this->obj['status'] == 2) {
							return $this->isExecutor($username);
						}
						return false;
						break;
					case "cancel": # Se o usuário pode cancelar a solicitação
						return ($username == $this->obj['pre_aprovador'] || $username == $this->obj['aprovador']) && ($this->obj['status'] > 0 && $this->obj['status'] < 7);
					default: return false; break;
				}
			} elseif (strpos($name,"is") !== false) {
				$check = strtolower(substr($name, 2));
				$username = $args[0];
				$is_superuser = DB::$roles->findOne(array('user'=>$username,'role'=>'superuser')) ? true : false;
				if($is_superuser) return true;
				switch($check){
					case "solicitante": # Se o usuário é o solicitante
						return $username == $this->obj['solicitante'];
						break;
					case "preaprovador": # Se o usuário é o pré-aprovador (gerente)
						return $username == $this->obj['pre_aprovador'];
						break;
					case "aprovador": # Se o usuário é o aprovador
						$aprovadores = $this->tipo['aprovador'];
						return in_array($username, $aprovadores);
						break;
					case "analisador": # Se o usuário é o analisador
						return $username == $this->obj['analisador'];
						break;
					case "cotador": # Se o usuário é o cotador
						$cotadores = $this->tipo['cotador'];
						return in_array($username, $cotadores);
						break;
					case "executor": # Se o usuário é o executor
						$executores = $this->tipo['executor'];
						return in_array($username, $executores);
						break;
					case "informado": # Se o usuário é o executor
						$informados = $this->tipo['informar'];
						return in_array($username, $informados);
						break;
					default: return false; break;
				}
			} else {
				throw new Exception("O método $name não existe.", 1);
			}
		}

		/********************************************************************
		 * FUNÇÔES AUXILIARES
		 ********************************************************************/

		/**
		 * Converte o objeto em um array para uso na API
		 */
		public function serial(){
			$serial = array(
				'id' => (string)$this->id,
				"data" => $this->data,
				"tipo" => $this->tipo,
				"descricao" => $this->descricao,
				"solicitante" => $this->solicitante,
				"departamento" => $this->departamento,
				"prazo" => $this->prazo,
				"centro" => $this->centro,
				"numero" => $this->numero,
				"status" => $this->status,
				"pre_aprovado" => $this->pre_aprovado,
				"aprovado" => $this->aprovado,
				"cotado" => $this->cotado,
				"analisado" => $this->analisado,
				"executado" => $this->executado,
				"pre_aprovador" => $this->pre_aprovador,
				"aprovador" => $this->aprovador,
				"cotador" => $this->cotador,
				"analisador" => $this->analisador,
				"executor" => $this->executor
			);
			return $serial;
		}

		/**
		 * Converte o objeto em um array para uso na API com todos os dados relacionados
		 */
		public function fullSerial(){
			$serial = array(
				'id' => (string)$this->id,
				"data" => $this->data,
				"tipo" => $this->tipo,
				"descricao" => $this->descricao,
				"solicitante" => $this->solicitante,
				"departamento" => $this->departamento,
				"prazo" => $this->prazo,
				"centro" => $this->centro,
				"numero" => $this->numero,
				"status" => $this->status,
				"pre_aprovado" => $this->pre_aprovado,
				"aprovado" => $this->aprovado,
				"cotado" => $this->cotado,
				"analisado" => $this->analisado,
				"executado" => $this->executado,
				"pre_aprovador" => $this->pre_aprovador,
				"aprovador" => $this->aprovador,
				"cotador" => $this->cotador,
				"analisador" => $this->analisador,
				"executor" => $this->executor
			);

			$it_observacoes = $this->getObservations();
			$observacoes = array();
			foreach ($it_observacoes as $o) {
				$author = AD::info($o['autor'],array('displayname'));
				$observacoes[] = array(
					"autor" => array("user" => $o['autor'],"displayname" => $author[0]['displayname'][0]),
					"data" => $o['data'],
					"texto" => $o['texto']
				);
			}
			$serial['observacoes'] = $observacoes;

			$it_historico = $this->getHistory();
			$historico = array();
			foreach ($it_historico as $h) {
				$historico[] = array(
					"data" => $h['data'],
					"message" => $h['msg']
				);
			}
			$serial['historico'] = $historico;

			$it_anexos = $this->getAttachments();
			$anexos = array();
			foreach ($it_anexos as $a) {
				$anexos[] = array(
					"link" => SITE_PATH . 'd/' . $a->file['_id'],
					"name" => $a->getFilename(),
					"size" => $a->getSize()
				);
			}
			$serial['anexos'] = $anexos;

			return $serial;
		}

		/**
		 * Contador sequencial para coleções
		 * @param name Nome do contador
		 * @return O próximo número na sequência
		 */
		public static function counter($name){
			$ret = DB::$counters->findOne(array("_id" => $name));
			if(!$ret){
				$ret = array("_id" => $name, "next"=>0);
			}
			$ret["next"]++;
			DB::$counters->save($ret);
			return $ret['next'];
		}

		/**
		 * Salva a solicitação atual no banco de dados
		 * @return void
		 */
		public function save(){
			DB::$solicitacao->save($this->obj);
		}

		/**
		 * Adiciona um item de detalhamento na solicitação
		 * @param mixed $detail O objeto (array com índices nomeados) que representa o detalhe
		 * @return mixed O objeto salvo
		 */
		public function saveDetail($detail){
			$ref_solicitacao = MongoDBRef::create('solicitacao',$this->id);
			$detail['solicitacao'] = $ref_solicitacao;
			DB::$item->save($detail);
			return $detail;
		}

		/**
		 * Remove um item de detalhamento na solicitação
		 * @param ObjectId $detail_id O ObjectId do item a ser removido
		 * @return void
		 */
		public function removeDetail($detail_id){
			DB::$item->remove(array("_id" => $detail_id));
		}

		/**
		 * @return MongoCursor Os itens de detalhamento da solicitação
		 */
		public function getDetails(){
			return DB::$item->find(array('solicitacao.$id' => $this->id));
		}

		/**
		 * Cria um histórico da solicitação
		 * @param string $mensagem A mensagem a ser gravada no histórico
		 * @return void
		 */
		private function addHistory($mensagem){
			$ref_solicitacao = MongoDBRef::create('solicitacao',$this->id);
			DB::$historico->save(array("data" => new MongoDate(), "solicitacao" => $ref_solicitacao, "msg" => $mensagem));
		}

		/**
		 * @return MongoCursor Os históricos da solicitação
		 */
		public function getHistory(){
			return DB::$historico->find(array('solicitacao.$id' => $this->id))->sort(array('data',1));
		}

		/**
		 * Altera o status da solicitação
		 * @param mixed $status
		 */
		private function setStatus($status){
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
			if(gettype($status) == 'string'){
				$status = $this->status_mapping[$status];
			} elseif (gettype($status) != 'integer' || $status < 0 || $status > 8) {
				throw new Exception("Status inválido.", 1);
			}
			$this->obj['status'] = $status;
			$this->addHistory("O status da solicitação foi alterado para <strong>" . $verbal_status[$status] . "</strong>");
			$this->save();
		}

		/**
		 * Altera o status da solicitação manualmente
		 * @param int $status O novo status da solicitação
		 * @param string $user O usuário que alterou o status
		 * @return void
		 */
		public function changeStatus($status, $user){
			$this->setStatus($status);
			$this->observe($user,"Status alterado manualmente pelo administrador.");
			$uinfo = AD::info($user,array('displayname'));
			$from = array('email' => $user . ADLDAP_ACCOUNT_SUFFIX, 'nome' => $uinfo['displayname']);
			$this->notifier->send($this,'status',array('from' => $from));
		}

		/**
		 * Anexa um arquivo a solicitação
		 * @param string $fieldName O nome do campo FILE do formulário (POST: multipart/form-data)
		 * @return ObjectId O id do arquivo anexado
		 */
		public function attachFile($fieldName, $user){
			$ref_solicitacao = MongoDBRef::create('solicitacao',$this->id);
			$uinfo = AD::info($user,array('displayname'));
			$name = $_FILES[$fieldName]['name'];
			$type = $_FILES[$fieldName]['type'];
			$ext = substr($name,-3);
			switch($ext) {
				case "jpg": $type = "image/jpeg"; break;
				case "gif": $type = "image/gif"; break;
				case "png": $type = "image/png"; break;
				case "txt": $type = "text/plain"; break;
				case "pdf": $type = "application/pdf"; break;
				case "zip": $type = "application/x-zip"; break;
			}
			$fid = DB::$grid->storeUpload($fieldName,array(
				"solicitacao" => $ref_solicitacao, 
				"data" => new MongoDate(),
				"user" => $user,
				"contentType" => $type
			));
			$this->addHistory("O arquivo " . $name . " foi anexado a solicitação por " . $uinfo[0]['displayname'][0]);
			return $fid;
		}

		/**
		 * @return MongoGridFSCursor A lista de anexos da solicitação
		 */
		public function getAttachments(){
			return DB::$grid->find(array('solicitacao.$id' => $this->id));
		}

		/**
		 * Remove um arquivo anexado a solicitação
		 * @param string $fileId O ObjectId do arquivo a ser removido
		 * @return void
		 */
		public function removeFile($fileId, $user){
			$file = DB::$grid->findOne(array('_id' => $fileId));
			$uinfo = AD::info($user,array('displayname'));
			DB::$grid->delete($fileId);
			$this->addHistory("O arquivo " . $file->getFilename() . " foi excluído por " . $uinfo[0]['displayname'][0]);
			
		}

		/**
		 * @return MongoCursor as observações da solicitação
		 */
		public function getObservations(){
			return DB::$observacao->find(array('solicitacao.$id' => $this->id))->sort(array('data',-1));			
		}

		/********************************************************************
		 * FUNÇÔES DE WORKFLOW
		 ********************************************************************/

		/**
		 * Envia a solicitação para aprovação
		 * @return void
		 */
		public function send(){
			# Atualiza as informações de aprovador, cotador e executor
			if($this->obj['pre_aprovador'] == null){
				$ad_pre_aprovador = $this->solicitante['manager'];
				$virg_pos = stripos($ad_pre_aprovador, ",");
				$ad_pre_aprovador = substr($ad_pre_aprovador, 3, $virg_pos-3);
				$ad_pre_aprovador = $this->ad->user()->all(false,$ad_pre_aprovador);
				$this->pre_aprovador = $ad_pre_aprovador[0];
				$this->save();
			}
			if($this->solicitante['user'] == $this->pre_aprovador['user']){
				$this->preApprove($this->pre_aprovador['user']);
				if(!$this->tipo['cotar']){
					# Altera o status da solicitação para pendente se a solicitação não precisa ser cotada
					$this->setStatus(1);
				}
			}else{
				# Altera o status da solicitação
				$this->setStatus(1);
				# Envia o e-mail de pré aprovação
				$this->notifier->send($this,'send');
			}
		}

		/**
		 * Processa a pré-aprovação
		 * @return void
		 */
		public function preApprove($pre_aprovador){
			# Confirma a pré-aprovação
			$this->pre_aprovador = $pre_aprovador;
			$this->pre_aprovado = true;
			$this->save();
			# Registra no histórico
			$this->addHistory("<strong>" . $this->pre_aprovador['displayname'] . " pré-aprovou essa solicitação</strong>");
			# Verifica se precisa cotar
			if($this->tipo['cotar'] && !$this->cotado){
				# Envia para cotação
				$this->sendToQuote();
			}
			# Envia para o aprovador
			$this->notifier->send($this,'pre-approve-1');
			# Avisa os stakeholders
			$this->notifier->send($this,'pre-approve-2');
		}

		/**
		 * Devolve a solicitação para revisão
		 * @return void
		 */
		public function devolve($aprovador){
			if($this->pre_aprovado){
				$this->aprovador = $aprovador;
				$this->save();
				$pessoa = $this->aprovador;
			}else{
				$pessoa = $this->pre_aprovador;
			}
			# Registra no histórico
			$this->addHistory("<strong>" . $pessoa['displayname'] . " devolveu a solicitação para revisão</strong>");
			# Altera o status
			$this->setStatus(4);
			# Avisa o usuário
			$this->notifier->send($this,'devolve-1');
			# Avisa os stakeholders
			$this->notifier->send($this,'devolve-2');
		}

		/**
		 * Avisa que a solicitação foi revisada
		 * @return void
		 */
		public function update(){
			# Registra no histórico
			$this->addHistory("<strong>" . $this->solicitante['displayname'] . " revisou a solicitação.</strong>");
			# Altera o status
			$this->setStatus(1);
			# Avisa os stakeholders
			$this->notifier->send($this,'update');
		}

		/**
		 * Adiciona uma observação na solicitação
		 * @param string $author O usuário (ad) que criou a observação
		 * @param string $text O texto da observação
		 * @return void
		 */
		public function observe($author, $text){
			$ref_solicitacao = MongoDBRef::create('solicitacao',$this->id);
			$uinfo = AD::info($author,array('displayname'));
			DB::$observacao->save(array("solicitacao" => $ref_solicitacao, "autor" => $author, "texto" => $text, "data" => new MongoDate()));
			$this->addHistory($uinfo[0]['displayname'][0] . " adicionou uma observação");
		}

		/**
		 * Envia a solicitação para o cotador
		 */
		public function sendToQuote(){
			# Registra no histórico
			$this->addHistory("<strong>A solicitação foi enviada para cotação.</strong>");
			# Altera o status
			$this->setStatus(5);
			# Avisa o cotador
			$this->notifier->send($this,'send-to-quote-1');
			# Avisa os stakeholders
			$this->notifier->send($this,'send-to-quote-2');
		}

		/**
		 * Anexa a cotação da solicitação
		 */
		public function quote($fieldName, $cotador){
			# Confirma a cotação
			$this->cotador = $cotador;
			$this->cotado = true;
			$this->save();
			$cotador = $this->cotador;
			# Anexa o arquivo
			$file = $this->attachFile($fieldName, $cotador['user']);
			# Registra no histórico
			$this->addHistory("<strong>" . $cotador['displayname'] . " cotou a solicitação</strong>");
			# Altera o status
			if($this->status[0] == 5){
				$this->setStatus(1);
			}
			# Avisa os stakeholders
			$this->notifier->send($this,'quote', array('quote_link' => 'SITE_BASEd/' . $file));
		}

		/**
		 * Envia a solicitação para análise
		 * @param string $user o usuário que vai fazer a análise
		 * @return void
		 */
		public function sendToAnalysis($user,$aprovador){
			if($this->pre_aprovado){
				$this->aprovador = $aprovador;
			}
			$this->analisador = $user;
			$this->save();
			# Registra no histórico
			$this->addHistory("<strong>A solicitação foi enviada para análise.</strong>");
			# Altera o status
			$this->setStatus(6);
			# Avisa o analisador
			$this->notifier->send($this,'send-to-analysis-1');
			# Avisa os stakeholders
			$this->notifier->send($this,'send-to-analysis-2', array('analisador_name' => $this->analisador['displayname']));
		}

		/**
		 * Registra o comentário do especialistas
		 * @param boolean $problem Se existe um problema com a solicitação
		 * @param string $text O comentário do especialista
		 */
		public function analyze($problem, $text){
			$analisador = $this->analisador;
			# Adiciona a observação
			$resposta = $problem ? "<strong>Com problemas: </strong>" : "<strong>Sem problemas: </strong>";
			$this->observe($analisador['user'], $resposta . $text);
			# Confirma a análise
			$this->analisado = true;
			$this->save();
			# Registra no histórico
			$this->addHistory("<strong>" . $analisador['displayname'] . " analisou a solicitação e respondeu <i>" . ($problem ? "com problemas" : "sem problemas") . "</i></strong>");
			# Altera o status
			$this->setStatus(1);
			# Avisa os stakeholders
			$this->notifier->send($this,'analyze',array('resposta' => $resposta, 'text' => $text));
		}

		/**
		 * Recusa a solicitação do usuário
		 * @return void
		 */
		public function refuse($aprovador){
			# Confirma a recusa
			if($this->pre_aprovado){
				$this->aprovador = $aprovador;
			}
			$this->aprovado = false;
			$this->save();
			# Registra no histórico
			$this->addHistory("<strong>" . $this->aprovador['displayname'] . " recusou a solicitação</strong>");
			# Altera o status
			$this->setStatus(3);
			# Avisa o solicitante
			$this->notifier->send($this,'refuse-1');
			# Avisa os stakeholders
			$this->notifier->send($this,'refuse-2');
		}

		/**
		 * Aprova a solicitação
		 * @return void
		 */
		public function approve($aprovador){
			if($this->aprovado){
				throw new Exception("A solicitação já foi aprovada antes.");
			}
			# Confirma a aprovação
			$this->aprovador = $aprovador;
			$this->aprovado = true;
			$this->save();
			# Registra no histórico
			$this->addHistory("<strong>" . $this->aprovador['displayname'] . " aprovou a solicitação</strong>");
			# Altera o status
			$this->setStatus(2);
			# Avisa o executor
			$this->notifier->send($this,'approve-1');
			# Avisa o solicitante
			$this->notifier->send($this,'approve-2');
			# Avisa os stakeholders
			$this->notifier->send($this,'approve-3');
		}

		/**
		 * Confirma a execução da solicitação
		 * @return void
		 */
		public function run($executor){
			# Confirma a execução
			$this->executor = $executor;
			$this->executado = true;
			$this->save();
			$executor = $this->executor;
			# Registra no histórico
			$this->addHistory("<strong>" . $this->executor['displayname'] . " executou a solicitação</strong>");
			# Altera o status
			$this->setStatus(7);
			# Avisa os stakeholders
			$this->notifier->send($this,'run');
		}

		/**
		 * Encaminha a solicitação
		 * @param string $user O usuário para quem a solicitação está sendo encaminhada
		 * @param string $message Uma mensagem para o usuário
		 * @return void
		 */
		public function refer($user,$message,$sender){
			$uinfo = AD::info($user,array('displayname'));
			switch ($this->status[0]) {
				case 1: 
					$acao = "aprovada";
					if($this->pre_aprovado){
						$this->aprovador = $sender;
						$pessoa = $this->aprovador;
					} else {
						$pessoa = $this->pre_aprovador;
					}
					break;
				case 5: 
					$acao = "cotada";
					$this->cotador = $sender;
					$pessoa = $this->cotador;
					break;
				case 6: 
					$acao = "análisada";
					$pessoa = $this->analisador;
					break;
				case 2: 
					$acao = "executada";
					$this->executor = $sender;
					$pessoa = $this->executor;
					break;
			}
			$this->save();

			# Registrar no histórico
			$this->addHistory("<strong>" . $pessoa['displayname'] . " encaminhou a solicitação para " . $uinfo[0]['displayname'][0] . "</strong>");
			# Avisa o novo usuário da função
			$this->notifier->send($this,'refer-1',array('to' => $user, 'acao' => $acao, 'message' => $message));
			# Avisa os stakeholders
			$this->notifier->send($this,'refer-2',array('acao' => $acao, 'user' => $uinfo[0]['displayname'][0]));
			# Substitui o usuário da função
			if ($this->status[0] == 1) {
				if ($this->pre_aprovado) {
					$this->aprovador = $user;
				} else {
					$this->pre_aprovador = $user;
				}
			} elseif ($this->status[0] == 5) {
				$this->cotador = $user;
			} elseif ($this->status[0] == 6) {				
				$this->analisador = $user;
			} elseif ($this->status[0] == 2 && !$this->executado) {
				$this->executor = $user;
			}
			$this->save();
		}

		/**
		 * Remove a solicitação e tudo relacionado
		 * @return void
		 */
		public function delete(){
			if($this->status[0] == 0 || $this->status[0] == 7 || $this->status[0] == 3){
				# Excluir o histórico
				foreach ($this->getHistory() as $h) {
					DB::$historico->remove(array( "_id" => $h['_id']));
				}
				# Excluir os anexos
				foreach ($this->getAttachments() as $a) {
					DB::$grid->delete($a->file['_id']);
				}
				# Excluir as observações
				foreach ($this->getObservations() as $o) {
					DB::$observacao->remove(array( "_id" => $o['_id']));
				}
				# Excluir os itens da solicitação
				foreach ($this->getDetails() as $d) {
					DB::$item->remove(array( "_id" => $d['_id']));
				}
				if($this->status[0] == 7 || $this->status[0] == 3){
					# Avisa os stakeholders
					$this->notifier->send($this,'delete');
				}
				# Exclui a solicitação
				DB::$solicitacao->remove(array( "_id" => $this->obj['_id']));
			}
		}

		/**
		 * Cancela a solicitação atual
		 * @param string $pessoa Usuário que fez o cancelamento
		 * @param string $causa Motivo do cancelamento
		 * @return void
		 */
		public function cancel($pessoa, $causa){
			$pname = AD::info($pessoa,array('displayname'));
			# Altera o status para cancelado
			$this->setStatus(8);
			# Registra o motivo do cancelamento
			$this->observe($pessoa,$causa);
			# Registra o cancelamento
			$this->addHistory("<strong>" . $pname[0]['displayname'][0] . " cancelou a solicitação.</strong>");
			# Avisa os stakeholders
			$from = array('email' => $pessoa . ADLDAP_ACCOUNT_SUFFIX, 'nome' => $pname[0]['displayname'][0]);
			$this->notifier->send($this,'cancel',array('from' => $from, 'causa' => $causa));
		}

		/********************************************************************
		 * FUNÇÔES ESTÁTICAS
		 ********************************************************************/

		/**
		 * Cria um novo rascunho de solicitação
		 * @param string $user O nome do usuário no AD
		 * @param string $tipo O ObjectId do tipo de solicitação
		 * @param string $descricao A descroção da solicitação
		 * @param string $prazo A data limite para a solicitação
		 * @param string $centro O ObjectId do centro de custos da solicitação
		 * @return Solicitacao
		 */
		public static function create($user,$tipo,$descricao,$prazo,$centro){
			if(!$user){
				throw new Exception("Sua sessão deve ter expirado, a solicitação não foi salva.");
			}
			$ad = AD::$ad;

			$ref_tipo = MongoDBRef::create("tipo", new MongoId($tipo));
			$ref_centro = MongoDBRef::create("centro", new MongoId($centro));
			$ad_solicitante = $ad->user()->info($user,array('department','manager'));
			$ad_pre_aprovador = $ad_solicitante[0]['manager'][0];
			$virg_pos = stripos($ad_pre_aprovador, ",");
			$ad_pre_aprovador = substr($ad_pre_aprovador, 3, $virg_pos-3);
			$ad_pre_aprovador = $ad->user()->all(false,$ad_pre_aprovador);
			$ad_pre_aprovador = $ad_pre_aprovador[0];
			$ad_departamento = $ad_solicitante[0]['department'][0];

			$solicitante = $user;
			
			$obj_solicitacao = array(
				"data" => new MongoDate(),
				"tipo" => $ref_tipo,
				"descricao" => $descricao,
				"solicitante" => $solicitante,
				"departamento" => $ad_departamento,
				"prazo" => $prazo,
				"centro" => $ref_centro,
				"numero" => self::counter("solicitacao"),
				"status" => 0,
				"pre_aprovado" => false,
				"aprovado" => false,
				"cotado" => false,
				"analisado" => false,
				"executado" => false,
				"pre_aprovador" => $ad_pre_aprovador,
				"aprovador" => null,
				"cotador" => null,
				"analisador" => null,
				"executor" => null
			);
			DB::$solicitacao->save($obj_solicitacao);
			$s = new Solicitacao($obj_solicitacao["_id"]);
			$ref_solicitacao = MongoDBRef::create('solicitacao',$obj_solicitacao['_id']);
			DB::$historico->save(array("data" => new MongoDate(), "solicitacao" => $ref_solicitacao, "msg" => "<strong>A " . $s->tipo['nome'] . " #" . $s->numero . " foi criada.</strong>"));
			return $s;
		}

		/**
		 * Retorna uma solicitação a partir do número sequencial dela.
		 * @param int numero O número sequencial da solicitação
		 * @return Solicitacao A solicitacao correspondente ao número
		 */
		public static function getByNumero($numero){
			$obj_solicitacao = DB::$solicitacao->findOne(array("numero"=>$numero));
			if(!$obj_solicitacao) throw new Exception("Não existe uma solicitação com esse número.", 1);
			
			return new Solicitacao((string)$obj_solicitacao["_id"]);
		}

	}
?>
