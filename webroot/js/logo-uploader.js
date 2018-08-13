let logoUploader = {
    init: function (params) {
        $('#picture-upload').uploadifive({
            'uploadScript': '/users/upload-logo.json',
            'checkScript': false,
            'onCheck': false,
            'fileSizeLimit': params.filesizeLimit,
            'buttonText': 'Click to select an image to upload',
            'width': 300,
            'fileType': '.jpg,.jpeg,.png,.gif',
            'formData': {
                'timestamp': params.timestamp,
                'token': params.token,
                'user_id': params.userId
            },
            'onUploadComplete': function(file, data) {
                let uploadStatus = $('#upload-status');
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    const msg =
                        'There was an error uploading that image. ' +
                        'Please try again with a smaller image, or contact an administrator if you need assistance.';
                    uploadStatus
                        .attr('class', 'alert alert-danger')
                        .html(msg)
                        .show();
                    return;
                }
                let img = $('<img src="' + data.filepath + '" alt="Logo" />');
                $('#my-logo').html(img);

                uploadStatus
                    .attr('class', 'alert alert-success')
                    .html('Logo uploaded')
                    .show();
            },
            'onError': function(errorType, files) {
                var response = JSON.parse(file.xhr.responseText);
                $('#upload-status')
                    .attr('class', 'alert alert-danger')
                    .html(response.message)
                    .show();
            },
            'onQueueComplete': function() {
                this.uploadifive('clearQueue');
            }
        });
    }
};
