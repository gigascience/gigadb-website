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
            	<td>{{ upload.name }}</td>
            	<td>{{ upload.datatype }}</td>
            	<td>{{ upload.extension }}</td>
            	<td>{{ upload.size }}</td>
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
	props: ['identifier','token'],
	data: function() {
		return {
			uploads: null,
			loading: true,
	      	errored: false,
	      	apiUrl: 'http://gigadb.gigasciencejournal.com:9170',
	      	endpoint: '/fuw/api/v1/public/upload/',
	      	webclient: null,
		}
	},
	methods: {
		fetchRemoteData: function () {

			console.log('Fetching remote data on endpoint '+this.endpoint)
			this.webclient
		      .get(this.endpoint, { params: {'filter[doi]': this.identifier}} )
		      .then(response => {
		        this.uploads = response
		        console.log('Response:'+response)
		      })
		      .catch(error => {
				if (error.response) {
				  // The request was made and the server responded with a status code
				  // that falls out of the range of 2xx
				  console.log(error.response.data)
				  console.log(error.response.status)
				  console.log(error.response.headers)
				} else if (error.request) {
				  // The request was made but no response was received
				  // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
				  // http.ClientRequest in node.js
				  console.log(error.request)
				} else {
				  // Something happened in setting up the request that triggered an Error
				  console.log('Error', error.message)
				}
				console.log(error.config)
		        this.errored = true
		      })
		      .finally(() => this.loading = false)
		}
	},
	mounted: function() {
		const vm = this
		this.$nextTick(function () {
			vm.webclient = axios.create({
			  baseURL: vm.apiUrl,
			  timeout: 1000,
			  headers: {'Authorization': 'Bearer '+vm.token}
			})
			vm.fetchRemoteData()
		})
	}
}
</script>