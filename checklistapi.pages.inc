<?php

/**
 * @file
 * Page callbacks for the Checklist API module.
 */

/**
 * Page callback: Form constructor for "Clear saved progress" confirmation form.
 *
 * @param string $id
 *   The checklist ID.
 *
 * @see checklistapi_menu()
 *
 * @ingroup forms
 */
function checklistapi_checklist_clear_confirm($form, &$form_state, $id) {
  $checklist = checklistapi_checklist_load($id);
  $form['#checklist'] = $checklist;
  return confirm_form(
    $form,
    t('Are you sure you want to clear %title progress?', array(
      '%title' => $checklist->title,
    )),
    $checklist->path,
    t('All progress details will be erased. This action cannot be undone.'),
    t('Clear'),
    t('Cancel')
  );
}

/**
 * Form submission handler for checklistapi_checklist_clear_confirm().
 */
function checklistapi_checklist_clear_confirm_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    $form['#checklist']->clearSavedProgress();
  }
  $form_state['redirect'] = $form['#checklist']->path;
}

/**
 * Page callback: Form constructor for the checklist form.
 *
 * @param string $id
 *   The checklist ID.
 *
 * @see checklistapi_checklist_form_submit()
 * @see checklistapi_menu()
 *
 * @ingroup forms
 */
function checklistapi_checklist_form($form, &$form_state, $id) {
  $form['#checklist'] = $checklist = checklistapi_checklist_load($id);
  $actions = array(
    '#type' => 'actions',
    '#access' => $user_has_edit_access = $checklist->userHasAccess('edit'),
    'save' => array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#submit' => array('checklistapi_checklist_form_submit'),
    ),
    'clear' => array(
      '#type' => 'link',
      '#title' => t('Clear saved progress'),
      '#href' => $checklist->path . '/clear',
      '#access' => $checklist->hasSavedProgress(),
    ),
  );
  $form['actions_top'] = $actions + array(
    '#weight' => -100,
    '#attributes' => array('class' => array('js-hide')),
  );
  $form['checklistapi'] = array(
    '#tree' => TRUE,
    '#type' => 'vertical_tabs',
    '#attached' => array(
      'css' => array(drupal_get_path('module', 'checklistapi') . '/checklistapi.css'),
      'js' => array(drupal_get_path('module', 'checklistapi') . '/checklistapi.js'),
    ),
  );
  // Loop through groups.
  $groups = $checklist->items;
  foreach (element_children($groups) as $group_key) {
    $group = &$groups[$group_key];
    $form['checklistapi'][$group_key] = array(
      '#type' => 'fieldset',
      '#title' => filter_xss($group['#title']),
    );
    if (!empty($group['#description'])) {
      $form['checklistapi'][$group_key]['#description'] = filter_xss_admin($group['#description']);
    }
    // Loop through items.
    $item_keys = element_children($group);
    foreach ($item_keys as $item_key) {
      $item = &$group[$item_key];
      // Set default value.
      $saved_item = !empty($checklist->savedProgress[$item_key]) ? $checklist->savedProgress[$item_key] : 0;
      $default_value = FALSE;
      if ($saved_item) {
        $default_value = TRUE;
      }
      elseif (!empty($item['#default_value'])) {
        $default_value = $item['#default_value'];
      }
      // Get description.
      $description = (isset($item['#description'])) ? '<p>' . $item['#description'] . '</p>' : '';
      // Build links.
      $links = array();
      $link_keys = element_children($item);
      foreach ($link_keys as $link_key) {
        $link = &$item[$link_key];
        $context = (!empty($link['#context'])) ? $link['#context'] : CHECKLISTAPI_LINK_CONTEXT_ANY;
        $show_link = FALSE;
        if ($context == CHECKLISTAPI_LINK_CONTEXT_ANY) {
          $show_link = TRUE;
        }
        elseif ($saved_item && $context == CHECKLISTAPI_LINK_CONTEXT_ITEM_CHECKED) {
          $show_link = TRUE;
        }
        elseif (!$saved_item && $context == CHECKLISTAPI_LINK_CONTEXT_ITEM_UNCHECKED) {
          $show_link = TRUE;
        }
        if ($show_link) {
          $options = (!empty($link['#options']) && is_array($link['#options'])) ? $link['#options'] : array();
          $links[] = l($link['#text'], $link['#path'], $options);
        }
      }
      if ($saved_item) {
        // Prepend completion details.
        $user = user_load($saved_item['#uid']);
        array_unshift($links, t(
          'Completed @time by !user',
          array(
            '@time' => format_date($saved_item['#completed'], 'short'),
            '!user' => theme('username', array('account' => $user)),
          )
        ));
      }
      $description .= '<div class="links">' . implode(' | ', $links) . '</div>';
      // Compile the list item.
      $form['checklistapi'][$group_key][$item_key] = array(
        '#type' => 'checkbox',
        '#title' => filter_xss($item['#title']),
        '#description' => filter_xss_admin($description),
        '#default_value' => $default_value,
        '#disabled' => !$user_has_edit_access,
      );
    }
  }
  $form['actions_bottom'] = $actions + array('#weight' => 100);
  return $form;
}

/**
 * Form submission handler for checklistapi_checklist_form().
 */
function checklistapi_checklist_form_submit($form, &$form_state) {
  $form['#checklist']->saveProgress($form_state['values']['checklistapi']);
}
