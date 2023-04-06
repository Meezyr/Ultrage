$(document).ready(function() {
    var textPercent = $('.text-percent span');
    var rangePercent = $('.range-state');

    textPercent.html(rangePercent.val());

    rangePercent.mousemove(function() {
        textPercent.html(rangePercent.val());
    });

    rangePercent.change(function() {
        textPercent.html(rangePercent.val());
    });
});