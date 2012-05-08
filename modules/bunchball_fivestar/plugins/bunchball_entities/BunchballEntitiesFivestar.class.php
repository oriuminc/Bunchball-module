<?php

class BunchballEntitiesFivestar implements BunchballPluginInterface, BunchballFivestarPluginInterface {

  private $options;
  private $nitro;
  
  function __construct() {
    $this->options = variable_get('bunchball_fivestar');
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
    $form['bunchball_fivestar'] = array(
        '#type' => 'fieldset',
        '#title' => t('Fivestar'),
        '#collapsible' => TRUE,
        '#tree' => TRUE,
    );
    $form['bunchball_fivestar']['settings'] = $this->buildFields();
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
    $values = $form_state['values']['bunchball_fivestar']['settings'];
    $this->options['fivestar']['check'] = $value['fivestar']['check'];
    $this->options['fivestar']['action'] = $value['fivestar']['action'];
    variable_set('bunchball_fivestar', $this->options);
  }

  /**
   * Register rating actions.
   *
   * @param $id
   * @param $type 
   * @param $user
   */
  private function rate($id, $type, $user) {
    if ($this->checkSend($id)) {
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
    return $this->options[$id][$op];
  }

  private function getActionName($id, $op) {
    return $this->options[$id]["{$op}_action"];
  }
  
  /**
   * Build the form fields for a content type.
   * 
   * @return array
   *    form field elements for one content type
   */
  private function buildFields() {
    $form = array();
    $form['fivestar'] = array(
      '#type' => 'fieldset',
      '#title' => t($type['name']),
      '#collapsible' => FALSE,
    );
    $form['fivestar']['fivestar_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Fivestar'),
      '#default_value' => isset($this->options['fivestar']['check']) ? $this->options['fivestar']['check'] : NULL,
    );
    $form['fivestar']['fivestar_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action name'),
      '#default_value' => isset($this->options['fivestar']['action']) ? $this->options['fivestar']['action'] : NULL,
    );
    return $form;
  }

}