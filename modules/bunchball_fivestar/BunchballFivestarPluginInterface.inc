<?php

interface BunchballFivestarPluginInterface {
  
  /**
   * Register content insert actions.
   * 
   * @param $id
   *    identifier for the content eg. nid (26)
   * 
   * @param $type
   *    type of content nid ie. "node"
   * 
   * @param $user
   *    user object
   */
  public function rate($id, $type, $user);
  
}