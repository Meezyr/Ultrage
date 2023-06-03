$(document).ready(function() {
    let textPercent = $('.text-percent span');
    let rangePercent = $('.range-state');

    textPercent.html(rangePercent.val());

    rangePercent.mousemove(function() {
        textPercent.html(rangePercent.val());
    });

    rangePercent.change(function() {
        textPercent.html(rangePercent.val());
    });
});