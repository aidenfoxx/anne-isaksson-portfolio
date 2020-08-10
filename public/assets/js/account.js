/**
 * Delete protection.
 */
(function() {
  const options = document.querySelectorAll('*[data-remove]');

  for (let i = 0; i < options.length; i++) {
    options[i].addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to remove this item?')) {
        e.preventDefault();
      }
    });
  }
})();
