<template>
  <div class="text-right">
    <button :class="['btn complete complete-btn m-0', disabled ? 'disabled-btn' : 'background-btn']"
      :aria-disabled="disabled" type="submit">
      Complete and return to Your Uploaded
      Datasets page
    </button>
  </div>
</template>

<style scoped>
.nav-link {
  width: 100%;
}

.complete-btn {
  margin: 5px;
}
</style>

<script>
export default {
  props: {
    disabled: {
      type: Boolean,
      default: false
    }
  },
};
</script>