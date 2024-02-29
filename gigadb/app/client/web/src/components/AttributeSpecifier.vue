<template>
  <div class="container-fluid">
    <fieldset>
      <legend>Add a new attribute</legend>
      <input-field
        label="Name"
        :modelValue="name"
        id="new-attr-name-field"
        name="name"
        @update:modelValue="name = $event"
      />
      <input-field
        label="Value"
        :modelValue="value"
        id="new-attr-name-field"
        name="name"
        @update:modelValue="value = $event"
      />
      <input-field
        label="Unit"
        :modelValue="unit"
        id="new-attr-name-field"
        name="name"
        @update:modelValue="unit = $event"
      />
      <button @click="addNewAttribute" class="btn background-btn btn-small pull-right add-new-attribute" id="add-new-attribute" aria-label="Add attribute">Add</button>
    </fieldset>
    <el-table class="attr-table" :data="attributes" height="250">
      <el-table-column prop="name" label="Name">
      </el-table-column>
      <el-table-column prop="value" label="Value">
      </el-table-column>
      <el-table-column prop="unit" label="Unit">
      </el-table-column>
      <el-table-column fixed="right" width="45">
        <template v-slot="{ $index }">
          <el-button type="danger" icon="el-icon-delete" size="small" @click.prevent="removeAttribute($index)" circle>
          </el-button>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<style scoped>
.attr-table {
  width: 90%;
}
.add-new-attribute {
  margin-bottom: 5px;
  min-width: 80px;
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
    }
  },
  methods: {
    addNewAttribute(event) {
      event.preventDefault()
      this.attributes.push({ name: this.name, value: this.value, unit: this.unit })
      this.name = ''
      this.value = ''
      this.unit = ''
    },
    removeAttribute(index) {
      this.attributes.splice(index, 1);
      console.log("remove attribute at " + index)
    }
  }
}
</script>