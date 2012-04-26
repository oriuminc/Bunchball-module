<?php

class BunchballUserInteractionDefault {

   public $options;
  //unlikely that I need to have the api object in here, but maybe
  //public $api;
  //
  function __construct(NitroAPI $api) {
    $this->options['bunchball_user_login'] = variable_get('bunchball_user_login','');
    $this->options['bunchball_user_register'] = variable_get('bunchball_user_register', '');
    $this->bunchballApi = $api;
  }

  public function getOptions() {
    return $this->options;
  }

  public function adminForm($form, &$form_state) {
    $form['bunchball_user_login'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate user login'),
      '#default_value' => $this->options['bunchball_user_login']['enabled'],
    );

    $form['bunchball_user_register'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate new user registration'),
      '#default_value' => $this->options['bunchball_user_register']['enabled'],
    );

    return $form;
  }

  public function adminFormValidate($form, &$form_state) {}

  //The thinking here is that modules could override this and set their own callbacks?
  public function adminFormSubmit($form, &$form_state) {
    isset($form_state['values']['bunchball_user_login']) ? variable_set('bunchball_user_login', array('enabled'=>1, 'method' => 'userLogin')) : variable_set('bunchball_user_login', array('enabled'=> 0, 'method' => ''));
    isset($form_state['values']['bunchball_user_register']) ? variable_set('bunchball_user_register', 'userRegister') : variable_set('bunchball_user_login', array('enabled'=>0, 'method' => ''));
  }

  public function userLogin($user) {
    $api = $this->bunchballApi;
    try {
      //We call the bunchball login action so that the logAction is associated
      //with the user currently logging in
      $api->login($user->uid, $user->name, $user->mail);
      //We call logAction with 'Login' as the 'actionTag'. We don't send any
      //values because for login there aren't any (from what I can gather)
      $api->logAction('Login');
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

  public function userRegister() {

  }


}