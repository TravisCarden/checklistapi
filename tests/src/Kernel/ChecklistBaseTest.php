<?php

namespace Drupal\Tests\checklistapi\Kernel;

use Drupal\checklistapi\ChecklistInterface;
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

  /**
   * A mocked checklist storage backend.
   *
   * @var \Drupal\checklistapi\Storage\StorageInterface|\Prophecy\Prophecy\ObjectProphecy
   */
  private $storage;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->storage = $this->prophesize(StorageInterface::class);
  }

  /**
   * Instantiates the example checklist for testing.
   */
  private function getChecklist() : ChecklistInterface {
    $plugin_definition = [
      'title' => t('Checklist API example'),
      'storage' => 'checklistapi_storage.config',
    ];
    $this->storage->setChecklistId('example')->shouldBeCalled();
    return new Example([], 'example', $plugin_definition, $this->storage->reveal());
  }

  /**
   * @covers ::getTitle
   */
  public function testGetTitle() {
    $this->assertEquals('Checklist API example', $this->getChecklist()->getTitle());
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
    $this->storage->getSavedProgress()->willReturn([]);
    $this->assertSame([0, 18], $this->getChecklist()->getProgress());
  }

  /**
   * @covers ::isComplete
   */
  public function testIsComplete() {
    $this->storage->getSavedProgress()->shouldBeCalled()->willReturn([
      'i_suck' => [
        'node_system' => TRUE,
      ],
    ]);
    $checklist = $this->getChecklist();
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

    $this->storage->getSavedProgress()->willReturn([])->shouldBeCalled();
    $this->storage->setSavedProgress([
      'i_kick_butt' => [
        'content_types_views' => [
          'uid' => 35,
          'time' => 2600,
          'data' => $data,
        ],
      ],
    ])->shouldBeCalled();

    $checklist = $this->getChecklist();
    $checklist->currentUser = $account->reveal();
    $checklist->time = $time->reveal();
    $checklist->setComplete('i_kick_butt', 'content_types_views', $data);
  }

  /**
   * @covers ::setIncomplete
   */
  public function testSetIncomplete() {
    $this->storage->getSavedProgress()->shouldBeCalled()->willReturn([
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
    $this->storage->setSavedProgress([
      'i_kick_butt' => [],
    ])->shouldBeCalled();

    $this->getChecklist()->setIncomplete('i_kick_butt', 'content_types_views');
  }

}
