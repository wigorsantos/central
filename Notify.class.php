<?php

class Notify {

	private $solicitacao;
	private $subjects;

	/**
	 * Construtor da classe
	 * @param Solicitacao $solicitacao A solicitação
	 */
	public function __construct(){
		$this->subjects = array(
			"send" => "APROVAR",
			"pre-approve-1" => "APROVAR",
			"pre-approve-2" => "PRÉ-APROVADO",
			"devolve-1" => "REVISAR",
			"devolve-2" => "DEVOLVIDO PARA REVISÃO",
			"update" => "REVISADO",
			"send-to-quote-1" => "COTAR",
			"send-to-quote-2" => "ENVIADO PARA COTAÇÃO",
			"quote" => "COTADO", # EXTRA quote_link
			"send-to-analysis-1" => "ANALISAR",
			"send-to-analysis-2" => "ENVIADO PARA ANALISE", # EXTRA analisador_name
			"analyze" => "ANALISADO", # EXTRAS resposta e text
			"refuse-1" => "RECUSADO",
			"refuse-2" => "RECUSADO",
			"approve-1" => "EXECUTAR",
			"approve-2" => "APROVADO",
			"approve-3" => "APROVADO",
			"run" => "EXECUTADO",
			"refer-1" => "ENCAMINHADO", # EXTRAS to, acao, message
			"refer-2" => "ENCAMINHADO", # EXTRAS acao, user
			"delete" => "EXCLUIDO",
			"cancel" => "CANCELADO", # EXTRAS from, causa
			"status" => "ALTERADO" # EXTRAS from
		);
	}

	/**
	 * Envia as notificações sobre uma ação executada
	 * @param string $action A ação que foi executada
	 * @param mixed $extra_params Parametros extras para o corpo da mensagem
	 */
	public function send($solicitacao, $action, $extra_params=array()){
		$this->solicitacao = $solicitacao;
		# 1. Quem está enviando a mensagem?
		$from = $this->getFrom($action);
		if(!$from){
			$from = array($extra_params['from']);
		}
		# 2. Para quem é a mensagem?
		$to = $this->getTo($action);
		if(!$to){
			$to = array($extra_params['to']);
		}
		# 3. Qual o assunto da mensagem?
		$subject = $this->getSubject($action, $from);
		//echo '<pre>';
		# uma mensagem para cada destinatário
		foreach ($to as $target) {
			if(!empty($target)){
				# 4. Qual o conteúdo da mensagem?
				$body = $this->getBody($action, $from, $target, $subject, $extra_params);
				# 5. Envie um e-mail
				EMAIL::send($from, $target . ADLDAP_ACCOUNT_SUFFIX, $subject, $body, false);
				# TO-DO: 6. Notifica via API
				//print_r(array($target, $body));
			}
		}
		//print_r(array($action,$from, $to, $subject, $extra_params));
		//echo '</pre>';
		//if($action == 'refer-2') exit();
	}

