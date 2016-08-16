//Global functions/variables for the Youtube header video
var player;
//This function creates an <iframe> (and YouTube player) after the API code downloads.
function onYouTubeIframeAPIReady() {

    var youTubeContainerCollection = jQuery("#youtube-header-container");
    var youTubeContainer = youTubeContainerCollection[0];
    var youtubeId = youTubeContainer.dataset.youtubeId;

    player = new YT.Player('youtube-header-container', { //ID of the container element
        videoId: youtubeId, //'LM_OqW_whTQ',
        playerVars: {
            controls: 0,
            showinfo: 0,
        },
    });

    //Scale the iFrame to match the aspect ratio of the video itself when it is as wide as the window
    var windowWidth = window.innerWidth;

    var myIframe = player.getIframe();
    var vidWidth = myIframe.width;
    var vidHeight = myIframe.height;

    vidAspectRatio = vidWidth / vidHeight;

    var scaledVidHeight = windowWidth / vidAspectRatio;

    myIframe.height = scaledVidHeight - 10; //To eliminate black bars on top and bottom
}

jQuery(document).ready(function() {
    //Used to bind an event listener to a video play overlay button for HTML5 header videos
    var overlayButtonCollection = jQuery("#video-play-overlay-button");
    if ( overlayButtonCollection.length > 0 ) {
        overlayButtonCollection.click(function(){
            var videoContainerAndroid = document.getElementById("video-container-android");
            if ( videoContainerAndroid != null ) {
                videoContainerAndroid.style.display = "block";
                var videoElement = document.getElementById("video-background");
                videoElement.play();
            }
        });
    }

    //Responsively select image files for the hero static image or image slideshow
    var windowInnerWidth = window.innerWidth;
    var theHeroImage = jQuery(".hero-image");
    var theHeroImageSlides = jQuery(".hero-image-slide");

    var jqImageObjects = {};
    if (theHeroImage.length > 0) {
        jqImageObjects = theHeroImage;
    } else if (theHeroImageSlides.length > 0) {
        jqImageObjects = theHeroImageSlides;
    }

    for ( var j = 0; j < jqImageObjects.length; j++ ) {
        var singleImageObject = jQuery( jqImageObjects[j] );
        var thisImageUrl = '';
        var imageInfoObject = singleImageObject.data("imageData");
        if ( imageInfoObject !== undefined ) { //If imageInfoObject = undefined, there is not a header image or header slideshow, so don't do anything
            for ( var i = 0; i < imageInfoObject.length; i++ ) {
                var imageWidth = imageInfoObject[i]["width"];
                if ( imageWidth < (windowInnerWidth)  ) {
                    //continue;
                } else {
                    thisImageUrl = imageInfoObject[i]["url"];
                    break;
                }
            }
            if ( thisImageUrl === '') { //The largest image was never bigger than windowInnerWidth
                thisImageUrl = imageInfoObject[ imageInfoObject.length-1 ]["url"]; //Use the biggest image
            }
            singleImageObject.css("background-image", "url('" + thisImageUrl +"')");
        }
    }

    //Initialize the Slick Slider for the header image slideshow
    var headerSlideshow = jQuery(".hero-image-slideshow");

    if ( headerSlideshow.length > 0 ) {
        headerSlideshow.slick({
            dots: true,
            arrows: false,
            autoplay: true,
            autoplaySpeed: 4000,
        });
    }

    //Youtube Header Video

    //Check to see if there is a Youtube video header
    var youTubeContainerCollection = jQuery("#youtube-header-container");
    if ( youTubeContainerCollection.length > 0 ) {

        //This code loads the IFrame Player API code libary asynchronously.
        var youtubeApiLibraryScript = document.createElement('script');

        youtubeApiLibraryScript.src = "https://www.youtube.com/iframe_api";
        var docScriptTagArray = document.getElementsByTagName('script');
        var firstScriptTag = docScriptTagArray[0];
        firstScriptTag.parentNode.insertBefore(youtubeApiLibraryScript, firstScriptTag);
    }
} );