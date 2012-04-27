<?php

/**
 * Administration form for which of the user / profile interactions
 * should be communicated to bunch ball
 */
function bunchball_user_interaction_admin_form($form, &$form_state) {
  //@TODO this assumes multiple plugins could be used for bunchball_user_interaction
  //but the main callbacks in the main bunchball_user_interaction module
  //are making use of only one plugin.
  //So either this or the main module will have to change to pick one or the other
  //method of doing things.
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

/**
 * Admin form submit hander
 */
function bunchball_user_interaction_admin_form_submit($form, &$form_state) {
  //@TODO this assumes multiple plugins could be used for bunchball_user_interaction
  //but the main callbacks in the main bunchball_user_interaction module
  //are making use of only one plugin.
  //So either this or the main module will have to change to pick one or the other
  //method of doing things.
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction');
  foreach ($plugins as $id => $plugin) {
    $class = bunchball_user_interaction_get_plugin_instance($id);
    $class->adminFormSubmit($form, &$form_state);
  }
}