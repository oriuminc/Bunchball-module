<?php
/**
 * @file
 * Defines BunchBallUserInteractionInterface
 */

interface BunchballUserInteractionInterface {

  /**
   * @param $user - a user object of type defined by implementor
   *
   * A plugin callback that can take a user object and communicate login to
   * bunchball passing whichever arguments the implementer would like.
   *
   */
  public function userLogin($user);

  /**
   * @param $user - a user object of type defined by implementor
   *
   * A plugin callback that can take a user object and communicate regsitration to
   * bunchball passing whichever arguments the implementer would like.
   *
   */
  public function userRegister($user);

    /**
   * @param $user - a user object of type defined by implementor
   *
   * A plugin callback that can take a user object and communicate information
   * about the number of profile fields that have been completed to bunchball
   * passing whichever additional arguments the implementer would like.
   *
   */
  public function userProfileComplete($user);

  /**
   * @param $user - a user object of type defined by implementor
   *
   * A plugin callback that can take a user object and communicate that a
   * profile picture has been uploaded to bunchball passing whichever arguments
   * the implementer would like.
   *
   */
  public function userProfilePicture($user);

}