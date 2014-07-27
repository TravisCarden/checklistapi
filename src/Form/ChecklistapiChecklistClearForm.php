<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Form\ChecklistapiChecklistClearForm.
 */

namespace Drupal\checklistapi\Form;

use Drupal\Core\Form\ConfirmFormBase;

/**
 * Provides a form to clear saved progress for a given checklist.
 */
class ChecklistapiChecklistClearForm extends ConfirmFormBase {

  /**
   * The checklist object.
   *
   * @var Drupal\checklistapi\ChecklistapiChecklist
   */
  public $checklist;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'checklistapi_checklist_clear_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to clear saved progress?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->checklist->getUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return t('All progress details for %checklist will be erased. This action cannot be undone.', array(
      '%checklist' => $this->checklist->title,
    ));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Clear');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelText() {
    return t('Cancel');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state, $checklist_id = NULL) {
    $this->checklist = checklistapi_checklist_load($checklist_id);
    $form['#checklist'] = $this->checklist;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    // Clear saved progress.
    $form['#checklist']->clearSavedProgress();

    // Redirect back to checklist.
    $form_state['redirect'] = $form['#checklist']->path;
  }
}
