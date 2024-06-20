var DropZone = DropZone || {};

(function($, module) {

    module.dropzoneMultiImages = function () {
        Dropzone.options.images = {
            url: "/images",
            uploadMultiple: true,
            paramName: "file",
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            successmultiple: function(file, response) {
                var imageVal = $("#project_images");

                $('.project-images').addClass('image-row');
                $('.image-upload').addClass('visibility-hidden');

                if (response.images.length > 0) {
                    for (var i = 0; i < file.length; i++) {
                        var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-product-image top-5 right-5 z-index-10'></span>");
                        let imagePath = response.images[i];
                        $(file[i].previewElement).attr('data-image-path', imagePath.path)
                        $(file[i].previewElement).append(removeButton);
                    }
                }

                setTimeout(function () {
                    module.updateProductImages(imageVal);
                }, 200)
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

    module.initRemoveImage = function (removeImage, images, addRow, imageUpload, imageVal) {
        $(document).on("click", removeImage, function () {
            $(this).closest('.dz-preview').remove();

            if ($(images).find('.dz-image-preview').length === 0) {
                $(addRow).removeClass('image-row');
                $(imageUpload).removeClass('visibility-hidden');
            }

            module.updateProductImages(imageVal);
        })
    }

    module.updateProductImages = function (imageVal) {
        let productImages = [];

        $('.dz-image-preview').each(function () {
            let imagePath = $(this).data("image-path");
            productImages.push(imagePath);
        })

        $(imageVal).val(productImages.join(','));
    }

    module.initRemoveImages = function (removeImages, images, imagesRow, imageUpload) {
        $(document).on("click", removeImages, function () {

            $(this).closest('.dz-preview').remove();

            if ($(images).find('.dz-image-preview').length === 0) {
                $(imagesRow).removeClass('image-row');
                $(imageUpload).removeClass('visibility-hidden');
                // $('.dz-message').style.display = "block";
            }

            module.updateImages();
            module.updateTeamImages();
            module.updateServiceImages();
        })
    }

/////---------------------------client-logo---------------------------------//////
    module.updateImages = function () {
        let imagePath = $('.dz-image-preview').data("image-path") || '';
        $('#client_logo').val(imagePath);
    }
    module.dropzoneImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            maxFilesize: 2, // MB
            multiple: false,
            maxFiles: 1,
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                let clientLogo = document.getElementById('client_logo');
                clientLogo.value = response.fileName;

                if (response.fileName) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.fileName.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-client-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.fileName;
                    $(file.previewElement).attr('data-image-path', imagePath)
                    $(file.previewElement).append(removeButton);
                }
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
            },
            // error: function (file, response) {
            //     console.error(response);
            // },

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

            drop: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#image-upload").removeClass("drag-active");
            }
        };
    }

//\\//\\//\\//\\-------- team image-----------//\\//\\//\\//\\
    module.updateTeamImages = function () {
        let imagePath = $('.dz-image-preview').data("image-path") || '';
        $('#team_teamPhoto').val(imagePath);
    }
    module.dropzoneTeamImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            multiple: false,
            maxFiles: 1,
            maxFilesize: 2, // MB
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                let teamPhoto = document.getElementById('team_teamPhoto');
                teamPhoto.value = response.fileName;

                if (response.fileName) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.fileName.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-team-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.fileName;
                    $(file.previewElement).attr('data-image-path', imagePath)
                    $(file.previewElement).append(removeButton);
                }

            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
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
    }

//\\//\\//\\//\\-------- service image-----------//\\//\\//\\//\\
    module.updateServiceImages = function () {
        let imagePath = $('.dz-image-preview').data("image-path") || '';
        $('#service_servicePhoto').val(imagePath);
    }
    module.dropzoneServiceImage = function () {
        Dropzone.options.images = {
            url: "/logo", // Route to handle the image upload
            paramName: "file",
            multiple: false,
            maxFiles: 1,
            maxFilesize: 7, // MB
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            success: function (file, response) {
                let servicePhoto = document.getElementById('service_servicePhoto');
                servicePhoto.value = response.fileName;

                if (response.fileName) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.fileName.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-service-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.fileName;
                    $(file.previewElement).attr('data-image-path', imagePath)
                    $(file.previewElement).append(removeButton);
                }
            },
            init: function () {
                this.on("maxfilesexceeded", function (file) {
                    this.removeAllFiles();
                    this.addFile(file);
                });
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
    }

    module.initRemovePhotos = function (removePhoto, imageId) {
        $(document).on("click", removePhoto, function () {
            $(this).closest('.photo-remove').remove();
            $(imageId).val('');
        });
    }

    $(document).ready(function () {
        let checkedIsVisible = $('#service_is_visible');

        $(checkedIsVisible).on('change',function () {
            this.value = this.checked ? 1 : 0;
            this.isVisible = this.checked ? true : false;
            console.log(this.value);

            if(this.isVisible == true){
                checkedIsVisible.attr('is-visible', isVisible = true);
                checkedIsVisible.attr('checked', checked = true);
            }else{
                checkedIsVisible.attr('is-visible', isVisible = false);
                checkedIsVisible.attr('checked', checked = false);
            }
        })
    })
})(jQuery, DropZone)