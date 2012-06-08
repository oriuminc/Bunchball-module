<?php
/**
 * @file
 *   Define Interface for Bunchball entity plugins.
 */

interface BunchballEntitiesPluginInterface {
  
  /**
   * Register content insert actions.
   * 
   * @param $id
   *    identifier for the content eg. "node_page"
   * 
   * @param $type
   *    type of content added ie. "node" or "comment"
   * 
   * @param $user
   *    user object
   * 
   * @param $op
   *    operation to be sent
   */
  public function send($id, $type, $user, $op);
    
}