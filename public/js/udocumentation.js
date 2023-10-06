$(document).ready(function () {
    let orderBy = $('.orderBy');
    let valOrder = orderBy.value === undefined ? 'release_date_recent' : orderBy.value;

    function htmlListDocument(item) {
        let html = '';

        html += '<div class="col-4">' +
            '<div class="card h-100">' +
            '<div class="card-header text-body-secondary">';
        if (item.category) {
            item.category.forEach((category) => {
                html += '<span class="badge text-bg-secondary">' + category.charAt(0).toUpperCase() + category.slice(1) + '</span>\n'
            });
        }
        html += '</div>' +
            '<div class="card-body d-flex flex-column justify-content-between gap-4">' +
            '<div class="texts-card">' +
            '<h5 class="card-title"><strong>' + item.title + '</strong></h5>';
        if (item.excerpt) {
            html += '<p class="card-text">' + item.excerpt + '</p>';
        }
        html += '<p class="card-text">' +
            '<small class="text-body-secondary me-3">' +
            '<i class="bi bi-clock me-1"></i>' +
            '<time datetime="' + item.releaseDate + '">' + item.releaseDateLong + '</time>' +
            '</small>';
        if (item.updateDate) {
            html += '<small class="text-body-secondary">' +
                '<i class="bi bi-wrench-adjustable-circle me-1"></i>' +
                '<time datetime="' + item.updateDate + '">' + item.updateDateLong + '</time>' +
                '</small>';
        }
        html += '</p>' +
            '</div>' +
            '<a href="/u-documentation/documentation/' + item.slug + '" class="btn btn-primary stretched-link w-100">En savoir plus</a>' +
            '</div>' +
            '<div class="card-footer text-body-secondary">' +
            '<small class="text-body-secondary">Par ' + item.author + '</small>' +
            '</div>' +
            '</div>' +
            '</div>';

        return html;
    }

    function loadDocument(valOrder) {
        $('#listDocument').append('<div class="container-load my-4"><span class="loader"></span></div>');

        $.ajax({
            url: '/u-documentation/ordre',
            dataType: "json",
            type: "GET",
            data: {orderBy: valOrder, recherche: getParam('recherche'), categorie: getParam('categorie')},
            success: function (data) {
                $('#listDocument .container-load').remove();

                data.forEach((item) => {
                    $('#listDocument').append(htmlListDocument(item));
                });
            },
            error: function (error) {
                console.log(error);
            }
        });
    }


    orderBy.val('release_date_recent');

    loadDocument('release_date_recent');

    orderBy.click(function () {
        if (valOrder !== this.value) {
            valOrder = this.value;

            $('#listDocument .col-4').remove();

            loadDocument(valOrder);
        }
    });
});