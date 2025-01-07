jQuery(document).ready(function($){
    App.AjaxSetup = () => {
        $.ajaxSetup({
            type: "post",
            url: App.base_url + "ajax_helper.php",
            headers: {
                token: $('meta[name="token"]').attr("content"),
                is_ajax: 1,
            },
            dataType: "json",
            error: function (jqXHR, exception) {
                if (jqXHR.status == 404) {
                    alert("Requested page not found. [404]");
                } else if (jqXHR.status == 500) {
                    alert("Internal Server Error [500]");
                } else if (jqXHR.status == "429") {
                    alert("Refresh page and try again! [429]");
                } else if (exception === "parsererror") {
                    alert(jqXHR.responseText);
                } else if (exception === "timeout") {
                    alert("Time out error");
                } else if (exception === "abort") {
                    alert("Ajax request aborted.");
                }
            }
        })
    }

    App.notify = (type, text) => {
        $("#notify").removeClass("info");
        $("#notify").removeClass("warn");
        if (type === "error") {
            $("#notify").addClass("warn");
        } else if (type === "info") {
            $("#notify").addClass("info");
        }
        $("#notify").text(text).fadeIn();
        setTimeout(() => {
            $("#notify").fadeOut();
        }, 3000);
    }

    App.generateChecksum = async (blob) => {
        const buffer = await blob.arrayBuffer();
        const hashBuffer = await crypto.subtle.digest('SHA-256', buffer);
        return Array.from(new Uint8Array(hashBuffer))
            .map(b => b.toString(16).padStart(2, '0'))
            .join('');
    };

    App.uploadChunk = async (chunk, chunkIndex, totalChunks, fileName, finalChecksum) => {
        const checksum = await App.generateChecksum(chunk);
        if(!checksum) {
            App.notify('Unable to get checksum');
            return;
        }
        const formData = new FormData();
            formData.append('file_name', fileName);
            formData.append('chunk_index', chunkIndex);
            formData.append('total_chunks', totalChunks);
            formData.append('checksum', checksum);
            formData.append('final_checksum', finalChecksum);
            formData.append('action', 'upload_image');
            formData.append('chunk', chunk);

        $.ajax({
            type: 'POST',
            data: formData,
            contentType: false,
            cache: false,
            processData:false,
            error: function (xhr, status, errorThrown) {
                App.notify('error', 'לא ניתן לעלות קובץ. נסה שוב.');
            },
            success: function(res){
                console.log(res.error);
                if(res.error) {
                    App.notify('error', res.error);
                }
                if(res.success) {
                    $('#uploadForm').trigger('reset');
                    $('.file-message').text = '';
                    App.notify('success', res.success);
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                }
                return res;
            }
        });
    }

    App.Initialize = () => {
        App.AjaxSetup();
        $('[data-toggle="tooltip"]').tooltip()
        $(document).on('dragover dragleave drop', '.upload-area', function(e) {
            if(e.type === 'dragover') {
                return $('.file-drop-area').addClass('dragging');
            }
            $('.file-drop-area').removeClass('dragging');
        });
        $(document).on('change', '.file-input', function() {
            var parent = $(this);
            var filesCount = $(this)[0].files.length;
            var textbox = $(this).prev();
            
            if(filesCount !== 1) {
                $('#uploadForm').trigger('reset');
                $('.file-message').text = '';
                App.notify('error', 'ניתן לעלות קובץ אחד בלבד.');
                return;
            }
            const allowedExtensions = ['jpeg', 'jpg', 'png'];
            if ($.inArray($(this).val().split('.').pop().toLowerCase(), allowedExtensions) == -1) {
                $('#uploadForm').trigger('reset');
                $('.file-message').text = '';
                App.notify('error', `ניתן לעלות קבצים רק מסוג ${allowedExtensions.join(', ')}`);
                return;
            }
            var fileName = $(this).val().split('\\').pop();
            textbox.text(fileName);
        });
        $(document).on('submit', '#uploadForm', async function(e) {
            var parent = $(this);
            e.preventDefault();
            
            const file = $('.file-input')[0].files[0];
            if(!file) {
                App.notify('אנא בחר קובץ!');
                return;
            }

            const CHUNK_SIZE = (App.file_limit * 1024);
            const totalFileChunks = Math.ceil(file.size / CHUNK_SIZE);

            const finalChecksum = await App.generateChecksum(file);
            let uploadedChunks = 0;

            for (let start = 0; start < file.size; start += CHUNK_SIZE) {
                const chunk = file.slice(start, start + CHUNK_SIZE);
                const chunkIndex = Math.floor(start / CHUNK_SIZE);

                try {
                    await App.uploadChunk(chunk, chunkIndex, totalFileChunks, file.name, finalChecksum);
                    uploadedChunks++;
                    console.log(`Uploaded ${uploadedChunks}/${totalFileChunks} chunks...`);
                } catch (error) {
                    console.log(`Unable to upload chunkindex ${chunkIndex}`, error);
                    App.notify('error', 'לא ניתן לעלות קובץ.');
                    return;
                }
            }
        });
    }

    App.Initialize();

});