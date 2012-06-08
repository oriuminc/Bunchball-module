<?php
/**
 * @file
 *    Define ctools plugin parameters for bunchball user interaction plugin.
 */

$plugin = array(
  'BunchballUserInteractionDefault' => array(
    'name' => 'Bunchball Default User Interaction Plugin',
    'module' => 'bunchball_user_interaction',
    'label' => t('Bunchball Default User Interactions'),
    'file' => 'BunchballUserInteractionDefault.class.php',
    'handler' => array(
      'class' => 'BunchballUserInteractionDefault',
    ),
  ),
);