<?php
/**
 * @file
 *    Define base interface for Bunchball plugins.
 */

interface BunchballPluginInterface {
  /**
   * Form callback for this plugin.
   * 
   * @param $form
   * @param $form_state
   * @return array
   *    form to be rendered
   */
  public function adminForm($form, &$form_state);

  /**
   * Form validation callback for this plugin.
   * 
   * @param $form
   * @param $form_state 
   */
  public function adminFormValidate($form, &$form_state);

  /**
   * Submit callback for this plugin.
   * 
   * @param $form
   * @param $form_state 
   */
  public function adminFormSubmit($form, &$form_state);
  
  /**
   * AJAX callback for plugin.
   * 
   * @param $form
   * @param $form_state
   * @param $op
   * @param $data
   */
  public function adminFormAjax($form, &$form_state, $op, $data);
  
}
