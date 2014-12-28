<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Access\ChecklistapiAccessCheck.
 */

namespace Drupal\checklistapi\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;

/**
 * An access check service determining access rules for checklist routes.
 */
class ChecklistapiAccessCheck implements AccessInterface {

  /**
   * Checks routing access for the checklist.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Returns an access result.
   */
  public function access() {
    $request = \Drupal::request();
    $op = $request->attributes->get('op');
    $op = !empty($op) ? $op : 'any';

    return AccessResult::allowedIf(checklistapi_checklist_access($request->attributes->get('checklist_id'), $op));
  }
}
