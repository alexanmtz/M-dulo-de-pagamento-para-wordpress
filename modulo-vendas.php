<?php

	global $wpdb;
	$table_name = $wpdb->prefix . "modulo_venda";

	$obter_vendas = "SELECT * from " . $table_name;
  	$vendas = $wpdb->get_results($obter_vendas);

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

?>
<?php if ( $message != false ) { ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } ?>
<div class="wrap">
	<h2><?php _e('Gerenciar vendas'); ?></h2>
	<form action="admin-post.php" method="post">
		<?php wp_nonce_field('modulo_venda_transacao'); ?>
		<input type="hidden" name="action" value="modulo_venda_transacao">
		<div class="tablenav">
			<div class="alignleft">
			<input type="submit" value="Apagar" name="modulo_venda_transacao" class="button-secondary delete" />
			<select name='modulo-venda-status' id='filtro' class='postform' >
				<option value='pendente'>Pendente</option>
				<option value="aguardando pagamento">Aguardando Pagamento</option>
				<option value="enviando">Enviando</option>
				<option value="finalizado">Finalizado</option>
			</select>
			<input type="submit" id="post-query-submit" value="Modificar Status" name="modulo_venda_transacao" class="button-secondary" />
		</div>
		</div>
		<br class="clear">
		<table class="widefat">
			<thead>
				<tr valign="top">
					<th class="check-column" scope="col"></th>
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
					<th scope="row" class="check-column <?php if ($count == 1){echo 'alternate';} ?>">
					<input type="checkbox" valign="bottom" value="<?php echo $venda->id; ?>" name="vendas[]"/></th>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $venda->data; ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $venda->nome; ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php $produtos = modulo_venda_obter_produtos($array_produtos); ?>
						<?php foreach($produtos as $i => $produto ) {
						      echo $produtos[$i]['descricao'];
							  echo ' - ';
							  echo $produtos[$i]['quantidade']."itens - ";
							  echo 'R$'.$produtos[$i]['valor']."<br/>";
						 } ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $venda->valor; ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $modos_de_envio[$venda->envio]; ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $venda->email; ?>
					</td>
					<td class="<?php if ($count == 1){echo 'alternate';} ?>" valign="top">
						<?php echo $venda->status; ?>
					</td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</form>

</div>