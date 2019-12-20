<?php

namespace Drupal\checklistapi;

use Drupal\checklistapi\Storage\StorageInterface;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a base class for checklist plugins.
 */
abstract class ChecklistBase extends PluginBase implements ChecklistInterface, ContainerFactoryPluginInterface {

  /**
   * The checklist storage backend.
   *
   * @var \Drupal\checklistapi\Storage\StorageInterface
   */
  private $storage;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  private $time;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  private $user;

  /**
   * Constructs an instance.
   *
   * @param array $configuration
   *   The plugin configuration.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   The plugin definition.
   * @param \Drupal\checklistapi\Storage\StorageInterface $storage
   *   The progress storage backend.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, StorageInterface $storage, TimeInterface $time, AccountInterface $user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $storage->setChecklistId($plugin_id);
    $this->storage = $storage;
    $this->time = $time;
    $this->user = $user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    /** @var \Drupal\checklistapi\Storage\StorageInterface $storage */
    $storage = $container->get($plugin_definition['storage']);
    /** @var \Drupal\Component\Datetime\TimeInterface $time */
    $time = $container->get('datetime.time');
    /** @var \Drupal\Core\Session\AccountInterface $user */
    $user = $container->get('current_user');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $storage,
      $time,
      $user
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
  abstract protected function items(): array;

  /**
   * {@inheritdoc}
   */
  public function getTitle(): TranslatableMarkup {
    $plugin_definition = $this->getPluginDefinition();
    return $plugin_definition['title'];
  }

  /**
   * {@inheritdoc}
   */
  public function getItems(string $group = NULL): array {
    $items = $this->items();

    if (!$group) {
      return $items;
    }

    if (!array_key_exists($group, $items)) {
      throw new \InvalidArgumentException("Unknown group: '$group'");
    }

    return $items[$group];
  }

  /**
   * {@inheritdoc}
   */
  public function getProgress(): array {
    $reducer = static function (int $total, array $group) {
      $group = Element::children($group);
      return $total + count($group);
    };

    return [
      array_reduce($this->storage->getSavedProgress(), $reducer, 0),
      array_reduce($this->getItems(), $reducer, 0),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isComplete(string $group, string $item): bool {
    $progress = $this->storage->getSavedProgress();
    return isset($progress[$group][$item]);
  }

  /**
   * {@inheritdoc}
   */
  public function setComplete(string $group, string $item, array $data = []) {
    $progress = $this->storage->getSavedProgress();

    // Record the user who completed the item and when.
    $progress[$group][$item] = [
      'uid' => $this->user->id(),
      'time' => $this->time->getRequestTime(),
      'data' => $data,
    ];
    $this->storage->setSavedProgress($progress);
  }

  /**
   * {@inheritdoc}
   */
  public function setIncomplete(string $group, string $item) {
    $progress = $this->storage->getSavedProgress();
    unset($progress[$group][$item]);
    $this->storage->setSavedProgress($progress);
  }

}
