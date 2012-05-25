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
   * Constructs a ChecklistapiChecklist object.
   *
   * @param array $definition
   *   A checklist definition, as returned by checklistapi_get_checklist_info().
   */
  public function __construct($definition) {
    foreach ($this->getPropertiesFromDefinition($definition) as $key => $value) {
      $this->$key = $value;
    }
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
   * Gets the items for the checklist.
   *
   * @return array
   *   A multidimensional array of groups and items as defined by
   *   hook_checklistapi_checklist_info().
   */
  public function getItems() {
    $definition = checklistapi_get_checklist_info($this->id);
    $items = array();
    foreach (element_children($definition) as $key) {
      $items[$key] = $definition[$key];
    }
    return $items;
  }

  /**
   * Gets the top-level properties from a checklist definition.
   *
   * @param array $definition
   *   A checklist definition, as from hook_checklistapi_checklist_info().
   *
   * @return array
   *   An array of property values, keyed by camelCased versions of their names.
   */
  protected function getPropertiesFromDefinition($definition) {
    $element_children = element_children($definition);
    $properties = array();
    foreach ($definition as $key => $value) {
      if (!in_array($key, $element_children)) {
        $property_name = $this->strtocamel(substr($key, 1));
        $properties[$property_name] = $value;
      }
    }
    return $properties;
  }

  /**
   * Gets the saved progress for the checklist.
   *
   * @return array
   *   A multidimensional array of saved progress data.
   */
  public function getSavedProgress() {
    return variable_get($this->getSavedProgressVariableName(), array());
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
  public function saveProgress($values) {
    global $user;
    $saved_values = $this->getSavedProgress();
    $time = time();
    $changed_items_counter = 0;
    // Loop through groups.
    foreach ($values as $group_key => $group) {
      // Loop through items.
      if (is_array($group)) {
        foreach ($group as $item_key => $item) {
          $old_item = &$saved_values[$group_key][$item_key];
          $new_item = &$values[$group_key][$item_key];
          // Item is checked.
          if ($item == 1) {
            // Item was previously checked. Use saved value.
            if ($old_item) {
              $new_item = $old_item;
            }
            // Item is newly checked. Set new value.
            else {
              $new_item = array(
                'completed' => $time,
                'uid' => $user->uid,
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
    variable_set($this->getSavedProgressVariableName(), $values);
    drupal_set_message(format_plural(
      $changed_items_counter,
      'Checklist %title has been updated. 1 item changed.',
      'Checklist %title has been updated. @count items changed.',
      array('%title' => $this->title)
    ));
  }

  /**
   * Converts a string to camelCase, as suitable for a class property name.
   *
   * @param string $string
   *   The input string.
   *
   * @return string
   *   The input string converted to camelCase.
   */
  protected function strtocamel($string) {
    $string = str_replace('_', ' ', $string);
    $string = ucwords($string);
    $string = str_replace(' ', '', $string);
    return lcfirst($string);
  }

}
