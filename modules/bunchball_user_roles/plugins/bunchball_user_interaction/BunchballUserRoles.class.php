<?php

class BunchballUserRoles implements BunchballPluginInterface, BunchballUserInteractionInterface {

  private $options;
  private $nitro;
  
  function __construct() {
    $this->options = variable_get('bunchball_user_roles');
    $this->nitro = NitroAPI_Factory::getInstance();
  }

  /**
   * Form callback for this plugin.
   * 
   * @param $form
   * @param $form_state
   * @return array
   *    form to be rendered
   */
  public function adminForm($form, &$form_state) {
    $form['bunchball_user_roles'] = array(
        '#type' => 'fieldset',
        '#title' => t('User roles'),
        '#collapsible' => TRUE,
        '#tree' => TRUE,
    );
    $form['bunchball_user_roles']['settings'] = $this->buildFields();

    return $form;
  }

  /**
   * Form validation callback for this plugin.
   * 
   * @todo check that checkboxes and action textboxes are consistent
   * 
   * @param $form
   * @param $form_state 
   */
  public function adminFormValidate($form, &$form_state) {}

  /**
   * Submit callback for this plugin.
   * 
   * @param $form
   * @param $form_state 
   */
  public function adminFormSubmit($form, &$form_state) {
    $values = $form_state['values']['bunchball_user_roles']['settings'];
    $this->options['roles']['check'] = $values['roles']['check'];
    $this->options['roles']['whitelist'] = $values['roles']['whitelist'];
    variable_set('bunchball_user_roles', $this->options);
  }

  
  public function send($user, $op) {
    if ($op == 'setRole' && $this->checkSend()) {
      try {
        // log in
        $this->nitro->drupalLogin($user);
        $action = $this->getActionName();
        $this->nitro->logAction($action);
      }
      catch (NitroAPI_LogActionException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
    }
  }

  private function checkSend() {
    return $this->options['roles']['check'];
  }

  private function getActionName() {
    return $this->options['roles']['whitelist'];
  }
  
  /**
   * Build the form fields for a content type.
   * 
   * @return array
   *    form field elements for one content type
   */
  private function buildFields() {
    $form = array();
    $form['roles']['check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Update user role based on Bunchball level'),
      '#default_value' => isset($this->options['roles']['check']) ? $this->options['roles']['check'] : NULL,
    );
    $blacklist_roles = array('anonymous user', 'authenticated user', 'administrator');
    $all_roles = user_roles();
    $role_list = drupal_map_assoc(array_diff($all_roles, $blacklist_roles));
    $form['roles']['whitelist'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Whitelist roles'),
      '#description' => t('Bunchball can add checked roles to user accounts. Avoid roles with security implications.'),
      '#options' => $role_list,
      '#default_value' => isset($this->options['roles']['whitelist']) ? $this->options['roles']['whitelist'] : NULL,
    );
    return $form;
  }

}