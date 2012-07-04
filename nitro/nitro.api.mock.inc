<?php

class NitroAPI_Mock implements NitroAPI {
  /**
   * Log in to set session.
   *
   * @param $userName
   *    User name
   *
   * @param $firstName
   *    optional.  Does not need to be the user's real first name
   *
   * @param $lastName
   *    option. Does not need to be the user's real last name
   */
  public function login($userName, $firstName = '', $lastName = '') {
    drupal_set_message('NITROAPI_MOCK_login');
  }

  /**
   * Log an action for the established session.
   *
   * @param $userName
   *    the user name to record info for
   * @param $actionTag
   *    The action tag to log
   * @param $value
   *    Value associated with the action tag
   *
   * @throws NitroAPI_NoSessionException
   */
  public function logAction($actionTag, $value = '') {
    drupal_set_message("NITROAPI_MOCK_logAction :: {$actionTag}");
  }

  /**
   * Return the user point balance for current session.
   *
   * @param $userName
   *    the user name to record info for
   *
   * @return
   *    the user point balance
   */
  public function getUserPointsBalance() {
    drupal_set_message('NITROAPI_MOCK_getUserPointsBalance');
  }

  /**
   * Retrieve site action leaders.
   *
   * @param $userName
   *    the user name to record info for
   * @param $actionTag
   *    action tag to retrieve
   *
   * @return
   *    array containing leaders
   */
  public function getSiteActionLeaders($actionTag) {
    drupal_set_message('NITROAPI_MOCK_getSiteActionLeaders');
  }

  /**
   * Retrieve the user's current level.
   *
   * @return
   *    user's level
   */
  public function getLevel() {
    drupal_set_message('NITROAPI_MOCK_getLevel');
  }

  /**
   * Add user to a group.
   *
   * @param $group
   *    Group to which user is added.
   */
  public function addUserToGroup($group) {
    drupal_set_message('NITROAPI_MOCK_addUserToGroup');
  }

  /**
   * Register callbacks to be run at various events.
   *
   * @param $object
   *    the object on which to run the callback.
   *
   * @param $event
   *    the event on which to run the callback. eg. 'postLogin'
   *
   * @param $function
   *    the callback function to call
   */
  public function registerCallback($object, $event, $function) {
    drupal_set_message('NITROAPI_MOCK_registerCallback');
  }

  public function drupalLogin($account) {
    drupal_set_message('NITROAPI_MOCK_drupalLogin');
  }

}
