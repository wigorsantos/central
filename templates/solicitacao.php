<?php require_once('templates/header.php'); ?>
<?php if(!$_CAN_VIEW){ ?>
		<h1>Não autorizado</h1>
		<p>Desculpe, você não pode ver essa solicitação. Se isso for um engano, envie um <a href="<?= SITE_BASE ?>feedback">feedback</a>.</p>
<?php }else{ ?>
<div class="well">
	<h1><?= $_TITLE ?></h1>
	<div class="btn-group pull-right">
		<a class="btn" href="<?= SITE_BASE . $s->id ?>/imprimir"><i class="icon-print"></i> <span class="hidden-phone">Imprimir</span></a>
	<?php if($s->canEdit($_SESSION['user'])){ ?>
		<a class="btn" href="<?= SITE_BASE . $s->id ?>/editar"><i class="icon-edit"></i> <span class="hidden-phone">Editar</span></a>
	<?php } ?>
	<?php if($s->canSend($_SESSION['user'])){ ?>
		<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/enviar"><i class="icon-ok icon-white"></i> Enviar</a>
	<?php } ?>
	<?php if($s->canDelete($_SESSION['user'])){ ?>
		<a class="btn btn-danger" href="<?= SITE_BASE . $s->id ?>/excluir"><i class="icon-trash icon-white"></i> <span class="hidden-phone">Excluir</span></a>
	<?php } ?>
	<?php if($s->status[0] == 1 && (($s->isPreAprovador($_SESSION['user']) && !$s->pre_aprovado) || $s->isAprovador($_SESSION['user']))){ $msg = "Essa solicitação precisa da sua autorização para prosseguir."; ?>
		<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/decidir"><i class="icon-ok icon-white"></i> Decidir</a>
	<?php } ?>
	<?php if($s->status[0] == 6 && $s->isAnalisador($_SESSION['user'])){ $msg = "Essa solicitação precisa da sua análise para prosseguir."; ?>
		<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/analisar"><i class="icon-ok icon-white"></i> Analisar</a>
	<?php } ?>
	<?php if($s->status[0] == 5 && $s->isCotador($_SESSION['user'])){ $msg = "Essa solicitação precisa da sua cotação para prosseguir."; ?>
		<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/cotar"><i class="icon-ok icon-white"></i> Cotar</a>
	<?php } ?>
	<?php if($s->status[0] == 2 && $s->isExecutor($_SESSION['user']) && !$s->executado){ $msg = "Informe quando você executou a solicitação ou encaminhe para outra pessoa."; ?>
		<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/executar"><i class="icon-ok icon-white"></i> Executar</a>
	<?php } ?>
	<?php if($s->canCancel($_SESSION['user'])){ ?>
		<a class="btn btn-warning" href="<?= SITE_BASE . $s->id ?>/cancelar"><i class="icon-remove icon-white"></i> <span class="hidden-phone">Cancelar</span></a>
	<?php } ?>
	</div>
	
	<?php if(isset($msg)) { echo '<span class="label label-info"><i class="icon-info-sign icon-white"></i> ' . $msg . '</span>'; } ?>
	<div class="clear"></div>
</div>
<div class="container"><div class="row">
	<div class="document doc_<?= $s->status[1] ?> span12">
		<div style="text-align:center;"><img src="<?= SITE_BASE . ORG_WATERMARK ?>" /></div>
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
		<table width="100%" cellpadding="1" cellspacing="1" border="0" class="itens">
			<thead>
			<tr>
			<?php
			$columns = array();
			foreach ($s->tipo['detalhe'] as $d) {
				$columns[] = $d;
				echo "<th>" . $d['nome'] . "</th>";
			}
			?>
			</tr>
			</thead>
			<tbody>
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
			</tbody>
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
		<?php 
		}
		if($s->canObserve($_SESSION['user'])){
			if($s->status[0] == 6 && $s->isAnalisador($_SESSION['user'])){
		?>
			<a class="btn btn-small btn-primary" href="<?= SITE_BASE . $s->id ?>/analisar"><i class="icon-comment icon-white"></i> Responder a análise</a>
		<?php } else { ?>
			<a class="btn btn-small" href="#" data-toggle="modal" data-target="#frm-observe"><i class="icon-comment"></i> Adicionar observação</a>
		<?php
		} }
		?>
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
</div></div>
<div class="row-fluid">
	<div class="span6">
		<h2>Anexos</h2>
		<table class="table table-striped" width="100%">
		<?php
		$attachments = $s->anexos;
		foreach ($attachments as $a) {
		?>
			<tr><td>
				<a href="<?= SITE_BASE . 'd/' . $a->file['_id']  ?>" rel="tooltip" title="Clique para download"><i class="icon-file"></i> <?= $a->getFilename() ?> (<?= Size::format($a->getSize()) ?>)</a> 
			</td><td>
				<a class="btn btn-mini btn-danger pull-right" href="?a=delete&fid=<?= $a->file['_id'] ?>"><i class="icon-remove icon-white"></i> Excluir</a>
			</td></tr>

		<?php 
		}
		echo '</table><div>';
		if($s->canAttach($_SESSION['user'])){
			if($s->status[0] == 5 && $s->isCotador($_SESSION['user'])) {
		?>
			<a class="btn btn-primary" href="<?= SITE_BASE . $s->id ?>/cotar"><i class="icon-upload icon-white"></i> Adicionar cotação</a>
		<?php } else { ?>
			<a class="btn btn-small" href="#" data-toggle="modal" data-target="#frm-attach"><i class="icon-upload"></i> Adicionar anexo</a>
		<?php
		} }
		?>
		</div>
	</div>
	<div class="span6 col2-bordered">
		<h2>Histórico</h2>
		<dl>
		<?php 
			$history = $s->historico;
			foreach ($history as $h) {
		?>
			<dt>Em <?= date('d/m/Y H:i',$h['data']->sec) ?>:</dt> 
			<dd><?= $h['msg'] ?></dd>
		<?php
			}
		?>
		</dl>
	</div>
