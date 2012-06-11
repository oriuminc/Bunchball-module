<?php
/**
 * @file
 *    Define ctools plugin parameters for bunchball groups plugin.
 */

$plugin = array(
  'BunchballGroups' => array(
    'name' => 'Bunchball Groups',
    'module' => 'bunchball_groups',
    'label' => t('Bunchball Groups'),
    'file' => 'BunchballGroups.class.php',
    'handler' => array(
      'class' => 'BunchballGroups',
    ),
  ),
);