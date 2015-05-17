(function () {
  'use strict';

  var gallery = document.querySelector('.meh-slider');
  // init Flickity
  new Flickity( gallery );
  // set initial focus
  gallery.focus();
});
