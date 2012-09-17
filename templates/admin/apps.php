<?php require_once('base-top.php'); ?>
<p>Veja os aplicativos que podem acessar a API da Central de solicitações.</p>
<?php if ($D['apps']->count() == 0 ) { ?> 
<p>Ainda não existem aplicativos autorizados a acessar a API.</p>
<?php } else { ?>
<table class="table-striped" width="100%">
	<thead>
		<tr>
			<th width="150">Nome</th>
			<th>Descrição</th>
			<th width="200">ID</th>
			<th width="200">SECRET</th>
			<th width="100"></th>
		</tr>
	</thead>
	<tbody>
	<?php foreach ($D['apps'] as $a) { $app = new Application($a['_id']) ?>
		<tr>
			<td width="150"><?= $app->name ?></td>
			<td><?= $app->description ?></td>
			<td width="200"><?= $app->id ?></td>
			<td width="200"><?= $app->secret ?></td>
			<td width="100"><a class="btn btn-mini btn-danger" href="?a=remove&id=<?= $app->id ?>">Remover</a></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } ?>
<p>&nbsp;</p>
<p>
	<button class="btn btn-primary" data-toggle="modal" data-target="#novo">Novo aplicativo</button>
</p>
<form method="post" action="?a=new" class="form-horizontal">
<div class="modal hide fade" id="novo">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal"*>×</button>
		<h3>Criar um novo aplicativo</h3>
	</div>
	<div class="modal-body">
		<div class="control-group">
			<label class="control-label" for="name">Nome do aplicativo</label>
			<div class="controls">
				<input type="text" name="name" id='name'/>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="description">Descrição do aplicativo</label>
			<div class="controls">
				<textarea name="description" id='description'></textarea>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
		<button class="btn btn-primary" type="submit">Criar</button>
	</div>
</div>
</form>
<?php require_once('base-bottom.php'); ?>