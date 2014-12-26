<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Form\ChecklistapiChecklistForm.
 */

namespace Drupal\checklistapi\Form;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Url;

/**
 * Provides a checklist form.
 */
class ChecklistapiChecklistForm implements FormInterface {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'checklistapi_checklist_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $checklist_id = NULL) {
    $form['#checklist'] = $checklist = checklistapi_checklist_load($checklist_id);
    $user_has_edit_access = $checklist->userHasAccess('edit');
    $renderer = \Drupal::service('renderer');

    // Progress bar.
    $progress_bar = array(
      '#theme' => 'checklistapi_progress_bar',
      '#message' => ($checklist->hasSavedProgress()) ? t('Last updated @date by !user', array(
        '@date' => $checklist->getLastUpdatedDate(),
        '!user' => $checklist->getLastUpdatedUser(),
      )) : '',
      '#number_complete' => $checklist->getNumberCompleted(),
      '#number_of_items' => $checklist->getNumberOfItems(),
      '#percent_complete' => (int) round($checklist->getPercentComplete()),
    );
    $form['progress_bar'] = array(
      '#type' => 'markup',
      '#markup' => $renderer->render($progress_bar),
    );

    // Compact mode.
    if (checklistapi_compact_mode()) {
      $form['#attributes']['class'] = array('compact-mode');
    }
    $compact_link = array('#theme' => 'checklistapi_compact_link');
    $form['compact_mode_link'] = array(
      '#markup' => $renderer->render($compact_link),
    );

    // General properties.
    $form['checklistapi'] = array(
      '#attached' => array(
        'library' => array('checklistapi/checklistapi'),
      ),
      '#tree' => TRUE,
      '#type' => 'vertical_tabs',
    );

    // Loop through groups.
    $num_autochecked_items = 0;
    $groups = $checklist->items;
    foreach (Element::children($groups) as $group_key) {
      $group = &$groups[$group_key];
      $form[$group_key] = array(
        '#title' => Xss::filter($group['#title']),
        '#type' => 'details',
        '#group' => 'checklistapi',
      );
      if (!empty($group['#description'])) {
        $form[$group_key]['#description'] = Xss::filterAdmin($group['#description']);
      }

      // Loop through items.
      foreach (Element::children($group) as $item_key) {
        $item = &$group[$item_key];
        $saved_item = !empty($checklist->savedProgress[$item_key]) ? $checklist->savedProgress[$item_key] : 0;
        // Build title.
        $title = Xss::filter($item['#title']);
        if ($saved_item) {
          // Append completion details.
          $user = array(
            '#theme' => 'username',
            '#account' => user_load($saved_item['#uid']),
          );
          $title .= t(
            '<span class="completion-details"> - Completed @time by !user</a>',
            array(
              '@time' => format_date($saved_item['#completed'], 'short'),
              '!user' => \Drupal::service('renderer')->render($user),
            )
          );
        }
        // Set default value.
        $default_value = FALSE;
        if ($saved_item) {
          $default_value = TRUE;
        }
        elseif (!empty($item['#default_value'])) {
          if ($default_value = $item['#default_value']) {
            $num_autochecked_items++;
          }
        }
        // Get description.
        $description = (isset($item['#description'])) ? '<p>' . Xss::filterAdmin($item['#description']) . '</p>' : '';
        // Append links.
        $links = array();
        foreach (Element::children($item) as $link_key) {
          $link = &$item[$link_key];
          $links[] = \Drupal::l($link['#text'], Url::fromUri($link['#url']));
        }
        if (count($links)) {
          $description .= '<div class="links">' . implode(' | ', $links) . '</div>';
        }
        // Compile the list item.
        $form[$group_key][$item_key] = array(
          '#attributes' => array('class' => array('checklistapi-item')),
          '#default_value' => $default_value,
          '#description' => Xss::filterAdmin($description),
          '#disabled' => !($user_has_edit_access),
          '#title' => Xss::filterAdmin($title),
          '#type' => 'checkbox',
          '#group' => $group_key,
          '#parents' => array('checklistapi', $group_key, $item_key),
        );
      }
    }

    // Actions.
    $form['actions'] = array(
      '#access' => $user_has_edit_access,
      '#type' => 'actions',
      '#weight' => 100,
      'save' => array(
        '#button_type' => 'primary',
        '#type' => 'submit',
        '#value' => t('Save'),
      ),
      'clear' => array(
        '#access' => $checklist->hasSavedProgress(),
        '#button_type' => 'danger',
        '#attributes' => array('class' => array('clear-saved-progress')),
        '#submit' => array(array($this, 'clear')),
        '#type' => 'submit',
        '#value' => t('Clear saved progress'),
      ),
    );

    // Alert the user of autochecked items. Only set the message on GET requests
    // to prevent it from reappearing after saving the form. (Testing the
    // request method may not be the "correct" way to accomplish this.)
    if ($num_autochecked_items && $_SERVER['REQUEST_METHOD'] == 'GET') {
      $args = array(
        '%checklist' => $checklist->title,
        '@num' => $num_autochecked_items,
      );
      $message = \Drupal::translation()->formatPlural(
        $num_autochecked_items,
        t('%checklist found 1 unchecked item that was already completed and checked it for you. Save the form to record the change.', $args),
        t('%checklist found @num unchecked items that were already completed and checked them for you. Save the form to record the changes.', $args)
      );
      drupal_set_message($message, 'status');
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue('checklistapi');
    $form['#checklist']->saveProgress($values);
  }

  /**
   * Form submission handler for the 'clear' action.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param array $form_state
   *   A reference to a keyed array containing the current state of the form.
   */
  public function clear(array $form, array &$form_state) {
    $form_state['redirect_route']['route_name'] = $form['#checklist']->getRouteName() . '.clear';
  }

}
