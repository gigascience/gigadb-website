<div class="font-selector">
  <button id="font-toggle" class="btn background-btn-o font-regular" aria-label="Toggle dyslexic font">
    Aa
  </button>
</div>

<script>
  $(document).ready(function () {
    const toggleBtn = $('#font-toggle');
    let useDyslexicFont = false;

    try {
      useDyslexicFont = localStorage.getItem('useDyslexicFont') === 'true';
    } catch (e) {
      console.warn('localStorage is not available:', e);
    }

    updateFont(useDyslexicFont);

    toggleBtn.on('click', function () {
      useDyslexicFont = !useDyslexicFont;
      updateFont(useDyslexicFont);
      try {
        localStorage.setItem('useDyslexicFont', useDyslexicFont);
      } catch (e) {
        console.warn('Failed to save font preference:', e);
      }
    });

    function updateFont(useDyslexic) {
      $('body').css('font-family', useDyslexic ? "'OpenDyslexic', sans-serif" : "'Open Sans', sans-serif");

      toggleBtn.addClass(useDyslexic ? 'font-regular' : 'font-dyslexic');
      toggleBtn.removeClass(useDyslexic ? 'font-dyslexic' : 'font-regular');
    }
  });
</script>