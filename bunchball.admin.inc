<?php

function bunchball_admin_form($form, &$form_state) {

  $form['gogodaddyo'] = array(
    '#type' => 'submit',
    '#value' => t('Go Go Daddy O'),
  );

  return $form;
}