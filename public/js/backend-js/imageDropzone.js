var Main = Main || {};

(function($, module) {

    module.dropzoneMultiImages = function () {
        Dropzone.options.images = {
            url: "/images",
            uploadMultiple: true,
            paramName: "file",
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function(file, response) {
                var uploadedImages  = document.getElementById('project_images');
                var currentImages = uploadedImages.value ? uploadedImages.value.split(',') : [];
                var newImages = response.images.map(image => image.path);

                newImages.forEach(newImage => {
                    if (!currentImages.includes(newImage)) {
                        currentImages.push(newImage);
                    }
                });

                uploadedImages.value = currentImages.join(',');

                if(uploadedImages.value){
                    $('.project-images').addClass('image-row');
                    $('.image-upload').addClass('visibility-hidden');
                    // $('.select-photo-container').addClass('visibility-hidden');
                }
            },

            error: function(file, response) {
                console.error(response);
            },

            dragenter: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").addClass("drag-active");
            },

            dragleave: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").removeClass("drag-active");
            },

            drop:function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").removeClass("drag-active");
            },
        };

        $(document).on('click', '.image-remove' ,function (e){
            e.preventDefault();

            var imageId = $(this).data('image-id');
            var projectId = $(this).data('project-id');  // Assuming project ID is available in the template context
            $.post('/projects/edit/' + projectId + '/remove-image/' + imageId, function(response) {
                if (response.status === 'success') {
                    $('div[data-image-id="' + imageId + '"]').remove();
                } else {
                    alert(response.message);
                }
            });

            $(e.target).parents('.image-show').slideUp(1000, function () {
                $(this).remove();
            })
    });
    }
    //\\//\\//\\//\\-------- client image-----------//\\//\\//\\//\\

    module.dropzoneImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            uploadMultiple: false,
            maxFilesize: 2, // MB
            maxFiles: 1,
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                var uploadLogo = document.getElementById('client_logo');
                uploadLogo.value = response.fileName;

                if (uploadLogo.value) {
                    $('.image-upload').addClass('visibility-hidden');
                }
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        };
    }

    //\\//\\//\\//\\-------- team image-----------//\\//\\//\\//\\

    module.dropzoneTeamImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            uploadMultiple: false,
            maxFilesize: 2, // MB
            maxFiles: 1,
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                var uploadLogo = document.getElementById('team_teamPhoto');
                uploadLogo.value = response.fileName;

                if (uploadLogo.value) {
                    $('.image-upload').addClass('visibility-hidden');
                }
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        };
    }
    //\\//\\//\\//\\-------- service image-----------//\\//\\//\\//\\

    module.dropzoneServiceImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            uploadMultiple: false,
            maxFilesize: 7, // MB
            maxFiles: 1,
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                var uploadLogo = document.getElementById('service_servicePhoto');
                uploadLogo.value = response.fileName;

                if (uploadLogo.value) {
                    $('.image-upload').addClass('visibility-hidden');
                }
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            }
        };
    }
})(jQuery, Main)