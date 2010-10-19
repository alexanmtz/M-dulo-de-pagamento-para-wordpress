<?php

$email_padrao = get_option('modulo_venda_email_padrao');
if (empty($email_padrao)) $email_padrao = get_bloginfo('admin_email');

$cat_padrao_id = get_option('modulo_venda_cat');
$modulo_cat_dropdown = 'hide_empty=0&name=modulo_venda_cat&class=code';
if (empty($cat_padrao_id)&&$cat_padrao_id) {
	$cat_padrao_id = 0;
} else {
	$modulo_cat_dropdown .= '&selected='.$cat_padrao_id;
}

$cat_padrao_id = get_option('modulo_venda_cat');
if (empty($cat_padrao_id)) $cat_padrao_id = 0;

$modulo_preco = get_option('modulo_preco');
if (empty($modulo_preco)) $modulo_preco = 0;

$modulo_subpreco = get_option('modulo_subpreco');
if (empty($modulo_subpreco)) $modulo_subpreco = 0;

$modulo_peso = get_option('modulo_peso');
if (empty($modulo_peso)) $modulo_peso = 0;

$modulo_entrega_email = get_option('modulo_entrega_email');
if (empty($modulo_entrega_email)) $modulo_entrega_email = false;

if($modulo_entrega_email) $modulo_entrega_email_checked = 'checked="true"';

$modulo_entrega_sedex = get_option('modulo_entrega_sedex');
if (empty($modulo_entrega_sedex)) $modulo_entrega_sedex = false;

if($modulo_entrega_sedex) $modulo_entrega_sedex_checked = 'checked="true"';

$modulo_entrega_pac = get_option('modulo_entrega_pac');
if (empty($modulo_entrega_pac)) $modulo_entrega_pac = false;

if($modulo_entrega_pac) $modulo_entrega_pac_checked = 'checked="true"';

$modulo_entrega_gratis = get_option('modulo_entrega_gratis');
if (empty($modulo_entrega_gratis)) $modulo_entrega_gratis = false;

if($modulo_entrega_gratis) $modulo_entrega_gratis_checked = 'checked="true"';

$modulo_entrega_trans = get_option('modulo_entrega_trans');
if (empty($modulo_entrega_trans)) $modulo_entrega_trans = false;

if($modulo_entrega_trans) $modulo_entrega_trans_checked = 'checked="true"';

$updated = $_GET['updated'];
if($updated) {
	$message = 'Atualizado com sucesso';
} else if(!empty($updated)) {
	$message = 'Não foi possível salvar as configurações';
} else {
	$message = false;
}

?>
<div class="wrap"><?php if ( $message ) : ?>
<div id="message" class="updated fade">
<p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<h2>Opções do Módulo Pagseguro</h2>
<p>Para acessar sua conta do pagseguro, por favor acesse <a
	href="http://www.pagseguro.com.br">o site do pagseguro</a></p>
<form method="post" action="options.php"><?php 
settings_fields('opcoes-modulo-venda'); //a partir da versao 2.7
?>
<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="modulo_venda_cat">Categoria pai</label></th>
			<td><?php wp_dropdown_categories($modulo_cat_dropdown); ?> <br />
			Esta é a categoria padrão que você usará para disponibilizar de forma
			organizada os itens para venda <br />
			Todos os posts nesta categoria possuiram um carrinho</td>
		</tr>
		<?php
		
		$categories = get_categories('parent='.$cat_padrao_id);
		foreach ($categories as $category) :
		?>		
		<tr valign="top">
			<th scope="row"><label for="modulo_venda_cat">Preços por subcategorias</label></th>
			<td>
				<?php echo $category->category_nicename;  ?> :
				<input type="text" name="modulo_subpreco[<?php echo $category->cat_ID; ?>]" value="<?php echo $modulo_subpreco[$category->cat_ID];  ?>" />
			</td>
		</tr>
		<?php 
		endforeach;
		?>
		<tr valign="top">
			<th scope="row"><label for="modulo_preco">Preços:</label></th>
			<td><input type="text" class="code" size="10" name="modulo_preco"
				value="<?php echo $modulo_preco; ?>" /> <br />
			Valor de cada mercadoria nesta categoria. <em>Ex:</em> 12.00</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="modulo_preco">E-mail:</label></th>
			<td><input type="text" class="code" size="40"
				name="modulo_venda_email_padrao"
				value="<?php echo $email_padrao; ?>" /> <br />
			E-mail cadastrado no pagseguro</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label>Opções:</label></th>
			<td><input type="checkbox" name="modulo_entrega_email" value="true"
			<?php echo $modulo_entrega_email_checked; ?> /> <label
				for="modulo_entrega_email">Envia e-mail automático para o cliente
			quando realizar a compra</label> <br />
			<input type="checkbox" name="modulo_entrega_sedex" value="true"
			<?php echo $modulo_entrega_sedex_checked; ?> /> <label
				for="modulo_entrega_correios">Sedex</label> <br />
			<input type="checkbox" name="modulo_entrega_pac" value="true"
			<?php echo $modulo_entrega_pac_checked; ?> /> <label
				for="modulo_entrega_correios">Pac</label> <br />
			<input type="checkbox" name="modulo_entrega_gratis" value="true"
			<?php echo $modulo_entrega_gratis_checked; ?> /> <label
				for="modulo_entrega_correios">Gratis</label> <br />
			<input type="checkbox" name="modulo_entrega_trans" value="true"
			<?php echo $modulo_entrega_trans_checked; ?> /> <label
				for="modulo_entrega_trans">Transferência Bancária</label>
			<p>Habilitar formas de envio. Lembre-se que é necessário configurar
			nas preferências web e frete do pagseguro para sedex e PAC</p>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="modulo_peso">Peso:</label></th>
			<td><input type="text" class="code" size="10" name="modulo_peso"
				value="<?php echo $modulo_peso; ?>" /> <br />
			Peso do produto em gramas. <em>Ex:</em> 1000 para 1kg</td>
		</tr>
	</tbody>
</table>
<input type="hidden" name="action" value="update" />
<p class="submit"><input type="submit" class="button"
	value="Save Changes" name="Submit" /></p>
</form>
</div>
