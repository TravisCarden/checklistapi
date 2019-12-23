<?php

namespace Drupal\Tests\checklistapi\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\checklistapi\ChecklistManager
 *
 * @group checklistapi
 */
class ChecklistManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['checklistapi', 'checklistapiexample'];

  /**
   * @covers ::getDefinition
   */
  public function testGetDefinition() {
    $definition = $this->container->get('plugin.manager.checklist')
      ->getDefinition('example_checklist');

    $this->assertSame('example_checklist', $definition['id']);
    $this->assertEquals('Checklist API example', $definition['title']);
    $this->assertSame('checklistapi_storage.state', $definition['storage']);
    $this->assertSame('/admin/config/development/checklistapi-example', $definition['path']);
    $this->assertEquals('An example implementation of the Checklist API.', $definition['description']);
    // This help text is altered by
    // checklistapiexample_checklistapi_checklist_info_alter().
    $this->assertEquals('<p>This checklist based on <a href="http://www.unleashedmind.com/files/drupal-learning-curve.png">sun\'s modification</a> of <a href="http://buytaert.net/drupal-learning-curve">Dries Buytaert\'s Drupal learning curve</a> is an example implementation of the <a href="http://drupal.org/project/checklistapi">Checklist API</a>.</p>', $definition['help']);
  }

}
