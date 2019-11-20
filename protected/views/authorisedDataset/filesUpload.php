<?php ?>

<div id="gigadb-dataset-uploader">

<h1>{{ message}} for dataset {{ doi }}</h1>

<form id="dataset-metadata-form">
	<input id="dataset" type="hidden" v-bind:value="doi">
</form>
	<div id="drag-drop-area"></div>

<script>
document.addEventListener("DOMContentLoaded", function(event) {

	var app = new Vue({
		el:'#gigadb-dataset-uploader',
	  	data: {
		  	doi: <?php  echo "'".$identifier."'"; ?>,
		  	message: 'GigaDB File Uploader',
		  	seen: true,
		    todos: [
		      { text: 'Buy jacket'},
		      { text: 'Call Dad'},
		      { text: 'Feed the cat'}
		    ]
		 },
	  	methods: {
		  	reverseMessage: function () {
		  		this.message = this.message.split('').reverse().join('')
		  	}
		}
	});

	var uppy = Uppy.Core()
					.use(Uppy.Dashboard, {
						inline: true,
						target: '#drag-drop-area',
						hideAfterFinish: true,
						showProgressDetails: true,
						hideUploadButton: false,
						hideRetryButton: false,
						hidePauseResumeButton: false,
						hideCancelButton: false,
						proudlyDisplayPoweredByUppy: false,
						metaFields: [
						    { id: 'name', name: 'Name', placeholder: 'file name' },
						    { id: 'description', name: 'Description', placeholder: 'describe what the file is about' }
						  ],
						locale: {}
					})
					.use(Uppy.Form, {
						  target: "#dataset-metadata-form",
						  getMetaFromForm: true,
						  addResultToForm: false,
						  triggerUploadOnSubmit: false,
						  submitOnSuccess: false
						})
					// .use(Uppy.Tus, {endpoint: 'https://master.tus.io/files/'});
					.use(Uppy.Tus, {endpoint: '/files/'});

		uppy.on('complete', function (result) {
			console.log('Upload complete! We’ve uploaded these files:', result.successful);
		});

});
</script>