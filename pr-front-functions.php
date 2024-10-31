<?php
function postPRGraumailing(){
	global $post;

	$args = array(
		'post_type' => 'prgraumailing',
		'numberposts' => -1
	);
	$graumailingforms = get_posts($args);
	if($graumailingforms):
	?>
		<script>
		jQuery(document).ready(function($){
			<?php
			foreach($graumailingforms as $form):
				$values = get_post_meta($form->ID, 'prGraumailing', true);
				$prGraumailingNome = $values['nome'];
				$prGraumailingEmail = $values['email'];
				$prGraumailingTelefone = $values['telefone'];
				$prGraumailingDataNascimento = $values['datanascimento'];
				$prGraumailingExtra = $values['extra'];

				if( WPCF7_VERSION > 3.6 ) {
					if ( in_the_loop() ) {
						$unit_tag = sprintf( 'wpcf7-f%1$d-p%2$d-o%3$d',
							absint( $form->post_title ), get_the_ID(), 1 );
					} else {
						$unit_tag = sprintf( 'wpcf7-f%1$d-o%2$d',
							absint( $form->post_title ), 1 );
					}
				} else {
					$paginainicial = get_option('page_on_front');
					$tipodepagina = 'p';
					if($paginainicial == 0 && (is_home() || is_front_page())) $tipodepagina = 't';

					$postid = $post->ID;
					if($paginainicial == 0 && (is_home() || is_front_page())) $postid = '1';

					$unit_tag = 'wpcf7-f' . $form->post_title . '-' . $tipodepagina.$postid . '-o1';
				}
			?>
			$(document).on('click', '#<?php echo $unit_tag; ?> .wpcf7-submit', function(){
					console.log('Integração Graumailing!');
					<?php if($prGraumailingNome): ?>
						var prnome = $(this).parents('.wpcf7-form').find('input[name=<?php echo $prGraumailingNome; ?>]').val();
					<?php endif; ?>
					<?php if($prGraumailingEmail): ?>
						var premail = $(this).parents('.wpcf7-form').find('input[name=<?php echo $prGraumailingEmail; ?>]').val();
					<?php endif; ?>
					<?php if($prGraumailingTelefone): ?>
						var prtelefone = $(this).parents('.wpcf7-form').find('input[name=<?php echo $prGraumailingTelefone; ?>]').val();
					<?php endif; ?>
					<?php if($prGraumailingDataNascimento): ?>
						var prdatanascimento = $(this).parents('.wpcf7-form').find('input[name=<?php echo $prGraumailingDataNascimento; ?>]').val();
					<?php endif; ?>
					<?php if($prGraumailingExtra): ?>
						var prextra = $(this).parents('.wpcf7-form').find('input[name=<?php echo $prGraumailingExtra; ?>]').val();
					<?php endif; ?>
				$.post("<?php echo admin_url( 'admin-ajax.php' ); ?>", {
					<?php if($prGraumailingNome): ?>'<?php echo $prGraumailingNome; ?>': prnome,<?php endif; ?>
					<?php if($prGraumailingEmail): ?>'<?php echo $prGraumailingEmail; ?>': premail,<?php endif; ?>
					<?php if($prGraumailingTelefone): ?>'<?php echo $prGraumailingTelefone; ?>': prtelefone,<?php endif; ?>
					<?php if($prGraumailingDataNascimento): ?>'<?php echo $prGraumailingDataNascimento; ?>': prdatanascimento,<?php endif; ?>
					<?php if($prGraumailingExtra): ?>'<?php echo $prGraumailingExtra; ?>': prextra,<?php endif; ?>
					'form_title': '<?php echo $form->post_title; ?>',
					'form_id': '<?php echo $form->ID; ?>',
					'action': 'ajax_graumailing'
				});
			});
			<?php endforeach; ?>
		});
		</script>
	<?php
	endif;
}

add_action( 'wp_head', 'postPRGraumailing' );

add_action( 'wp_ajax_ajax_graumailing', 'ajax_graumailing_callback' );
add_action( 'wp_ajax_nopriv_ajax_graumailing', 'ajax_graumailing_callback' );

function ajax_graumailing_callback() {
	global $wpdb; // this is how you get access to the database

	$values = get_post_meta($_POST['form_id'], 'prGraumailing', true);
	$prGraumailingNome = $_POST[$values['nome']];
	$prGraumailingEmail = $_POST[$values['email']];
	$prGraumailingTelefone = $_POST[$values['telefone']];
	$prGraumailingDataNascimento = $_POST[$values['datanascimento']];
	$prGraumailingExtra = $_POST[$values['extra']];
	$prGraumailingCliente = $values['cliente'];
	$prGraumailingGrupo = $values['grupo'];

	// IMPORTANTE: É fundamental que esteja ativado no servidor do cliente, lê-se o servidor que hospeda o site, o Soap.
	$webservice_ip = gethostbyname('www.mailin.com.br');
	$option = array(
		'location'        => 'http://'.$webservice_ip.'/webservice/server.php',
		'uri'            => 'http://'.$webservice_ip.'/webservice/',
		'encoding'      => 'ISO-8859-1',
		'trace'         => 1,
		'exceptions'    => 0
	);

	// Instância do cliente Soap
	$client = new SoapClient(NULL, $option);

	/*
	* Cadastrar um novo contato
	* $result -> array : $result[0] : boolean : se a ação foi efetuada com sucesso, retorno true. Caso contrário, false.
	*                    $result[1] : string : mensagem do sistema.
	*                    $result[2] : string : o email para cadastro.
	*/
	$result = $client->cadastrar_email($prGraumailingCliente, array($prGraumailingGrupo), $prGraumailingEmail, "", $prGraumailingNome, "", "", "", "", "", "", "", $prGraumailingTelefone, "", "", "", "", $prGraumailingDataNascimento, $prGraumailingExtra, "", "", "", "");

	// Testando o resultado de retorno do webservice
	if($result[0] == '1') {
		echo "A ação com o email <strong>".$result[2]."</strong> foi efetuada com sucesso!<br><i>".$result[1]."</i>";
	} else {
		echo "Ocorreu um erro com o email <strong>".$result[2]."</strong><br><i>".$result[1]."</i>";
		add_filter( 'wp_mail_content_type', 'set_html_content_type' );
		wp_mail( 'contato@paulor.com.br', 'Erro Graumailing - ' . get_bloginfo( 'name' ), "<p>Ocorreu um erro com o email <strong>".$result[2]."</strong><br><i>".$result[1]."</i></p><p>Site: " . get_bloginfo( 'name' ) ."</p>" );
		remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
	}

	die(); // this is required to return a proper result
}

function set_html_content_type() {
	return 'text/html';
}
