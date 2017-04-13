$('.previewbtn').click(function(){

    $.ajax({
        type: 'POST',
        url: '/file/preview',
        data:{'location': $(this).attr('href') },
        success: function(output){
            var response = JSON.parse(output);
            var options = {
                 url: response.preview_url,
                 title:'Preview',
                 size: eModal.size.sm,
             };
             console.log(response.status);
             if(response.status === 'UNSUPPORTED') {
                 eModal.alert('Preview is not supported for this type of document', 'Preview');
             }

             else if(response.status === 'PENDING' || response.status === 'INITIATED' ) {
                 eModal.alert('Preview for this document is not available at the moment.<br/> Please check back later.', 'Preview');
             }

             else if(response.status === 'OK' && response.preview_url) {
                 eModal.iframe(options);
             } else {
                 eModal.alert('No preview available', 'Preview');
             }
        },
        error:function(){
            console.log('error!');
        }

    });

    return false;


});