	/**
	 * Descobre quem está enviando a mensagem
	 * @param string $action A ação notificada
	 * @return Um array com o email e nome do usuário, ou FALSE caso não tenha sido possível detectar o emitente
	 */
	private function getFrom($action){
		$user = null;
		switch ($action) {
			
			case 'send':
			case 'update':
			case 'delete':
				$user = $this->solicitacao->solicitante;	
				break;
			
			case 'pre-approve-1':
			case 'pre-approve-2':
			case 'send-to-quote-1':
			case 'send-to-quote-2':
				$user = $this->solicitacao->pre_aprovador;
				break;

			case 'approve-1':
			case 'approve-2':
			case 'approve-3':
				$user = $this->solicitacao->aprovador;
				break;

			case 'quote':
				$user = $this->solicitacao->cotador;
				break;

			case 'analyze':
				$user = $this->solicitacao->analisador;
				break;

			case 'run':
				$user = $this->solicitacao->executor;
				break;

			case 'devolve-1':
			case 'devolve-2':
			case 'send-to-analysis-1':
			case 'send-to-analysis-2':
			case 'refuse-1':
			case 'refuse-2':
				if($this->solicitacao->pre_aprovado){
					$user = $this->solicitacao->aprovador;
				}else{
					$user = $this->solicitacao->pre_aprovador;
				}
				break;

			case 'refer-1':
			case 'refer-2':
				if($this->solicitacao->status[0] == 1){
					if($this->solicitacao->pre_aprovado){
						$user = $this->solicitacao->aprovador;
					}else{
						$user = $this->solicitacao->pre_aprovador;
					}
				}elseif($this->solicitacao->status[0] == 5){
					$user = $this->solicitacao->cotador;
				}elseif($this->solicitacao->status[0] == 6){
					$user = $this->solicitacao->analisador;
				}elseif($this->solicitacao->status[0] == 2 && !$this->executado){
					$user = $this->solicitacao->executor;
				}
				break;
			default:
				$user = false;
		}

		return $user ? array('email' => $user['user'] . ADLDAP_ACCOUNT_SUFFIX, 'nome' => $user['displayname']) : false;
	}

	/**
	 * Descobre para quem é a mensagem
	 * @param string $action A ação notificada
	 * @return Um array com o emails e nomes dos usuários, ou FALSE caso não tenha sido possível detectar os destinatários
	 */
	private function getTo($action){
		$users = array();
		switch ($action) {
			
			case 'send':
				$users[] = $this->solicitacao->pre_aprovador['user'];
				break;

			case 'pre-approve-1':
				$users = $this->solicitacao->tipo['aprovador'];
				break;

			case 'pre-approve-2':
				$users = $this->solicitacao->tipo['informar'];
				$users[] = $this->solicitacao->pre_aprovador['user'];
				$users[] = $this->solicitacao->solicitante['user'];
				break;

			case 'devolve-1':
			case 'approve-2':
			case 'refuse-1':
				$users[] = $this->solicitacao->solicitante['user'];
				break;

			case 'devolve-2':
				if($this->solicitacao->pre_aprovado){
					$users = $this->solicitacao->tipo['informar'];
				}
				$users[] = $this->solicitacao->pre_aprovador['user'];
				break;

			case 'update':
			case 'refuse-2':
				if($this->solicitacao->pre_aprovado){
					$users = $this->solicitacao->tipo['informar'];
					$users[] = $this->solicitacao->aprovador['user'];
				}
				$users[] = $this->solicitacao->pre_aprovador['user'];
				break;

			case 'send-to-quote-1':
				$users = $this->solicitacao->tipo['cotador'];
				break;

			case 'send-to-quote-2':
			case 'quote':
			case 'run':
			case 'delete':
			case 'status':
				$users = $this->solicitacao->tipo['informar'];
				if(!$this->solicitacao->aprovador){
					$users = array_merge($users, $this->solicitacao->tipo['aprovador']);
				}else{
					$users[] = $this->solicitacao->aprovador['user'];
				}
				$users[] = $this->solicitacao->solicitante['user'];
				$users[] = $this->solicitacao->pre_aprovador['user'];
				
				break;

			case 'send-to-analysis-1':
				$users[] = $this->solicitacao->analisador['user'];
				break;

			case 'send-to-analysis-2':
			case 'analyze':
			case 'refer-2':
			case 'cancel':
				if($this->solicitacao->pre_aprovado){
					$users = $this->solicitacao->tipo['informar'];
					$users[] = $this->solicitacao->aprovador['user'];
				}
				$users[] = $this->solicitacao->solicitante['user'];
				$users[] = $this->solicitacao->pre_aprovador['user'];
				break;

			case 'approve-1':
				$users = $this->solicitacao->tipo['executor'];
				break;

			case 'approve-3':
				$users = $this->solicitacao->tipo['informar'];
				$users[] = $this->solicitacao->aprovador['user'];
				$users[] = $this->solicitacao->pre_aprovador['user'];
				break;
			
			default:
				$users = false;
				break;
		}
		return $users;
	}

