/**
 * @author Alexandre Magno
 */

 jQuery(function(){

	var $modulo_entrega_envio = jQuery('input:radio','.modulo-venda-forma-entrega');
	var $modulo_entrega_pagamento = jQuery('input:radio','.modulo-venda-forma-pagamento');
	var $modulo_peso = jQuery('#modulo_venda_peso');
	var $modulo_venda_gratis = jQuery('#modulo_venda_gratis');
	var peso_atual = $modulo_peso.val();
	var current_submit_value = jQuery('.modulo-venda-enviar');

	$modulo_venda_gratis.bind('click',function(){
		$modulo_peso.val("0");
	});

	$modulo_entrega_envio.not($modulo_venda_gratis).bind('click',function(){
		$modulo_peso.val(peso_atual);
	});

    jQuery('.modulo-venda-ajuda').bind('click', function(){
		jQuery('<div class="carregando"><p>carregando...<p></div>').dialog({
			modal: true,
			width: 500
		});
		jQuery.ajax({
			type : 'POST',
			dataType : 'json',
			url : ajaxurl,
			data : {
			'action' : 'obter_info',
			'envio' : jQuery(this).text(),
			'pagamento' : jQuery(this).parents().parents().siblings('h5').attr('id')
			},
			success : function(data) {
				jQuery('.carregando').dialog('close');
				jQuery('.carregando').dialog('destroy');
				mais_info = '';
				if (data.link) {
					mais_info = '<p>Mais informa&ccedil;&otilde;es: <a target="_blank" href="'+data.link+'">' + data.link + '</a></p>';
				}

				if (data!=null) {
					jQuery('<div class="modulo-venda-dialog"><p>' + data.conteudo + '<p>'+mais_info+'</div>').dialog({
						modal: true,
						title: data.titulo,
						width: 500
					});
				} else {
					if(console) {
						console.info('problema para carregar dialog');
					}
				}
			}
    	});
		return false;
	});
	$modulo_entrega_pagamento.change(function(){
		if(jQuery(this).val()=='transferencia'){
			jQuery('#modulo-venda-popin').show();
		} else {
			jQuery('#modulo-venda-popin').hide();
		}
	});

	jQuery('#modulo-comprar').submit(function(){

		if(jQuery("input:radio:checked",'.modulo-venda-forma-pagamento').val()=='transferencia'){
			var checkmail = /^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z0-9]{2,4})+$/;
			var name = jQuery('#modulo-venda-nome').val();
			var email = jQuery('#modulo-venda-email').val();
			var modo_envio = jQuery('input:radio[name=tipo_frete]:checked','.modulo-venda-forma-entrega').val();
			if(name && email && checkmail.test(email) && modo_envio) {

				var produto = [];
				var valor = [];
				var quantidade = [];

				jQuery('.modulo-venda-item-produto').each(function(){
					produto.push(jQuery(this).text());
				});

				jQuery('.modulo-venda-item-valor').each(function(){
					valor.push(jQuery(this).text());
				});

				jQuery('.modulo-venda-item-quantidade').each(function(){
					quantidade.push(jQuery(this).text());
				});

				jQuery.post(ajaxurl,{
					'action' : 'gravar_cliente',
					'modulo-venda-nome' : jQuery('#modulo-venda-nome').val(),
					'modulo-venda-email' : jQuery('#modulo-venda-email').val(),
					'modulo-venda-total' : jQuery('#modulo-venda-total .valor').text(),
					'modulo-venda-envio' : modo_envio,
					'modulo-venda-peso' : jQuery('#modulo_venda_peso').val(),
					'modulo-venda-valor[]' : valor,
					'modulo-venda-produto[]' : produto,
					'modulo-venda-quantidade[]' : quantidade
				},function(data){
					if(data) {
						jQuery('.success').remove();
						jQuery('.error').remove();
						jQuery('.fields','#modulo-venda-popin').after('<p class="success">Seu contato foi realizada com sucesso. O vendedor entrar&aacute; em contato com voc&ecirc; para fornecer os dados para transfer&ecirc;ncia banc&aacute;ria</p>');
					} else {
						jQuery('.success').remove();
						jQuery('.error').remove();
						jQuery('.fields','#modulo-venda-popin').after('<p class="error">Hove um erro para finalizar a compra, por favor entre em contato com o administrador do blog</p>');
					}
				});
			} else {
				jQuery('.success').remove();
				jQuery('.error').remove();
				jQuery('.fields','#modulo-venda-popin').after('<p class="error">Campo nome, e-mail ou forma de envio inv&aacute;lido(s)</p>');
			}

			return false;
		}
	});

	// Dialog do pagamento por transferencia bancaria

 });
