<?php 

$mail_template = get_option('modulo_pagamento_mail_template');
if (empty($mail_template)) $mail_template = '';

if($_GET['error']) {
		$message = "Não foi possível salvar o template";
	} else {
		if($_GET['error']=='NULL') {
			$message = "Template salvo com sucesso";
		}
	}

?>

<div class="wrap">
<?php if ( $message ) : ?>
<div id="message" class="updated fade">
<p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<h2><?php _e('Configurações de e-mail do módulo de venda'); ?></h2>
<form action="admin-post.php" method="post"><?php wp_nonce_field('modulo_pagamento_mail_template'); ?>

<table class="form-table">
	<tbody>
		<tr valign="top">
			<th scope="row"><label for="modulo_pagamento_mail_template">Template de e-mail</label></th>
			<td>
				<textarea name="modulo_pagamento_mail_template" rows="20" cols="65"><?php  echo $mail_template; ?></textarea>
				<input type="hidden" name="action" value="modulo_pagamento_mail_template">
			</td>
		</tr>
	</tbody>
</table>
 <p class="submit">
	<input type="submit" class="button" value="Save Changes" name="Submit"/>
	</p>
</form>
</div>
