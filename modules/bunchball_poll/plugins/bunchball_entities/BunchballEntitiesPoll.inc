<?php
/**
 * @file
 *    Define ctools plugin parameters for bunchball poll plugin.
 */

$plugin = array(
  'BunchballEntitiesPoll' => array(
    'name' => 'Bunchball Poll Plugin',
    'module' => 'bunchball_poll',
    'label' => t('Bunchball Poll'),
    'file' => 'BunchballEntitiesPoll.class.php',
    'handler' => array(
      'class' => 'BunchballEntitiesPoll',
    ),
  ),
);