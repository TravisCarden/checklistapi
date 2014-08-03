<?php

/**
 * @file
 * Drush commands for the Checklist API module.
 */

/**
 * Implements hook_drush_command().
 */
function checklistapi_drush_command() {
  $items = array();

  // Table formatting isn't available until Drush 6.
  if (function_exists('drush_format_table')) {
    $items['checklistapi-list'] = array(
      'aliases' => array('capi-list'),
      'description' => 'List of available checklists.',
    );
  }

  return $items;
}

/**
 * Drush callback for checklist listing.
 */
function drush_checklistapi_list() {
  $definitions = checklistapi_get_checklist_info();

  if (empty($definitions)) {
    return drush_print(dt('No checklists available.'));
  }

  // Build table rows.
  $rows = array();
  // The first row is the table header.
  $rows[] = array(
    dt('Checklist'),
    dt('Progress'),
    dt('Last updated'),
    dt('Last updated by'),
  );
  foreach ($definitions as $id => $definition) {
    $checklist = checklistapi_checklist_load($id);
    $row = array();
    $row[] = $checklist->title;
    $row[] = dt('@completed of @total (@percent%)', array(
      '@completed' => $checklist->getNumberCompleted(),
      '@total' => $checklist->getNumberOfItems(),
      '@percent' => round($checklist->getPercentComplete()),
    ));
    $row[] = $checklist->getLastUpdatedDate();
    $row[] = strip_tags($checklist->getLastUpdatedUser());
    $rows[] = $row;
  }

  return drush_format_table($rows, TRUE);
}
