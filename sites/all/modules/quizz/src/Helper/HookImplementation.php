<?php

namespace Drupal\quizz\Helper;

use Drupal\quizz\Helper\HookImplementation\HookEntityInfo;
use Drupal\quizz\Helper\HookImplementation\HookMenu;
use Drupal\quizz\Helper\HookImplementation\HookPermission;
use Drupal\quizz\Helper\HookImplementation\HookQuizScored;
use Drupal\quizz\Helper\HookImplementation\HookUserCancel;

class HookImplementation {

  private $hookMenu;
  private $hookEntityInfo;
  private $hookQuizScored;
  private $hookUserCancel;
  private $hookPermission;

  /**
   * @return HookMenu
   */
  public function getHookMenu() {
    if (null === $this->hookMenu) {
      $this->hookMenu = new HookMenu();
    }
    return $this->hookMenu;
  }

  public function setHookMenu($hookMenu) {
    $this->hookMenu = $hookMenu;
    return $this;
  }

  /**
   * @return HookEntityInfo
   */
  public function getHookEntityInfo() {
    if (null === $this->hookEntityInfo) {
      $this->hookEntityInfo = new HookEntityInfo();
    }
    return $this->hookEntityInfo;
  }

  public function setHookEntityInfo($hookEntityInfo) {
    $this->hookEntityInfo = $hookEntityInfo;
    return $this;
  }

  /**
   * @return HookQuizScored
   */
  public function getHookQuizScored($quiz, $score, $result_id) {
    if (NULL === $this->hookQuizScored) {
      $this->hookQuizScored = new HookQuizScored($quiz, $score, $result_id);
    }
    return $this->hookQuizScored;
  }

  public function setHookQuizScored($hookQuizScored) {
    $this->hookQuizScored = $hookQuizScored;
    return $this;
  }

  /**
   * @return HookUserCancel
   */
  public function getHookUserCancel($account, $method) {
    if (NULL === $this->hookUserCancel) {
      $this->hookUserCancel = new HookUserCancel($account, $method);
    }
    return $this->hookUserCancel;
  }

  public function setHookUserCancel($hookUserCancel) {
    $this->hookUserCancel = $hookUserCancel;
    return $this;
  }

  /**
   * @return HookPermission
   */
  public function getHookPermission() {
    if (NULL === $this->hookPermission) {
      $this->hookPermission = new HookPermission();
    }
    return $this->hookPermission;
  }

  public function setHookPermission($hookPermission) {
    $this->hookPermission = $hookPermission;
    return $this;
  }

}
