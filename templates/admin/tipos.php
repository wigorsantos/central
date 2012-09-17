<?php require_once('base-top.php'); ?>
<?php switch ($_REQUEST['form']) {
	case 'basic': ?>

<form method="post" class="form-horizontal">
	<input type="hidden" name="form" value="basic">
	<input type="hidden" name="mode" value="<?= $D['mode'] ?>">
	<input type="hidden" name="id" value="<?= $D['id'] ?>">
	<legend><?= $D['mode'] == 'edit' ? 'Editar tipo de solicitação' : 'Novo tipo de solicitação' ?></legend>
	<div class="control-group">
		<label class="control-label" for="nome">Nome</label>
		<div class="controls">
			<input type="text" name="nome" id="nome" value="<?= $D['tipo']['nome'] ?>">
			<span class="help-inline">Qual o nome desse tipo de solicitação?</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="descricao">Descrição</label>
		<div class="controls">
			<input type="text" name="descricao" id="descricao" value="<?= $D['tipo']['descricao'] ?>">
			<span class="help-inline">Descreva de uma forma que os solicitantes não fiquem com dúvida.</span>
		</div>
	</div>
	<div class="control-group">
		<div class="controls">
			<label class="checkbox">
			<input type="checkbox" name="cotar" id="cotar" <?= $D['tipo']['cotar'] ? 'checked' : '' ?>> Cotar?
			</label>
			<span class="help-inline">As solicitações desse tipo precisam ser cotadas?</span>
		</div>
	</div>
	<div class="form-actions">
		<a href="?" class="btn">Cancelar</a>
		<button type="submit" class="btn btn-primary"><?= $D['mode'] == 'new' ? 'Continuar <i class="icon-chevron-right icon-white"></i>' : 'Salvar' ?></button>
	</div>
</form>

<?php	break;
	case 'people': ?>

<form method="post" class="form-horizontal">
	<input type="hidden" name="form" value="people">
	<input type="hidden" name="mode" value="<?= $D['mode'] ?>">
	<input type="hidden" name="id" value="<?= $D['id'] ?>">
	<legend><?= $D['mode'] == 'edit' ? 'Editar tipo de solicitação' : 'Novo tipo de solicitação' ?></legend>
	<p>
		Informe abaixo as pessoas relacionadas e as suas responsabilidades para o tipo <i><?= $D['tipo']['nome'] ?></i>.
		<span class="label label-info"><i class="icon-info-sign icon-white"></i> Utilize o nome de usuário das pessoas, separando por ; (ponto-e-vírgula).</span>
	</p>
	<div class="control-group">
		<label class="control-label" for="aprovador"><strong>Aprovadores</strong></label>
		<div class="controls">
			<textarea class="input-xxlarge" name="aprovador" id="aprovador"><?php
			if ($D['mode'] == 'edit') {
				foreach ($D['tipo']['aprovador'] as $pessoa) {
					echo $pessoa . ";";
				}
			}
			?></textarea>
			<span class="help-block">Pessoas que podem aprovar solicitações desse tipo</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="cotador"><strong>Cotadores</strong></label>
		<div class="controls">
			<textarea class="input-xxlarge" name="cotador" id="cotador"><?php
			if ($D['mode'] == 'edit') {
				foreach ($D['tipo']['cotador'] as $pessoa) {
					echo $pessoa . ";";
				}
			}
			?></textarea>
			<span class="help-block">Pessoas que podem cotar solicitações desse tipo</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="executor"><strong>Executores</strong></label>
		<div class="controls">
			<textarea class="input-xxlarge" name="executor" id="executor"><?php
			if ($D['mode'] == 'edit') {
				foreach ($D['tipo']['executor'] as $pessoa) {
					echo $pessoa . ";";
				}
			}
			?></textarea>
			<span class="help-block">Pessoas que executam solicitações desse tipo</span>
		</div>
	</div>
	<div class="control-group">
		<label class="control-label" for="informar"><strong>Informados</strong></label>
		<div class="controls">
			<textarea class="input-xxlarge" name="informar" id="informar"><?php
			if ($D['mode'] == 'edit') {
				foreach ($D['tipo']['informar'] as $pessoa) {
					echo $pessoa . ";";
				}
			}
			?></textarea>
			<span class="help-block">Pessoas que são notificadas sobre solicitações desse tipo</span>
		</div>
	</div>
	<div class="form-actions">
		<a href="?" class="btn">Cancelar</a>
		<button type="submit" class="btn btn-primary"><?= $D['mode'] == 'new' ? 'Continuar <i class="icon-chevron-right icon-white"></i>' : 'Salvar' ?></button>
	</div>
</form>

<?php	break;
	case 'detail': ?>

<form method="post">
	<input type="hidden" name="form" value="detail">
	<input type="hidden" name="mode" value="<?= $D['mode'] ?>">
	<input type="hidden" name="id" value="<?= $D['id'] ?>">
	<legend><?= $D['mode'] == 'edit' ? 'Editar tipo de solicitação' : 'Novo tipo de solicitação' ?></legend>
	<p>
		Informe abaixo as colunas de detalhes do tipo <i><?= $D['tipo']['nome'] ?></i>.
		<span class="label label-info"><i class="icon-info-sign icon-white"></i> Ao remover uma coluna de um tipo de solicitação já em uso, o valor referente a ela continuará registrado, possibilitando a reintrodução da coluna caso necessário.</span>
	</p>
	<table class="table-datasheet" id="details">
		<thead>
			<tr>
				<th>Nome único<small>O nome que identifica a coluna</small></th>
				<th>Nome<small>O nome que o solicitante vai ver</small></th>
				<th>Tipo<small>O tipo de dado armazenado na coluna</small></th>
				<th>Dica<small>Uma instrução para o solicitante</small></th>
				<th>Remover?</th>
			</tr>
		</thead>
		<tbody>
		<?php for ($i = 0; $i < count($D['tipo']['detalhe']); $i++){ $d = $D['tipo']['detalhe'][$i] ?>
			<tr>
				<td><input type="text" name="detalhe[<?= $i ?>][nome_unico]" value="<?= $d['nome_unico'] ?>" /></td>
				<td><input type="text" name="detalhe[<?= $i ?>][nome]" value="<?= $d['nome'] ?>" /></td>
				<td><select name="detalhe[<?= $i ?>][tipo]">
					<option value="text" <?= $d['tipo'] == "text" ? "selected" : "" ?>>Texto</option>
					<option value="number" <?= $d['tipo'] == "number" ? "selected" : "" ?>>Número</option>
					<option value="money" <?= $d['tipo'] == "money" ? "selected" : "" ?>>Dinheiro</option>
					<option value="date" <?= $d['tipo'] == "date" ? "selected" : "" ?>>Data</option>
					<option value="datetime" <?= $d['tipo'] == "datetime" ? "selected" : "" ?>>Data e hora</option>
					<option value="checkbox" <?= $d['tipo'] == "checkbox" ? "selected" : "" ?>>Verdadeiro/Falso</option>
				</select></td>
				<td><input type="text" name="detalhe[<?= $i ?>][dica]" value="<?= $d['dica'] ?>" /></td>
				<td><a class="btn btn-mini btn-danger" href="#" onclick="removeMe(this.id);" id="row<?= $i ?>">Remover</a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	<a class="btn btn-mini btn-primary" href="#" onclick="addRow();return false;"><i class="icon-plus icon-white"></i> Adicionar coluna</a>
	<div class="form-actions">
		<a href="?" class="btn">Cancelar</a>
		<button type="submit" class="btn btn-primary">Salvar</i></button>
	</div>
</form>
<script>
var i = <?= $i ?>;
function addRow(){
	$("#details tbody").append('<tr><td><input type="text" name="detalhe[' + i +'][nome_unico]" /></td><td><input type="text" name="detalhe[' + i +'][nome]"/></td><td><select name="detalhe[' + i +'][tipo]"><option value="text">Texto</option><option value="number">Número</option><option value="money">Dinheiro</option><option value="date">Data</option><option value="datetime">Data e hora</option><option value="checkbox">Verdadeiro/Falso</option></select></td><td><input type="text" name="detalhe[' + i +'][dica]"/></td><td><a class="btn btn-mini btn-danger" href="#" onclick="removeMe(this.id);" id="row' + i + '">Remover</a></td></tr>');
	i++;
}
function removeMe(id){
	if($("#details tbody").find("tr").length > 2){
		$("#" + id).parent().parent().remove();
		return true;
	}else{
		alert("É necessário pelo menos uma coluna para o tipo de solicitação.");
		return false;
	}
}
if($("#details").find("tr").length < 2){addRow();}
</script>

<?php	break;
	case 'delete': ?>

<form method="post">
	<input type="hidden" name="form" value="delete">
	<input type="hidden" name="mode" value="<?= $D['mode'] ?>">
	<input type="hidden" name="id" value="<?= $D['id'] ?>">
	<legend>Excluir o tipo de solicitação</legend>
	<p>A exclusão do tipo <i><?= $D['tipo']['nome'] ?></i> não poderá ser desfeita e todas as solicitações desse tipo serão arquivadas.</p>
	<p>É recomendável efetuar um <a href="<?= SITE_BASE ?>admin/backup">backup</a> antes. <strong>Deseja continuar?</strong></p>
	<div class="form-actions">
		<a href="?" class="btn">Cancelar</a>
		<button type="submit" class="btn btn-danger">Excluir</button>
	</div>
</form>

<?php	break;
	default: ?>

<div>
	<a href="?form=basic" data-toggle="modal" class="btn pull-right">Novo</a>
</div>
<div class="clear"></div>
<table class="table table-striped">
	<thead>
		<tr>
			<th>Nome</th>
			<th class="hidden-phone hidden-tablet">Descrição</th>
			<th class="hidden-phone">Cotar?</th>
			<th>Ações</th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($D['tipos'] as $tipo) { ?>
		<tr>
			<td><?= $tipo['nome'] ?></td>
			<td class="hidden-phone hidden-tablet"><?= $tipo['descricao'] ?></td>
			<td class="hidden-phone"><?= $tipo['cotar'] ? "Sim" : "Não" ?></td>
			<td>
				<div class="btn-group">
					<a href="?form=basic&id=<?= $tipo['_id'] ?>" class="btn btn-mini"><i class="icon-edit"></i> <span class="hidden-phone">Editar</span></a>
					<a href="?form=people&id=<?= $tipo['_id'] ?>" class="btn btn-mini"><i class="icon-user"></i> <span class="hidden-phone">Pessoas</span></a>
					<a href="?form=detail&id=<?= $tipo['_id'] ?>" class="btn btn-mini"><i class="icon-th-list"></i> <span class="hidden-phone">Colunas</span></a>
					<a href="?form=delete&id=<?= $tipo['_id'] ?>" class="btn btn-mini"><i class="icon-trash"></i> <span class="hidden-phone">Excluir</span></a>
				</div>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>

<?php	break;
} ?>
<?php require_once('base-bottom.php'); ?>