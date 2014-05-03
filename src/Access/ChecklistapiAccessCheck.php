<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Access\ChecklistapiAccessCheck.
 */

namespace Drupal\checklistapi\Access;

use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class ChecklistapiAccessCheck implements AccessInterface {

  /**
   * {@inheritdoc}
   */
  public function access(Route $route, Request $request, AccountInterface $account) {
    $op = $request->attributes->get('op');
    $op = !empty($op) ? $op : 'any';

    return checklistapi_checklist_access($request->attributes->get('checklist_id'), $op) ? static::ALLOW : static::DENY;
  }
}
