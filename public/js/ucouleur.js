$(document).ready(function () {
    //Fonction actualisation
    function actualisationColor() {
        let hexa = colorPicker.color.hex8String;
        let rgba = colorPicker.color.rgbaString;
        let hsla = colorPicker.color.hslaString;
        $('#codeHexColor').val(hexa);
        $('#codeRgbColor').val(rgba);
        $('#codeHslColor').val(hsla);
    }

    //Fonction changement de couleur
    function changerCouleurSelect(codeColor) {
        colorPicker.color.hexString = codeColor;
        actualisationColor();
    }

    //Set défaut couleur
    let colorDefault = $('#picker').data('color');

    //Set colorpicker
    let colorPicker = new iro.ColorPicker("#picker", {
        // Set the size of the color picker
        width: 350,
        // Set the initial color
        color: colorDefault ? colorDefault : "#ffffff",
        borderWidth: 5,
        borderColor: "#ffffff",
        layout: [
            {
                component: iro.ui.Wheel,
                options: {
                    sliderType: 'hue'
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'saturation',
                    sliderSize: 40,
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'value',
                    sliderSize: 40,
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'kelvin',
                    sliderSize: 40,
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'alpha',
                    sliderSize: 40,
                }
            },
        ]
    });

    //Initialisation
    actualisationColor();
    let hexa = colorPicker.color.hex8String;
    $('#color').css('background-color', hexa);

    //Souris qui bouge
    $("#picker").mousemove(function () {
        actualisationColor();
    });

    //Quand la couleur change
    colorPicker.on('color:change', function (color) {
        let hexa = colorPicker.color.hex8String;
        $('#color').css('background-color', hexa);
    });

    //Appui boutons
    $('input[type=text]').keyup(function () {
        if (this.value.length >= 9 && this.id === 'codeHexColor') {
            colorPicker.color.hex8String = this.value;
            actualisationColor();
        }
        if (this.value.length >= 16 && this.id === 'codeRgbColor') {
            colorPicker.color.rgbaString = this.value;
            actualisationColor();
        }
        if (this.value.length >= 18 && this.id === 'codeHslColor') {
            colorPicker.color.hslaString = this.value;
            actualisationColor();
        }
    });

    //Appui touche entrer
    $("input[type=text]").keypress(function (event) {
        if ((event.which === 13) && (this.id === 'codeHexColor')) {
            colorPicker.color.hex8String = $('#codeHexColor').val();
            actualisationColor();
        }
        if ((event.which === 13) && (this.id === 'codeRgbColor')) {
            colorPicker.color.rgbaString = $('#codeRgbColor').val();
            actualisationColor();
        }
        if ((event.which === 13) && (this.id === 'codeHslColor')) {
            colorPicker.color.hslaString = $('#codeHslColor').val();
            actualisationColor();
        }
    });

    //Clic sur la couleur
    let stateOkImg = false;
    let imgOk;
    $('#color').click(function (e) {
        e.preventDefault();
        copyToClipboard(colorPicker.color.hex8String);
        if (stateOkImg === false) {
            stateOkImg = true;
            imgOk = $('<div class="copy-notif bg-dark bg-gradient"><i class="bi bi-clipboard-check fs-1 text-white"></i></div>');
            $("body #color").append(imgOk);
        }
        setTimeout(() => {
            imgOk.remove();
            stateOkImg = false;
        }, "1000");
    });

    //Clic sur le bouton random color
    $('#randomColor').click(function () {
        let randomNumber = Math.floor(Math.random() * 16777215);
        let randomColor = randomNumber.toString(16);

        if (randomColor.length < 6) {
            for (let i = 0; randomColor.length < 6; i++) {
                randomColor = randomColor.concat(0);
            }
        }

        changerCouleurSelect(randomColor);
    });

    //Clic sur les couleurs pré-enregistré
    $('.color-presave').click(function () {
        if (this.id === 'colorPresaveNoir') {
            changerCouleurSelect('000000');
        }
        if (this.id === 'colorPresaveBlanc') {
            changerCouleurSelect('ffffff');
        }
        if (this.id === 'colorPresaveRouge') {
            changerCouleurSelect('ff0000');
        }
        if (this.id === 'colorPresaveVert') {
            changerCouleurSelect('00ff00');
        }
        if (this.id === 'colorPresaveBleu') {
            changerCouleurSelect('0000ff');
        }
    });

    //Clic sur les couleurs personnalisés
    $('.color-fond-user').click(function (e) {
        let selectColorUser = e.currentTarget.attributes["color"].value;

        changerCouleurSelect(selectColorUser);
    });

    //Ajout d'une couleur
    $('#addColor').click(function () {
        let keywordsText = $('#texteKeywords');
        let colorCode = colorPicker.color.hex8String;
        let texteKeywords = keywordsText.val();
        let keywords = texteKeywords.split(/,\s*/);

        $.ajax({
            type: 'POST',
            dataType: "json",
            url: '/color/addcolor',
            data: {colorCode: colorCode, arrayKeyword: keywords}
        }).done(function (msg) {
            location.reload();
        });

        keywordsText.val('');
    });

});