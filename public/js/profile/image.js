function ProfileImage(
    image,
    imageUploadButton,
    csrfToken,
    zoomInButton,
    zoomOutButton,
    rotateButton,
    profileImageSuccessMessage,
    profileImageFailureMessage
) {
    this.image = image;
    this.imageUploadButton = imageUploadButton;
    this.csrfToken = csrfToken;
    this.zoomInButton = zoomInButton;
    this.zoomOutButton = zoomOutButton;
    this.rotateButton = rotateButton;
    this.profileImageSuccessMessage = profileImageSuccessMessage;
    this.profileImageFailureMessage = profileImageFailureMessage;

    this.image.cropper({
        autoCropArea: 0.5,
        aspectRatio: 1,
        ready: function () {
        }
    });

    this.resetUploadButton();

    this.imageUploadButton.on('click', function () {
            console.log('upload click');
            this.postImage();
        }.bind(this)
    );

    this.zoomInButton.on('click', function () {
            this.image.cropper('zoom', '0.1');
        }.bind(this)
    );

    this.zoomOutButton.on('click', function () {
            this.image.cropper('zoom', '-0.1');
        }.bind(this)
    );

    this.rotateButton.on('click', function () {
            this.image.cropper('rotate', 90);
        }.bind(this)
    );
}

ProfileImage.prototype.resetUploadButton = function () {
    this.imageUploadButton.html('<span class="glyphicon glyphicon-ok menuIcon" aria-hidden="true"></span>&nbsp;Submit');
};

ProfileImage.prototype.getImageBlob = function() {
    var $deferredImageBlob = Q.defer();

    this.image.cropper('getCroppedCanvas').toBlob(function (blob) {
        $deferredImageBlob.resolve(blob);
    }, 'image/jpeg', 0.9);

    return $deferredImageBlob.promise;
};

ProfileImage.prototype.displayPostImageSuccess = function () {
    this.profileImageSuccessMessage.show();
};

ProfileImage.prototype.displayPostImageFailure = function () {
    this.profileImageFailureMessage.show();
};

ProfileImage.prototype.postImage = function() {
    this.image.cropper('disable');
    this.imageUploadButton.off('click');
    this.imageUploadButton.html('<span class="glyphicon glyphicon-ok menuIcon" aria-hidden="true"></span>&nbsp;Uploading');
    this.getImageBlob().then(function (blob) {
        var formData = new FormData();
        formData.append('image', blob);


        // TODO reset cropper on failure / success
        $.ajax({
            method: 'POST',
            url: '/profile/image',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken
            },
            success: function (data) {
                this.displayPostImageSuccess();
                this.resetUploadButton();
            }.bind(this),
            error: function (data) {
                this.displayPostImageFailure();
                this.resetUploadButton();
            }.bind(this)
        });
    }.bind(this));
};