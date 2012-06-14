<?php

/**
 * @file
 * Class for Checklist API checklists.
 */

/**
 * Defines the checklist class.
 */
class ChecklistapiChecklist {

  /**
   * The checklist ID.
   *
   * @var string
   */
  public $id;

  /**
   * The checklist title.
   *
   * @var string
   */
  public $title;

  /**
   * The menu item description.
   *
   * @var string
   */
  public $description;

  /**
   * The checklist path.
   *
   * @var string
   */
  public $path;

  /**
   * The checklist help.
   *
   * @var string
   */
  public $help;

  /**
   * The name of the menu to put the menu item in.
   *
   * @var string
   */
  public $menuName;

  /**
   * The checklist weight.
   *
   * @var float
   */
  public $weight;

  /**
   * The number of list items in the checklist.
   *
   * @var int
   */
  public $totalItems = 0;

  /**
   * The checklist groups and items.
   *
   * @var array
   */
  public $items = array();

  /**
   * The saved progress data.
   *
   * @var array
   */
  public $savedProgress;

  /**
   * Constructs a ChecklistapiChecklist object.
   *
   * @param array $definition
   *   A checklist definition, as returned by checklistapi_get_checklist_info().
   */
  public function __construct(array $definition) {
    foreach (element_children($definition) as $group_key) {
      $this->totalItems += count(element_children($definition[$group_key]));
      $this->items[$group_key] = $definition[$group_key];
      unset($definition[$group_key]);
    }
    foreach ($definition as $key => $value) {
      $property_name = checklistapi_convert_string_to_lower_camel(substr($key, 1));
      $this->$property_name = $value;
    }
    $this->savedProgress = variable_get($this->getSavedProgressVariableName(), array());
  }

  /**
   * Clears the saved progress for the checklist.
   *
   * Deletes the Drupal variable containing the checklist's saved progress.
   */
  public function clearSavedProgress() {
    variable_del($this->getSavedProgressVariableName());
    drupal_set_message(t('%title saved progress has been cleared.', array(
      '%title' => $this->title,
    )));
  }

  /**
   * Gets the name of the Drupal variable for the checklist's saved progress.
   *
   * @return string
   *   The Drupal variable name.
   */
  public function getSavedProgressVariableName() {
    return 'checklistapi_checklist_' . $this->id;
  }

  /**
   * Determines whether the checklist has saved progress.
   *
   * @return bool
   *   TRUE if the checklist has saved progress, or FALSE if it doesn't.
   */
  public function hasSavedProgress() {
    return (bool) variable_get($this->getSavedProgressVariableName(), FALSE);
  }

  /**
   * Saves checklist progress to a Drupal variable.
   *
   * @param array $values
   *   A multidimensional array representing.
   */
  public function saveProgress(array $values) {
    global $user;
    $time = time();
    $completed_items_counter = 0;
    $changed_items_counter = 0;
    // Loop through groups.
    foreach ($values as $group_key => $group) {
      // Loop through items.
      if (is_array($group)) {
        foreach ($group as $item_key => $item) {
          $old_item = (!empty($this->savedProgress[$group_key][$item_key])) ? $this->savedProgress[$group_key][$item_key] : 0;
          $new_item = &$values[$group_key][$item_key];
          // Item is checked.
          if ($item == 1) {
            $completed_items_counter++;
            // Item was previously checked. Use saved value.
            if ($old_item) {
              $new_item = $old_item;
            }
            // Item is newly checked. Set new value.
            else {
              $new_item = array(
                '#completed' => $time,
                '#uid' => $user->uid,
              );
              // Increment changed items counter.
              $changed_items_counter++;
            }
          }
          // Item is unchecked.
          else {
            // Item was previously checked off. Increment changed items counter.
            if ($old_item) {
              $changed_items_counter++;
            }
          }
        }
      }
    }
    $progress = array(
      '#changed' => $time,
      '#changed_by' => $user->uid,
      '#completed_items' => $completed_items_counter,
    ) + $values;
    variable_set($this->getSavedProgressVariableName(), $progress);
    drupal_set_message(format_plural(
      $changed_items_counter,
      'Checklist %title has been updated. 1 item changed.',
      'Checklist %title has been updated. @count items changed.',
      array('%title' => $this->title)
    ));
  }

  /**
   * Determines whether the current user has access to the checklist.
   *
   * @param string $operation
   *   The operation to test access for. Possible values are "view", "edit", and
   *   "any". Defaults to "any".
   *
   * @return bool
   *   Returns TRUE if the user has access, or FALSE if not.
   */
  public function userHasAccess($operation = 'any') {
    return checklistapi_checklist_access($this->id, $operation);
  }

}
