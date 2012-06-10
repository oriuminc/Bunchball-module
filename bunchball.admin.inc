<?php
/**
 * @file 
 *    Main admin form for bunchball module.
 */

/**
 * Form callback for admin form.
 */
function bunchball_admin_form($form, &$form_state) {

  ctools_include('dependent');
  
  $form['bunchball_apikey'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_apikey']) ? $form_state['values']['bunchball_apikey'] : variable_get('bunchball_apikey', ''),
    '#title' => t('Bunchball API Key'),
    '#required' => TRUE,
  );

  $form['bunchball_apisecret'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_apisecret']) ? $form_state['values']['bunchball_apisecret'] : variable_get('bunchball_apisecret', ''),
    '#title' => t('Bunchball API Secret'),
    '#required' => TRUE,
  );

  $form['bunchball_environment'] = array(
    '#type' => 'select',
    '#options' => array(
       0 => ' ',
       'production' => t('Production'),
       'sandbox' => t('Sandbox')
    ),
    '#required' => TRUE,
    '#default_value' => isset($form_state['values']['bunchball_environment']) ? $form_state['values']['bunchball_environment'] : variable_get('bunchball_environment', ''),
  );

  $form['bunchball_production_url'] = array(
    '#title' => t('Production server url'),
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_production_url']) ? $form_state['values']['bunchball_production_url'] : variable_get('bunchball_production_url', ''),
    '#process' => array('ctools_dependent_process'),
    '#dependency' => array('edit-bunchball-environment' => array('production')),
    '#description' => t('The Buncball production URL. Most likely http://<domain>.bunchball.com/nitro/xml'),
  );

  $form['bunchball_sandbox_url'] = array(
    '#title' => t('Sandbox server url'),
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_sandbox_url']) ? $form_state['values']['bunchball_sandbox_url'] : variable_get('bunchball_sandbox_url', 'http://sandbox.bunchball.net/nitro/xml'),
    '#process' => array('ctools_dependent_process'),
    '#dependency' => array('edit-bunchball-environment' => array('sandbox')),
    '#description' => t('The Bunchball sandbox URL. Most likely http://sandbox.bunchball.com/nitro/xml'),
  );

  // Load the available bunchball user interaction plugins, so the admin can
  // choose which one to make use of.
  ctools_include('plugins');
  if ($plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction')) {
    foreach ($plugins as $id => $plugin) {
      $options[$id] = $plugin['label'];
    }
    $form['bunchball_user_interaction_plugin'] = array(
      '#title' => t('Bunchball user/profile iteraction plugin'),
      '#type' => 'select',
      '#options' => $options
    );
  }

  return system_settings_form($form);
}

//function bunchball_admin_form_submit(&$form, &$form_state) {
//  variable_set('bunchball_apikey', $form_state['values']['apikey']);
//  variable_set('bunchball_apisecret', $form_state['values']['apisecret']);
//  variable_set('bunchball_environment', $form_state['values']['environment']);
//}