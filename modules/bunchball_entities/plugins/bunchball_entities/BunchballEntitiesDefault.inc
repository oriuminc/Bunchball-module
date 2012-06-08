<?php
/**
 * @file
 *    Define ctools plugin parameters.
 */

$plugin = array(
  'BunchballEntitiesDefault' => array(
    'name' => 'Bunchball Default Entities Plugin',
    'module' => 'bunchball_entities',
    'label' => t('Bunchball Default Entities'),
    'file' => 'BunchballEntitiesDefault.class.php',
    'handler' => array(
      'class' => 'BunchballEntitiesDefault',
    ),
  ),
);