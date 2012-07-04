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

  $form['bunchball_dev_options_fieldset'] = array(
    '#type' => 'fieldset',
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
    '#title' => t('Development and performance tuning options'),
  );

  $timeout_options = array();
  foreach(range(5, 60, 5) as $timeout) {
    $timeout_options[(float)$timeout] = t('@timeout sec', array('@timeout' => $timeout));
  }
  $form['bunchball_dev_options_fieldset']['bunchball_client_timeout'] = array(
    '#type' => 'select',
    '#options' => $timeout_options,
    '#title' => t('API Client timeout'),
    '#default_value' => isset($form_state['values']['bunchball_client_timeout']) ? $form_state['values']['bunchball_client_timeout'] : variable_get('bunchball_client_timeout', 10.0),
    '#description' => t('Default timeout of the API client.  This should be set
      sufficiently high to accomodate most normal requests, but the lowest possible
      value above that. Setting this value too high will potentially cause page
      requests to hang for the length of this value in the event of the bunchball
      server being unresponsive.'),
  );

  $form['bunchball_dev_options_fieldset']['bunchball_logger_timeout'] = array(
    '#type' => 'select',
    '#options' => $timeout_options,
    '#title' => t('Action logging API client timeout'),
    '#default_value' => isset($form_state['values']['bunchball_logger_timeout']) ? $form_state['values']['bunchball_logger_timeout'] : variable_get('bunchball_logger_timeout', 10.0),
    '#description' => t('Default timeout of the action logging client.  Essentially
      the same as the API client timeout, but can be configured to a separate value.
      Action logging responses typically are not essential to the further functioning
      of the site, so it is possible to set this timeout somewhat lower than the
      client timeout (though should still be high enough to capture the large majority
      of API requests).'),
  );

  $form['bunchball_dev_options_fieldset']['bunchball_debug_mode'] = array(
    '#type' => 'select',
    '#options' => array(
      BUNCHBALL_DEBUG_NONE => t('Off'),
      BUNCHBALL_DEBUG_LOG => t('Log only'),
      BUNCHBALL_DEBUG_LOG | BUNCHBALL_DEBUG_DISPLAY => t('Display and log'),
    ),
    '#title' => t('Debug mode'),
    '#default_value' => isset($form_state['values']['bunchball_debug_mode']) ? $form_state['values']['bunchball_debug_mode'] : variable_get('bunchball_debug_mode', 0),
    '#description' => t('Set debug mode to show messages about what has been sent to bunchball and responses. "Display" will only display messages to the same user that triggered them, but could still show undesired information to end users, so should be used with caution.')
  );

  return system_settings_form($form);
}

/**
 * Page callback for viewing the bunchball event log.
 */
function bunchball_log_page() {
  $header = array(
    array('data' => t('ID'), 'field' => 'l.blid', 'sort' => 'desc'),
    array('data' => t('Date'), 'field' => 'l.timestamp'),
    t('Message'),
    array('data' => t('User'), 'field' => 'l.uid'),
  );

  $result = db_select('bunchball_log', 'l')
    ->extend('PagerDefault')
    ->extend('TableSort')
    ->fields('l')
    ->limit(50)
    ->orderByHeader($header)
    ->execute();

  $rows = array();
  foreach ($result as $row) {
    $rows[] = array('data' =>
      array(
        $row->blid,
        format_date($row->timestamp, 'short'),
        t($row->message, unserialize($row->variables)),
        $row->uid,
      ),
    );
  }

  $build = array();
  $build['bunchball_log_truncate'] = drupal_get_form('bunchball_log_truncate_form');
  $build['bunchball_log_table'] = array(
    '#theme' => 'table',
    '#header' => $header,
    '#rows' => $rows,
    '#empty' => t('No log messages available.'),
  );

  $build['bunchball_log_pager'] = array('#theme' => 'pager');

  return $build;
}

function bunchball_log_truncate_form($form, &$form_state) {
  $form['truncate'] = array(
    '#type' => 'submit',
    '#value' => t('Clear logs')
  );

  return $form;
}

function bunchball_log_truncate_form_submit($form, &$form_state) {
  db_query('TRUNCATE TABLE {bunchball_log}');
}
