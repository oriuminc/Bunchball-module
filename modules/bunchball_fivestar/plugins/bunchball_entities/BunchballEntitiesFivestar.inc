<?php
/**
 * @file
 *    Plugin parameters for bunchball fivestar plugin.
 */

$plugin = array(
  'BunchballEntitiesFivestar' => array(
    'name' => 'Bunchball Fivestar Plugin',
    'module' => 'bunchball_fivestar',
    'label' => t('Bunchball Fivestar'),
    'file' => 'BunchballEntitiesFivestar.class.php',
    'handler' => array(
      'class' => 'BunchballEntitiesFivestar',
    ),
  ),
);