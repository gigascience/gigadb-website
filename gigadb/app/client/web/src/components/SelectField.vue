<template>
  <div class="form-group row" :class="[error && 'has-error']">
    <label :for="_uid" class="col-sm-2 control-label">
      {{ label }}<span v-if="required" aria-label="required" class="required">*</span>:
    </label>
    <div class="col-sm-10">
      <select
        v-bind="$attrs"
        :required="required"
        :aria-required="required.toString()"
        :id="_uid"
        :name="name"
        v-model="selectedValue"
        class="form-control"
        @change="$emit('update:modelValue', selectedValue)"
        :aria-describedby="error && `${_uid}-error`"
        ref="selectRef"
      >
        <option value="" disabled>Select an option</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
          {{ option.label || option.value }}
        </option>
      </select>
      <div v-if="enableOptionDefinition && selectedOption && selectedOption.definition" class="help-block" :id="`${_uid}-option-description`">
        {{ selectedOption.definition }}
      </div>
      <div :id="`${_uid}-error`" :class="[error && 'control-error help-block']" role="alert">
        <span v-if="error">
          {{ error }}
        </span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SelectField',
  props: {
    label: {
      type: String,
      required: true,
    },
    modelValue: {
      default: '',
    },
    name: {
      type: String,
      required: true,
    },
    options: {
      type: Array,
      required: true,
      validator: (options) => {
        return options.every((option) => {
          return option.hasOwnProperty('value');
        });
      },
    },
    error: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    enableOptionDefinition: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      selectedValue: this.modelValue,
    };
  },
  computed: {
    selectedOption() {
      return this.options.find((option) => option.attribute_name === this.selectedValue);
    },
  },
  watch: {
    modelValue(newValue) {
      this.selectedValue = newValue;
    },
  },
  methods: {
    focus() {
      this.$refs.selectRef.focus();
    }
  }
};
</script>

<style scoped>
.form-group-element {
  margin-bottom: 15px;
}
</style>
