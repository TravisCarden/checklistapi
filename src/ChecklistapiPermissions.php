<?php

/**
 * @file
 * Contains \Drupal\checklistapi\ChecklistapiPermissions.
 */

namespace Drupal\checklistapi;

use Drupal\Core\Url;

/**
 * Defines a class containing permission callbacks.
 */
class ChecklistapiPermissions {

  /**
   * Constructs a ChecklistapiPermissions object.
   */
  public function __construct() {
    $this->editPermissionDescription = t('Check and uncheck list items and save changes, or clear saved progress.');
    $this->viewPermissionDescription = t('Read-only access: View list items and saved progress.');
  }

  /**
   * Returns an array of universal permissions.
   *
   * @return array
   *   An array of permission details.
   */
  public function universalPermissions() {
    $perms['view checklistapi checklists report'] = array(
      'title' => t('View the !name report', array(
        '!name' => (\Drupal::currentUser()->hasPermission('view checklistapi checklists report')) ? \Drupal::l(t('Checklists'), Url::fromRoute('checklistapi.report')) : drupal_placeholder('Checklists'),
      )),
    );
    $perms['view any checklistapi checklist'] = array(
      'title' => t('View any checklist'),
      'description' => $this->viewPermissionDescription,
    );
    $perms['edit any checklistapi checklist'] = array(
      'title' => t('Edit any checklist'),
      'description' => $this->editPermissionDescription,
    );
    return $perms;
  }

  /**
   * Returns an array of per checklist permissions.
   *
   * @return array
   *   An array of permission details.
   */
  public function perChecklistPermissions() {
    $perms = array();

    // Per checklist permissions.
    foreach (checklistapi_get_checklist_info() as $id => $definition) {
      $checklist = checklistapi_checklist_load($id);

      if (!$checklist) {
        continue;
      }

      $checklist_name = drupal_placeholder($checklist->title);
      // Hyperlink the checklist name if the current user has access to view it.
      if (checklistapi_checklist_access($id)) {
        $checklist_name = \Drupal::l($checklist->title, Url::fromRoute($checklist->getRouteName()));
      }

      $perms["view {$id} checklistapi checklist"] = array(
        'title' => t('View the !name checklist', array('!name' => $checklist_name)),
        'description' => $this->viewPermissionDescription,
      );
      $perms["edit {$id} checklistapi checklist"] = array(
        'title' => t('Edit the !name checklist', array('!name' => $checklist_name)),
        'description' => $this->editPermissionDescription,
      );
    }

    return $perms;
  }

}
