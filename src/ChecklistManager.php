<?php

namespace Drupal\checklistapi;

use Drupal\checklistapi\Annotation\Checklist;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Defines a plugin manager for checklist plugins.
 */
final class ChecklistManager extends DefaultPluginManager {

  /**
   * ChecklistManager constructor.
   *
   * @param \Traversable $namespaces
   *   The available extension namespaces.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    $this->alterInfo('checklistapi_checklist_info');
    $this->setCacheBackend($cache_backend, 'checklistapi_checklist_info');

    parent::__construct('Plugin/Checklist', $namespaces, $module_handler, ChecklistInterface::class, Checklist::class);
  }

}
