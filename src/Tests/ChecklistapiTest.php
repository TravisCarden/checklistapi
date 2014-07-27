<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Tests\ChecklistapiTest.
 */

namespace Drupal\checklistapi\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Functionally tests Checklist API.
 *
 * @group checklistapi
 *
 * @todo Add tests for vertical tabs progress indicators.
 * @todo Add tests for saving and retrieving checklist progress.
 * @todo Add tests for clearing saved progress.
 */
class ChecklistapiTest extends WebTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = array(
    'checklistapi',
    'checklistapiexample',
    'help',
    'block',
  );

  /**
   * @var \Drupal\user\Entity\User
   *   A user object with permission to edit any checklist.
   */
  protected $privilegedUser;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    // Create a privileged user.
    $permissions = array('edit any checklistapi checklist');
    $this->privilegedUser = $this->drupalCreateUser($permissions);
    $this->drupalLogin($this->privilegedUser);

    // Place help block.
    $this->drupalPlaceBlock('system_help_block', array(
      'label' => '',
      'region' => 'help',
    ));
  }

  /**
   * Tests checklist access.
   */
  public function testChecklistAccess() {
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(200, 'Granted access to user with "edit any checklistapi checklist" permission.');

    $permissions = array('edit example_checklist checklistapi checklist');
    $semi_privileged_user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($semi_privileged_user);
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(200, 'Granted access to user with checklist-specific permission.');

    $this->drupalLogout();
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertResponse(403, 'Denied access to non-privileged user.');
  }

  /**
   * Tests checklist composition.
   */
  public function testChecklistComposition() {
    $permissions = array('edit example_checklist checklistapi checklist');
    $this->assertTrue($this->checkPermissions($permissions), 'Created per-checklist permission.');

    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertRaw('This checklist based on', 'Created per-checklist help block.');
  }

}
