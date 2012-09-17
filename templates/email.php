<?php ob_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title><?= $subject ?></title>
	<style type="text/css">
		/* Based on The MailChimp Reset INLINE: Yes. */  
		/* Client-specific Styles */
		#outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
		body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;} 
		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes.*/ 
		.ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */  
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;}
		/* Forces Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */ 
		#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 130% !important;}
		/* End reset */

		/* Some sensible defaults for images
		Bring inline: Yes. */
		img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;} 
		a img {border:none;} 
		.image_fix {display:block;}

		/* Yahoo paragraph fix
		Bring inline: Yes. */
		p {margin: 1em 0;}

		/* Hotmail header color reset
		Bring inline: Yes. */
		h1, h2, h3, h4, h5, h6 {color: #333 !important;}

		h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: #0088cc !important;}

		h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
		color: #005580 !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
		}

		h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
		color: #005580 !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
		}

		/* Outlook 07, 10 Padding issue fix
		Bring inline: No.*/
		table td {border-collapse: collapse;}

		/* Remove spacing around Outlook 07, 10 tables
		Bring inline: Yes */
		table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }

		/* Styling your links has become much simpler with the new Yahoo.  In fact, it falls in line with the main credo of styling in email and make sure to bring your styles inline.  Your link colors will be uniform across clients when brought inline.
		Bring inline: Yes. */
		a {color: #0088cc;}

	</style>

	<style type="text/css">
		body {
			color: #333 !important;
			font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			font-size: 14px;
		}
		h1 {font-size: 20px;}
		hr {border:none;border-bottom: solid 1px #333;margin:10px;}
		#document {
			background-color: white !important;
			border: solid 1px #333 !important;
			font-size: 12px;
			color: #333 !important;
			text-shadow: none;
			-webkit-box-shadow: 1px 2px 4px #666;
			   -moz-box-shadow: 1px 2px 4px #666;
			    -ms-box-shadow: 1px 2px 4px #666;
			     -o-box-shadow: 1px 2px 4px #666;
			        box-shadow: 1px 2px 4px #666;
		}
		#document hr {border:none;border-bottom: solid 1px #333333;}
		#document td { padding: 5px !important;}
		a.btn {
			font-weight: bold;
      	}
	</style>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
	<tr>
		<td valign="top">

		<table cellpadding="0" cellspacing="0" border="0" align="center" style="margin-top:10px;">
			<tr>
				<td width="40" valign="middle"><img class="image_fix" src="{SITE_BASE}res/img/icon.png" alt="ORG_ICON" title="ORG_ICON" width="40" height="40" /></td>
				<td valign="middle"><h1>Central de Solicitações</h1></td>
			</tr>
		</table>

		<table id="document" cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td style="background: no-repeat top right url({SITE_BASE}res/img/doc-<?= $solicitacao->status[1] ?>.png);padding: 10px !important;">
					<div align="center"><img src="<?= SITE_PATH . ORG_WATERMARK ?>" /></div>
					<div align="center" style="font-size:22px;"><?= mb_strtoupper($solicitacao->tipo['nome'],'UTF-8') ?></div>
					<hr>
					<table width="100%" cellpadding="0" cellspacing="2" border="0">
						<tr>
							<td><strong>Descrição: </strong><span id="descricao"><?= $solicitacao->descricao ?></span></td>
							<td width="150"><strong>Data: </strong><span id="data"><?= date('d/m/Y',$solicitacao->data->sec) ?></span></td>
							<td width="200"><strong>Prazo: </strong><span id="prazo"><?= $solicitacao->prazo ?> (<?= $solicitacao->prazo_textual ?>)</span></td>
						</tr>
					</table>
					<table width="100%" cellpadding="0" cellspacing="2" border="0">
						<tr>
							<td><strong>Solicitante: </strong><span id="requisitante"><?= $solicitacao->solicitante['displayname'] ?></span></td>
							<td><strong>Departamento: </strong><span id="departamento"><?= $solicitacao->departamento ?></span></td>
							<td width="150"><strong>C. Custo: </strong><span id="centro"><?= $solicitacao->centro['apelido'] ?></span></td>
						</tr>
					</table>
					<table width="100%" cellpadding="2" cellspacing="0" border="1">
						<tr>
						<?php
						$columns = array();
						foreach ($solicitacao->tipo['detalhe'] as $d) {
							$columns[] = $d;
						?>
							<th align='left' style='background:#E0E0E0;'><?= $d['nome'] ?></th>
						<?php
						}
						$detalhes = $solicitacao->detalhes;
						$odd = false;
						?>
						</tr>
						<?php foreach ($detalhes as $item) { ?>
						<tr>
							<?php 
							foreach ($columns as $column) { 
								$v = $item[$column['nome_unico']];
								$v = $column['tipo'] == "checkbox" ? $v ? "Sim" : "Não" : $v; 
							?>
							<td<?= ($odd ? ' style="background:#F3F3F3;"' : '') ?>><?= $v ?></td>
							<?php } ?>
						</tr>
						<?php $odd = !$odd; } ?>
					</table>
					<strong>Observações:</strong>
					<div>
					<?php 
						$observations = $solicitacao->observacoes;
						foreach ($observations as $o) {
							$author = AD::info($o['autor'],array('displayname'));
							$author = $author[0]['displayname'][0];
					?>
						<div style="margin-left:10px;margin-bottom:5px;">
							<i style="font-size:11px;">Em <?= date('d/m/Y',$o['data']->sec) ?> por <?= $author ?></i>
							<div style="font-size:11px;"><?= $o['texto'] ?></div>
						</div>
					<?php } ?>
					</div>
					<hr>
					<table width="100%" cellpadding="1" cellspacing="1" border="0" class="control">
						<tr>
							<td width="200" height="14" align="center"><?= ($solicitacao->pre_aprovado ? '<strong>Pré-aprovado por</strong>' : '') ?></td>
							<td width="200" height="14" align="center"><?= ($solicitacao->aprovado ? '<strong>Aprovado por</strong>' : '') ?></td>
							<td width="200" height="14" align="center"><?= ($solicitacao->analisado ? '<strong>Analisado por</strong>' : '') ?></td>
							<td align="right" style="font-size: 9px;" height="14"><?= $solicitacao->id ?></td>
							<td rowspan="2" width="70" height="70"><img src="<?= SITE_PATH . $solicitacao->id ?>.qr" width="70" height="70"></td>
						</tr><tr>
							<td width="200" height="14" align="center"><?= ($solicitacao->pre_aprovado ? $solicitacao->pre_aprovador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->pre_aprovador['title'] . '</div>' : '') ?></td>
							<td width="200" height="14" align="center"><?= ($solicitacao->aprovado ? $solicitacao->aprovador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->aprovador['title'] . '</div>' : '') ?></td>
							<td width="200" height="14" align="center"><?= ($solicitacao->analisado ? $solicitacao->analisador['displayname'] . '<div style="font-size:10px;">' . $solicitacao->analisador['title'] . '</div>' : '') ?></td>
							<td align="right" style="font-size: 22px;"><?= $solicitacao->numero ?></td>
						</tr>
					</table>
				</td>
			</tr>
			
		</table>

		<table cellpadding="0" cellspacing="0" border="0" align="center" style="margin:20px;">
			<tr>
				<td valign="top" style="text-align:justify;">
					<p style="font-weight:bold;margin-bottom:20px;display:block;"><?= $message ?></p>
					<?php foreach ($actions as $action) { ?>
						<p><a href="<?= $action['link'] ?>" class="btn"><?= $action['label'] ?></a> - <?= $action['tip'] ?></p>					
					<?php } ?>
				</td>
			</tr>

		</table>

		<hr>

		<table cellpadding="0" cellspacing="0" align="center" style="margin:10px;margin-top:0px;">
			<tr>
				<td>
					<div style="font-size:18px;font-weight:bold;line-height:27px;"><?= ORG_NAME ?></div>
					<div style="color:#999999;font-size:11px;"><?= ORG_LEGAL ?></div>
					<div style="margin-top:30px;font-size:10px;">Esse e-mail foi enviado automaticamente pela central de solicitações, não é preciso responder.</div>
				</td>
			</tr>
		</table>

		</td>
	</tr>
</table>
</body>
</html>
<?php 
$template = ob_get_clean();
$variables['{SITE_BASE}'] = SITE_PATH;
$variables['{ID}'] = (string)$solicitacao->id;
foreach ($variables as $key => $value) {
	$template = str_replace($key, $value, $template);
}
?>
