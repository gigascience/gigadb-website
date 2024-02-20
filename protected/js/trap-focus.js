function trapFocus(element) {
  const focusableElements = element.find('a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex="0"], [contenteditable]').filter(':visible');
  const firstFocusableElement = focusableElements[0];
  const lastFocusableElement = focusableElements[focusableElements.length - 1];

  element.on('keydown', function(e) {
      if (e.key === 'Tab' || e.code === 'Tab') {
          if ( e.shiftKey ) {
              // focus prev
              if (document.activeElement === firstFocusableElement) {
                  lastFocusableElement.focus();
                  e.preventDefault();
              }
          } else {
              // focus next
              if (document.activeElement === lastFocusableElement) {
                  firstFocusableElement.focus();
                  e.preventDefault();
              }
          }
      }
  });
}