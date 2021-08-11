$(document).ready (function() {

    $('.custom-file input').change(function (e) { $(this).next('.custom-file-label').html(e.target.files[0].name); });

    $('.card').hover(
        function(){
            $(this).animate({
                marginTop: "-=1%",
            },200);
        },

        function(){
            $(this).animate({
                marginTop: "0%",
            },200);
        }
    );

});