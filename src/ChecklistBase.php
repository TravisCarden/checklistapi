<?php

namespace Drupal\checklistapi;

use Drupal\checklistapi\Storage\StorageInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base class for checklist plugins.
 */
abstract class ChecklistBase extends PluginBase implements ChecklistInterface, ContainerFactoryPluginInterface {

  /**
   * The progress storage backend.
   *
   * @var \Drupal\checklistapi\Storage\StorageInterface
   */
  private $storage;

  /**
   * ChecklistBase constructor.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\checklistapi\Storage\StorageInterface $storage
   *   The progress storage backend.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StorageInterface $storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $storage->setChecklistId($plugin_id);
    $this->storage = $storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get($plugin_definition['storage'])
    );
  }

  /**
   * Defines the items in the checklist.
   *
   * @return array
   *   The items in the checklist, divided by group.
   *
   * @see callback_checklistapi_checklist_items()
   */
  abstract protected function items() : array;

  /**
   * {@inheritdoc}
   */
  public function getTitle() : TranslatableMarkup {
    $plugin_definition = $this->getPluginDefinition();
    return $plugin_definition['title'];
  }

  /**
   * {@inheritdoc}
   */
  public function getItems(?string $group = NULL) : array {
    $items = $this->items();

    if (isset($group)) {
      if (array_key_exists($group, $items)) {
        return $items[$group];
      }
      else {
        throw new \InvalidArgumentException("Unknown group: '$group'");
      }
    }
    else {
      return $items;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getProgress() : array {
    $reductor = function (int $total, array $group) {
      return $total + count($group);
    };

    return [
      array_reduce($this->storage->getSavedProgress(), $reductor, 0),
      array_reduce($this->getItems(), $reductor, 0),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isComplete(string $group, string $item) : bool {
    $progress = $this->storage->getSavedProgress();
    return isset($progress[$group][$item]);
  }

  /**
   * {@inheritdoc}
   */
  public function setComplete(string $group, string $item, AccountInterface $account = NULL, array $data = []) : void {
    $account = $account ?: \Drupal::currentUser();
    $data += [
      'uid' => $account->id(),
    ];

    $progress = $this->storage->getSavedProgress();
    $progress[$group][$item] = $data;
    $this->storage->setSavedProgress($progress);
  }

  /**
   * {@inheritdoc}
   */
  public function setIncomplete(string $group, string $item) : void {
    $progress = $this->storage->getSavedProgress();
    unset($progress[$group][$item]);
    $this->storage->setSavedProgress($progress);
  }

}
