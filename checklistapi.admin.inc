<?php

/**
 * @file
 * Admin page callback file for the Checklist API module.
 */

/**
 * Page callback: Form constructor for the report form.
 *
 * @see checklistapi_menu()
 *
 * @ingroup forms
 */
function checklistapi_report_form() {
  $header = array(
    t('Checklist'),
    t('Progress'),
    t('Last updated'),
    t('Last updated by'),
    t('Operations'),
  );
  $definitions = checklistapi_get_checklist_info();
  if (count($definitions)) {
    $rows = array();
    foreach ($definitions as $id => $definition) {
      $checklist = checklistapi_checklist_load($id);
      $row = array();
      $row[] = array(
        'data' => ($checklist->userHasAccess()) ? l($checklist->title, $checklist->path) : drupal_placeholder($checklist->title),
        'title' => (!empty($checklist->description)) ? $checklist->description : '',
      );
      $row[] = t('@completed of @total (@percent%) complete', array(
        '@completed' => $completed = (!empty($checklist->savedProgress['#completed_items'])) ? $checklist->savedProgress['#completed_items'] : 0,
        '@total' => $checklist->totalItems,
        '@percent' => round($completed / $checklist->totalItems * 100),
      ));
      $row[] = (!empty($checklist->savedProgress['#changed'])) ? format_date($checklist->savedProgress['#changed']) : t('n/a');
      $row[] = (isset($checklist->savedProgress['#changed_by'])) ? theme('username', array('account' => user_load($checklist->savedProgress['#changed_by']))) : t('n/a');
      $row[] = ($checklist->userHasAccess('edit') && $checklist->hasSavedProgress()) ? l(t('clear saved progress'), $checklist->path . '/clear', array(
        'query' => array('destination' => 'admin/reports/checklistapi'),
      )) : '';
      $rows[] = $row;
    }
  }
  else {
    $rows[][] = array('data' => t('No checklists available.'), 'colspan' => 5);
  }
  return theme('table', array('header' => $header, 'rows' => $rows));
}
