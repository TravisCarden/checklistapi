<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Routing\ChecklistapiRouteSubscriber.
 */

namespace Drupal\checklistapi\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class ChecklistapiRouteSubscriber extends RouteSubscriberBase {

  /**
   * Provides dynamic routes for Checklist API.
   *
   * @return \Symfony\Component\Routing\Route[]
   *   An array of route objects.
   */
  public function routes() {
    $routes = array();
    foreach (checklistapi_get_checklist_info() as $id => $definition) {
      // Ignore incomplete definitions.
      if (empty($definition['#path']) || empty($definition['#title'])) {
        continue;
      }

      // View/edit checklist.
      $routes["checklistapi.checklists.{$id}"] = new Route($definition['#path'], array(
        '_title' => $definition['#title'],
        '_form' => '\Drupal\checklistapi\Form\ChecklistapiChecklistForm',
        'checklist_id' => $id,
        'op' => 'any',
      ), $requirements = array('_checklistapi_access' => 'TRUE'));

      // Clear saved progress.
      $routes["checklistapi.checklists.{$id}.clear"] = new Route("{$definition['#path']}/clear", array(
        '_title' => 'Clear',
        '_form' => '\Drupal\checklistapi\Form\ChecklistapiChecklistClearForm',
        'checklist_id' => $id,
        'op' => 'edit',
      ), $requirements);

      // Toggle compact mode.
      $routes["checklistapi.checklists.{$id}.compact"] = new Route("{$definition['#path']}/compact/{mode}", array(
        '_title' => 'Compact mode',
        '_controller' => '\Drupal\checklistapi\Controller\ChecklistapiController::setCompactMode',
        'checklist_id' => $id,
        'op' => 'any',
        'mode' => 'off',
      ), $requirements);

      return $routes;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {}
}
