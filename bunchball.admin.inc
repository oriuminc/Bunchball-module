<?php

function bunchball_admin_form($form, &$form_state) {

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
    '#default_value' => isset($form_state['values']['bunchball_environment']) ? $form_state['values']['bunchball_environment'] : variable_get('bunchball_environment', ''),
  );

  return system_settings_form($form);
}

//function bunchball_admin_form_submit(&$form, &$form_state) {
//  variable_set('bunchball_apikey', $form_state['values']['apikey']);
//  variable_set('bunchball_apisecret', $form_state['values']['apisecret']);
//  variable_set('bunchball_environment', $form_state['values']['environment']);
//}