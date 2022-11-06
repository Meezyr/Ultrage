$(document).ready(function() {
    //Fonction actualisation
    function actualisationColor() {
        var hexa = colorPicker.color.hex8String;
        var rgba = colorPicker.color.rgbaString;
        var hsla = colorPicker.color.hslaString;
        $('#codeHexColor').val(hexa);
        $('#codeRgbColor').val(rgba);
        $('#codeHslColor').val(hsla);
    }

    //Fonction copie dans presse papier
    function copyToClipboard(text) {
        var temp = $('<input>');
        $("body").append(temp);
        temp.val(text).select();
        document.execCommand("copy");
        temp.remove();
    }

    //Fonction changement de couleur
    function changerCouleurSelect(codeColor) {
        colorPicker.color.hexString = codeColor;
        actualisationColor();
    }


    //Set colorpicker
    var colorPicker = new iro.ColorPicker("#picker", {
        // Set the size of the color picker
        width: 350,
        // Set the initial color to pure red
        color: "#ffffff",
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
                    sliderType: 'saturation'
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'value'
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'kelvin'
                }
            },
            {
                component: iro.ui.Slider,
                options: {
                    sliderType: 'alpha'
                }
            },
        ]
    });

    //Initialisation
    actualisationColor();
    var hexa = colorPicker.color.hex8String;
    $('#color').css('background-color', hexa);

    //Souris qui bouge
    $("#picker").mousemove(function() {
        actualisationColor();
    });

    //Quand la couleur change
    colorPicker.on('color:change', function(color) {
        var hexa = colorPicker.color.hex8String;
        $('#color').css('background-color', hexa);
    });

    //Appui boutons
    $('input[type=button]').click(function() {
        if (this.id == 'btnHexColor') {
            var hexaChange = $('#codeHexColor').val();
            colorPicker.color.hex8String = hexaChange;
            actualisationColor();
        }
        if (this.id == 'btnRgbColor') {
            var rgbaChange = $('#codeRgbColor').val();
            colorPicker.color.rgbaString = rgbaChange;
            actualisationColor();
        }
        if (this.id == 'btnHslColor') {
            var hslaChange = $('#codeHslColor').val();
            colorPicker.color.hslaString = hslaChange;
            actualisationColor();
        }
    });

    //Appui touche entrer
    $("input[type=text]").keypress(function(event) {
        if ((event.which == 13) && (this.id == 'codeHexColor')) {
            var hexaChange = $('#codeHexColor').val();
            colorPicker.color.hex8String = hexaChange;
            actualisationColor();
        }
        if ((event.which == 13) && (this.id == 'codeRgbColor')) {
            var rgbaChange = $('#codeRgbColor').val();
            colorPicker.color.rgbaString = rgbaChange;
            actualisationColor();
        }
        if ((event.which == 13) && (this.id == 'codeHslColor')) {
            var hslaChange = $('#codeHslColor').val();
            colorPicker.color.hslaString = hslaChange;
            actualisationColor();
        }
    });

    //Clic sur la couleur
    var stateOkImg = false;
    $('#color').click(function(e) {
        e.preventDefault();
        copyToClipboard(colorPicker.color.hex8String);
        if (stateOkImg == false) {
            stateOkImg = true;
            var imgOk = $('<img src="/img/copy.png">');
            $("body #color").append(imgOk);
        }
        setTimeout(() => {
            imgOk.remove();
            stateOkImg = false;
        }, "1000");
    });

    //Clic sur le bouton random color
    $('#randomColor').click(function() {
        var randomColor = Math.floor(Math.random()*16777215).toString(16);
        changerCouleurSelect(randomColor);
    });

    //Clic sur les couleurs pré-enregistré
    $('.colorPresave').click(function() {
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
    $('.colorFondUser').click(function(e) {
        var selectColorUser = e.currentTarget.attributes["color"].value;

        changerCouleurSelect(selectColorUser);
    });

    //Ajout d'une couleur
    $('#addColor').click(function() {
        var colorCode = colorPicker.color.hex8String;
        var texteKeywords = $('#texteKeywords').val();
        var keywords = texteKeywords.split(/,\s*/);

        $.ajax({
            type: 'POST',
            url: '/u-couleur/addcolor',
            data: { colorCode: colorCode, arrayKeyword: keywords }
        }).done(function(msg) {
            location.reload();
        });

        $('#texteKeywords').val('');
    });

});