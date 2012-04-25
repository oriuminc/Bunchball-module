<?php

class BunchballUserInteractionDefault {

  //unlikely that I need to have the api object in here, but maybe
  //public $api;
  //
  function __construct() {

    $this->options['bunchball_user_login'] = variable_get('bunchball_user_login','');
    $this->options['bunchball_user_register'] = variable_get('bunchball_user_register', '');
  }

  public function adminForm($form, &$form_state) {
    $form['bunchball_user_login'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate user login'),
      '#default_value' => $this->options['bunchball_user_login'],
    );

    $form['bunchball_user_register'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate new user registration'),
      '#default_value' => $this->options['bunchball_user_register'],
    );

    return $form;
  }

  public function adminFormValidate($form, &$form_state) {

  }

  public function adminFormSubmit($form, &$form_state) {
    isset($form_state['values']['bunchball_user_login']) ? variable_set('bunchball_user_login', $form_state['values']['bunchball_user_login']) : '';
    isset($form_state['values']['bunchball_user_register']) ? variable_set('bunchball_user_register', $form_state['values']['bunchball_user_register']) : '';
  }
}