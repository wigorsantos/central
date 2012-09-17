<!DOCTYPE html>
<html lang="pt">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="x-ua-compatible" content="ie=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
		<meta name="author" content="Luiz Fernando da Silva <lfsilva@sccorinthians.com.br>" />
		<meta name="copyright" content="Sport Club Corinthians Paulista" />
		<meta name="description" content="Site para fluxo de solicitações diversas" />
		<title><?= $_TITLE ?> - Central de Solicitações</title>
		<link rel="stylesheet" href="<?= SITE_BASE ?>res/css/print.css" />
	</head>
	<body>
		<div class="no-print">
			<a href="<?= SITE_BASE . $s->id ?>">&larr;Voltar</a>
			<div>
				<strong>Como imprimir:</strong>
				<ul>
					<li>Utilize papel no tamanho A4 e com orientação retrato</li>
					<li>Verifique se as margens estão entre 1cm e 3cm</li>
					<li>Remova os cabeçalhos e rodapés da página</li>
					<li>Utilize o atalho <kbd>CTRL</kbd>+<kbd>P</kbd>, o menu Arquivo &rarr; Imprimir ou clique <a href="javascript:window.print();">aqui</a>.</li>
				</ul>
			</div>
		</div>
		<div class="document">
		<div class="document-status"><img src="<?= SITE_BASE ?>res/img/doc-<?= $s->status[1] ?>.png"></div>
		<div class="align-to-center"><img src="<?= SITE_BASE . ORG_WATERMARK ?>" /></div>
		<div class="document-title"><?= mb_strtoupper($s->tipo['nome'],'UTF-8') ?></div>
		<hr>
		<table width="100%" cellpadding="0" cellspacing="2" border="0">
			<tr>
				<td>
					<strong>Descrição: </strong>
					<span id="descricao"><?= $s->descricao ?></span>
				</td>
				<td width="150">
					<strong>Data: </strong>
					<span id="data"><?= date('d/m/Y',$s->data->sec) ?></span>
				</td>
				<td width="200">
					<strong>Prazo: </strong>
					<span id="prazo"><?= $s->prazo ?> (<?= $s->prazo_textual ?>)</span>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="2" border="0">
			<tr>
				<td>
					<strong>Solicitante: </strong>
					<span id="requisitante"><?= $s->solicitante['displayname'] ?></span>
				</td>
				<td>
					<strong>Departamento: </strong>
					<span id="departamento"><?= $s->departamento ?></span>
				</td>
				<td width="150">
					<strong>C. Custo: </strong>
					<span id="centro"><?= $s->centro['apelido'] ?></span>
				</td>
			</tr>
		</table>
		<table width="100%" cellpadding="2" cellspacing="0" class="itens">
			<tr>
			<?php
			$columns = array();
			foreach ($s->tipo['detalhe'] as $d) {
				$columns[] = $d;
				echo "<th>" . $d['nome'] . "</th>";
			}
			?>
			</tr>
			<?php
			$detalhes = $s->detalhes;
			foreach ($detalhes as $item) {
				echo '<tr>';
				foreach ($columns as $column) {
					$v = $item[$column['nome_unico']];
					$v = $column['tipo'] == "checkbox" ? $v ? "Sim" : "Não" : $v;
					echo '<td>' . $v . '</td>';
				}
				echo '</tr>';
			}
			?>
		</table>
		<strong>Observações:</strong>
		<div class="observations">
		<?php
		$observations = $s->observacoes;
		foreach ($observations as $o) {
			$author = AD::info($o['autor'],array('displayname'));
			$author = $author[0]['displayname'][0];
		?>
			<div class="obs">
				<i>Em <?= date('d/m/Y',$o['data']->sec) ?> por <?= $author ?></i>
				<div><?= $o['texto'] ?></div>
			</div>
		<?php } ?>
		</div>
		<hr>
		<table width="100%" cellpadding="1" cellspacing="1" border="0" class="control">
			<tr>
				<td width="200" height="14"><?php if($s->pre_aprovado){ echo '<strong>Pré-aprovado por</strong>'; } ?></td>
				<td width="200" height="14"><?php if($s->aprovado){ echo '<strong>Aprovado por</strong>'; } ?></td>
				<td width="200" height="14"><?php if($s->analisado){ echo '<strong>Analisado por</strong>'; } ?></td>
				<td class="oid" height="14"><?= $s->id ?></td>
				<td rowspan="2" width="70" height="70"><img src="<?= SITE_BASE . $s->id ?>.qr"  width="70" height="70"></td>
			</tr>
			<tr>
				<td><?php if($s->pre_aprovado){ $p = $s->pre_aprovador; echo $p['displayname'] . '<div class="cargo">' . $p['title'] . '</div>'; } ?></td>
				<td><?php if($s->aprovado){ $p = $s->aprovador; echo $p['displayname'] . '<div class="cargo">' . $p['title'] . '</div>'; } ?></td>
				<td><?php if($s->analisado){ $p = $s->analisador; echo $p['displayname'] . '<div class="cargo">' . $p['title'] . '</div>'; } ?></td>
				<td class="oinc"><?= $s-> numero ?></td>
			</tr>
		</table>
	</div>
	</body>
</html>
