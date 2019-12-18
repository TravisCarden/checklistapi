<?php

namespace Drupal\checklistapi;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines the base interface for all checklist plugins.
 */
interface ChecklistInterface extends PluginInspectionInterface {

  /**
   * Returns the title of the checklist.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The title of the checklist.
   */
  public function getTitle() : TranslatableMarkup;

  /**
   * Returns the items in the checklist, optionally limited to a single group.
   *
   * @param string $group
   *   (optional) The group of items to return. If omitted, all groups are
   *   returned.
   *
   * @return array
   *   The items in the checklist, grouped. If a particular group is requested,
   *   only the items in that group are returned.
   *
   * @throws \InvalidArgumentException
   *   If the requested group does not exist.
   */
  public function getItems(?string $group = NULL) : array;

  /**
   * Returns the percentage of the items that have been marked complete.
   *
   * @return float
   *   The percentage of the checklist's items (across all groups) that have
   *   been marked as complete.
   */
  public function getPercentComplete() : float;

  /**
   * Indicates if a particular item in the checklist is marked complete.
   *
   * @param string $group
   *   The item's group.
   * @param string $item
   *   The item's machine name.
   *
   * @return bool
   *   TRUE if them item has been marked as complete; FALSE otherwise, or if the
   *   item is not known.
   */
  public function isComplete(string $group, string $item) : bool;

  /**
   * Marks an item as complete.
   *
   * @param string $group
   *   The item's group.
   * @param string $item
   *   The item's machine name.
   */
  public function setComplete(string $group, string $item) : void;

  /**
   * Marks an item as incomplete.
   *
   * @param string $group
   *   The item's group.
   * @param string $item
   *   The item's machine name.
   */
  public function setIncomplete(string $group, string $item) : void;

}
