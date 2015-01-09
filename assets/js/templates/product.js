$(function() {
    $('.image-box').cycle({ 
        fx:     'fade', 
        speed:  300,
        containerResize: 0,
        slideResize: 1,
        next:   '.image-box', 
        timeout: 0,
        pager:  '.dpimages-icons-box',              
        // callback fn that creates a thumbnail to use as pager anchor 
        pagerAnchorBuilder: function(idx, slide) { 
            return '<div class="individual-thumb-box"><a href="javascript: void(0)"><img src="' + slide.src + '" alt="" /></a></div>';
        } 
    });         
});


//Grab the height of the image on resize, and apply that height to the parent div
$(window).resize(function() {
    var imageHeight2 = $(".image-box img").height();
    $(".image-box img").parent().css('height', imageHeight2);
}); 