function toggleFullscreen() {
    var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
        (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
        (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
        (document.msFullscreenElement && document.msFullscreenElement !== null);

    var fullscreenIcon = document.getElementById("fullscreenIcon");
    var body = document.getElementsByTagName("body")[0];
    
    if (isInFullScreen) {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
        fullscreenIcon.src="images/icons/fullscreenIcon.svg";
    } else {
        if (body.requestFullscreen) {
            body.requestFullscreen();
        } else if (body.mozRequestFullScreen) { /* Firefox */
            body.mozRequestFullScreen();
        } else if (body.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
            body.webkitRequestFullscreen();
        } else if (body.msRequestFullscreen) { /* IE/Edge */
            body.msRequestFullscreen();
        }
        fullscreenIcon.src="images/icons/compressIcon.svg";
    }
}