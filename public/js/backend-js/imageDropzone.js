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
                var uploadedImages = document.getElementById('project_images');
                var currentImages = uploadedImages.value ? uploadedImages.value.split(',') : [];
                var newImages = response.images.map(image => image.path);

                newImages.forEach(newImage => {
                    if (!currentImages.includes(newImage)) {
                        currentImages.push(newImage);
                    }
                });

                uploadedImages.value = currentImages.join(',');

                if (uploadedImages.value) {
                    $('.project-images').addClass('image-row');
                    $('.image-upload').addClass('visibility-hidden');
                }

                createRemoveButton(newImages, currentImages, uploadedImages, file);
            },

            error: function(file, response) {
                console.error(response);
            },

            dragenter: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").addClass("drag-active");
            },

            dragleave: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").removeClass("drag-active");
            },

            drop: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").removeClass("drag-active");
            }
        };

        $(document).on('click', '.image-remove' ,function (e){
            e.preventDefault();

            var imageId = $(this).data('image-id');
            $.post('/remove-image/' + imageId, function(response) {
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

    function createRemoveButton(newImage, currentImages, uploadedImages, file) {
        var removeButton = Dropzone.createElement("<button class='btn btn-danger mt-2'><i class='fa fa-trash' aria-hidden='true'></i></button>");

        newImage.forEach(newImages => {
         $(removeButton).on("click",function(e) {
            e.preventDefault();
            e.stopPropagation();

             var url = Routing.generate('project_remove_images', { path: encodeURIComponent(newImages) });

             $.ajax({
                url: url,
                success: function(response) {
                    if (response.status === 'success') {
                        currentImages = currentImages.filter(img => img !== newImages);
                        uploadedImages.value = currentImages.join(',');

                        file.previewElement.remove();

                        if (currentImages.length === 0) {
                            $('.project-images').removeClass('image-row');
                            $('.image-upload').removeClass('visibility-hidden');
                        }

                        console.log(uploadedImages.value); // Debugging output
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error deleting image:', error);
                    alert('Error deleting image. Please try again.');
                }
            });
        });
        file.previewElement.appendChild(removeButton);
        });
    }

    // function addRemoveButtonsToImages(newImages, currentImages, uploadedImages, file) {
    //     newImages.forEach(newImage => {
    //         var removeButton = createRemoveButton(newImage, currentImages, uploadedImages, file);
    //         file.previewElement.appendChild(removeButton);
    //     });
    // }
    // // \\//\\//\\//\\-------- client image-----------//\\//\\//\\//\\

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