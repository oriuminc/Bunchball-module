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
    $form['bunchball_user_interaction'] = array(
      '#type' => 'fieldset',
      '#title' => t('User Actions'),
      '#collapsible' => TRUE,
      '#description' => t('Enable the user actions to track, which maps them to the Bunchball Nitro console.'),
      '#tree' => TRUE,
    );

    $form['bunchball_user_interaction']['bunchball_user_login_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('User login'),
      '#description' => t('Notify the Bunchball service when a user logs into the site'),
      '#default_value' => $this->options['bunchball_user_login']['enabled'],
    );

    $form['bunchball_user_interaction']['bunchball_user_login_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Nitro action name'),
      '#description' => t('The machine name used to map this action to your Bunchball Nitro Server.'),
      '#default_value' => $this->options['bunchball_user_login']['method'],
    );

    $form['bunchball_user_interaction']['bunchball_user_register_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('User registration'),
      '#description' => t('Notify the Bunchball service when a user registers on the site.'),
      '#default_value' => $this->options['bunchball_user_register']['enabled'],
    );

    $form['bunchball_user_interaction']['bunchball_user_register_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Nitro action name'),
      '#description' => t('The machine name used to map this action to your Bunchball Nitro Server.'),
      '#default_value' => $this->options['bunchball_user_register']['method'],
    );

    $form['bunchball_user_interaction']['bunchball_user_profile_complete_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Profile completion'),
      '#description' => t('Notify the Bunchball service with the number of fields a user has completed on their profile.'),
      '#default_value' => $this->options['bunchball_user_profile_complete']['enabled'],
    );

    $form['bunchball_user_interaction']['bunchball_user_profile_complete_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Nitro action name'),
      '#description' => t('The machine name used to map this action to your Bunchball Nitro Server.'),
      '#default_value' => $this->options['bunchball_user_profile_complete']['method'],
    );

    $form['bunchball_user_interaction']['bunchball_user_profile_picture_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Profile picture upload'),
      '#description' => t('Notify the Bunchball service when a user uploads a profile picture.'),
      '#default_value' => $this->options['bunchball_user_profile_picture']['enabled'],
    );

    $form['bunchball_user_interaction']['bunchball_user_profile_picture_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Nitro action name'),
      '#description' => t('The machine name used to map this action to your Bunchball Nitro Server.'),
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
    if ($values['bunchball_user_interaction']['bunchball_user_login_check']) {
      $login_value = array(
          'enabled'=>1,
          'method' => $values['bunchball_user_interaction']['bunchball_user_login_action'],
          );
      variable_set('bunchball_user_login', $login_value);
    }
    else {
      variable_set('bunchball_user_login', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['bunchball_user_interaction']['bunchball_user_register_check']) {
      $register_value = array(
          'enabled' => 1,
          'method' => $values['bunchball_user_interaction']['bunchball_user_register_action'],
          );
      variable_set('bunchball_user_register', $register_value);
    }
    else {
      variable_set('bunchball_user_register', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['bunchball_user_interaction']['bunchball_user_profile_complete_check']) {
      $profile_value = array(
          'enabled' => 1,
          'method'  => $values['bunchball_user_interaction']['bunchball_user_profile_complete_action'],
          );
      variable_set('bunchball_user_profile_complete', $profile_value);
    }
    else {
      variable_set('bunchball_user_profile_complete', array('enabled'=> 0, 'method' => ''));
    }
    if ($values['bunchball_user_interaction']['bunchball_user_profile_picture_check']) {
      $picture_value = array(
          'enabled' => 1,
          'method'  => $values['bunchball_user_interaction']['bunchball_user_profile_picture_action']
          );
      variable_set('bunchball_user_profile_picture', $picture_value);
    }
    else {
      variable_set('bunchball_user_profile_picture', array('enabled'=> 0, 'method' => ''));
    }
  }

  /**
   * AJAX callback.
   *
   * @param $form
   * @param $form_state
   * @param $op
   * @param $data
   */
  public function adminFormAjax($form, &$form_state, $op, $data) {}

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
        $action = $this->options['bunchball_user_login']['method'];
        $this->bunchballApi->logAction("$action, Identity Provider: $identity_provider");
      }
      catch (NitroAPI_LogActionException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
      catch(NitroAPI_HttpException $e) {
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
        $action = $this->options['bunchball_user_register']['method'];
        $this->bunchballApi->logAction($action);
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
        $action = $this->options['bunchball_user_profile_complete']['method'];
        $this->bunchballApi->logAction($action, $count);

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
          $action = $this->options['bunchball_user_profile_picture']['method'];
          $this->bunchballApi->logAction($action);
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
    catch (NitroAPI_HttpException $e) {
      drupal_set_message($e->getMessage(), 'error');
    }
  }

}