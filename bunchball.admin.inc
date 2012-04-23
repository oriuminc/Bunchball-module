<?php

function bunchball_admin_form($form, &$form_state) {

  $form['apikey'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['apikey']) ? $form_state['values']['apikey'] : variable_get('bunchball_apikey', ''),
    '#title' => t('Bunchball API Key'),
    '#required' => TRUE,
  );

  $form['apisecret'] = array(
    '#type' => 'textfield',
    '#default_value' => isset($form_state['values']['apisecret']) ? $form_state['values']['apisecret'] : variable_get('bunchball_apisecret', ''),
    '#title' => t('Bunchball API Secret'),
    '#required' => TRUE,
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit'),
  );

  return $form;
}

function bunchball_admin_form_submit(&$form, &$form_state, $form_id) {
  variable_set('bunchball_apikey', $form_state['values']['apikey']);
  variable_set('bunchball_apisecret', $form_state['values']['apisecret']);
}