</div>

<!-- Popup de observação -->
<form action="?a=observe" method="post">
<div class="modal hide fade" id="frm-observe">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"*>×</button>
		<h3>Adicionar observação</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label" for="text">Escreva a sua observação:</label>
			<div class="controls">
				<textarea name="text" id="text" style="width:90%;" rows="5"></textarea>
				<span class="help-block">Essa observação não poderá ser retirada posteriormente.</span>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button class="btn btn-primary" type="submit"><i class="icon-comment icon-white"></i> Adicionar</button>
	</div>
</div>
</form>

<!-- Popup de anexo -->
<form action="?a=attach" enctype="multipart/form-data" method="post">
	<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
	<div class="modal hide fade" id="frm-attach">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"*>×</button>
		<h3>Adicionar anexo</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label" for="file">Escolha o arquivo:</label>
			<div class="controls">
				<div class="input-prepend">
					<span class="add-on"><i class="icon-file"></i></span>
					<input name="file" id="file" type="file" />
				</div>
				<span class="help-block">Ele deve conter no máximo 10MB</span>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button class="btn btn-primary" type="submit"><i class="icon-upload icon-white"></i> Anexar</button>
	</div>
</div>
</form>

<script type="text/javascript">
//$("#frm-attach, #frm-observe").hide();
$("#lk-observe").click(function(){
	if($("#frm-observe").is(":visible")){
		//$("#frm-observe").hide("normal");
		$("#lk-observe").html('<i class="icon-comment"></i> Adicionar observação');
	}else{
		//$("#frm-observe").show("normal");
		$("#lk-observe").html('Cancelar');
	}
	return false;
});
$("#lk-attach").click(function(){
	if($("#frm-attach").is(":visible")){
		$("#frm-attach").slideUp();
		$("#lk-attach").html('<i class="icon-upload"></i> Adicionar anexo');
	}else{
		$("#frm-attach").slideDown();
		$("#lk-attach").html('Cancelar');
	}
	return false;
});

</script>

<?php } require_once('templates/footer.php'); ?>
