$(document).ready(function () {
    const inputCategory = $('.add-category #category');
    const buttonCategory = $('.add-category .button-category');
    let arrayCategory = [];

    $('<div class="text-category"></div>').appendTo('.add-category');

    buttonCategory.click(function() {
        const valInput = inputCategory.val();
        const textOnlyRegex = /^[a-zA-ZÀ-ÖØ-öø-ÿ0-9\s'-]+$/;

        if (textOnlyRegex.test(valInput) && valInput.trim() !== '') {
            arrayCategory.push(valInput);
            $('#documentation_form_categories').val(arrayCategory);

            $('<p class="value-category">'+valInput+'</p>').appendTo('.add-category .text-category');
            inputCategory.val('');
        } else {
            $('<p class="error-category">Le nom de la catégorie est invalide.</p>').appendTo('.add-category');

            setTimeout(function(){
                $('.add-category .error-category').remove();
            },5000);
        }
    });
});