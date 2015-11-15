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
    $routes = [];
    foreach (checklistapi_get_checklist_info() as $id => $definition) {
      // Ignore incomplete definitions.
      if (empty($definition['#path']) || empty($definition['#title'])) {
        continue;
      }

      $requirements = ['_checklistapi_access' => 'TRUE'];

      // View/edit checklist.
      $routes["checklistapi.checklists.{$id}"] = new Route($definition['#path'], [
        '_title' => (string) $definition['#title'],
        '_form' => '\Drupal\checklistapi\Form\ChecklistapiChecklistForm',
        'checklist_id' => $id,
        'op' => 'any',
      ], $requirements);

      // Clear saved progress.
      $routes["checklistapi.checklists.{$id}.clear"] = new Route("{$definition['#path']}/clear", [
        '_title' => 'Clear',
        '_form' => '\Drupal\checklistapi\Form\ChecklistapiChecklistClearForm',
        'checklist_id' => $id,
        'op' => 'edit',
      ], $requirements);

      return $routes;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {}

}
