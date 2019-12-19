<?php

namespace Drupal\checklistapi;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Session\AccountInterface;
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
  public function getItems(string $group = NULL) : array;

  /**
   * Returns the current progress of the checklist.
   *
   * @return int[]
   *   An array of two integers: the number of items in the checklist that have
   *   been marked complete, and the total number of items in the checklist (in
   *   that order).
   */
  public function getProgress() : array;

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
   * @param \Drupal\Core\Session\AccountInterface $account
   *   (optional) The user account which is completing the item. Defaults to
   *   the current user.
   * @param array $data
   *   (optional) Any additional information to be stored.
   */
  public function setComplete(string $group, string $item, AccountInterface $account = NULL, array $data = []);

  /**
   * Marks an item as incomplete.
   *
   * @param string $group
   *   The item's group.
   * @param string $item
   *   The item's machine name.
   */
  public function setIncomplete(string $group, string $item);

}
