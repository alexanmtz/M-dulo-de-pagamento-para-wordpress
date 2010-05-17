<?php


/*
Plugin Name: Modulo de pagamento para Wordpress utilizando o pagseguro
Version: v1.0
Plugin URI: http://www.conexaoparis.com.br
Author: Alexandre Magno
Author URI: http://blog.alexandremagno.net
Description: Plugin de integração do Wordpress com o pagseguro de uma forma amigável, desenvolvido para o blog Conexão Paris
*/
session_start();

/* Includes de libs para o plugin */
require_once(ABSPATH."/wp-includes/js/tinymce/plugins/spellchecker/classes/utils/JSON.php");

/* gerais */


/* instalacao e estrutura de dados */
function instalar_modulo_venda() {
   global $wpdb;

   $table_name_venda = $wpdb->prefix . "modulo_venda";
   $table_name_produto = $wpdb->prefix . "modulo_produto";
   $exist_venda = $wpdb->get_var("SHOW TABLES LIKE '$table_name_venda'") != $table_name_venda;
   $exist_produto = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name_produto;

   if($exist_produto) {
		$sql_produto = "CREATE TABLE " . $table_name_produto . " (
			  id int(11) primary key AUTO_INCREMENT,
			  descricao text NOT NULL,
			  quantidade int(11) NOT NULL,
			  valor text NOT NULL
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_produto);
   	}

   if($exist_venda) {
		$sql_venda = "CREATE TABLE " . $table_name_venda . " (
			  id int(11) primary key AUTO_INCREMENT,
			  data datetime NOT NULL,
			  nome text NOT NULL,
			  valor text NOT NULL,
			  email text NOT NULL,
			  status text NOT NULL,
			  envio text NOT NULL,
			  produto_id text NOT NULL
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql_venda);
   	}

}

/* desinstalacao */
function desinstalar_modulo_venda() {
   global $wpdb;
   /*$table_name = $wpdb->prefix . "modulo_venda";

   if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "DROP TABLE IF EXISTS $table_name";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
   	}*/

}

register_deactivation_hook(__FILE__, 'desinstalar_modulo_venda');
register_activation_hook(__FILE__,'instalar_modulo_venda');

function modulo_venda_listar_vendas() {
	add_management_page('modulo-vendas.php', "Vendas realizadas por transferencia", 10, plugin_basename(dirname(__FILE__)).'/modulo-vendas.php');
}

