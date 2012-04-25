<?php

class BunchballUserInteractionDefault {

  //unlikely that I need to have the api object in here, but maybe
  //public $api;
  //
  //function __construct(NitroAPI $api) {
  //  $this->api = $api;
  //}

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
}