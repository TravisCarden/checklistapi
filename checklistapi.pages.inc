<?php

/**
 * @file
 * Page callbacks for the Checklist API module.
 */

/**
 * Form constructor for the checklist form.
 *
 * @param string $checklist_id
 *   The unique ID of the checklist to be displayed, which is its array key from
 *   hook_checklistapi_checklist_info().
 *
 * @see checklistapi_form_submit()
 * @ingroup forms
 */
function checklistapi_form($form, &$form_state, $checklist_id) {
  $checklists = checklistapi_get_checklist_info();
  $checklist = $checklists[$checklist_id];
  $saved_values = variable_get('checklistapi_checklist_' . $checklist_id, array());

  $form['save_above'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#attributes' => array('class' => array('js-hide')),
    '#weight' => '-100',
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
  $form['save'] = array(
    '#type' => 'submit',
    '#value' => t('Save'),
    '#weight' => 100,
  );

  // Stash the checklist title for use in the submission handler.
  $form['#checklist_title'] = check_plain($checklist['#title']);

  return $form;
}

/**
 * Form submission handler for checklistapi_form().
 */
function checklistapi_form_submit($form, &$form_state) {
  global $user;
  $checklist_id = $form_state['build_info']['args'][0];
  $saved_values = variable_get('checklistapi_checklist_' . $checklist_id, array());
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
  variable_set('checklistapi_checklist_' . $checklist_id, $new_values);
  drupal_set_message(format_plural(
    $changed_items,
    'Checklist %checklist has been updated. 1 item changed.',
    'Checklist %checklist has been updated. @count items changed.',
    array('%checklist' => $form['#checklist_title'])
  ));
}