function modulo_venda_pagina_opcoes() {
	echo '<div class="wrap"><h2>Opções do Módulo Pagseguro</h2>';

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

    $modulo_peso = get_option('modulo_peso');
    if (empty($modulo_peso)) $modulo_peso = 0;

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

    echo '<p>Para acessar sua conta do pagseguro, por favor acesse ';
    echo '<a href="http://www.pagseguro.com.br">o site do pagseguro</a></p>';
	echo '<form method="post" action="options.php">';
	//settings_fields('opcoes-modulo-venda'); versao 2.7
	wp_nonce_field('update-options');
    echo '<table class="form-table">';
    echo '<tbody>';
    echo '<tr valign="top">';
    echo '<th scope="row">';
    echo '<label for="modulo_venda_cat">Categoria</label>';
    echo '</th>';
    echo '<td>';
    //exibe as categorias em um select
    wp_dropdown_categories($modulo_cat_dropdown);
    echo '<br/>';
    echo 'Esta é a categoria padrão que você usará para disponibilizar de forma organizada os itens para venda';
    echo '<br />Todos os posts nesta categoria possuiram um carrinho';
    echo '</td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">';
    echo '<label for="modulo_preco">E-mail:</label>';
    echo '</th>';
    echo '<td>';
	echo '<input type="text" class="code" size="40" name="modulo_venda_email_padrao" value="'.$email_padrao.'" />';
    echo '<br/>';
    echo 'E-mail cadastrado no pagseguro';
    echo '</td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">';
    echo '<label for="modulo_preco">Preço:</label>';
    echo '</th>';
    echo '<td>';
	echo '<input type="text" class="code" size="10" name="modulo_preco" value="'.$modulo_preco.'" />';
    echo '<br/>';
    echo 'Valor de cada mercadoria nesta categoria. <em>Ex:</em> 12.00';
    echo '</td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">';
    echo '<label>Formas de envio:</label>';
    echo '</th>';
    echo '<td>';
	echo '<input type="checkbox" name="modulo_entrega_sedex" value="true" '.$modulo_entrega_sedex_checked.'  />';
	echo '<label for="modulo_entrega_correios">Sedex</label>';
    echo '<br/>';
    echo '<input type="checkbox" name="modulo_entrega_pac" value="true" '.$modulo_entrega_pac_checked.' />';
    echo '<label for="modulo_entrega_correios">Pac</label>';
    echo '<br/>';
    echo '<input type="checkbox" name="modulo_entrega_gratis" value="true" '.$modulo_entrega_gratis_checked.'  />';
    echo '<label for="modulo_entrega_correios">Gratis</label>';
    echo '<br/>';
    echo '<input type="checkbox" name="modulo_entrega_trans" value="true" '.$modulo_entrega_trans_checked.'  />';
	echo '<label for="modulo_entrega_trans">Transferência Bancária</label>';
    echo '<p>Habilitar formas de envio. Lembre-se que é necessário configurar nas preferências web e frete do pagseguro para sedex e PAC</p>';
    echo '</td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row">';
    echo '<label for="modulo_peso">Peso:</label>';
    echo '</th>';
    echo '<td>';
	echo '<input type="text" class="code" size="10" name="modulo_peso" value="'.$modulo_peso.'" />';
    echo '<br/>';
    echo 'Peso do produto em gramas. <em>Ex:</em> 1000 para 1kg';
    echo '</td>';
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    echo '<input type="hidden" name="action" value="update" />'; //remover para versao 2.7
    echo '<input type="hidden" name="page_options" value="modulo_venda_email_padrao,modulo_venda_cat,modulo_preco,modulo_entrega_sedex,modulo_entrega_pac,modulo_entrega_gratis,modulo_entrega_trans,modulo_peso" />'; //remover versao 2.7
    echo '<p class="submit">';
	echo '<input type="submit" class="button" value="Save Changes" name="Submit"/>';
	echo '</p>';
    echo '</form>';
	echo '</div>';
}

