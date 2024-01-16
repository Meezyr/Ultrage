$(document).ready(function () {
    $('.contenu-documentation h2, .contenu-documentation h3').each(function(index) {
        $(this).attr('id', $(this).text());
    });
});