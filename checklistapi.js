(function ($) {

  "use strict";
  Drupal.behaviors.checklistapiFieldsetSummaries = {
    attach: function (context) {

      // Vertical tabs summaries.
      $('#checklistapi-checklist-form .vertical-tabs-panes > fieldset', context).drupalSetSummary(function (context) {
        var total = $(':checkbox.checklistapi-item', context).size(), args = {};
        if (total) {
          args['@complete'] = $(':checkbox.checklistapi-item:checked', context).size();
          args['@total'] = total;
          args['@percent'] = Math.round(args['@complete'] / args['@total'] * 100);
          return Drupal.t('@complete of @total (@percent%) complete', args);
        }
      });

      // Compact mode link.
      $('#checklistapi-checklist-form .compact-link a', context).click(function () {
        $(this).closest('#checklistapi-checklist-form').toggleClass('compact-mode');
        var is_compact_mode = $(this).closest('#checklistapi-checklist-form').hasClass('compact-mode');
        $(this)
          .text((is_compact_mode) ? Drupal.t('Show descriptions') : Drupal.t('Hide descriptions'))
          .attr('title', (is_compact_mode) ? Drupal.t('Expand layout to include descriptions.') : Drupal.t('Compress layout by hiding descriptions.'))
        document.cookie = 'Drupal.visitor.checklistapi_compact_mode=' + ((is_compact_mode) ? 1 : 0);
        return false;
      });

    }
  };

})(jQuery);
