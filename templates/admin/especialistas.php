<?php require_once('base-top.php'); ?>
<form method="post" action="?a=save">
	<p>Gerencie abaixo os usuário que podem analisar solicitações em casos específicos.</p>
	<table class="table-datasheet" width="100%" id="especialistas">
		<thead>
			<tr>
				<th width="150">Usuário</th>
				<th>Especialidade</th>
				<th width="60">Excluir?</th>
			</tr>
		</thead>
		<tbody>
		<?php 
			$i = 0;
			foreach ($D['especialistas'] as $e) { 
		?>
			<tr>
				<td>
					<input type="hidden" name="e[<?= $i ?>][id]" value="<?= $e['_id'] ?>" />
					<input type="text" name="e[<?= $i ?>][usuario]" value="<?= $e['usuario'] ?>" />
				</td>
				<td><input type="text" name="e[<?= $i ?>][especialidade]" value="<?= $e['especialidade'] ?>" /></td>
				<td><a class="btn btn-mini btn-danger" href="?a=excluir&id=<?= $e['_id'] ?>">Excluir</a></td>
			</tr> 
		<?php 
			$i++;
			} 
		?>
		</tbody>
	</table>
	<div class="form-actions">
		<button type="button" class="btn" onclick="addRow();return false;"><i class="icon-plus"></i> Adicionar especialista</button>
		<button type="submit" class="btn btn-primary">Salvar alterações</button>
	</div>
</form>
<script>
var i = <?= $i ?>;
function addRow(){
	var html = '<tr>';
	html += '<td><input type="text" name="e[' + i + '][usuario]" /></td>';
	html += '<td><input type="text" name="e[' + i + '][especialidade]" /></td>';
	html += '<td><a class="btn btn-mini btn-danger" href="#" id="remove' + i + '" onclick="removeRow(this.id);return false;">Excluir</a></td>';
	html += '</tr>';
	$("#especialistas").append(html);
	i++;
}
function removeRow(id){
	$("#" + id).parent().parent().remove();
}
</script>
<?php require_once('base-bottom.php'); ?>