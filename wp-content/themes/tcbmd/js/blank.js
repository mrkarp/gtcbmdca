// Global
// Site wide JavaScript

$(document).ready(function() {
    console.log('Global Init');

    // Bind Enter key
    $('[id^=dog-grid-item-]').hover(function(e) { 
        $(this).slideDown();
    });

    function hasNumber(string) {
        return /\d/.test(string);
    }

    function initMap(e) {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 8
          });
    }
});