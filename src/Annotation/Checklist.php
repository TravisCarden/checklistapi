<?php

namespace Drupal\checklistapi\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
final class Checklist extends Plugin {

  /**
   * The checklist ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the checklist.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public $title;

  /**
   * The ID of the storage backend service to use.
   *
   * @var string
   */
  public $storage = 'checklistapi_storage.config';

  /**
   * The path at which the checklist should be visible.
   *
   * @var string
   */
  public $path = NULL;

  /**
   * The menu name in which a link to the checklist should be visible.
   *
   * @var string
   */
  public $menu_name = NULL;

  /**
   * The description of the checklist's menu link, if any.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public $description = NULL;

  /**
   * Text to be displayed in the "Help" block on the checklist's page.
   *
   * @var \Drupal\Core\StringTranslation\TranslatableMarkup
   */
  public $help = NULL;

}
