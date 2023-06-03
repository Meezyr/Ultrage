$(document).ready(function () {
    let makeTask = $("#makeTask");
    let waitTask = $("#waitTask");
    let progressTask = $("#progressTask");
    let endTask = $("#endTask");

    let tempPosition = null;

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
            $('<div class="temp-task w-100 bg-secondary bg-gradient rounded-2">').appendTo('#makeTask');
            tempPosition = 'makeTask';
        } else if (event.pageX <= leftPosition(makeTask) || event.pageX >= rightPosition(makeTask)) {
            $('#makeTask .temp-task').remove();

            if (tempPosition === 'makeTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'waitTask' && localTask !== 'waitTask' && event.pageX >= leftPosition(waitTask) && event.pageX <= rightPosition(waitTask)) {
            $('<div class="temp-task w-100 bg-secondary bg-gradient rounded-2">').appendTo('#waitTask');
            tempPosition = 'waitTask';
        } else if (event.pageX <= leftPosition(waitTask) || event.pageX >= rightPosition(waitTask)) {
            $('#waitTask .temp-task').remove();

            if (tempPosition === 'waitTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'progressTask' && localTask !== 'progressTask' && event.pageX >= leftPosition(progressTask) && event.pageX <= rightPosition(progressTask)) {
            $('<div class="temp-task w-100 bg-secondary bg-gradient rounded-2">').appendTo('#progressTask');
            tempPosition = 'progressTask';
        } else if (event.pageX <= leftPosition(progressTask) || event.pageX >= rightPosition(progressTask)) {
            $('#progressTask .temp-task').remove();

            if (tempPosition === 'progressTask') {
                tempPosition = null;
            }
        }

        if (tempPosition !== 'endTask' && localTask !== 'endTask' && event.pageX >= leftPosition(endTask) && event.pageX <= rightPosition(endTask)) {
            $('<div class="temp-task w-100 bg-secondary bg-gradient rounded-2">').appendTo('#endTask');
            tempPosition = 'endTask';
        } else if (event.pageX <= leftPosition(endTask) || event.pageX >= rightPosition(endTask)) {
            $('#endTask .temp-task').remove();

            if (tempPosition === 'endTask') {
                tempPosition = null;
            }
        }
    }

    $(".dragTask").draggable({
        start: function (event, ui) {
            $(this).css('cursor', 'grabbing').css('z-index', '10');
        },
        drag: function (event, ui) {
            $(this).css('cursor', 'grabbing').css('transform', 'rotate(-10deg)');

            onDragTask(event, $(this).parent().attr('id'));
        },
        stop: function (event, ui) {
            $(this).css('cursor', 'grab').css('z-index', '0').css('transform', 'rotate(0deg)');
            $('.temp-task').remove();

            tempPosition = null;

            dragPosition(this, event, $(this).parent().attr('id'));

            let localTask = $(this);
            let idTask = $(this).data('id');
            let statusTask = $(this).data('status');
            let nbStatus = $(this).parent().data('nb');

            if (nbStatus !== statusTask) {
                $.ajax({
                    type: 'POST',
                    url: '/u-tache/modifier-statut',
                    data: {idTask: idTask, moveStatus: nbStatus}
                }).done(function () {
                    localTask.data('status', localTask.parent().data('nb'));
                });
            }
        }
    });

    //Comment
    function getAllComment(id) {
        $('#listComment .comment').remove();

        $('#listComment').append('<div class="container-load my-4"><span class="loader"></span></div>');

        $.ajax({
            url: '/u-tache/commentaires',
            dataType: "json",
            type: "POST",
            data: {id: id},
            success: function (data) {
                $('#listComment .container-load').remove();

                data.forEach((item, index) => {
                    $('#listComment').append(
                        '<div class="comment mb-3 pb-1 border-bottom border-dark-subtle">' +
                        '<p class="m-0">'+item.text+'</p>' +
                        '<div title="'+item.date+'">' +
                        '<i class="bi bi-calendar-date"></i>' +
                        '</div>' +
                        '</div>'
                    );
                });
            }
        });
    }

    $('.comment-task').click(function () {
        const idTask = $(this).data('id-task');

        $('#commentModal').data('id', idTask)

        getAllComment(idTask);
    });

    $('#addComment .send-comment').click(function () {
        const idTask = $('#commentModal').data('id');
        const commentTask = $('#addComment textarea').val();

        if (commentTask !== '') {
            $.ajax({
                url: '/u-tache/ajouter-commentaire',
                dataType: "json",
                type: "POST",
                data: {id: idTask, comment: commentTask},
                success: function (data) {
                    if (data != null) {
                        $('#addComment textarea').val('');
                        getAllComment(idTask);
                    }
                }
            });
        }
    });
});