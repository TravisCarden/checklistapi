<?php

/**
 * @file
 * Hooks provided by the Checklist API module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define all checklists provided by the module.
 *
 * Any number of checklists can be defined in an implementation of this hook.
 * Checklist API will register a menu item and create a permission for each one.
 *
 * @return array
 *   An array of checklists. Each checklist is keyed by an arbitrary unique
 *   identifier. The corresponding multidimensional array describing the
 *   checklist may contain the following key-value pairs:
 *   - #title: Required. The title of the checklist.
 *   - #description: A brief description of the checklist for its corresponding
 *     menu item.
 *   - #path: Required. The Drupal path where the checklist will be accessed.
 *   - #menu_name: Set this to a custom menu if you don't want your item to be
 *     placed in Navigation.
 *   - #weight: An integer used to sort the list of checklists before being
 *     output; lower numbers appear before higher numbers.
 *   - Any number of arrays representing groups of items, presented as vertical
 *     tabs. Each group is keyed by an arbitrary unique identifier. The
 *     corresponding multimensional array describing the group may contain the
 *     following key-value pairs:
 *     - #title: Required. The title of the group used as the vertical tab
 *       label.
 *     - #description: A description of the group.
 *     - #weight: An integer used to sort the list of groups before being
 *       output; lower numbers appear before higher numbers.
 *     - Any number of arrays representing checklist items. Each item is keyed
 *       by an arbitrary unique identifier. The corresponding multimensional
 *       array describing the item may contain the following key-value pairs:
 *       - #title: Required. The title of the item.
 *       - #description: A description of the item.
 *       - #default_value: The default checked state of the item--TRUE for
 *         checked or FALSE for unchecked. Defaults to FALSE. This is useful for
 *         automatically checking off tasks that can be programmatically tested
 *         (e.g. a module is installed or a setting set).
 *       - #weight: An integer used to sort the list of items before being
 *         output; lower numbers appear before higher numbers.
 *       - Any number of arrays representing links. Each link is keyed by an
 *         arbitrary unique identifier. The corresponding multimensional array
 *         describing the link may contain the following key-value pairs:
 *         - #text: The link text.
 *         - #path: The link path.
 *         - #context: The context in which the link may appear. May be one of
 *           the following:
 *           - CHECKLISTAPI_LINK_CONTEXT_ANY: Default. The link will always
 *             appear.
 *           - CHECKLISTAPI_LINK_CONTEXT_ITEM_CHECKED: The link will appear if
 *             the item it belongs to has been checked off.
 *           - CHECKLISTAPI_LINK_CONTEXT_ITEM_UNCHECKED: The link will appear if
 *             the item it belongs to has not checked off.
 *         - #options: An associative array of additional options used by the
 *           l() function.
 *
 * For a working example, see checklistapi_example.module.
 *
 * @see checklistapi_example_checklistapi_checklist_info()
 * @see hook_checklistapi_checklist_info_alter()
 */
function hook_checklistapi_checklist_info() {
  $checklists = array();

  $checklists['example'] = array(
    '#title' => t('Example checklist'),
    '#description' => t('An example checklist.'),
    '#path' => 'example-checklist',
    '#help' => t('<p>This is an example checklist.</p>'),
    'example_group' => array(
      '#title' => t('Example group'),
      '#description' => t('<p>Here are some example items.</p>'),
      'example_item' => array(
        '#title' => t('Example item'),
        'example_link' => array(
          'text' => t('Example.com'),
          'path' => 'http://www.example.com/',
        ),
      ),
    ),
  );

  return $checklists;
}

/**
 * Alter checklist definitions after hook_checklistapi_checklist_info() is
 * invoked.
 *
 * This hook is invoked by checklistapi_get_checklist_info(). The checklist
 * definitions are passed in by reference. Each element of the $checklists array
 * is one returned by a module from checklistapi_get_checklist_info().
 * Additional checklists may be added, or existing checklists may be altered or
 * removed.
 *
 * @param array $checklists
 *   A multidimensional array of checklists definitions returned from
 *   hook_checklistapi_checklist_info().
 *
 * For a working example, see checklistapi_example.module.
 *
 * @see checklistapi_get_checklist_info()
 * @see hook_checklistapi_checklist_info()
 */
function hook_checklistapi_checklist_info_alter(&$checklists) {
  // Add an item.
  $checklists['example']['example_group']['sample_item'] = array(
    'title' => t('Sample item'),
  );
  // Remove an item.
  unset($checklists['example']['example_group']['example_item']);
}

/**
 * @} End of "addtogroup hooks".
 */
