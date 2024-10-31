<?php
/*
Plugin Name: PR Graumailing
Plugin URI: http://paulor.com.br
Description: Integração do Graumailing com Contact Form 7.
Version: 1.2.3
Author: Paulo Iankoski
*/

DEFINE('prGraumailingOptions', 'PRGraumailingOptions');
require_once(dirname(__FILE__).'/pr-admin-functions.php');
require_once(dirname(__FILE__).'/pr-front-functions.php');

function prgraumailing_backend_scripts($hook) {
	//wp_enqueue_style( 'pr-graumailing', plugin_dir_url(__FILE__).'/css/pr-graumailing.css');
	//wp_enqueue_script( 'pr-graumailing', plugin_dir_url(__FILE__).'/js/pr-graumailing.js', array( 'jquery' ) );
}
add_action( 'admin_enqueue_scripts', 'prgraumailing_backend_scripts' );

if(!function_exists("PRGraumailing_ap")){
	function PRGraumailing_ap(){
		if(function_exists('add_submenu_page')):
			add_submenu_page( 'wpcf7', 'PR Graumailing', 'PR Graumailing', 'manage_options', 'edit.php?post_type=prgraumailing');
		endif;
	}
}
add_action('admin_menu', 'PRGraumailing_ap');
