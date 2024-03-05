<template>
  <div>
    <div v-if="attributes.length === 0">
      <p>No attributes added yet.</p>
    </div>

    <table v-else class="table attr-table">
      <caption class="caption">
        Attribute list
      </caption>
      <thead>
        <tr>
          <th v-for="col in cols" scope="col" class="table-header" :key="col.label">{{ col.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr class="table-row" v-for="({ name, unit, value }, index) in attributes" :key="index">
          <td class="cell" aria-colindex="1" :aria-rowindex="index">{{ name }}</td>
          <td class="cell" aria-colindex="2" :aria-rowindex="index">{{ value }}</td>
          <td class="cell" aria-colindex="3" :aria-rowindex="index">{{ unit }}</td>
          <td class="cell" aria-colindex="4" :aria-rowindex="index">
            <el-button type="danger" icon="el-icon-delete" size="mini" circle @click.prevent="removeAttribute(index)"
              :aria-label="`Remove attribute ${name}`">
            </el-button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<style lang="less" scoped>
.table-header {
  font-weight: bold;

  &:not(:last-child) {
    text-align: left;
  }
}

.attr-table {
  .caption {
    color: #333333;
  }

  .cell {
    max-width: 100px;
    vertical-align: middle;
    overflow-wrap: break-word;

    &:last-child {
      text-align: center;
    }
  }
}

.table-row:nth-child(even) {
  background-color: #f8f8f8;
}
</style>

<script>
export default {
  name: "AttributesTable",
  props: {
    attributes: {
      type: Array,
      default: () => []
    }
  },
  data: function () {
    return {
      cols: [
        { label: "Name" },
        { label: "Value" },
        { label: "Unit" },
        { label: "Actions" }
      ]
    }
  },
  methods: {
    removeAttribute(index) {
      this.$emit('remove-attribute', index);
    }
  }
}
</script>