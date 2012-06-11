<?php
/**
 * @file
 *    Ctools plugin parameters for bunchball user roles plugin.
 */

$plugin = array(
  'BunchballUserRoles' => array(
    'name' => 'Bunchball User Roles',
    'module' => 'bunchball_user_roles',
    'label' => t('Bunchball User Roles'),
    'file' => 'BunchballUserRoles.class.php',
    'handler' => array(
      'class' => 'BunchballUserRoles',
    ),
  ),
);