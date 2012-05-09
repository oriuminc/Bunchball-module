<?php

class BunchballEntitiesDefault implements BunchballPluginInterface, BunchballEntitiesPluginInterface {

  private $options;
  private $nitro;
  
  function __construct() {
    $this->options = variable_get('bunchball_entities');
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
    $form['bunchball_entities'] = array(
        '#type' => 'fieldset',
        '#title' => t('Content types'),
        '#collapsible' => TRUE,
        '#tree' => TRUE,
    );
    $form['bunchball_entities']['settings'] = $this->buildEntityFormFields();
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
    $this->options = $this->getDrupalContentTypes();
    $values = $form_state['values']['bunchball_entities']['settings'];
    foreach ($values as $key => $value) {
      $this->options[$key]['insert'] = $value[$key . '_insert_check'];
      $this->options[$key]['insert_action'] = $value[$key . '_insert_action'];
      $this->options[$key]['update'] = $value[$key . '_update_check'];
      $this->options[$key]['update_action'] = $value[$key . '_update_action'];
      $this->options[$key]['comment'] = $value[$key . '_comment_check'];
      $this->options[$key]['comment_action'] = $value[$key . '_comment_action'];
    }
    variable_set('bunchball_entities', $this->options);
  }
  
  /**
   * Register content actions.
   *
   * @param $id
   * @param $type 
   * @param $user
   * @param $op
   *    operation to register (ie: insert / update / comment) 
   */
  public function send($id, $type, $user, $op) {
    if (in_array($op, array('insert', 'update', 'comment')) && $this->checkSend($id, $op)) {
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
  
  private function checkSend($id, $op) {
    return $this->options[$id][$op];
  }

  private function getActionName($id, $op) {
    return $this->options[$id]["{$op}_action"];
  }
  
  /**
   * Build a set of form fields for all content types.
   * 
   * @return array
   *    form elements for all content types
   */
  private function buildEntityFormFields() {
    $form = array();
    foreach ($this->getDrupalContentTypes() as $id => $type) {
      $form += $this->buildEntityField($id, $type);
    }
    return $form;
  }
  
  /**
   * Build the form fields for a content type.
   * 
   * @param $id
   *    identifier for the content type
   * @param $type
   *    details for the the content type
   * @return array
   *    form field elements for one content type
   */
  private function buildEntityField($id, $type) {
    $form = array();
    $form[$id] = array(
      '#type' => 'fieldset',
      '#title' => t($type['name']),
      '#collapsible' => FALSE,
    );
    $form[$id][$id . '_insert_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Insert'),
      '#default_value' => isset($this->options[$id]['insert']) ? $this->options[$id]['insert'] : NULL,
    );
    $form[$id][$id . '_insert_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action name'),
      '#default_value' => isset($this->options[$id]['insert_action']) ? $this->options[$id]['insert_action'] : NULL,
    );
    $form[$id][$id . '_update_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Update'),
      '#default_value' => isset($this->options[$id]['update']) ? $this->options[$id]['update'] : NULL,
    );
    $form[$id][$id . '_update_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action name'),
      '#default_value' => isset($this->options[$id]['update_action']) ? $this->options[$id]['update_action'] : NULL,
    );
    $form[$id][$id . '_comment_check'] = array(
      '#type' => 'checkbox',
      '#title' => t('Comment'),
      '#default_value' => isset($this->options[$id]['comment']) ? $this->options[$id]['comment'] : NULL,
    );
    $form[$id][$id . '_comment_action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action name'),
      '#default_value' => isset($this->options[$id]['comment_action']) ? $this->options[$id]['comment_action'] : NULL,
    );
    return $form;
  }

  /**
   * Get an array of content types know to Drupal.
   * 
   * @return array
   *    array of the available system content types. 
   */
  private function getDrupalContentTypes() {
    $types = array();
    // node types
    foreach (node_type_get_types() as $node_type) {
      $types['node_' . $node_type->type] = array('name' => $node_type->name, 'type' => 'node');
    }
    $types['entity_comment'] = array('name' => 'Comment', 'type' => 'entity');
    return $types;
  }


}