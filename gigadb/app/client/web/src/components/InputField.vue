<template>
  <div class="form-group row">
    <label :for="_uid" class="col-sm-2 control-label">
      {{ label }}:
    </label>
    <div class="col-sm-10">
      <input
        :id="_uid"
        :name="name"
        v-model="inputValue"
        class="form-control"
        :type="type"
        @input="$emit('update:modelValue', inputValue)"
      />
    </div>
  </div>
</template>

<style scoped>
.form-group-element {
  margin-bottom: 15px;
}
</style>

<script>
export default {
  name: 'InputField',
  props: {
    label: {
      type: String,
      required: true,
    },
    modelValue: {
      type: [String, Number],
      default: '',
    },
    name: {
      type: String,
      required: true,
    },
    type: {
      type: String,
      default: 'text',
    },
  },
  data() {
    return {
      inputValue: this.modelValue,
    };
  },
  watch: {
    modelValue(newValue) {
      this.inputValue = newValue;
    },
  },
};
</script>
