<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Tests\ChecklistapiControllerTest.
 */

namespace Drupal\checklistapi\Tests;

use Drupal\Tests\UnitTestCase;
use Drupal\checklistapi\Controller\ChecklistapiController;

/**
 * Tests the ChecklistapiController class.
 */
class ChecklistapiControllerTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'ChecklistapiController class',
      'description' => 'Test the ChecklistapiController class.',
      'group' => 'Checklist API',
    );
  }

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
