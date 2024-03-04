This component handles:

- Focus trap in elements that need it, usually dialogs.
- Handles returning focus to the element that held it (togglerElement), before this one is mounted (typical usecase for dialogs).
- Handles focus in the case of addition and removal of focusable elements within (updates first and last focusable and keeps focus within)

<template>
  <div ref="focusTrapContainer">
    <slot></slot>
  </div>
</template>

<script>
const focusableElementsSelector = 'a[href], area[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), button:not([disabled]), iframe, object, embed, [tabindex]:not([tabindex="-1"]), [contenteditable]';

export default {
  data() {
    return {
      firstFocusableElement: null,
      lastFocusableElement: null,
      focusableElements: null,
      togglerElement: null,
      observer: null,
    }
  },
  mounted() {
    this.togglerElement = document.activeElement

    this.$nextTick(() => {
      this.observeFocusableElements();
      this.updateFocusableElements();
      document.addEventListener('keydown', this.trapFocus);
      if (this.firstFocusableElement) {
        this.firstFocusableElement.focus()
      }
    });
  },
  beforeDestroy() {
    if (this.observer) {
      this.observer.disconnect();
    }
    document.removeEventListener('keydown', this.trapFocus);
    if (this.togglerElement) {
      this.togglerElement.focus();
    }
  },
  methods: {
    observeFocusableElements() {
      // listen for changes in element addition / removal within the entire subtree
      const config = { childList: true, subtree: true };

      this.observer = new MutationObserver((mutationsList) => {
        mutationsList.forEach((mutation) => {
          if (mutation.type === 'childList') {
            this.updateFocusableElements();
            this.handleFocusForRemovedElements();
          }
        });
      });
      this.observer.observe(this.$refs.focusTrapContainer, config);
    },
    updateFocusableElements() {
      this.focusableElements = this.$refs.focusTrapContainer.querySelectorAll(focusableElementsSelector);

      if (this.focusableElements && this.focusableElements.length > 0) {
        this.firstFocusableElement = this.focusableElements[0];
        this.lastFocusableElement = this.focusableElements[this.focusableElements.length - 1];
      }
    },
    trapFocus(e) {
      const isTabPressed = e.key === 'Tab' || e.keyCode === 9;

      if (!isTabPressed || !this.focusableElements) {
        return;
      }

      if (e.shiftKey) {
        if (this.firstFocusableElement && document.activeElement === this.firstFocusableElement) {
          this.lastFocusableElement.focus();
          e.preventDefault();
        }
      } else {
        if (this.lastFocusableElement && document.activeElement === this.lastFocusableElement) {
          this.firstFocusableElement.focus();
          e.preventDefault();
        }
      }
    },
    handleFocusForRemovedElements() {
      if (document.activeElement && !this.$refs.focusTrapContainer.contains(document.activeElement) && this.firstFocusableElement) {
        this.firstFocusableElement.focus();
      }
    },
  },
};
</script>