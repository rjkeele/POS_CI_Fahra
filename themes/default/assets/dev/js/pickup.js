$(document).ready(function() {
    posScreen();
    var video = document.getElementById('video');

    // Get access to the camera!
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
        // Not adding `{ audio: true }` since we only want video now
        navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
            //video.src = window.URL.createObjectURL(stream);
            video.srcObject = stream;
            video.play();
        });
    }

    var canvas = document.getElementById('canvas');
    // console.log($(video).width())
    var context = canvas.getContext('2d');

    // Trigger photo take
    document.getElementById("snap").addEventListener("click", function() {
        // console.log('scan picture')
        context.drawImage(video, 0, 0, $(video).width(), 300);
    });

});

function posScreen() {
    var wh = $(window).height();
    $('.box.box-primary').height(wh-97);
}

$(window).bind('resize', posScreen);