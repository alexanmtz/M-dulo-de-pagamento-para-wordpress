<?php

require "pagination.class.php";

global $wpdb;
$table_name = $wpdb->prefix . "modulo_venda";

if($_GET['action']=='status') {
	if($_GET['error']) {
		$message = "Não foi possível modificar o status";
	} else {
		$message = "Status modificado com sucesso";
	}
}

if($_GET['action']=='apagar') {
	if($_GET['error']) {
		$message = "Não foi possível excluir a venda";
	} else {
		$message = "Vendas excluídas com sucesso";
	}
}

$items = mysql_num_rows(mysql_query("SELECT * from $table_name")); // number of total rows in the database

if($items > 0) {
	
	$ordenar_por = $_GET['ordenar_por'];
	
	$filtrar_por = $_GET['filtrar_por'];
	
	if($ordenar_por) {
		$ordenar_query = "&ordenar_por=".$ordenar_por;
	}
	
	if($filtrar_por) {
		$filtrar_query = "&filtrar_por=".$filtrar_por;
	}
	
	$p = new pagination;
	$p->items($items);
	$p->limit(20); // Limit entries per page
	$p->target("tools.php?page=".plugin_basename(dirname(__FILE__))."/modulo-vendas.php".$ordenar_query.$filtrar_query);
	$p->currentPage($_GET[$p->paging]); // Gets and validates the current page
	$p->calculate(); // Calculates what to show
	$p->parameterName('paging');
	$p->adjacents(1); //No. of page away from the current page

	if(!isset($_GET['paging'])) {
		$p->page = 1;
	} else {
		$p->page = $_GET['paging'];
	}

	//Query for limit paging
	$limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;

	if($ordenar_por) {
		$obter_vendas = "SELECT * from $table_name order by $ordenar_por asc $limit";
	} else {
		$obter_vendas = "SELECT * from $table_name order by id asc $limit";			
	}
	
	if($filtrar_por) {
		$obter_vendas = "SELECT * from $table_name order by id asc $limit where status=$filtrar_por";
	}
	
	$vendas = $wpdb->get_results($obter_vendas);
	$colunas = $wpdb->get_col_info('name');


} else {
	echo "No Record Found";
}


?>
<?php if ( $message != false ) { ?>
<div id="message" class="updated fade">
<p><?php echo $message; ?></p>
</div>
<?php } ?>
<div class="wrap">
<h2><?php _e('Gerenciar vendas'); ?></h2>
<form action="admin-post.php" method="post"><?php wp_nonce_field('modulo_venda_transacao'); ?>
<input type="hidden" name="action" value="modulo_venda_transacao">
<div class="tablenav">
<div class="tablenav-pages"><?php echo $p->show();  // Echo out the list of paging. ?>
</div>
<div class="alignleft"><input type="submit" value="Apagar"
	name="modulo_venda_transacao" class="button-secondary delete" /> <select
	name="modulo-venda-status" id="status" class= "postform">
	<option value='pendente'>Pendente</option>
	<option value="aguardando pagamento">Aguardando Pagamento</option>
	<option value="enviando">Enviando</option>
	<option value="finalizado">Finalizado</option>
</select> <input type="submit" id="post-query-submit"
	value="Modificar Status" name="modulo_venda_transacao"
	class="button-secondary" /><input type="submit" id="post-query-submit"
	value="Filtrar" name="modulo_venda_transacao"
	class="button-secondary" /></div>
<div class="alignleft">
	<label for="modulo-venda-filtrar">Ordenar</label>
	<select name="modulo-venda-ordenar" id="ordem" class="postform">
	<?php foreach($colunas as $coluna) :?>
		<?php if($coluna!="produto_id") : ?>
			<option value='<?php echo $coluna; ?>'><?php echo $coluna; ?></option>
		<?php endif; ?>
	<?php endforeach; ?>
</select> <input type="submit" id="post-query-submit"
	value="Ordenar" name="modulo_venda_transacao"
	class="button-secondary" /></div>
</div>
<br class="clear">
<table class="widefat">
	<thead>
		<tr valign="top">
			<th class="check-column" scope="col"></th>
			<th scope="col"><?php _e('ID'); ?></th>
			<th scope="col"><?php _e('Data'); ?></th>
			<th scope="col"><?php _e('Nome'); ?></th>
			<th scope="col"><?php _e('Produtos'); ?></th>
			<th scope="col"><?php _e('Valor'); ?></th>
			<th scope="col"><?php _e('Modo de envio'); ?></th>
			<th scope="col"><?php _e('E-mail'); ?></th>
			<th scope="col"><?php _e('Status'); ?></th>
		</tr>
	</thead>
	<tbody>
	<?php $modos_de_envio = array(
					'SD' => 'Sedex',
					'EN' => 'Encomenda normal',
					'gratis' => 'Grátis'
					); ?>
					<?php foreach( $vendas as $venda ) { ?>
					<?php
					$produtos = $venda->produto_id;
					$array_produtos = explode(',',$produtos);
					?>
		<tr>
			<th scope="row"
				class="check-column <?php if ($count == 1){echo 'alternate';} ?>"><input
				type="checkbox" valign="bottom" value="<?php echo $venda->id; ?>"
				name="vendas[]" /></th>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->id; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->data; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->nome; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php $produtos = modulo_venda_obter_produtos($array_produtos); ?> <?php foreach($produtos as $i => $produto ) {
				echo $produtos[$i]['descricao'];
				echo ' - ';
				echo $produtos[$i]['quantidade']."itens - ";
				echo 'R$'.$produtos[$i]['valor']."<br/>";
			} ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->valor; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $modos_de_envio[$venda->envio]; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->email; ?></td>
			<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
			<?php echo $venda->status; ?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
<div class="tablenav">
<div class="tablenav-pages"><?php echo $p->show();  // Echo out the list of paging. ?>
</div>
</form>

</div>
