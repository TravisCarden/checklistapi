<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Tests\ChecklistapiModuleTest.
 */

namespace Drupal\checklistapi\Tests;

use Drupal\Core\Render\Element;
use Drupal\Tests\UnitTestCase;

require_once __DIR__ . '/../../../../checklistapi.module';

/**
 * Tests the functions in checklistapi.module.
 */
class ChecklistapiModuleTest extends UnitTestCase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Checklist API module',
      'description' => 'Test checklistapi.module.',
      'group' => 'Checklist API',
    );
  }

  /**
   * Tests checklistapi_sort_array().
   */
  public function testChecklistapiSortArray() {
    $input = array(
      '#title' => 'Checklist API test',
      '#path' => 'admin/config/development/checklistapi-test',
      '#description' => 'A test checklist.',
      '#help' => '<p>This is a test checklist.</p>',
      'group_two' => array(
        '#title' => 'Group two',
      ),
      'group_one' => array(
        '#title' => 'Group one',
        '#description' => '<p>Group one description.</p>',
        '#weight' => -1,
        'item_three' => array(
          '#title' => 'Item three',
          '#weight' => 1,
        ),
        'item_one' => array(
          '#title' => 'Item one',
          '#description' => 'Item one description',
          '#weight' => -1,
          'link_three' => array(
            '#text' => 'Link three',
            '#path' => 'http://example.com/three',
            '#weight' => 3,
          ),
          'link_two' => array(
            '#text' => 'Link two',
            '#path' => 'http://example.com/two',
            '#weight' => 2,
          ),
          'link_one' => array(
            '#text' => 'Link one',
            '#path' => 'http://example.com/one',
            '#weight' => 1,
          ),
        ),
        'item_two' => array(
          '#title' => 'Item two',
        ),
      ),
      'group_four' => array(
        '#title' => 'Group four',
        '#weight' => 1,
      ),
      'group_three' => array(
        '#title' => 'Group three',
        '#weight' => 'invalid',
      ),
    );

    $output = checklistapi_sort_array($input);

    $this->assertEquals(0, $output['group_two']['#weight'], 'Failed to supply a default for omitted element weight.');
    $this->assertEquals(0, $output['group_three']['#weight'], 'Failed to supply a default in place of invalid element weight.');
    $this->assertEquals(-1, $output['group_one']['#weight'], 'Failed to retain a valid element weight.');
    $this->assertEquals(
      array('group_one', 'group_two', 'group_three', 'group_four'),
      Element::children($output),
      'Failed to sort elements by weight.'
    );
    $this->assertEquals(
      array('link_one', 'link_two', 'link_three'),
      Element::children($output['group_one']['item_one']),
      'Failed to recurse through element descendants.'
    );
  }

  /**
   * Tests checklistapi_strtolowercamel().
   */
  public function testChecklistapiStrtolowercamel() {
    $this->assertEquals('abcDefGhi', checklistapi_strtolowercamel('Abc def_ghi'), 'Failed to convert string to lowerCamel case.');
  }

  /**
   * Tests that checklistapi_checklist_access() rejects an invalid mode.
   *
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage No such operation "invalid operation"
   */
  public function testChecklistapiChecklistAccessInvalidMode() {
    checklistapi_checklist_access(NULL, 'invalid operation');
  }

}
