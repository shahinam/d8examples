/**
 * @file
 * Assets Test behaviours.
 */

/**
 * We should have core/drupal set as dependency to attach any behaviors.
 */
(function ($) {
  "use strict";

  /**
   * Delete a node via AJAX callback.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach.
   *   Attaches the behavior for list of items.
   */
  Drupal.behaviors.assetsTestList = {
    attach: function(context, settings) {
      var delete_confirmation = settings.attach_assets.delete_confirmation;

      var $nodes = $('li.delete');
      $nodes.click(function(e) {
        e.preventDefault();
        var $this = $(this);
        var url = $this.children('a').attr('href');
        $.ajax(url, {
          success: function(data) {
            $this.html(delete_confirmation);
            $('.message').text('Deleted Successfully.');
          },
          error: function() {
            $('.message').text('Oops!, something went wrong.');
          }
        });
      });
    }
  };

})(jQuery);