	/**
	 * Constroi o assunta da mensagem
	 * @param string $action A ação notificada
	 * @param array $from A pessoa que está enviando a notificação
	 * @return Uma string com o assunto da mensagem
	 */
	private function getSubject($action,$from){
		$subject = "[" . $this->subjects[$action] . "] " . $this->solicitacao->tipo['nome'] . " #" . $this->solicitacao->numero . " - " . $this->solicitacao->descricao;
		return $subject;
	}

	/**
	 * Constroi o conteúdo da mensagem
	 * @param string $action A ação notificada
	 * @param array $from A pessoa que está enviando a notificação
	 * @param string $target A nome de usuário da pessoa que vai receber a notificação
	 * @param array $extra_params Parametros extra para a construção da mensagem
	 * @return Uma string com o corpo da mensagem
	 */
	private function getBody($action, $from, $target, $subject, $extra_params){
		$message = "";
		$actions = array(
			array(
				'link' => '{SITE_BASE}{ID}',
				'label' => 'Visualizar',
				'tip' => 'Veja mais detalhes da solicitação.'
			),
			array(
				'link' => '{SITE_BASE}{ID}/imprimir',
				'label' => 'Imprimir',
				'tip' => 'Imprima a solicitação.'
			)
		);
		$solicitacao = $this->solicitacao;
		switch ($action) {

			/* Mensagens com ações */
			case 'send':
			case 'pre-approve-1':
				$message = "Esta solicitação precisa da sua aprovação para prosseguir.";
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/decidir?a=aprovar',
					'label' => 'Aprovar',
					'tip' => 'Você está ciente e autoriza essa solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/decidir?a=recusar',
					'label' => 'Recusar',
					'tip' => 'Você não autoriza essa solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/decidir?a=devolver',
					'label' => 'Devolver para revisão',
					'tip' => 'Alguma coisa está errada e precisa ser corrigida.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/decidir?a=analisar',
					'label' => 'Enviar para análise',
					'tip' => 'É preciso que um especialista verifique as informações da solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/encaminhar',
					'label' => 'Encaminhar',
					'tip' => 'Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.'
				);
				break;

			case 'devolve-1':
				$message = "Você precisa revisar esta solicitação. Será necessário editar e salvar a solicitação para que a aprovação continue.";
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/editar',
					'label' => 'Revisar',
					'tip' => 'Verifique as informações e atualize a solicitação.'
				);
				break;

