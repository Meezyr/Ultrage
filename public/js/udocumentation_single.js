$(document).ready(function () {
    let activeCopy = false;

    $('.contenu-documentation h2, .contenu-documentation h3').each(function (index) {
        $(this).attr('id', $(this).text());
    });

    $('.contenu-documentation h2 strong, .contenu-documentation h3 strong').click(function () {
        const idTitle = $(this).parent().attr('id');
        const textTitle = $(this).text();

        copyToClipboard(encodeURI(window.location.origin + window.location.pathname + '#' + idTitle));

        if (!activeCopy) {
            activeCopy = true;

            $('main > .content').append(`
                <div class="toast toast-notif toast-copy-doc show align-items-center text-bg-secondary border-0 rounded-0 rounded-top" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="d-flex">
                    <div class="toast-body">
                      Lien du chapitre <em>"${textTitle}"</em> copié ✅
                    </div>
                    <button type="button" class="btn-close stretched-link me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                  </div>
                </div>
            `);

            setTimeout(function () {
                activeCopy = false;

                $('main > .content > .toast-copy-doc').remove();
            }, 5000);
        }
    });
});