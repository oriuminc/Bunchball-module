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

  $links = array(
      '!content_actions' => l('content actions', 'admin/config/people/bunchball/entities'),
      '!user_actions' => l('user actions', 'admin/config/people/bunchball/user-interaction'),
  );
  $form['description'] = array(
    '#markup' => t('Connection to Bunchball Nitro requires a subscription. Once subscribed, use the values on the Nitro Admin console to complete the fields below to connect the service.  Then configure the !content_actions and !user_actions to track.', $links),
  );

  $form['bunchball_apikey'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_apikey']) ? $form_state['values']['bunchball_apikey'] : variable_get('bunchball_apikey', ''),
    '#title' => t('API Key'),
    '#required' => TRUE,
  );

  $form['bunchball_apisecret'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_apisecret']) ? $form_state['values']['bunchball_apisecret'] : variable_get('bunchball_apisecret', ''),
    '#title' => t('API Secret'),
    '#required' => TRUE,
  );

  $form['bunchball_environment'] = array(
    '#type' => 'select',
    '#description' => t('Point to Sandbox for testing and Production for your live environment.'),
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
    '#description' => t('The Buncball production URL. Most likely http://<domain>.bunchball.net/nitro/xml'),
  );

  $form['bunchball_sandbox_url'] = array(
    '#title' => t('Sandbox server url'),
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['bunchball_sandbox_url']) ? $form_state['values']['bunchball_sandbox_url'] : variable_get('bunchball_sandbox_url', 'http://sandbox.bunchball.net/nitro/xml'),
    '#process' => array('ctools_dependent_process'),
    '#dependency' => array('edit-bunchball-environment' => array('sandbox')),
    '#description' => t('The Bunchball sandbox URL. Most likely http://sandbox.bunchball.net/nitro/xml'),
  );

  $form['bunchball_unique_id_fieldset'] = array(
    '#title' => t('Unique id configuration'),
    '#type' => 'fieldset',
    '#description' => t('This id will identify a user in Bunchball.'),
  );

  $unique_id_options = array(
    'userid' => t('Drupal userid'),
    'username' => t('Drupal username'),
    'email' => t('Email address'),
  );

  if (module_exists('janrain_capture')) {
    $unique_id_options['janrain_uuid'] = t('Janrain UUID');
  }

  $form['bunchball_unique_id_fieldset']['bunchball_unique_id'] = array(
    '#title' => t('Unique id'),
    '#type' => 'select',
    '#options' => $unique_id_options,
    '#default_value' => variable_get('bunchball_unique_id', 'email'),
  );

  $form['bunchball_unique_id_fieldset']['bunchball_unique_id_include_site_hash'] = array(
    '#title' => t('Include site hash'),
    '#type' => 'checkbox',
    '#description' => t('Enabling this will make sure that users are separated site-by-site.')
      . ' ' . t('Uncheck it if you want to track users across multiple sites.'),
    '#default_value' => variable_get('bunchball_unique_id_include_site_hash', FALSE),
  );

  return system_settings_form($form);
}
