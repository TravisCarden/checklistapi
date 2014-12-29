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
   * A user object with permission to edit any checklist.
   *
   * @var \Drupal\user\Entity\User
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
    $this->drupalPlaceBlock('help_block', array('region' => 'help'));
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
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertRaw('This checklist based on', 'Created per-checklist help block.');
  }

  /**
   * Tests compact mode.
   */
  public function testCompactMode() {
    $this->drupalGet('admin/config/development/checklistapi-example');
    $this->assertTrue($this->isCompactModeEffective(), 'Compact mode disabled by default.');
    $this->assertLink('Hide item descriptions', 0, 'Enable compact mode link present.');

    $this->clickLink('Hide item descriptions');
    $this->assertFalse($this->isCompactModeEffective(), 'Compact mode in effect.');
    $this->assertLink('Show item descriptions', 0, 'Disable compact mode link present.');

    $this->clickLink('Show item descriptions');
    $this->assertTrue($this->isCompactModeEffective(), 'Compact mode back in effect.');
    $this->assertLink('Hide item descriptions', 0, 'Enable compact mode link present again.');
  }

  /**
   * Determines whether compact mode has taken effect or not.
   *
   * @return bool
   *   Returns TRUE if compact mode is effective, or FALSE if not.
   */
  public function isCompactModeEffective() {
    return !$this->cssSelect('#checklistapi-checklist-form.compact-mode');
  }

  /**
   * Tests permissions.
   */
  public function testPermissions() {
    $this->assertTrue($this->checkPermissions(array(
      'view checklistapi checklists report',
      'view any checklistapi checklist',
      'edit any checklistapi checklist',
    )), 'Created universal permissions.');
    $this->assertTrue($this->checkPermissions(array(
      'view example_checklist checklistapi checklist',
      'edit example_checklist checklistapi checklist',
    )), 'Created per-checklist permissions.');
  }

}
