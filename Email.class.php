<?php

/**
 * Classe estática para envio de e-mails
 * @author folksilva
 *
 */
class EMAIL {
	/**
	 * Enviar e-mail
	 * @param mixed $from A pessoa que enviou a mensagem, um array ou uma string. Ex.: array('nome'=>'Fulano','email'=>'fulano@dominio.com') ou fulano@dominio.com
	 * @param mixed $to As pessoas destinadas, um array ou uma string. Ex.: array(array('nome'=>'Fulano','email'=>'fulano@dominio.com'),array('nome'=>'Fulano','email'=>'fulano@dominio.com'))
	 * @param string $subject O assunto do e-mail
	 * @param string $body O corpo do e-mail
	 * @return boolean Se o e-mail foi enviado com sucesso
	 */
	public static function send($from,$to,$subject,$body,$cc){
		$mail = new PHPMailer();
		$mail->isSMTP();
		$mail->Host = SMTP_SERVER;
		$mail->IsHTML(true);
		$mail->CharSet = 'utf-8';
		if(SMTP_AUTH){
			$mail->SMTPAuth = true;
			$mail->Username = SMTP_USERNAME;
			$mail->Password = SMTP_PASSWORD;
		}
		$mail->From = SMTP_DEFAULT_SENDER_EMAIL;
		$mail->FromName = SMTP_DEFAULT_SENDER_NAME;
		# A pessoa responsável pelo e-mail
		if (gettype($from) == "string") {
			$mail->AddReplyTo($from);
		} elseif (gettype($from) == "array") {
			$mail->AddReplyTo($from['email'],$from['nome']);
		}
		# As pessoas destinadas
		if (gettype($to) == "string"){
			$mail->AddAddress($to);
		} elseif (gettype($to) == "array") {
			foreach ($to as $to_a) {
				$mail->AddAddress($to_a);
			}
		}
		# As pessoas para copiar no e-mail
		if($cc){
			if (gettype($cc) == "string"){
				$mail->AddCC($cc);
			} elseif (gettype($cc) == "array") {
				foreach ($cc as $cc_a) {
					$mail->AddCC($cc_a);
				}
			}
		}
		# O assunto da mensagem
		$mail->Subject = $subject;
		# O corpo da mensagem
		$mail->Body = $body;

		$e = $mail->Send();
		if(!$e){
			throw new Exception($mail->ErrorInfo, 1);
		}
		return $e;
	}

