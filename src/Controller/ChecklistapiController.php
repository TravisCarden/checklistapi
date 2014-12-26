<?php

/**
 * @file
 * Contains \Drupal\checklistapi\Controller\ChecklistapiController.
 */

namespace Drupal\checklistapi\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Route;
use Drupal\Core\Url;

/**
 * Controller for Checklist API.
 */
class ChecklistapiController extends ControllerBase {

  /**
   * Returns the Checklists report.
   *
   * @return array
   *   Returns a render array.
   */
  public function report() {
    // Define table header.
    $header = array(
      array('data' => t('Checklist')),
      array(
        'data' => t('Progress'),
        'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
      ),
      array(
        'data' => t('Last updated'),
        'class' => array(RESPONSIVE_PRIORITY_MEDIUM),
      ),
      array(
        'data' => t('Last updated by'),
        'class' => array(RESPONSIVE_PRIORITY_LOW),
      ),
      array('data' => t('Operations')),
    );

    // Build table rows.
    $rows = array();
    $definitions = checklistapi_get_checklist_info();
    foreach ($definitions as $id => $definition) {
      $checklist = checklistapi_checklist_load($id);
      $row = array();
      $row[] = array(
        'data' => ($checklist->userHasAccess()) ? \Drupal::l($checklist->title, $checklist->getUrl()) : drupal_placeholder($checklist->title),
        'title' => (!empty($checklist->description)) ? $checklist->description : '',
      );
      $row[] = t('@completed of @total (@percent%)', array(
        '@completed' => $checklist->getNumberCompleted(),
        '@total' => $checklist->getNumberOfItems(),
        '@percent' => round($checklist->getPercentComplete()),
      ));
      $row[] = $checklist->getLastUpdatedDate();
      $row[] = $checklist->getLastUpdatedUser();
      if ($checklist->userHasAccess('edit') && $checklist->hasSavedProgress()) {
        $row[] = array(
          'data' => array(
            '#type' => 'operations',
            '#links' => array(
              'clear' => array(
                'title' => t('Clear'),
                'href' => "{$checklist->path}/clear",
                'query' => array('destination' => 'admin/reports/checklistapi'),
              ),
            ),
          ),
        );
      }
      else {
        $row[] = '';
      }
      $rows[] = $row;
    }

    // Compile output.
    $output['table'] = array(
      '#theme' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No checklists available.'),
    );

    return $output;
  }

  /**
   * Sets whether the admin menu is in compact mode or not.
   *
   * @param string $mode
   *   The mode to set compact mode to. Accepted values are "on" and "off".
   *
   * @throws NotFoundHttpException
   *   Throws an exception if an invalid mode is supplied.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Return a redirect response object.
   */
  public function setCompactMode($mode) {
    $all_modes = array('on', 'off');
    if (!in_array($mode, $all_modes)) {
      throw new NotFoundHttpException();
    }

    // Persist the setting for the current user.
    user_cookie_save(array('checklistapi_compact_mode' => ($mode == 'on')));

    // Redirect to the checklist.
    // @todo There must be a better way than this.
    $path = explode('/', Url::fromRoute('<current>'));
    array_pop($path);
    array_pop($path);
    $checklist_path = implode('/', $path);
    return $this->redirect(new Route($checklist_path));
  }
}
