<template>
    <div>
        <form name="new-attribute-form">
            <label class='control-label'>
                Name:
                <input v-model="name" type="text" id="new-attr-name-field" name="name"/>
            </label>
            <label class='control-label'>
                Value:
                <input v-model="value" type="text" id="new-attr-value-field" name="value"/>
            </label>
            <label class='control-label'>
                Unit:
                <input v-model="unit" type="text" id="new-attr-unit-field" name="unit"/>
            </label>
            <button v-on:click="addNewAttribute" class="btn btn-success btn-small" id="add-new-attribute">Add</button>
        </form>
        <el-table :data="attributes" height="250" style="width: 90%">
            <el-table-column prop="name" label="Name">
            </el-table-column>
            <el-table-column prop="value" label="Value">
            </el-table-column>
            <el-table-column prop="unit" label="Unit">
            </el-table-column>
            <el-table-column fixed="right" width="45">
                <template slot-scope="scope">
                    <el-button 
                        type="danger" icon="el-icon-delete" size="small" 
                        v-on:click.prevent="removeAttribute(scope.$index, attributes)"
                    circle>
                    </el-button>
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>
<style>
</style>
<script>
export default {
    props: [ 'fileAttributes' ],
    data: function() {
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
        removeAttribute(index, rows) {
            rows.splice(index, 1);
            console.log("remove attribute at "+index)
        }
    }
}
</script>