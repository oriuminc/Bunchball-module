<?php
/**
 * @file
 *    Admin screens for bunchball user-related plugins.
 */

/**
 * Administration form for which of the user / profile interactions should be
 * communicated to bunch ball.
 */
function bunchball_user_interaction_admin_form($form, &$form_state) {
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction');

  foreach ($plugins as $id => $plugin) {
    $class = bunchball_user_interaction_get_plugin_instance($id);
    $form += $class->adminForm($form, $form_state);
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
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_user_interaction', 'bunchball_user_interaction');
  foreach ($plugins as $id => $plugin) {
    $class = bunchball_user_interaction_get_plugin_instance($id);
    $class->adminFormSubmit($form, &$form_state);
  }
  drupal_set_message(t('The configuration options have been saved.'));
}