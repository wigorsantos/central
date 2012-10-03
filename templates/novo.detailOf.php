<table class="table-datasheet" id="details">
	<thead><tr>
	<?php
	foreach ($t['detalhe'] as $d) {
		echo '<th>' . $d['nome'] . '<small>' . $d['dica'] . '</small></th>';
	}
	?>
		<th class="opt-col"></th>
	</tr></thead>
	<tbody></tbody>
</table>
<a class="btn" href="javascript:addRow()"><i class="icon-plus"></i> Adicionar item</a>
<script type="text/javascript">
	var rCount = 0;
	var c = 0;
	function addRow(){
		var template = "<tr>";
	<?php
	foreach ($t['detalhe'] as $d) {
		echo 'template += \'<td><input name="item[__c__][' . $d['nome_unico'] . ']" type="' . ($d['tipo'] != 'money' ? $d['tipo'] : 'text') . '" data-type="' . $d['tipo'] . '" title="' . $d['dica'] . '" /></td>\';';
	}
	?>
		template += "<td><a class='btn btn-danger btn-mini' id='remove-" + rCount + "' href='#' onclick='removeRow(this.id);return false;'><i class='icon-remove icon-white'></i> <span class='hidden-phone'>Remover</span></a></td></tr>";
		rCount++;
		while (template.indexOf('__c__') != -1) {
	 		template = template.replace('__c__', c);
		}
		c++;
		$("#details tbody").append(template);
	}
	function removeRow(child){
		if($("#details").find("tr").length > 2){
			$("#" + child).parent().parent().remove();
		}else{
			alert("É necessário pelo menos um detalhe na solicitação.");
		}
	}
	addRow();
</script>