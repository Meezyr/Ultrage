$(document).ready(function () {
    const inputCategory = $('.add-category #category');
    const buttonCategory = $('.add-category .button-category');
    let arrayCategory = [];

    function addCategory(array) {
        const valInput = inputCategory.val().toLowerCase();
        const textOnlyRegex = /^[a-zÀ-ÖØ-öø-ÿ0-9\s'-]+$/;

        if (textOnlyRegex.test(valInput) && valInput.trim() !== '') {
            if (array.find(e => e === valInput)) {
                $('<div class="invalid-feedback error-category">La catégorie est déjà existante.</div>').appendTo('.add-category');

                setTimeout(function () {
                    $('.add-category .error-category').remove();
                    inputCategory.val('');
                }, 5000);
            } else {
                array.push(valInput);
                $('#documentation_form_categories').val(array);

                $('<span class="badge text-bg-info value-category mt-2">' + valInput + ' <button class="btn-close remove-category" aria-label="Close" data-category="' + valInput + '"></button></span>').appendTo('.add-category .text-category');
                inputCategory.val('');
            }
        } else {
            $('<div class="invalid-feedback error-category">Le nom de la catégorie est invalide.</div>').appendTo('.add-category');

            setTimeout(function () {
                $('.add-category .error-category').remove();
                inputCategory.val('');
            }, 5000);
        }
    }

    // Modifier documentation
    if (window.location.pathname.includes("modifier")) {
        const slugDoc = window.location.pathname.slice(40, window.location.pathname.length);

        $.ajax({
            url: '/u-documentation/info-documentation',
            dataType: "json",
            type: "GET",
            data: {slug: slugDoc},
            success: function (data) {
                data.category.forEach((item) => {
                    arrayCategory.push(item);
                    $('#documentation_form_categories').val(arrayCategory);

                    $('<span class="badge text-bg-info value-category mt-2">' + item + ' <button class="btn-close remove-category" aria-label="Close" data-category="' + item + '"></button></span>').appendTo('.add-category .text-category');
                });
            },
            error: function (error) {
                console.log(error);
            }
        });
    }

    $('<div class="text-category d-flex flex-row gap-2"></div>').appendTo('.add-category');

    buttonCategory.click(function () {
        addCategory(arrayCategory);
    });

    inputCategory.keypress(function (event) {
        if (event.which === 13) {
            addCategory(arrayCategory);
        }
    });

    $('.add-category').on('click', '.remove-category', function () {
        const valueCategory = $(this).data("category");

        arrayCategory = arrayCategory.filter(function (element) {
            return element !== valueCategory;
        });

        $('#documentation_form_categories').val(arrayCategory);

        $(this).parent().remove();
    });
});