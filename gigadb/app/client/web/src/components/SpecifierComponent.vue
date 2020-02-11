<template>
    <div>
        <label class='control-label'>
        	Name: 
            <input v-model="attribute.name" type="text" name="Attribute[name]" id="new-attr-name-field" />
        </label>
        <label class='control-label'>
        	Value: 
            <input v-model="attribute.value" type="text" name="Attribute[value]" id="new-attr-value-field" />
        </label>
        <label class='control-label'>
        	Unit: 
            <input v-model="attribute.unit" type="text" name="Attribute[unit]" id="new-attr-unit-field" />
        </label>
        <button class="btn btn-success btn-small" type="submit">Add</button>
    </div>
</template>
<style>
</style>
<script>
export default {
	data: function () {
		return {
			attribute: [],
		}
	}
}
</script>