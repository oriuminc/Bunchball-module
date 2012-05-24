<?php
/**
 * @file
 * Defines BunchballUserInteractionDefault class
 * that is used as a ctools plugin defined by the bunchball_user_interaction
 * module.
 */

class BunchballUserInteractionDefault implements BunchballUserInteractionInterface, BunchballPluginInterface{

  private $options;

  /**
   * @param $api a class implmenting the NitroAPI interface (could be json or xml)
   */
  function __construct(NitroAPI $api) {
    $this->options['bunchball_user_login'] = variable_get('bunchball_user_login','');
    $this->options['bunchball_user_register'] = variable_get('bunchball_user_register', '');
    $this->options['bunchball_user_profile_complete'] = variable_get('bunchball_user_profile_complete', '');
    $this->options['bunchball_user_profile_picture'] = variable_get('bunchball_user_profile_picture', '');
    $this->bunchballApi = $api;
  }

  public function getOptions() {
    return $this->options;
  }

  /**
   * Form callback for plugin.
   */
  public function adminForm($form, &$form_state) {
    $form['user_interaction'] = array(
      '#type' => 'fieldset',
      '#title' => t('User Interaction'),
      '#collapsible' => TRUE,
      '#tree' => TRUE,
    );

    $form['user_interaction']['bunchball_user_login'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate user login'),
      '#default_value' => $this->options['bunchball_user_login']['enabled'],
    );

    $form['user_interaction']['bunchball_user_login_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action'),
      '#default_value' => $this->options['bunchball_user_login']['method'],
    );

    $form['user_interaction']['bunchball_user_register'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate new user registration'),
      '#default_value' => $this->options['bunchball_user_register']['enabled'],
    );

    $form['user_interaction']['bunchball_user_register_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action'),
      '#default_value' => $this->options['bunchball_user_register']['method'],
    );

    $form['user_interaction']['bunchball_user_profile_complete'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate the number of user profile fields completed when a user saves their account.'),
      '#default_value' => $this->options['bunchball_user_profile_complete']['enabled'],
    );

    $form['user_interaction']['bunchball_user_profile_complete_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action'),
      '#default_value' => $this->options['bunchball_user_profile_complete']['method'],
    );

    $form['user_interaction']['bunchball_user_profile_picture'] = array(
      '#type' => 'checkbox',
      '#title' => t('Communicate whether a user has uploaded a profile picture'),
      '#default_value' => $this->options['bunchball_user_profile_picture']['enabled'],
    );

    $form['user_interaction']['bunchball_user_profile_picture_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action'),
      '#default_value' => $this->options['bunchball_user_profile_picture']['method'],
    );

    return $form;
  }

  /**
   * Validation callback for plugin.
   */
  public function adminFormValidate($form, &$form_state) {}

  /**
   * Submit callback for plugin.
   */
  public function adminFormSubmit($form, &$form_state) {
    $values = $form_state['values'];
    if ($values['user_interaction']['bunchball_user_login']) {
      $login_value = array(
          'enabled'=>1, 
          'method' => $values['user_interaction']['bunchball_user_login_action'],
          );
      variable_set('bunchball_user_login', $login_value);
    }
    else {
      variable_set('bunchball_user_login', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['user_interaction']['bunchball_user_register']) {
      $register_value = array(
          'enabled' => 1,
          'method' => $values['user_interaction']['bunchball_user_register_action'],
          );
      variable_set('bunchball_user_register', $register_value);
    }
    else {
      variable_set('bunchball_user_register', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['user_interaction']['bunchball_user_profile_complete']) {
      $profile_value = array(
          'enabled' => 1,
          'method'  => $values['user_interaction']['bunchball_user_profile_complete_action'],
          );
      variable_set('bunchball_user_profile_complete', $profile_value);
    }
    else {
      variable_set('bunchball_user_profile_complete', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['user_interaction']['bunchball_user_profile_picture']) {
      $picture_value = array(
          'enabled' => 1,
          'method'  => $values['user_interaction']['bunchball_user_profile_picture_action']
          );
      variable_set('bunchball_user_profile_picture', $picture_value);
    }
    else {
      variable_set('bunchball_user_profile_picture', array('enabled'=> 0, 'method' => ''));
    }
  }

  /**
   * Callback for user interactions. Send user data to server for specified operation
   * 
   * @param $user
   *    Drupal user object
   * 
   * @param $op 
   *    Operation to send. EG: login, register
   */
  public function send($user, $op) {
    switch ($op) {
      case 'login':
        $this->userLogin($user);
        break;

      case 'register':
        $this->userRegister($user);
        break;

      case 'profileComplete':
        $this->userProfileComplete($user);
        break;

      case 'profilePicture':
        $this->userProfilePicture($user);
        break;

    }
  }
  
  /**
   * A plugin callback that can take a user object and communicate login to
   * bunchball passing whichever arguments the implementer would like.
   * 
   * @param $user - a valid drupal user object
   */
  private function userLogin($user) {
    if ($this->options['bunchball_user_login']['enabled']) {
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
  }

  /**
   * A plugin callback that can take a user object and communicate regsitration to
   * bunchball passing whichever arguments the implementer would like.
   * 
   * @param $user - a valid drupal user object
   */
  private function userRegister($user) {
    if ($this->options['bunchball_user_register']['enabled']) {
      try {
        // Get a create a user and/or get a bunchball session
        // with the user currently logging in
        $this->apiUserLogin($user);
        $this->bunchballApi->logAction('Register');
        $this->userProfileComplete($user);
      }
      catch (NitroAPI_LogActionException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
    }
  }

  /**
   * A plugin callback that can take a user object and communicate information
   * about the number of profile fields that have been completed to bunchball
   * passing whichever additional arguments the implementer would like.
   * 
   * @param $user - a valid drupal user object
   */
  private function userProfileComplete($user) {
    if ($this->options['bunchball_user_profile_complete']['enabled']) {
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
  }

  /**
   * A plugin callback that can take a user object and communicate that a
   * profile picture has been uploaded to bunchball passing whichever arguments
   * the implementer would like.
   * 
   * @param $user - a valid drupal user object
   */
  private function userProfilePicture($user) {
    if ($this->options['bunchball_user_profile_picture']['enabled']) {
      try {
        $this->apiUserLogin($user);
        if (isset($user->picture_upload)) {
          $this->bunchballApi->logAction('Profile_Photo_Added');
        }
      }
      catch (NitroAPI_LogActionException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
    }
  }

  /**
   * @param $user - a valid drupal user object
   */
  private function apiUserLogin($user) {
    try {
      //In this default bunchball.login we are making some assumptions about
      //what bits of information are sent to bunchball to identify a user.
      //See nitro.api.class::login doxygen for more information if you want to
      //extend this class to override this method.
      $this->bunchballApi->drupalLogin($user);
    }
    catch (NitroAPI_LogActionException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

}