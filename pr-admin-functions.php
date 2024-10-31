<?php
function printAdminGraumailingPage(){
	register_post_type(
		'prgraumailing',
		array(
			'labels' => array(
				'name' => 'Graumailing',
				'singular_name' => 'Graumailing',
			),
			'public' => true,
			'show_ui' => true,
			'show_in_menu' => false,
			'supports' => array(''),
		)
	);
}
function printAdminGraumailingAddMetaBox(){
	add_meta_box('graumailingInfo', "Integração do Graumailing", 'printAdminGraumailingInfo', 'prgraumailing', 'normal', 'high');
}
function printAdminGraumailingInfo($post){
	$values = get_post_meta($post->ID, 'prGraumailing', true);
	$prGraumailingNome = $values['nome'];
	$prGraumailingEmail = $values['email'];
	$prGraumailingTelefone = $values['telefone'];
	$prGraumailingDataNascimento = $values['datanascimento'];
	$prGraumailingExtra = $values['extra'];
	$prGraumailingCliente = $values['cliente'];
	$prGraumailingGrupo = $values['grupo'];

	/* RECUPERA INFORMAÇÕES DOS FORMULÁRIOS */
	$args = array(
		'post_type' => 'wpcf7_contact_form',
		'numberposts' => -1
	);
	$forms = get_posts($args);

	if($post->post_title):
		preg_match_all('/\[(.*?)\]/', get_post_meta($post->post_title, '_form', true), $array_wpcf7_form, PREG_SET_ORDER);
		foreach($array_wpcf7_form as $array_wpcf7_form_fields):
			$campo = explode(' ', $array_wpcf7_form_fields[1]);
			if($campo[0] != 'submit')
				$array_campos[] = $campo[1];
		endforeach;
	endif;

	wp_nonce_field( 'prgraumailing_nonce', 'metabox_nonce' );
?>
<div class="poststuff">
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row">
					Formulário
				</th>
				<td>
					<select name="post_title" id="post_title" class="postform">
						<option value="">---</option>
						<?php foreach($forms as $key => $form): ?>
							<option class="level-<?php echo $key; ?>" value="<?php echo $form->ID; ?>" <?php selected($post->post_title, $form->ID);?>><?php echo $form->post_title; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Campo Nome
				</th>
				<td>
					<select name="prGraumailing[nome]" id="prGraumailingNome" class="postform">
						<option value="">---</option>
						<?php foreach($array_campos as $key => $campo): ?>
							<option class="level-<?php echo $key; ?>" <?php selected($prGraumailingNome, $campo); ?>><?php echo $campo; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Campo Email
				</th>
				<td>
					<select name="prGraumailing[email]" id="prGraumailingEmail" class="postform">
						<option value="">---</option>
						<?php foreach($array_campos as $key => $campo): ?>
							<option class="level-<?php echo $key; ?>" <?php selected($prGraumailingEmail, $campo); ?>><?php echo $campo; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Campo Telefone
				</th>
				<td>
					<select name="prGraumailing[telefone]" id="prGraumailingTelefone" class="postform">
						<option value="">---</option>
						<?php foreach($array_campos as $key => $campo): ?>
							<option class="level-<?php echo $key; ?>" <?php selected($prGraumailingTelefone, $campo); ?>><?php echo $campo; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Campo Data de Nascimento
				</th>
				<td>
					<select name="prGraumailing[datanascimento]" id="prGraumailingDataNascimento" class="postform">
						<option value="">---</option>
						<?php foreach($array_campos as $key => $campo): ?>
							<option class="level-<?php echo $key; ?>" <?php selected($prGraumailingDataNascimento, $campo); ?>><?php echo $campo; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Campo Extra
				</th>
				<td>
					<select name="prGraumailing[extra]" id="prGraumailingExtra" class="postform">
						<option value="">---</option>
						<?php foreach($array_campos as $key => $campo): ?>
							<option class="level-<?php echo $key; ?>" <?php selected($prGraumailingExtra, $campo); ?>><?php echo $campo; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Código do Cliente
				</th>
				<td>
					<input type="text" required="required" name="prGraumailing[cliente]" id="prGraumailingCliente" value="<?php echo $prGraumailingCliente; ?>" class="regular-text code">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Código do Grupo
				</th>
				<td>
					<input type="text" required="required" name="prGraumailing[grupo]" id="prGraumailingGrupo" value="<?php echo $prGraumailingGrupo; ?>" class="regular-text code">
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
}
function printAdminGraumailingSave($post_id){
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;
	if( !isset( $_POST['metabox_nonce'] ) || !wp_verify_nonce( $_POST['metabox_nonce'], 'prgraumailing_nonce' ) )
		return $post_id;
	if( !current_user_can( 'edit_post' ) )
		return $post_id;
	if( get_post_type($post_id) != 'prgraumailing' )
		return $post_id;
	update_post_meta( $post_id, 'prGraumailing', $_POST['prGraumailing']);
}
function printAdminGraumailingColumns($columns) {
	return $columns = array(
		'cb' => '<input type="checkbox" />',
		'prform' => 'Formulário',
		'prnome' => 'Nome',
		'premail' => 'Email',
		'prtelefone' => 'Telefone',
		'prnascimento' => 'Data de Nascimento',
		'prextra' => 'Campo Extra',
	);
}
function printAdminGraumailingCustomColumns($column, $post_id){
	switch ($column){
		case 'prform':
			$prGraumailing = get_the_title(get_the_title($post_id));
			echo '<strong><a title="Editar Consulta" href="'.admin_url('post.php?post='.$post_id.'&amp;action=edit').'" class="row-title">'.$prGraumailing.'</a></strong>';
			echo '<div class="row-actions"><span class="edit"><a title="Editar Consulta" href="'.admin_url('post.php?post='.$post_id.'&amp;action=edit').'">Editar</a> | </span><span class="trash"><a href="'.get_delete_post_link($post_id).'" title="Mover para lixeira" class="submitdelete">Apagar</a></span></div>';
			break;
		case 'prnome':
			$value = get_post_meta($post_id, 'prGraumailing', true);
			if($value['nome'])
				echo '['.$value['nome'].']';
			break;
		case 'premail':
			$value = get_post_meta($post_id, 'prGraumailing', true);
			if($value['email'])
				echo '['.$value['email'].']';
			break;
		case 'prtelefone':
			$value = get_post_meta($post_id, 'prGraumailing', true);
			if($value['telefone'])
				echo '['.$value['telefone'].']';
			break;
		case 'prdatanascimento':
			$value = get_post_meta($post_id, 'prGraumailing', true);
			if($value['datanascimento'])
				echo '['.$value['datanascimento'].']';
			break;
		case 'prextra':
			$value = get_post_meta($post_id, 'prGraumailing', true);
			if($value['extra'])
				echo '['.$value['extra'].']';
			break;
	}
}

