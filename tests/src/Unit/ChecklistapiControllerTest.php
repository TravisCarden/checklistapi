<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Tests\ChecklistapiControllerTest.
 */

namespace Drupal\Tests\checklistapi\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\checklistapi\Controller\ChecklistapiController;

/**
 * @coversDefaultClass \Drupal\checklistapi\ChecklistapiController
 * @group checklistapi
 */
class ChecklistapiControllerTest extends UnitTestCase {

  /**
   * Tests that setCompactMode() rejects an invalid mode.
   *
   * @covers \Drupal\checklistapi\Controller\ChecklistapiController::setCompactMode()
   * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
   */
  public function testSetCompactModeToInvalidMode() {
    $controller = new ChecklistapiController();
    $controller->setCompactMode('invalid mode');
  }

}
