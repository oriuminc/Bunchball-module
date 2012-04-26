<?php

class BunchballUserInteractionDefault {

   public $options;
  //unlikely that I need to have the api object in here, but maybe
  //public $api;
  //
  function __construct(NitroAPI $api) {
    $this->options['bunchball_user_login'] = variable_get('bunchball_user_login','');
    $this->options['bunchball_user_register'] = variable_get('bunchball_user_register', '');
    $this->options['bunchball_user_profile_complete'] = variable_get('bunchball_user_profile_complete', '');
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

    $form['bunchball_user_profile_complete'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate a user completing their user profile'),
      '#default_value' => $this->options['bunchball_user_profile_complete']['enabled'],
    );
    return $form;
  }

  public function adminFormValidate($form, &$form_state) {}

  //The thinking here is that modules could override this and set their own callbacks?
  public function adminFormSubmit($form, &$form_state) {
    isset($form_state['values']['bunchball_user_login']) ? variable_set('bunchball_user_login', array('enabled'=>1, 'method' => 'userLogin')) : variable_set('bunchball_user_login', array('enabled'=> 0, 'method' => ''));
    isset($form_state['values']['bunchball_user_register']) ? variable_set('bunchball_user_register', array('enabled' => 1, 'method' => 'userRegister')) : variable_set('bunchball_user_register', array('enabled'=>0, 'method' => ''));
    isset($form_state['values']['bunchball_user_profile_complete']) ? variable_set('bunchball_user_profile_complete', array('enabled' => 1, 'method'  => 'userProfileComplete')) : variable_set('bunchball_user_profile_complete', array('enabled'=>0, 'method' => ''));
  }

  /**
   * @param $user - a valid drupal user object
   */
  public function userLogin($user) {
     try {
      // Get a create a user and/or get a bunchball session
      // with the user currently logging in
      $this->apiUserLogin($user);

      $identity_provider = 'Drupal';
      // We call logAction with 'Login' as the 'actionTag'. In addition to the
      // 'actionTag' word we can pass additional tagging information as a comma
      // seperated list of Key/Value pairs (e.g. "Login, Identity Provider: Drupal").
      // If we've got the Janrain module (rpx) we can extract providerName from
      // $user->data and pass that along.

      if (module_exists('rpx_core')){
        if(isset($user->data['rpx_data']['profile']['providerName'])) {
          $identity_provider = $user->data['rpx_data']['profile']['providerName'];
        }
      }
      $this->bunchballApi->logAction('Login, Identity Provider: '. $identity_provider);
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

  /**
   * @param $user - a valid drupal user object
   */
  public function userRegister($user) {
    try {
      // Get a create a user and/or get a bunchball session
      // with the user currently logging in
      $this->apiUserLogin($user);
      $this->bunchballApi->logAction('Register');
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

  /**
   * @param $user - a valid drupal user object
   */
  public function userProfileComplete($user) {
    try {
      $count = 0;
      //We call the bunchball login action so that the logAction is associated
      //with the user currently logging in
      $this->apiUserLogin($user);

      $custom_user_fields = field_info_instances('user', 'user');
      foreach($custom_user_fields as $field => $value) {
        if (isset($user->{$field}[LANGUAGE_NONE][0])) {
          $count++;
        }
      }
      $this->bunchballApi->logAction('Profile_Fields_Entered', $count);
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }

  }

  /**
   * @param $user - a valid drupal user object
   */
  public function apiUserLogin($user) {
    try {
      //In this default bunchball.login we are making some assumptions about
      //what bits of information are sent to bunchball to identify a user.
      //See nitro.api.class::login doxygen for more information if you want to
      //extend this class to override this method.
      $this->bunchballApi->login($user->uid, $user->name, $user->mail);
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

}