	/**
	* Cria o código HTML para o corpo de um email contendo a solicitação
	* @param Solicitacao $solicitacao O wrapper da solicitacao
	* @param string $text O texto a adicionar no fim do template e-mail
	* @param array $variables As variáveis a substituir no texto adicionado array('chave' => 'valor')
	* @return string O template renderizado
	*/
	public static function getTemplate($solicitacao,$text,$variables){
		# Definindo a base do template
		$template = '<html><body><style>div,span,td,th,strong,p{font-family: Helvetica, Arial, sans-serif;font-size:12px;}hr {border: none;border-bottom: solid 1px #000000;margin-bottom: 10px;}</style>';
		$template .= '<table width="100%" cellpadding="0" cellspacing="10" border="0" style="border:solid 1px #000000;"><tr><td><div align="center"><img src="' . SITE_PATH . ORG_WATERMARK . '" /></div>';
		$template .= '<div align="center" style="font-size:22px;">' . mb_strtoupper($solicitacao->tipo['nome'],'UTF-8') . '</div><hr>';
		$template .= '<table width="100%" cellpadding="0" cellspacing="2" border="0"><tr><td><strong>Descrição: </strong><span id="descricao">' . $solicitacao->descricao . '</span></td><td width="150"><strong>Data: </strong><span id="data">' . date('d/m/Y',$solicitacao->data->sec) . '</span></td><td width="200"><strong>Prazo: </strong><span id="prazo">' . $solicitacao->prazo . " (" . $solicitacao->prazo_textual . ')</span></td></tr></table>';
		$template .= '<table width="100%" cellpadding="0" cellspacing="2" border="0"><tr><td><strong>Solicitante: </strong><span id="requisitante">' . $solicitacao->solicitante['displayname'] . '</span></td><td><strong>Departamento: </strong><span id="departamento">' . $solicitacao->departamento . '</span></td><td width="150"><strong>C. Custo: </strong><span id="centro">' . $solicitacao->centro['apelido'] . '</span></td></tr></table><table width="100%" cellpadding="2" cellspacing="0" border="1"><tr>';
		$columns = array();
		foreach ($solicitacao->tipo['detalhe'] as $d) {
			$columns[] = $d['nome_unico'];
			$template .= "<th align='left' style='background:#E0E0E0;'>" . $d['nome'] . "</th>";
		}
		$template .= '</tr>';
		$detalhes = $solicitacao->detalhes;
		$odd = false;
		foreach ($detalhes as $item) {
			$template .= '<tr>';
			foreach ($columns as $column) {
				$template .= '<td ' . ($odd ? 'style="background:#F3F3F3;"' : '') . '>' . $item[$column] . '</td>';
			}
			$odd = !$odd;
			$template .= '</tr>';
		}
		$template .= '</table><strong>Observações:</strong><div>';
		$observations = $solicitacao->observacoes;
		foreach ($observations as $o) {
			$author = AD::info($o['autor'],array('displayname'));
			$author = $author[0]['displayname'][0];
			$template .= '<div style="margin-left:10px;margin-bottom:5px;"><i style="font-size:11px;">Em ' . date('d/m/Y',$o['data']->sec) . ' por ' . $author . '</i><div style="font-size:11px;">' . $o['texto'] . '</div></div>';
		}
		$template .= '</div><hr><table width="100%" cellpadding="1" cellspacing="1" border="0" class="control"><tr>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->pre_aprovado ? '<strong>Pré-aprovado por</strong>' : '') . '</td>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->aprovado ? '<strong>Aprovado por</strong>' : '') . '</td>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->analisado ? '<strong>Analisado por</strong>' : '') . '</td>';
		$template .= '<td align="right" style="font-size: 9px;" height="14">' . $solicitacao->id . '</td>';
		$template .= '<td rowspan="2" width="70" height="70"><img src="' . SITE_PATH . $solicitacao->id . '.qr"  width="70" height="70"></td>';
		$template .= '</tr><tr>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->pre_aprovado ? $solicitacao->pre_aprovador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->pre_aprovador['title'] . '</div>' : '') . '</td>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->aprovado ? $solicitacao->aprovador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->aprovador['title'] . '</div>' : '') . '</td>';
		$template .= '<td width="200" height="14" align="center">' . ($solicitacao->analisado ? $solicitacao->analisador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->analisador['title'] . '</div>' : '') . '</td>';
		$template .= '<td align="right" style="font-size: 22px;">' . $solicitacao->numero . '</td></tr></table></table>';
		$template .= '<p>Esta é uma solicitação da central de solicitações. Veja mais detalhes no link <a href="' . SITE_PATH . $solicitacao->numero . '">' . SITE_PATH . $solicitacao->numero . '</a>.<hr>';
		# Preparando o texto da mensagem
		$variables['SITE_BASE'] = SITE_PATH;
		$variables['ID'] = (string)$solicitacao->id;
		foreach ($variables as $key => $value) {
			$text = str_replace($key, $value, $text);
		}
		$template .= $text;
		# Fechando o template
		$template .= '<div>Se deseja imprimir essa solicitação acesse <a href="' . SITE_PATH . $solicitacao->numero . '/imprimir">' . SITE_PATH . $solicitacao->numero . '/imprimir</a>.</div>';
		$template .= '<div style="margin-top:30px;font-size:10px;">Esse e-mail foi enviado automaticamente pela central de solicitações, não é preciso responder.</div>';
		$template .= '<hr><div style="font-size:18px;font-weight:bold;line-height:27px;">' . ORG_NAME . '</div>';
		$template .= '<div style="color:#999999;font-size:11px;">' . ORG_LEGAL . '</div></body></html>';
		# Retornando o template
		return $template;
	}
}