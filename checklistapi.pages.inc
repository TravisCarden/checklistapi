<?php

/**
 * @file
 * Page callbacks for the Checklist API module.
 */

/**
 * Page callback: Form constructor for "Clear saved progress" confirmation form.
 *
 * @param string $checklist_id
 *   The unique ID of the checklist to be displayed, which is its array key from
 *   hook_checklistapi_checklist_info().
 *
 * @see checklistapi_menu()
 *
 * @ingroup forms
 */
function checklistapi_checklist_clear_confirm($form, &$form_state, $checklist_id) {
  $checklists = checklistapi_get_checklist_info();
  $checklist = $checklists[$checklist_id];
  $form['#checklist'] = $checklist;
  return confirm_form(
    $form,
    t('Are you sure you want to clear %title progress?', array(
      '%title' => $checklist['#title'],
    )),
    $checklist['#path'],
    t('All progress details will be erased. This action cannot be undone.', array(
      '%title' => $checklist['#title'],
    )),
    t('Clear'),
    t('Cancel')
  );
}

/**
 * Form submission handler for checklistapi_checklist_clear_confirm().
 */
function checklistapi_checklist_clear_confirm_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    variable_del('checklistapi_checklist_' . $form['#checklist']['#id']);
    drupal_set_message(t('%title saved progress has been cleared.', array(
      '%title' => $form['#checklist']['#title'],
    )));
  }

  $form_state['redirect'] = $form['#checklist']['#path'];
}

/**
 * Page callback: Form constructor for the checklist form.
 *
 * @param string $checklist_id
 *   The unique ID of the checklist to be displayed, which is its array key from
 *   hook_checklistapi_checklist_info().
 *
 * @see checklistapi_checklist_form_submit()
 * @see checklistapi_menu()
 *
 * @ingroup forms
 */
function checklistapi_checklist_form($form, &$form_state, $checklist_id) {
  $checklists = checklistapi_get_checklist_info();
  $checklist = $checklists[$checklist_id];
  $saved_values = variable_get('checklistapi_checklist_' . $checklist_id, array());
  $save_button = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#submit' => array('checklistapi_checklist_form_submit'),
  );
  $clear_button = array(
    '#type' => 'link',
    '#title' => t('Clear saved progress'),
    '#href' => $checklist['#path'] . '/clear',
    '#access' => variable_get('checklistapi_checklist_' . $checklist_id, FALSE),
  );

  $form['#checklist'] = $checklist;
  $form['submit_top'] = array(
    '#type' => 'container',
    '#weight' => -100,
    '#attributes' => array('class' => array('js-hide')),
    'save' => $save_button,
    'clear' => $clear_button,
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
  $group_keys = element_children($checklist);
  foreach ($group_keys as $group_key) {
    $group = &$checklist[$group_key];
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
      // Get saved state.
      $saved_item = !empty($saved_values[$group_key][$item_key]) ? $saved_values[$group_key][$item_key] : 0;
      // Set default value.
      $default_value = FALSE;
      if ($saved_item) {
        $default_value = TRUE;
      }
      elseif (!empty($item['default_value'])) {
        $default_value = $item['default_value'];
      }
      // Get description.
      $description = (isset($item['description'])) ? '<p>' . $item['description'] . '</p>' : '';
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
        $user = user_load($saved_item['uid']);
        array_unshift($links, t(
          'Completed @time by !user',
          array(
            '@time' => format_date($saved_item['completed'], 'short'),
            '!user' => theme('username', array('account' => $user)),
          )
        ));
      }
      $description .= '<div class="links">' . implode(' | ', $links) . '</div>';
      // Build the list item.
      $form['checklistapi'][$group_key][$item_key] = array(
        '#type' => 'checkbox',
        '#title' => filter_xss($item['#title']),
        '#description' => filter_xss_admin($description),
        '#default_value' => $default_value,
      );
    }
  }
  $form['submit_bottom'] = array(
    '#type' => 'container',
    '#weight' => 100,
    'save' => $save_button,
    'clear' => $clear_button,
  );

  return $form;
}

/**
 * Form submission handler for checklistapi_checklist_form().
 */
function checklistapi_checklist_form_submit($form, &$form_state) {
  global $user;
  $saved_values = variable_get('checklistapi_checklist_' . $form['#checklist']['#id'], array());
  $new_values = array_slice($form_state['values']['checklistapi'], 0, -1);
  $time = time();
  $changed_items = 0;
  // Loop through groups.
  foreach ($new_values as $group_key => $group) {
    // Loop through items.
    foreach ($group as $item_key => $item) {
      $old_item = &$saved_values[$group_key][$item_key];
      $new_item = &$new_values[$group_key][$item_key];
      // Item is checked.
      if ($item == 1) {
        // Item was previously checked off. Use saved value.
        if ($old_item) {
          $new_item = $old_item;
        }
        // Item is newly checked off. Set new value.
        else {
          $new_item = array(
            'completed' => $time,
            'uid' => $user->uid,
          );
          // Increment changed items counter.
          $changed_items++;
        }
      }
      // Item is unchecked.
      else {
        // Item was previously checked off. Increment changed items counter.
        if ($old_item) {
          $changed_items++;
        }
      }
    }
  }
  variable_set('checklistapi_checklist_' . $form['#checklist']['#id'], $new_values);
  drupal_set_message(format_plural(
    $changed_items,
    'Checklist %title has been updated. 1 item changed.',
    'Checklist %title has been updated. @count items changed.',
    array('%title' => $form['#checklist']['#title'])
  ));
}
