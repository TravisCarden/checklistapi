<?php

namespace Drupal\checklistapi;

use Drupal\Core\Access\AccessibleInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\Routing\Route;

/**
 * Defines an interface for checklist plugins which can expose a route or link.
 */
interface RoutableChecklistInterface extends AccessibleInterface, ChecklistInterface {

  /**
   * Defines a route at which the checklist can be accessed.
   *
   * @return \Symfony\Component\Routing\Route
   *   A route at which the checklist can be accessed, or NULL to not expose
   *   a route.
   */
  public function getRoute() : ?Route;

  /**
   * Defines a menu link for the checklist's route.
   *
   * @return array
   *   A menu link definition for the checklist's route, or NULL to not expose
   *   a link.
   */
  public function getMenuLink() : ?array;

  /**
   * Returns text for the "Help" block on the checklist route.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   Text to display in the "Help" block on the checklist route, or NULL to
   *   not display any help text.
   */
  public function getHelp() : ?TranslatableMarkup;

}
