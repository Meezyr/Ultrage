$(document).ready(function () {
    function generateRandomString(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789ù%#<>/àçéè&$*-+?!:;~';
        const charactersLength = characters.length;

        for (let i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }

        return result;
    }

    let inputPassword = $('#password');
    let copyPassword = $('#copyPassword');
    let lengthPassword = $('#lengthPassword');

    inputPassword.val(generateRandomString(15));

    lengthPassword.on("change", function(event) {
        inputPassword.val(generateRandomString(event.currentTarget.value));
    });

    $('#buttonPassword').click(function () {
        let lengthMDP = $('#lengthPassword').val();
        let stringMDP = generateRandomString(lengthMDP);

        copyPassword.prop("disabled", false);
        inputPassword.val(stringMDP);
    });

    copyPassword.click(function () {
        if (inputPassword.val()) {
            copyToClipboard(inputPassword.val());

            $(this).html('<i class="bi bi-clipboard-check"></i>');

            setTimeout(() => {
                $(this).html('<i class="bi bi-clipboard"></i>');
            }, "1500");
        }
    });

    inputPassword.keyup(function () {
        if ($(this).val() === '') {
            copyPassword.prop("disabled", true);
        } else {
            copyPassword.prop("disabled", false);
        }
    });
});