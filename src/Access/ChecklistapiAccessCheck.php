<?php

namespace Drupal\checklistapi\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * An access check service for checklist routes.
 */
class ChecklistapiAccessCheck implements AccessInterface  {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new ChecklistapiAccessCheck.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * Checks routing access for the checklist.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Returns an access result.
   */
  public function access() {
    $request = $this->requestStack->getCurrentRequest();
    $op = $request->attributes->get('op');
    $op = !empty($op) ? $op : 'any';

    $id = $request->attributes->get('checklist_id');

    if (!$id) {
      return AccessResult::neutral();
    }

    return AccessResult::allowedIf(checklistapi_checklist_access($id, $op));
  }
}
