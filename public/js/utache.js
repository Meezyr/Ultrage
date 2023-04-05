$(document).ready(function() {
    var makeTask = $("#makeTask");
    var waitTask = $("#waitTask");
    var progressTask = $("#progressTask");
    var endTask = $("#endTask");

    var tempPosition = null;

    function leftPosition(colon) {
        return colon.offset().left;
    }

    function rightPosition(colon) {
        return colon.offset().left + colon.outerWidth();
    }

    function dragPosition(boxDrag, event, localTask) {
        if (localTask !== 'makeTask' && event.pageX >= leftPosition(makeTask) && event.pageX <= rightPosition(makeTask)) {
            $(boxDrag).appendTo('#makeTask');
        }

        if (localTask !== 'waitTask' && event.pageX >= leftPosition(waitTask) && event.pageX <= rightPosition(waitTask)) {
            $(boxDrag).appendTo('#waitTask');
        }

        if (localTask !== 'progressTask' && event.pageX >= leftPosition(progressTask) && event.pageX <= rightPosition(progressTask)) {
            $(boxDrag).appendTo('#progressTask');
        }

        if (localTask !== 'endTask' && event.pageX >= leftPosition(endTask) && event.pageX <= rightPosition(endTask)) {
            $(boxDrag).appendTo('#endTask');
        }

        $(boxDrag).css('top', '0').css('left', '0');
    }

    function onDragTask(event, localTask) {
        if (tempPosition !== 'makeTask' && localTask !== 'makeTask' && event.pageX >= leftPosition(makeTask) && event.pageX <= rightPosition(makeTask)) {
            $('<div class="tempTask w-100 bg-secondary" style="height: 66px;">').appendTo('#makeTask');
            tempPosition = 'makeTask';
        } else if (event.pageX <= leftPosition(makeTask) || event.pageX >= rightPosition(makeTask)) {
            $('#makeTask .tempTask').remove();

            if (tempPosition === 'makeTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'waitTask' && localTask !== 'waitTask' && event.pageX >= leftPosition(waitTask) && event.pageX <= rightPosition(waitTask)) {
            $('<div class="tempTask w-100 bg-secondary" style="height: 66px;">').appendTo('#waitTask');
            tempPosition = 'waitTask';
        } else if (event.pageX <= leftPosition(waitTask) || event.pageX >= rightPosition(waitTask)) {
            $('#waitTask .tempTask').remove();

            if (tempPosition === 'waitTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'progressTask' && localTask !== 'progressTask' && event.pageX >= leftPosition(progressTask) && event.pageX <= rightPosition(progressTask)) {
            $('<div class="tempTask w-100 bg-secondary" style="height: 66px;">').appendTo('#progressTask');
            tempPosition = 'progressTask';
        } else if (event.pageX <= leftPosition(progressTask) || event.pageX >= rightPosition(progressTask)) {
            $('#progressTask .tempTask').remove();

            if (tempPosition === 'progressTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'endTask' && localTask !== 'endTask' && event.pageX >= leftPosition(endTask) && event.pageX <= rightPosition(endTask)) {
            $('<div class="tempTask w-100 bg-secondary" style="height: 66px;">').appendTo('#endTask');
            tempPosition = 'endTask';
        } else if (event.pageX <= leftPosition(endTask) || event.pageX >= rightPosition(endTask)) {
            $('#endTask .tempTask').remove();

            if (tempPosition === 'endTask') {
                tempPosition = null;
            }
        }
    }

    $(".dragTask").draggable({
        start: function(event, ui) {
            $(this).css('cursor', 'grabbing').css('z-index', '10');
        },
        drag: function(event, ui) {
            $(this).css('cursor', 'grabbing');

            onDragTask(event, $(this).parent().attr('id'));
            console.log(tempPosition)
        },
        stop: function(event, ui) {
            $(this).css('cursor', 'grab');
            $('.tempTask').remove();

            tempPosition = null;

            dragPosition(this, event, $(this).parent().attr('id'));

            //Enregistrer la position
        }
    });
});