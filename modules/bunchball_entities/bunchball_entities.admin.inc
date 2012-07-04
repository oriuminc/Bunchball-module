<?php
/**
 * @file
 *    Admin screen for bunchball entity plugins.
 */

/**
 * Administration form for which user / profile interactions should be
 * communicated to bunch ball.
 */
function bunchball_entities_admin_form($form, &$form_state) {
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_entities', 'bunchball_entities');

  $form['#attached']['css'] = array(
    drupal_get_path('module', 'bunchball') . '/bunchball.admin.css',
  );

  $form['help']['#markup'] = t('<div class="form-item">Enable the content types and actions to track, and assign a name to map them to the Bunchball Nitro console.</div>');

  foreach ($plugins as $id => $plugin) {
    $class = bunchball_entities_get_plugin_instance($id);
    $form += $class->adminForm($form, $form_state);
  }

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
function bunchball_entities_admin_form_submit($form, &$form_state) {
  ctools_include('plugins');
  $plugins = ctools_get_plugins('bunchball_entities', 'bunchball_entities');
  foreach ($plugins as $id => $plugin) {
    $class = bunchball_entities_get_plugin_instance($id);
    $class->adminFormSubmit($form, &$form_state);
  }
  drupal_set_message(t('The configuration options have been saved.'));
}
