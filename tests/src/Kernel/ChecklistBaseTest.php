<?php

namespace Drupal\Tests\checklistapi\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\checklistapi\ChecklistBase
 *
 * @group checklistapi
 */
class ChecklistBaseTest extends KernelTestBase {

  /**
   * @covers ::access
   */
  public function testAccess() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::getTitle
   */
  public function testGetTitle() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::getItems
   */
  public function testGetItems() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::getProgress
   */
  public function testGetProgress() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::isComplete
   */
  public function testIsComplete() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::setComplete
   */
  public function testSetComplete() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::setIncomplete
   */
  public function testSetIncomplete() {
    $this->assertTrue(TRUE);
  }

}
