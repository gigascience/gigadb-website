<template>
  <form class="container-fluid">
    <fieldset class="attributes-input-group">
      <legend class="legend">Add a new attribute</legend>
      <input-field
        label="Name"
        :modelValue="name"
        ref="name"
        id="new-attr-name-field"
        name="name"
        @update:modelValue="name = $event"
        required
        :error="getErrorMsg('name')"
      />
      <input-field
        label="Value"
        :modelValue="value"
        ref="value"
        id="new-attr-value-field"
        name="value"
        @update:modelValue="value = $event"
        required
        :error="getErrorMsg('value')"
      />
      <input-field
        label="Unit"
        ref="unit"
        :modelValue="unit"
        id="new-attr-unit-field"
        name="unit"
        @update:modelValue="unit = $event"
      />
      <button @click.prevent="addNewAttribute" class="btn background-btn btn-small pull-right add-new-attribute" id="add-new-attribute" aria-label="Add attribute">Add</button>
    </fieldset>
    <attributes-table :attributes="attributes" @remove-attribute="removeAttribute" />
  </form>
</template>

<style lang="less" scoped>
.attributes-input-group {
  margin-bottom: 20px;
  .legend {
    color: #333333;
  }
}
.add-new-attribute {
  margin-bottom: 5px;
  min-width: 80px;
}
</style>

<script>
import InputField from './InputField.vue';
import AttributesTable from './AttributesTable.vue';

export default {
  components: {
    'input-field': InputField,
    'attributes-table': AttributesTable
  },
  props: {
    fileAttributes: {
      type: Array,
      default: () => []
    }
  },
  data: function () {
    return {
      name: '',
      value: '',
      unit: '',
      attributes: this.fileAttributes || [],
      errors: []
    }
  },
  methods: {
    addNewAttribute() {
      this.validateForm()
      this.focusFirstInvalidInput()

      if (this.errors.length > 0) {
        return
      }

      this.attributes.push({ name: this.name, value: this.value, unit: this.unit })
      this.name = ''
      this.value = ''
      this.unit = ''
    },
    validateForm() {
      this.errors = []

      if (!this.name) {
        this.errors.push({
          field: 'name',
          message: 'Name is required'
        })
      }

      if (!this.value) {
        this.errors.push({
          field: 'value',
          message: 'Value is required'
        })
      }
    },
    getErrorMsg(field) {
      const error = this.errors.find(err => err.field === field)
      if (!error) {
        return
      }
      return error.message
    },
    focusFirstInvalidInput() {
      const firstError = this.errors[0]

      if (!firstError) {
        return
      }

      const firstInvalidInput = this.$refs[firstError.field]

      if (firstInvalidInput) {
        firstInvalidInput.focus()
      }
    },
    removeAttribute(index) {
      this.attributes.splice(index, 1);
    }
  }
}
</script>