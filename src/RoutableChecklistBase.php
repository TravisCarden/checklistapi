<?php

namespace Drupal\checklistapi;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\Routing\Route;

/**
 * Defines a base class for checklist plugins that expose a route and/or link.
 */
abstract class RoutableChecklistBase extends ChecklistBase implements RoutableChecklistInterface {

  /**
   * {@inheritdoc}
   */
  public function access($operation, AccountInterface $account = NULL, $as_object = FALSE) {
    $id = $this->getPluginId();

    $can_view = AccessResult::allowedIfHasPermissions($account, [
      'view any checklistapi checklist',
      "view $id checklistapi checklist",
    ], 'OR');

    $can_edit = AccessResult::allowedIfHasPermissions($account, [
      'edit any checklistapi checklist',
      "edit $id checklistapi checklist",
    ], 'OR');

    switch ($operation) {
      case 'view':
        $access = $can_view;
        break;

      case 'edit':
        $access = $can_edit;
        break;

      default:
        $access = $can_view->orIf($can_edit);
        break;
    }
    return $as_object ? $access : $access->isAllowed();
  }

  /**
   * {@inheritdoc}
   */
  public function getRoute() : ?Route {
    $plugin_definition = $this->getPluginDefinition();
    return new Route($plugin_definition['path']);
  }

  /**
   * {@inheritdoc}
   */
  public function getMenuLink() : ?array {
    $plugin_definition = $this->getPluginDefinition();

    $link = [
      'menu_name' => $plugin_definition['menu_name'],
    ];
    if ($plugin_definition['description'] instanceof TranslatableMarkup) {
      $link['description'] = $plugin_definition['description'];
    }
    return $link;
  }

  /**
   * {@inheritdoc}
   */
  public function getHelp() : ?TranslatableMarkup {
    $plugin_definition = $this->getPluginDefinition();
    return $plugin_definition['help'];
  }

}