add_action( 'init', 'printAdminGraumailingPage' );
add_action( 'add_meta_boxes', 'printAdminGraumailingAddMetaBox');
add_action( 'save_post', 'printAdminGraumailingSave' );
add_filter( 'manage_prgraumailing_posts_columns' , 'printAdminGraumailingColumns');
add_action( 'manage_prgraumailing_posts_custom_column' , 'printAdminGraumailingCustomColumns', 10, 2);

add_action( 'wp_ajax_ajax_campos', 'ajax_campos_callback' );
add_action( 'wp_ajax_nopriv_ajax_campos', 'ajax_campos_callback' );

function ajax_campos_callback() {
	global $wpdb; // this is how you get access to the database

	$form_id = $_GET['form_id'];

	preg_match_all('/\[(.*?)\]/', get_post_meta($form_id, '_form', true), $array_wpcf7_form, PREG_SET_ORDER);
	foreach($array_wpcf7_form as $array_wpcf7_form_fields):
		$campo = explode(' ', $array_wpcf7_form_fields[1]);
		if($campo[0] != 'submit')
			$array_campos[] = $campo[1];
	endforeach;

	echo json_encode($array_campos);

	die(); // this is required to return a proper result
}

add_action( 'admin_footer', 'my_action_javascript' );

function my_action_javascript() {
?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		$('#post_title').change(function(){
			$.getJSON("<?php echo admin_url( 'admin-ajax.php' ); ?>",
				{
					'action' : 'ajax_campos',
					'form_id': $(this).val()
				},
				function(data){
					var html = '<option value="">---</option>';
					var len = data.length;
					for(var i=0; i<len; i++){
						html += '<option>' + data[i] + '</option>';
					}
					$("#prGraumailingNome").empty().append(html);
					$("#prGraumailingEmail").empty().append(html);
					$("#prGraumailingTelefone").empty().append(html);
					$("#prGraumailingDataNascimento").empty().append(html);
					$("#prGraumailingExtra").empty().append(html);
				}
			);
		});
	});
	</script>
<?php
}
