<?php
/**
 * @file
 * Defines BunchBallUserInteractionInterface
 */

interface BunchballUserInteractionInterface {

  /**
   * Callback for user interactions. Send user data to server for specified operation
   * 
   * @param $user
   *    Drupal user object
   * 
   * @param $op 
   *    Operation to send. EG: login, register
   */
  public function send($user, $op);
  
}