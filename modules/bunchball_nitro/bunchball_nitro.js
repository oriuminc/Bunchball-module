// JavaScript closure. Defined jQuery $.
(function ($) {
  
  function doSomethingFun() {
	  alert('This Is Fun!!');
  }
}) (jQuery);

jQuery(document).ready(function($) {
    doSomethingFun();
    //alert('called?');
});
