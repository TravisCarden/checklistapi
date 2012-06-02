(function ($) {

Drupal.behaviors.checklistapiFieldsetSummaries = {
  attach: function (context) {
    $('#checklistapi-checklist-form .vertical-tabs-panes > fieldset', context).drupalSetSummary(function (context) {
      var total = $(':checkbox', context).size();
      var args = {};
      if (total) {
        args['@complete'] = $(':checkbox:checked', context).size();
        args['@total'] = total;
        args['@percent'] = Math.round(args['@complete'] / args['@total'] * 100);
        return Drupal.t('@complete of @total (@percent%) complete', args);
      }
    });
  }
};

})(jQuery);
