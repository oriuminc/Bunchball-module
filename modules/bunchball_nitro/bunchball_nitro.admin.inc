<?php
/**
 * @file
 *    Admin screen for bunchball entity plugins.
 */

/**
 * Administration form for which user / profile interactions should be
 * communicated to bunch ball.
 */
function bunchball_nitro_admin_form($form, &$form_state) {
  // get all the content types
  $node_types = node_type_get_names();
  $nitro_ct_enabled = variable_get('bunchball_nitro_admin', array());
  
  $form = array();

  // add jQuery file
  drupal_add_js(
      drupal_get_path('module', 'bunchball_nitro') . '/bunchball_nitro.admin.js'
  );
  
  // build form
  foreach ($node_types as $machine => $human) {
    $form['element_' . $machine] = array(
      '#type' => 'fieldset', 
      '#title' => $human, 
      '#collapsible' => FALSE, 
      '#collapsed' => FALSE,
    );
    $form['element_' . $machine]['checkbox_' . $machine] = array(
      '#type' => 'checkbox', 
      '#title' => t('Enabled'),
      '#default_value' => isset($nitro_ct_enabled[$machine]) ? 1 : 0,
    );
    $form['element_' . $machine]['name_' . $machine] = array(
      '#type' => 'textfield', 
      '#title' => t('Action name'), 
      '#default_value' => isset($nitro_ct_enabled[$machine]) ? $nitro_ct_enabled[$machine] : '', 
      '#size' => 60, 
      '#maxlength' => 128, 
    );
  }
  
  // youtube element..
  $nitro_youtube_enabled = variable_get('bunchball_nitro_admin_youtube', array());
  
  $form['element_youtube'] = array(
    '#type' => 'fieldset', 
    '#title' => t('YouTube'), 
    '#collapsible' => FALSE, 
    '#collapsed' => FALSE,
  );
  $form['element_youtube']['checkbox_youtube'] = array(
    '#type' => 'checkbox', 
    '#title' => t('Enabled'),
    '#default_value' => isset($nitro_youtube_enabled['start']) && 
      isset($nitro_youtube_enabled['end']) ? 1 : 0,
  );
  $form['element_youtube']['youtube_start'] = array(
    '#type' => 'textfield', 
    '#title' => t('Start action name'), 
    '#default_value' => isset($nitro_youtube_enabled['start']) ? $nitro_youtube_enabled['start'] : '', 
    '#size' => 60, 
    '#maxlength' => 128, 
  );
  $form['element_youtube']['youtube_end'] = array(
    '#type' => 'textfield', 
    '#title' => t('End action name'), 
    '#default_value' => isset($nitro_youtube_enabled['end']) ? $nitro_youtube_enabled['end'] : '', 
    '#size' => 60, 
    '#maxlength' => 128, 
  );
  
  
  // add the save button
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
  );

  return $form;
}

/**
 * Submit callback for admin form.
 * 
 * @param $form
 * @param $form_state 
 */
function bunchball_nitro_admin_form_submit($form, &$form_state) {
  $action_names = array();
  
  // check to see which elements are set for action name tracking
  $node_types = node_type_get_names();
  foreach ($node_types as $machine => $human) {
    if($form_state['values']['checkbox_' . $machine] > 0 && strlen($form_state['values']['name_' . $machine]) > 0) {
      $action_names[$machine] = $form_state['values']['name_' . $machine];
    }
  }
  variable_set('bunchball_nitro_admin', $action_names);
  
//  echo '<pre>';
//  print_r($form_state['values']);
//  echo '</pre>';
//  exit;
  variable_del('bunchball_nitro_admin_youtube');
  if ($form_state['values']['checkbox_youtube'] > 0 && strlen($form_state['values']['youtube_start']) > 0 && strlen($form_state['values']['youtube_end']) > 0) {
    $youtube = array();
    $youtube['start'] = $form_state['values']['youtube_start'];
    $youtube['end'] = $form_state['values']['youtube_end'];
    variable_set('bunchball_nitro_admin_youtube', $youtube);
  }
  
  
  drupal_set_message(t('The configuration options have been saved.'));
}