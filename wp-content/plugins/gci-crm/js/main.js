/**
     * Script Name: main
     * Description: main theme script.
     * Version: 1.0
     * Author: Zach Karp
     * Author URI: https://www.atomicdata.com
     */

(function($) {
  console.log('Main Loaded');

  // toggles that input
  function toggleInput(input) {
    if(input) {
      if (input.type === "password") {
        input.type = "text";
      } else {
        input.type = "password";
      }
    } 
  }

  // device id input
  $('#device-password-toggle').click(function(e) {
    e.preventDefault();
    var input = $('#device-password')[0];
    console.log(input);
    toggleInput(input);
  });

  // api key input
  $('#api-key-toggle').click(function(e) {
    e.preventDefault();
    var input = $('#api-key')[0];
    toggleInput(input);
  });

})(jQuery);

