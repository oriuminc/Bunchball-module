<?php

/**
 * Administration form for which user / profile interactions
 * should be communicated to bunch ball
 */
function bunchball_user_interaction_admin_form($form, &$form_state) {

  $form['boooton'] = array(
    '#type' => 'submit',
    '#value' => t('Click me big boy'),
  );

  return system_settings_form($form);
}