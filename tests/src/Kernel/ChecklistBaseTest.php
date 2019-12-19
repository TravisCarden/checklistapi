<?php

namespace Drupal\Tests\checklistapi\Kernel;

use Drupal\checklistapi\Storage\StorageInterface;
use Drupal\checklistapiexample\Plugin\Checklist\Example;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Session\AccountInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * @coversDefaultClass \Drupal\checklistapi\ChecklistBase
 *
 * @group checklistapi
 */
class ChecklistBaseTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'checklistapi',
    'checklistapiexample',
  ];

  private function getChecklist() {
    return Example::create($this->container, [], 'example', [
      'title' => t('Checklist API example'),
      'storage' => 'checklistapi_storage.config',
    ]);
  }

  public function providerAccess() {
    return [];
  }

  /**
   * @covers ::access
   *
   * @dataProvider providerAccess
   */
  public function testAccess() {
    $this->assertTrue(TRUE);
  }

  /**
   * @covers ::getTitle
   */
  public function testGetTitle() {
    $this->assertEqual('Checklist API example', $this->getChecklist()->getTitle());
  }

  /**
   * @covers ::getItems
   */
  public function testGetItems() {
    $checklist = $this->getChecklist();

    $items = $checklist->getItems();
    $this->assertCount(5, Element::children($items['i_suck']));
    $this->assertCount(5, Element::children($items['i_get_by']));
    $this->assertCount(8, Element::children($items['i_kick_butt']));

    $items = $checklist->getItems('i_get_by');
    $this->assertSame([
      'upgrade_patch_monitor',
      'navigation_menus_taxonomy',
      'locale_i18n',
      'customize_front_page',
      'theme_modification',
    ], Element::children($items));
  }

  /**
   * @covers ::getProgress
   */
  public function testGetProgress() {
    $storage = $this->prophesize(StorageInterface::class);
    $storage->setChecklistId('example')->shouldBeCalled();
    $storage->getSavedProgress()->willReturn([]);

    $checklist = new Example([], 'example', [], $storage->reveal());
    $this->assertSame([0, 18], $checklist->getProgress());
  }

  /**
   * @covers ::isComplete
   */
  public function testIsComplete() {
    $storage = $this->prophesize(StorageInterface::class);
    $storage->setChecklistId('example')->shouldBeCalled();
    $storage->getSavedProgress()->willReturn([
      'i_suck' => [
        'node_system' => TRUE,
      ],
    ]);

    $checklist = new Example([], 'example', [], $storage->reveal());
    $this->assertTrue($checklist->isComplete('i_suck', 'node_system'));
    $this->assertFalse($checklist->isComplete('i_suck', 'block_system'));
  }

  /**
   * @covers ::setComplete
   */
  public function testSetComplete() {
    $account = $this->prophesize(AccountInterface::class);
    $account->id()->willReturn(35)->shouldBeCalled();

    $time = $this->prophesize(TimeInterface::class);
    $time->getRequestTime()->willReturn(2600)->shouldBeCalled();

    $data = ['name' => 'Batman'];

    $storage = $this->prophesize(StorageInterface::class);
    $storage->setChecklistId('example')->shouldBeCalled();
    $storage->getSavedProgress()->willReturn([]);
    $storage->setSavedProgress([
      'i_kick_butt' => [
        'content_types_views' => [
          'uid' => 35,
          'time' => 2600,
          'data' => $data,
        ],
      ],
    ])->shouldBeCalled();

    $checklist = new Example([], 'example', [], $storage->reveal());
    $checklist->time = $time->reveal();
    $checklist->setComplete('i_kick_butt', 'content_types_views', $account->reveal(), $data);
  }

  /**
   * @covers ::setIncomplete
   */
  public function testSetIncomplete() {
    $storage = $this->prophesize(StorageInterface::class);
    $storage->setChecklistId('example')->shouldBeCalled();
    $storage->getSavedProgress()->willReturn([
      'i_kick_butt' => [
        'content_types_views' => [
          'uid' => 35,
          'time' => 2600,
          'data' => [
            'name' => 'Batman',
          ],
        ],
      ],
    ]);
    $storage->setSavedProgress([
      'i_kick_butt' => [],
    ])->shouldBeCalled();

    $checklist = new Example([], 'example', [], $storage->reveal());
    $checklist->setIncomplete('i_kick_butt', 'content_types_views');
  }

}
