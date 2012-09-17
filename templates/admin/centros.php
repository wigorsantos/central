<?php require_once('base-top.php'); ?>
<form method="post" action="?a=save">
	<p>Gerencie abaixo os centros de custos da organização.</p>
	<table class="table-datasheet" width="100%" id="centros">
		<thead>
			<tr>
				<th width="60">Apelido</th>
				<th>Descrição</th>
				<th width="60">Excluir?</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			$i = 0;
			foreach ($D['centros'] as $c) { 
		?>
			<tr>
				<td>
					<input type="hidden" name="c[<?= $i ?>][id]" value="<?= $c['_id'] ?>" />
					<input type="text" name="c[<?= $i ?>][apelido]" value="<?= $c['apelido'] ?>" />
				</td>
				<td><input type="text" name="c[<?= $i ?>][descricao]" value="<?= $c['descricao'] ?>" /></td>
				<td><a class="btn btn-mini btn-danger" href="?a=excluir&id=<?= $c['_id'] ?>">Excluir</a></td>
			</tr> 
		<?php 
			$i++;
			} 
		?>
		</tbody>
	</table>
	<div class="form-actions">
		<button type="button" class="btn" onclick="addRow();return false;"><i class="icon-plus"></i> Adicionar centro de custo</button>
		<button type="submit" class="btn btn-primary">Salvar alterações</button>
	</div>
</form>
<script>
var i = <?= $i ?>;
function addRow(){
	var html = '<tr>';
	html += '<td><input type="text" name="c[' + i + '][apelido]" /></td>';
	html += '<td><input type="text" name="c[' + i + '][descricao]" /></td>';
	html += '<td><a class="btn btn-mini btn-danger" href="#" id="remove' + i + '" onclick="removeRow(this.id);return false;">Excluir</a></td>';
	html += '</tr>';
	$("#centros").append(html);
	i++;
}
function removeRow(id){
	$("#" + id).parent().parent().remove();
}
</script>
<?php require_once('base-bottom.php'); ?>