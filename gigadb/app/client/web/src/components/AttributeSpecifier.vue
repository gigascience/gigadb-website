<template>
  <form class="container-fluid">
    <fieldset>
      <legend>Add a new attribute</legend>
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
    <el-table class="attr-table" :data="attributes">
      <el-table-column prop="name" label="Name">
      </el-table-column>
      <el-table-column prop="value" label="Value">
      </el-table-column>
      <el-table-column prop="unit" label="Unit">
      </el-table-column>
      <el-table-column fixed="right" label="Actions">
        <template v-slot="{ $index }">
          <el-button
            type="danger"
            icon="el-icon-delete"
            size="mini"
            circle
            @click.prevent="removeAttribute($index)"
            :aria-label="`Remove attribute ${attributes[$index].name}`"
          >
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </form>
</template>

<style lang="less" scoped>
.add-new-attribute {
  margin-bottom: 5px;
  min-width: 80px;
}
.attr-table::v-deep {
  .cell {
    overflow: visible;
  }
}
</style>

<script>
import InputField from './InputField.vue';

export default {
  components: {
    'input-field': InputField
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
        console.log(firstInvalidInput)
        firstInvalidInput.focus()
      }
    },
    removeAttribute(index) {
      this.attributes.splice(index, 1);
      console.log("remove attribute at " + index)
    }
  }
}
</script>