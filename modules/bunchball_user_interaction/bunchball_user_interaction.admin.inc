<?php

/**
 * Administration form for which user / profile interactions
 * should be communicated to bunch ball
 */
function bunchball_user_interaction_admin_form($form, &$form_state) {

  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction');
  $options = array();

  foreach ($plugins as $id => $plugin) {
    $form['plugins'][$id] = array(
      '#type' => 'fieldset',
      '#title' => $plugin['label'],
    );
    $class = bunchball_user_interaction_get_plugin_instance($id);
    $form['plugins'][$id]['stuff'] = $class->adminForm($form, $form_state);
  }

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save settings'),
  );

  return $form;
}

function bunchball_user_interaction_admin_form_submit($form, &$form_state) {
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction');
  foreach ($plugins as $id => $plugin) {
    $class = bunchball_user_interaction_get_plugin_instance($id);
    $class->adminFormSubmit($form, &$form_state);
  }
}