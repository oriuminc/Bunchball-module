
Drupal.bunchball = Drupal.bunchball || {};

/**
 * Set initial variable field disable status based on checkbox value.
 * Bind click behaviour for checkboxes.
 */
Drupal.behaviors.bunchball = {
  attach: function (context, settings) {
    var $ = jQuery;
    // get list of checkboxes
    var checkBoxes = $('input[id^="edit-bunchball"]').filter('input[id$="check"]').once('bunchball');
    // set click handler
    checkBoxes.click(Drupal.bunchball.setEnabled);
    // set initial value
    checkBoxes.each(Drupal.bunchball.setEnabled);
  }
};

/**
 * Enable or disable variable name field based on checkbox checked / unchecked.
 */
Drupal.bunchball.setEnabled = function() {
  var $ = jQuery;
  var varName = this.id.replace(/^(edit-bunchball-.+?)-check$/,'$1-action');
  var varField = $('input[id="' + varName + '"]').parent('.form-item');
  if (this.checked) {
    varField.slideDown('fast');
  }
  else {
    varField.slideUp('fast');
  }
}