			case 'send-to-quote-1':
				$message = "Esta solicitação precisa da sua cotação para prosseguir.";
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/cotar',
					'label' => 'Cotar',
					'tip' => 'Anexe a cotação para a solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/encaminhar',
					'label' => 'Encaminhar',
					'tip' => 'Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.'
				);
				break;

			case 'quote':
				$message = $from['nome'] . " cotou esta solicitação.";
				$actions[] = array(
					'link' => $extra_params['quote_link'],
					'label' => 'Baixar a cotação',
					'tip' => 'Faça download do arquivo de cotação enviado.'
				);
				break;

			case 'send-to-analysis-1':
				$message = $from['nome'] . " precisa que você analise esta solicitação para decidir se aprova ou não.";
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/analisar',
					'label' => 'Analisar',
					'tip' => 'Responda a análise desta solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/encaminhar',
					'label' => 'Encaminhar',
					'tip' => 'Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.'
				);
				break;

			case 'approve-1':
				$message = "Esta solicitação foi aprovada por " . $from['nome'] . " e precisa ser executada.";
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/executar',
					'label' => 'Confirmar a execução',
					'tip' => 'Confirme que executou esta solicitação.'
				);
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/encaminhar',
					'label' => 'Encaminhar',
					'tip' => 'Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.'
				);
				break;

			case 'refer-1':
				$refer_action = $extra_params['acao'];
				$refer_message = $extra_params['message'];
				$message = $from['nome'] . " encaminhou esta solicitação para ser " . $refer_action . " por você";
				if($refer_message){
					$message .= " e adicionou a seguinte mensagem: <blockquote>" . $refer_message . "</blockquote>";
				} else {
					$message .= ".";
				}
				if($refer_action == 'aprovada') {
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/decidir?a=aprovar',
						'label' => 'Aprovar',
						'tip' => 'Você está ciente e autoriza essa solicitação.'
					);
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/decidir?a=recusar',
						'label' => 'Recusar',
						'tip' => 'Você não autoriza essa solicitação.'
					);
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/decidir?a=devolver',
						'label' => 'Devolver para revisão',
						'tip' => 'Alguma coisa está errada e precisa ser corrigida.'
					);
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/decidir?a=analisar',
						'label' => 'Enviar para análise',
						'tip' => 'É preciso que um especialista verifique as informações da solicitação.'
					);
				} elseif ($refer_action == 'cotada') {
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/cotar',
						'label' => 'Cotar',
						'tip' => 'Anexe a cotação para a solicitação.'
					);
				} elseif ($refer_action == 'análisada') {
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/analisar',
						'label' => 'Analisar',
						'tip' => 'Responda a análise desta solicitação.'
					);
				} elseif ($refer_action == 'executada') {
					$actions[] = array(
						'link' => '{SITE_BASE}{ID}/executar',
						'label' => 'Confirmar a execução',
						'tip' => 'Confirme que executou esta solicitação.'
					);
				}
				$actions[] = array(
					'link' => '{SITE_BASE}{ID}/encaminhar',
					'label' => 'Encaminhar',
					'tip' => 'Se você não pode atender essa solicitação transfira para outra pessoa com autoridade semelhante a sua.'
				);
				break;

			/* Mensagens informativas */
			case 'pre-approve-2':
				$message = "Esta solicitação foi pré-aprovada.";
				break;

			case 'devolve-2':
				$message = "Esta solicitação foi devolvida para revisão.";
				break;

			case 'update':
				$message = "Esta solicitação foi revisada.";
				break;

			case 'send-to-quote-2':
				$message = "Esta solicitação foi enviada para cotação.";
				break;

			case 'send-to-analysis-2':
				$message = "Esta solicitação foi enviada para a análise de " . $extra_params['analisador_name'] . ".";
				break;

			case 'analyze':
				$message = $from['nome'] . " analisou esta solicitação e respondeu: " . $extra_params['resposta'] . $extra_params['text'] . " .";
				break;

			case 'refuse-1':
				$message = $from['nome'] . " recusou a sua solicitação.";
				break;

			case 'refuse-2':
				$message = "Esta solicitação foi recusada.";
				break;

			case 'approve-2':
				$message = $from['nome'] . " aprovou a sua solicitação. Agora ela poderá ser executada.";
				break;

			case 'approve-3':
				$message = "Esta solicitação foi aprovada e agora poderá ser executada.";
				break;

			case 'run':
				$message = $from['nome'] . " confirmou que executou esta solicitação, ela foi automaticamente arquivada.";
				break;

			case 'refer-2':
				$message = "Esta solicitação foi encaminhada para ser " . $extra_params['acao'] . " por " . $extra_params['user'] . ".";
				break;

			case 'delete':
				$message = "Esta solicitação foi excluída, não será mais possível acessá-la.";
				$actions = array();
				break;

			case 'cancel':
				$message = "Esta solicitação foi cancelada. Motivo: " . $extra_params['causa'];
				break;

			case 'status':
				$message = "O status desta solicitação foi alterado manualmente por " . $from['nome'];
				break;

		}
		include 'templates/email.php';
		return $template;
	}

}