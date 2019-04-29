<?php
	$thisurl = parse_url($_SERVER['REQUEST_URI']);
	parse_str($thisurl["query"], $params);

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Prototype for File Upload Wizard (Uploader)</title>
	<link href="https://transloadit.edgly.net/releases/uppy/v0.30.3/uppy.min.css" rel="stylesheet">
</head>
<body>
	<h1>Uploader for dataset <?= $params["d"]?> </h1>
	<form id="dataset-metadata-form">
		<input id="dataset" type="hidden" value="<?= $params["d"]?>">
	</form>
	<div id="drag-drop-area"></div>
	<script src="https://transloadit.edgly.net/releases/uppy/v0.30.3/uppy.min.js"></script>
	<script>
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
					.use(Uppy.Tus, {endpoint: 'http://gigadb.gigasciencejournal.com:9080/files/'});

		uppy.on('complete', (result) => {
			console.log('Upload complete! We’ve uploaded these files:', result.successful);
		})
	</script>

</body>
</html>