//funcao que adiciona os itens do menu
function modulo_venda_menu() {
	add_options_page('Módulo de venda Pagseguro para Wordpress', 'Modulo de venda Pagseguro para Wordpress', 'manage_options', __FILE__, 'modulo_venda_pagina_opcoes');
}
//registrando opcoes
function modulo_venda_plugin_init(){
	//versao 2.7
	//register_setting('opcoes-modulo-venda', 'modulo_venda_cat');
}
function adicionar_item() {
	$carrinho_id = $_POST['postid'];
	if(!is_array($_SESSION['carrinho'])) $_SESSION['carrinho'] = array();
	if(!array_key_exists($carrinho_id,$_SESSION['carrinho'])) {
		$_SESSION['carrinho'][$carrinho_id]['posttitle'] = $_POST['posttitle'];
		$_SESSION['carrinho'][$carrinho_id]['quantidade'] = $_POST['quantidade'];
		$_SESSION['carrinho'][$carrinho_id]['valor'] = get_option('modulo_preco');
	} else {
		$_SESSION['carrinho'][$carrinho_id]['quantidade'] += $_POST['quantidade'];
	}
}
function apagar_item() {
	$post_id = $_POST['deleteid'];
	unset($_SESSION['carrinho'][$post_id]);
}
function mod_get_total() {
	$qtidade_total = 0;
	foreach($_SESSION['carrinho'] as $key => $item) {
		$qtidade_total += $item['quantidade'];
	}
	$valor_total = $qtidade_total * get_option('modulo_preco');
	return sprintf('%1.2f',$valor_total);
}
function modulo_venda_carrinho_widget($args) {
    extract($args);
    if($_GET['destruir']) {
		session_destroy();
	}
	$add = $_POST['adicionar'];
	$delete = $_POST['apagar'];
	if($add=='Adicionar ao carrinho'){
		adicionar_item();
	}
	if($delete == 'Apagar') {
		apagar_item();
	}
	if($_SESSION['carrinho']) {
	    echo $before_widget;
	    echo '<div id="modulo-total">';
		echo 'Total: <span id="modulo-venda-total">R$ <span class="valor">'.mod_get_total().'</span> *</span>';
		echo '</div>';
		echo $before_title.'Carrinho'. $after_title;
		echo '<ul class="licate">';
	    foreach($_SESSION['carrinho'] as $key => $item) {
			$total_item = $item['quantidade']*$item['valor'];
	    	echo '<li class="page_item page-item-2">';
	    	echo '<form method="post" action="'.$_SERVER['PHP_SELF'].'">';
	    	echo '<input type="hidden" name="deleteid" value="'.$key.'" />';
		    echo '<a href="'.get_permalink($key).'" class="modulo-venda-item-produto" title="'.$item['posttitle'].'">'.$item['posttitle'].'</a>';
			echo '<input type="submit" name="apagar" value="Apagar" />';
		    echo '<p>Quantidade: <span class="modulo-venda-item-quantidade">'.$item['quantidade'].'</span> - Valor: R$ <span class="modulo-venda-item-valor">'.sprintf('%1.2f',$total_item).'</span></p>';
			echo '</form>';
			echo '</li>';
	    }
		echo '</ul>';
		echo '<form target="pagseguro" method="post" id="modulo-comprar" action="https://pagseguro.uol.com.br/security/webpagamentos/webpagto.aspx">';
		echo '<input type="hidden" name="email_cobranca" value="'.get_option('modulo_venda_email_padrao').'" />';
        echo '<input type="hidden" name="tipo" value="CP">';
        echo '<input type="hidden" name="moeda" value="BRL">';
        echo '<input type="hidden" name="encoding" value="utf-8">';
        $cont = 1;
        $modo_peso = get_option('modulo_peso');
		$modo_sedex = get_option('modulo_entrega_sedex');
		$modo_pac = get_option('modulo_entrega_pac');
		$modo_gratis = get_option('modulo_entrega_gratis');
		$modo_trans = get_option('modulo_entrega_trans');
		foreach($_SESSION['carrinho'] as $key => $item) {
	    	echo '<input type="hidden" name="item_id_'.$cont.'" value="'.$key.'" />';
	    	echo '<input type="hidden" name="item_descr_'.$cont.'" value="'.$item['posttitle'].'" />';
	    	echo '<input type="hidden" name="item_quant_'.$cont.'" value="'.$item['quantidade'].'" />';
	    	echo '<input type="hidden" name="item_valor_'.$cont.'" value="'.str_replace('.','',$item['valor']).'" />';
	    	$cont++;
		}
		echo '<input type="hidden" id="modulo_venda_peso" name="item_peso_1" value="'.$modo_peso*($cont-1).'" />';

		if($modo_sedex || $modo_pac || $modo_gratis || $modo_trans) {
			echo '<div class="modulo-venda-forma-entrega">';
			echo '<h5 id="opcoes-de-envio">Opções de envio</h5>';
			echo '<ul>';
			if($modo_pac) {
				echo '<li>';
				echo '<input type="radio" id="modulo_venda_en" name="tipo_frete" value="EN" />';
				echo '<label for="modulo_venda_en"><a href="#" class="modulo-venda-ajuda" title="ajuda">PAC</a></label>';
				echo '</li>';
			}
			if($modo_sedex) {
				echo '<li>';
				echo '<input type="radio" id="modulo_venda_sd" name="tipo_frete" value="SD" />';
				echo '<label for="modulo_venda_sd"><a href="#" class="modulo-venda-ajuda" title="ajuda">Sedex</a></label>';
				echo '</li>';
			}
			if($modo_gratis) {
				echo '<li>';
				// e necessario ter um valor para este campo, entao ele ficara com EN mas por javascript o campo peso sera zerado
				echo '<input type="radio" id="modulo_venda_gratis" name="tipo_frete" value="EN" />';
				echo '<label for="modulo_venda_gratis"><a href="#" class="modulo-venda-ajuda" title="ajuda">Grátis</a></label>';
				echo '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}

		echo '<div class="modulo-venda-forma-pagamento">';
		echo '<h5 id="opcoes-de-pagamento">Opções de pagamento</h5>';
		echo '<ul>';
		echo '<li>';
		// e necessario ter um valor para este campo, entao ele ficara com EN mas por javascript o campo peso sera zerado
		echo '<input type="radio" id="modulo_venda_pagseguro" name="modo_pagamento" value="pagseguro" />';
		echo '<label for="modulo_venda_gratis"><a href="#" class="modulo-venda-ajuda" title="ajuda">Cartão de crédito ou boleto bancário (via pagseguro)</a></label>';
		echo '</li>';
		if($modo_trans) {
			echo '<li>';
			// e necessario ter um valor para este campo, entao ele ficara com EN mas por javascript o campo peso sera zerado
			echo '<input type="radio" id="modulo_venda_trans" name="modo_pagamento" value="transferencia" />';
			echo '<label for="modulo_venda_gratis"><a href="#" class="modulo-venda-ajuda" title="ajuda">Transferência bancária</a></label>';
			echo '</li>';
			echo '<div id="modulo-venda-popin">';
			echo '<p>Por favor, é necessário fornecer o seu nome e e-mail antes de efetuar a compra para que o vendedor entre em contato.</p>';
			echo '<div class="fields">';
			echo '<label label for="modulo-venda-nome">Nome completo:</label>';
			echo '<input type="text" id="modulo-venda-nome" name="modulo-venda-nome" />';
			echo '<label label for="modulo-venda-email">E-mail:</label>';
			echo '<input type="text" id="modulo-venda-email" name="modulo-venda-email" />';
			echo '</div>';
			echo '</div>';
			echo '</li>';
		}
		echo '</ul>';
		echo '</div>';
		echo '<input type="submit" class="modulo-venda-enviar" value="Comprar" />';
		echo '</form>';
		echo '<p class="aviso-frete">* O frete será calculado de acordo com as opções de envio e pagamento escolhidas</p>';
	    echo $after_widget;
    }
}

function modulo_post_compravel($content) {
	global $id;
	$post_cat_id = get_the_category($id);
	if($post_cat_id[0]->term_id==get_option('modulo_venda_cat')){
		$content.= '<p><b>Valor: R$ '.get_option('modulo_preco').'</b>';
		$content.= '<div class="modulo-venda-post">';
		$content.= '<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="modulo-venda-post-form">';
		$content.= '<input type="hidden" name="postid" value="'.$id.'" />';
		$content.= '<input type="hidden" name="posttitle" value="'.get_the_title($id).'" />';
		$content.= '<label id="label-quantidade" for="quantidade">Quantidade: </label><input class="quantidade" type="text" name="quantidade" value="1" />';
		$content.= '<input type="submit" class="botao-post" name="adicionar" value="Adicionar ao carrinho" />';
		$content.= '</form>';
		$content.= '</div>';
	}
 	return $content;
}

function modulo_venda_banner_widget($args) {
	extract($args);
    $plugin_path = get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.'modulo-pagseguro'.'/';
    $cat_id = get_option('modulo_venda_cat');
    if(!$_REQUEST['adicionar']) {
	    echo $before_widget;
    	echo '<a href="'.get_category_link($cat_id).'">';
	    echo '<img src="'.$plugin_path.'banner.gif" />';
	    echo '</a>';
	    echo $after_widget;
    }
}
function modulo_sidebar_loaded() {
   register_sidebar_widget('Carrinho', 'modulo_venda_carrinho_widget');
   register_sidebar_widget('Banner de venda', 'modulo_venda_banner_widget');
}

function modulo_venda_obter_produtos($produtos_id) {
	global $wpdb;
	$table_name_produto = $wpdb->prefix . "modulo_produto";

	$produtos = array();
	if(is_array($produtos_id)) {
		foreach($produtos_id as $id) {
			$produtos[] = $wpdb->get_row('SELECT * from '.$table_name_produto.' WHERE id='.$id,ARRAY_A);
		}
	}

	return $produtos;

}

function modulo_venda_gravar_cliente() {
	global $wpdb;
	$table_name_produto = $wpdb->prefix . "modulo_produto";
	$table_name_venda = $wpdb->prefix . "modulo_venda";

	$nome = $_POST['modulo-venda-nome'];
	$email = $_POST['modulo-venda-email'];
	$data = date("Y-m-d H:i:s");

	$produtos = $_POST['modulo-venda-produto'];
	$id_list = array();
	$envio = $_POST['modulo-venda-envio'];
	$peso = intval($_POST['modulo-venda-peso']);
	if($envio=='EN' && $peso==0) {
		$envio = 'gratis';
	}

	foreach($produtos as $i => $produto ) {
		$results_produto = $wpdb->insert( $table_name_produto, array(
			'descricao' => $produto,
			'quantidade' => intval($_POST['modulo-venda-quantidade'][$i]),
			'valor' => $_POST['modulo-venda-valor'][$i]
		), array( '%s', '%d', '%s' ) );

		array_push($id_list,$wpdb->insert_id);
	}

	if($results_produto) {
		$results_venda = $wpdb->insert( $table_name_venda, array(
			'data' => $data,
			'nome' => $nome,
			'valor' => $_POST['modulo-venda-total'],
			'email' => $email,
			'status' => 'pendente',
			'envio' => $envio,
			'produto_id' => join(',',$id_list)
		), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
	}

  	if($results_venda) {
  		return true;
  	} else {
  		return false;
  	}
}

function modulo_venda_transacao() {
	global $wpdb;
	$table_name_produto = $wpdb->prefix . "modulo_produto";
	$table_name_venda = $wpdb->prefix . "modulo_venda";

	$transacao = $_POST['modulo_venda_transacao'];

	$status_escolhido = $_POST['modulo-venda-status'];

	if($transacao=='Apagar') {
		if(check_admin_referer('modulo_venda_transacao')){
			$apagar_registro = true;
			if($_POST['vendas']) {
				foreach($_POST['vendas'] as $venda) {
					if($apagar_registro) {
						$venda_apagar = $venda;
						$query_produtos = "SELECT produto_id from ".$table_name_venda." where id=".$venda_apagar;
						$produtos = $wpdb->get_var($query_produtos);
						if($produtos) {
							$array_produtos = explode(',',$produtos);
							foreach($array_produtos as $k => $v ) {
								if($apagar_registro) {
									$wpdb->query('DELETE FROM '.$table_name_produto." where id=".$array_produtos[$k]);
								} else {
									$apagar_registro = false;
								}
							}
							if($apagar_registro) {
								$apagar_venda = $wpdb->query('DELETE FROM '.$table_name_venda." where id=".$venda[$id]);
							}
							if($apagar_venda) {
								wp_redirect(get_bloginfo('wpurl') . '/wp-admin/edit.php?page='.plugin_basename(dirname(__FILE__)).'/modulo-vendas.php&action=apagar&error=0');
							} else {
								wp_redirect(get_bloginfo('wpurl') . '/wp-admin/edit.php?page='.plugin_basename(dirname(__FILE__)).'/modulo-vendas.php&action=apagar&error=1');
							}
						}
					}
				}
			} else {
				wp_redirect(get_bloginfo('wpurl') . '/wp-admin/edit.php?page='.plugin_basename(dirname(__FILE__)).'/modulo-vendas.php&action=apagar&error=1');
			}

		}
	}

	if($transacao=='Modificar Status') {

		if(check_admin_referer('modulo_venda_transacao')){
				$modificar_status = true;
				foreach($_POST['vendas'] as $venda) {
					if($modificar_status) {
						$modificar_status = $wpdb->update( $table_name_venda, array( 'status' => $status_escolhido), array( 'id' => $venda ), array( '%s' ), array( '%d' ) );
					} else {
						$modificar_status = false;
					}
				}
				if($modificar_status) {
					wp_redirect(get_bloginfo('wpurl') . '/wp-admin/edit.php?page='.plugin_basename(dirname(__FILE__)).'/modulo-vendas.php&action=status&error=0');
				} else {
					wp_redirect(get_bloginfo('wpurl') . '/wp-admin/edit.php?page='.plugin_basename(dirname(__FILE__)).'/modulo-vendas.php&action=status&error=1&status='.$status_escolhido);
				}
				
			}
	}
}

add_action("admin_post_modulo_venda_transacao", "modulo_venda_transacao");



function addHeaderCode() {
	echo '<script type="text/javascript">ajaxurl = "'.admin_url('admin-ajax.php').'";</script>';
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl').'/'. PLUGINDIR.'/'.'modulo-pagseguro/modulo_venda.css" />';
	echo '<link type="text/css" rel="stylesheet" href="' . get_bloginfo('wpurl').'/'. PLUGINDIR.'/'.'modulo-pagseguro/css/jquery-ui-1.7.2.custom.css" />';
}

function modulo_venda_scripts() {

	//if(!is_admin()) {
		wp_enqueue_script( 'jquery-new', plugins_url('/modulo-pagseguro/js/jquery-1.3.2.js'), array(),'1.3.2');
		//wp_enqueue_script('jquery-ui-core');
		//wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script( 'jquery-ui-core-new', plugins_url('/modulo-pagseguro/js/ui.core.min.js'), array('jquery-new'),'1.7.2');
		wp_enqueue_script( 'jquery-ui-dialog-new', plugins_url('/modulo-pagseguro/js/ui.dialog.min.js'), array('jquery-ui-core-new'),'1.7.2');
		wp_enqueue_script( 'modulo-pagseguro-main-js', WP_PLUGIN_URL.'/modulo-pagseguro/script.js', array('jquery-new','jquery-ui-core-new','jquery-ui-dialog-new'));
		//wp_enqueue_style( 'jquery-ui-new', plugins_url('/modulo-pagseguro/css/jquery-ui-1.7.2.custom.css'));
	//}
}

function modulo_venda_obter_info() {

	$envio = $_POST['envio'];
	$pagamento = $_POST['pagamento'];

	$modulo = array(

		'opcoes-de-envio' => array(

			'PAC' => array(
				'titulo' => 'Encomenda Normal',
				'conteudo' => 'o PAC é mais barato que o sedex.<br /> Prazo de entrega: 6 dias úteis. Envio totalmente rastreado.',
				'link' => 'http://www.correios.com.br/encomendas/servicos/Pac/default.cfm'
			),
			'Sedex' => array(
				'titulo' => 'Sedex',
				'conteudo' => 'Com o Sedex, o tempo médio de entrega é de 48 horas e a encomenda é totalmente rastreável',
				'link' => 'http://www.correios.com.br/encomendas/servicos/Sedex/sedex.cfm'
			),
			'Grátis' => array(
				'titulo' => 'Grátis',
				'conteudo' => 'O frete está incluído no preço do guia. <br />Prazo de entrega: 6 dias úteis. Envio totalmente rastreado.'
			),

		),
		'opcoes-de-pagamento' => array(
			'Cartão de crédito ou boleto bancário (via pagseguro)' => array(
				'titulo' => 'Pagseguro',
				'conteudo' => 'O Pagseguro é um serviço prático e seguro de efetuar pagamentos online. Não é necessário se cadastrar, não tem custo adicional para o comprador e possui as maiorias de formas de pagamento disponíveis no mercado, além de ter o cálculo de frete próprio.',
				'link' => 'https://pagseguro.uol.com.br/para_voce/como_funciona.jhtml'
			),
			'Transferência bancária' => array(

				'titulo' => 'Transferência bancária',
				'conteudo' => 'Através do Blog você poderá efetuar a compra e o vendedor entrará em contato fornecendo o número da conta. Após o pagamento ser efetuado, a mercadoria é enviada no modo de entrega escolhido. O valor do frete é enviado pelo vendedor',
			),
		),

	);

	$json_obj = new Moxiecode_JSON();
	/* encode */
	$json = $json_obj->encode($modulo[$pagamento][$envio]);
	echo $json;
	die();
}

// Adicionar menu de gerenciar vendas
add_action("admin_menu", "modulo_venda_listar_vendas");

/* Ajax */
add_action('wp_ajax_gravar_cliente', 'modulo_venda_gravar_cliente');
add_action('wp_ajax_nopriv_gravar_cliente', 'modulo_venda_gravar_cliente');

add_action('wp_ajax_obter_info', 'modulo_venda_obter_info');
add_action('wp_ajax_nopriv_obter_info', 'modulo_venda_obter_info');

// registrando scripts js e css
//add_action('admin_init', 'register_admin_scripts');

add_action('template_redirect','modulo_venda_scripts');

//inicializando o plugin com as opcoes registradas
add_action('admin_init', 'modulo_venda_plugin_init' );

// inserido a pagina de opcoes do plugin
add_action('admin_menu','modulo_venda_menu');

// acao para adicionar css
add_action('wp_head', 'addHeaderCode');

add_action('plugins_loaded','modulo_sidebar_loaded');

// inserir o botao de adicionar ao carrinho nos posts da categoria descrita
add_filter('the_content', 'modulo_post_compravel');
?>