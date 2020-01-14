<template>
    <table>
        <thead>
            <tr>
                <th>File Name</th>
                <th>Data Type</th>
                <th>Format</th>
                <th>Size</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="upload in uploads">
            	<td>{{ upload.FileName }}</td>
            	<td>{{ upload.DataType }}</td>
            	<td>{{ upload.Format }}</td>
            	<td>{{ upload.Size }}</td>
            	<td></td>
            	<td></td>
            </tr>

        </tbody>
    </table>
</template>
<style></style>
<script>

import axios from "axios"

export default {
	props: ['identifier'],
	data: function() {
		return {
			uploads: null,
			loading: true,
	      	errored: false
		}
	},
	mounted: function() {
		axios
	      .get('http://gigadb.gigasciencejournal:9170/fuw/api/v1/public/upload?doi='+this.identifier)
	      .then(response => {
	        this.uploads = response.uploads
	      })
	      .catch(error => {
	        console.log(error)
	        this.errored = true
	      })
	      .finally(() => this.loading = false)
	}
}
</script>