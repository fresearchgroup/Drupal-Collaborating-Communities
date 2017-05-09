<?php

 /* This function uses for all custom templates, for eg.: page--article.tpl.php, page--basic_page.tpl.php */
function bootxd_preprocess_page(&$vars){
	if(isset($vars['node']->type)){
		$vars['theme_hook_suggestions'][]= 'page__' . $vars['node']->type;
	}
}

/*function bootxd_theme() {
  $items = array();
  // create custom user-login.tpl.php
  $items['user_login'] = array(
  'render element' => 'form',
  'path' => drupal_get_path('theme', 'bootxd') . '/templates',
  'template' => 'user-login',
  'preprocess functions' => array(
  'bootxd_preprocess_user_login'
  ),
 );
return $items;
}*/
