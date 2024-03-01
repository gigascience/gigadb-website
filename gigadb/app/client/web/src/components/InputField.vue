<template>
  <div class="form-group row" :class="[error && 'has-error']">
    <label :for="_uid" class="col-sm-2 control-label">
      {{ label }}<span v-if="required" aria-label="required" class="required">*</span>:
    </label>
    <div class="col-sm-10">
      <input
        v-bind="$attrs"
        :required="required"
        :aria-required="required"
        :id="_uid"
        :name="name"
        v-model="inputValue"
        class="form-control"
        :type="type"
        @input="$emit('update:modelValue', inputValue)"
        :aria-describedby="error && `${_uid}-error`"
        ref="inputRef"
      />
      <div :id="`${_uid}-error`" :class="[error && 'control-error help-block']" role="alert">
        <span v-if="error">
          {{ error }}
        </span>
      </div>
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
    error: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    }
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
  methods: {
    focus() {
      this.$refs.inputRef.focus();
    }
  }
};
</script>
