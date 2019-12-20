<?php

namespace Drupal\checklistapi\Storage;

/**
 * Provides a base storage implementation.
 */
abstract class StorageBase implements StorageInterface {

  /**
   * The checklist ID.
   *
   * @var string
   */
  private $checklistId;

  /**
   * Sets the checklist ID.
   *
   * @param string $id
   *   The checklist ID.
   *
   * @return self
   *   The storage object.
   */
  public function setChecklistId(string $id): self {
    $this->checklistId = $id;
    return $this;
  }

  /**
   * Gets the checklist ID.
   *
   * @return string
   *   Returns the checklist ID.
   */
  protected function getChecklistId(): string {
    if (empty($this->checklistId)) {
      throw new \LogicException('You must set the checklist ID before accessing saved progress.');
    }
    return $this->checklistId;
  }

}
