<?php

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
   */
  public function insert($id, $type, $user);
  
  /**
   * Register content update actions.
   * 
   * @param $id
   *    identifier for the content eg. "node_page"
   * 
   * @param $type
   *    type of content added ie. "node" or "comment"
   * 
   * @param $user
   *    user object
   */
  public function update($id, $type, $user);
  
  /**
   * Register content comment actions.
   * 
   * @param $id
   *    identifier for the content eg. "node_page"
   * 
   * @param $type
   *    type of content added ie. "node" or "comment"
   * 
   * @param $user
   *    user object
   */
  public function comment($id, $type, $user);
  
}