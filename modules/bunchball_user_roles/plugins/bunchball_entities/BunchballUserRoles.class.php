<?php

class BunchballUserRoles implements BunchballPluginInterface {

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
        '#title' => t('Poll user roles'),
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

  /**
   * Register rating actions.
   *
   * @param $id
   * @param $type 
   * @param $user
   */
  public function send($id, $type, $user, $op) {
    if ($op == 'vote' && $this->checkSend($id)) {
      try {
        // log in
        $this->nitro->drupalLogin($user);
        $action = $this->getActionName($id, $op);
        $this->nitro->logAction($action);
      }
      catch (NitroAPI_LogActionException $e) {
        drupal_set_message($e->getMessage(), 'error');
      }
    }
  }
  
  private function checkSend($id) {
    return $this->options['roles']['check'];
  }

  private function getActionName($id, $op) {
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
      '#default_value' => isset($this->options['role']['check']) ? $this->options['role']['check'] : NULL,
    );
    $form['roles']['whitelist'] = array(
      '#type' => 'textfield',
      '#title' => t('Whitelist roles'),
      '#description' => t(),
      '#default_value' => isset($this->options['role']['whitelist']) ? $this->options['role']['whitelist'] : NULL,
    );
    return $form;
  }

}