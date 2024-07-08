var DropZone = DropZone || {};

(function($, module) {

    module.dropzoneMultiImages = function () {
        Dropzone.options.images = {
            url: "/images",
            uploadMultiple: true,
            paramName: "file",
            maxFilesize: 1, // MB
            acceptedFiles: "image/*",
            clickable: [".select-file"],
            dictFileTooBig: "The attachment exceeds the {{maxFilesize}} MB limit. Reduce the size of the attachment",
            successmultiple: function(file, response) {
                var imageVal = $("#project_hiddenimages");

                $('.project-images').addClass('image-row');
                $('.image-upload').addClass('visibility-hidden');

                if (response.images.length > 0) {
                    for (var i = 0; i < file.length; i++) {
                        var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-product-image top-5 right-5 z-index-10'></span>");
                        let imagePath = response.images[i];
                        $(file[i].previewElement).attr('data-image-path', imagePath)
                        $(file[i].previewElement).append(removeButton);
                    }
                }

                setTimeout(function () {
                    module.updateProductImages(imageVal);
                }, 200)
            },

            error: function (file, errorMessage) {
                if (file.size > this.options.maxFilesize * 1024 * 1024) {
                    alert(errorMessage);
                }
                this.removeAllFiles();
            },

            dragenter: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").addClass("drag-active");
            },

            dragleave: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            },

            drop: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            }

        };

        $(document).on('click', '.image-remove', function (e) {
            e.preventDefault();
            
            $(this).closest('.image-show').slideUp(0, function () {
                $(this).remove();
                module.updateProductImages();
            });
        });
        
     }

    module.initRemoveImage = function (removeImage, images, addRow, imageUpload) {
        $(document).on("click", removeImage, function () {
            $(this).closest('.dz-preview').remove();

            if ($(images).find('.dz-image-preview').length === 0) {
                $(addRow).removeClass('image-row');
                $(imageUpload).removeClass('visibility-hidden');
            }

            module.updateProductImages('#project_hiddenimages');
        })
    }

    module.initExistingImageVal = function () {
        $(document).ready(function () {
            let projectImages = [];
    
            $('.image-show').each(function () {
                let imagePath = $(this).data("image-path");
                projectImages.push(imagePath);
            });
    
            $('#project_hiddenimages').val(projectImages.join(','));
        });
    };
    
    module.updateProductImages = function () {
        let projectImages = [];
        let newProductImages = [];
    
        $('.image-show').each(function () {
            let imagePath = $(this).data("image-path");
            projectImages.push(imagePath);
        });
    
        $('.dz-image-preview').each(function () {
            let imagePath = $(this).data("image-path");
            newProductImages.push(imagePath);
        });
    
        let allProductImages = projectImages.concat(newProductImages);
    
        $('#project_hiddenimages').val(allProductImages.join(','));
    };

    module.initRemoveImages = function (removeImages, images, imagesRow, imageUpload, imageId) {
        $(document).on("click", removeImages, function () {
                
            $(this).closest('.dz-preview').remove();

            if ($(images).find('.dz-image-preview').length === 0) {
                $(imagesRow).removeClass('image-row');
                $(imageUpload).removeClass('visibility-hidden');
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
            url: "/images", // Route to handle the image upload
            paramName: "file",
            maxFilesize: 1, // MB
            multiple: false,
            maxFiles: 1,
            acceptedFiles: "image/*",
            dictFileTooBig: "The attachment exceeds the {{maxFilesize}} MB limit. Reduce the size of the attachment",
            clickable: [".select-file"],
            success: function (file, response) {
                let clientPhoto = document.getElementById('client_logo');
                clientPhoto.value = response.images;

                if (response.images) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.images.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-client-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.images;
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

            error: function (file, errorMessage) {
                if (file.size > this.options.maxFilesize * 1024 * 1024) {
                    alert(errorMessage);
                }
                this.removeAllFiles();
            },

            dragenter: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").addClass("drag-active");
            },

            dragleave: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            },

            drop: function (e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
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
            url: "/images", // Route to handle the image upload
            paramName: "file",
            multiple: false,
            maxFiles: 1,
            maxFilesize: 1, // MB
            acceptedFiles: "image/*",
            dictFileTooBig: "The attachment exceeds the {{maxFilesize}} MB limit. Reduce the size of the attachment",
            clickable: [".select-file"],
            success: function (file, response) {
                let teamPhoto = document.getElementById('team_teamPhoto');
                teamPhoto.value = response.images;

                if (response.images) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.images.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-team-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.images;
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

            error: function (file, errorMessage) {
                if (file.size > this.options.maxFilesize * 1024 * 1024) {
                    alert(errorMessage);
                }
                this.removeAllFiles();
            },

            dragenter: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").addClass("drag-active");
            },

            dragleave: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            },

            drop: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            }
        };
    }

//\\//\\//\\//\\-------- service image-----------//\\//\\//\\//\\
    module.updateServiceImages = function () {
    
        let serviceImages = [];

        $('.dz-image-preview').each(function () {
            let imagePath = $(this).data("image-path");
            serviceImages.push(imagePath);
        })

        $('#service_servicePhoto').val(serviceImages);
    }

    module.dropzoneServiceImage = function () {
        Dropzone.options.images = {
            url: "/images", // Route to handle the image-uploadimage upload
            paramName: "file",
            multiple: false,
            maxFiles: 1,
            maxFilesize: 1, // MB
            acceptedFiles: "image/*",
            dictFileTooBig: "The attachment exceeds the {{maxFilesize}} MB limit. Reduce the size of the attachment",
            clickable: [".select-file"],
            success: function (file, response) {
                let servicePhoto = document.getElementById('service_servicePhoto');
                servicePhoto.value = response.images;

                if (response.images) {
                    $('.image-upload').addClass('visibility-hidden');
                }

                if (response.images.length > 0) {
                    var removeButton = Dropzone.createElement("<span class='btn-close image-main remove-service-image top-5 right-5 z-index-10'></span>");
                    let imagePath = response.images;
                    $(file.previewElement).attr('data-image-path', imagePath)
                    $(file.previewElement).append(removeButton);
                }
            },

            error: function (file, errorMessage) {
                if (file.size > this.options.maxFilesize * 1024 * 1024) {
                    alert(errorMessage);
                }
                this.removeAllFiles();
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
                $("#images").addClass("drag-active");
            },

            dragleave: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            },

            drop: function(e) {
                e.stopPropagation();
                e.preventDefault();
                $("#images").removeClass("drag-active");
            }
        };
    }

    module.initRemovePhotos = function (removePhoto, imageId, oldImage) {
        $(document).on("click", removePhoto, function () {
            const photoElement = $(this).closest('.photo-remove');
            
            if(($(oldImage).val() == $(imageId).val())) {
                $(oldImage).val('');
                $(imageId).val('');
            }
        
            photoElement.remove();
        });

    }
})(jQuery, DropZone)