<?php
/**
 * @file
 *    Define ctools plugin parameters for bunchball webform plugin.
 */

$plugin = array(
  'BunchballEntitiesWebform' => array(
    'name' => 'Bunchball Webform Plugin',
    'module' => 'bunchball_webform',
    'label' => t('Bunchball Poll'),
    'file' => 'BunchballEntitiesWebform.class.php',
    'handler' => array(
      'class' => 'BunchballEntitiesWebform',
    ),
  ),
);