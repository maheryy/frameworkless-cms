/*
//Sticky Header
$(window).scroll(function () {
    if ($(window).scrollTop() > 100) {
        $('.main-header').addClass('sticky');
    } else {
        $('.main-header').removeClass('sticky');
    }
});
*/

function getFormData(form, options = null, with_validation = true) {
    let data = $(form).serializeArray();
    let isValid = true;

    if (with_validation) {
        data.forEach((el) => {
            let field_id = '#' + el.name;
            if ($(field_id).length && $(field_id).parent().hasClass('required') && el.value.trim() === '') {
                setErrorField(field_id, "Ce champ est obligatoire");
                isValid = false;
            }
        })
    }

    if (options && options.add_data) {
        for (const [k, v] of Object.entries(options.add_data)) {
            data.push({name: k, value: v});
        }
    }

    return isValid ? data : false;
}

function setHero(urlImage) {
    $('.hero-header').css('background-image', `url('${urlImage}')`);
    $('.hero-header').css('opacity', '1');
}

function setFormActions(inputElement) {
    $(inputElement).click(function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        let data = getFormData(form, null);

        $.ajax({
            method: 'POST',
            url: '/send-action?action=' + $(form).attr('action'),
            data: data,
            success: function (res) {
                console.log(res);
            }
        });
    });
}


$(document).ready(function () {
    setHero($('#hero-img').data('url'));
    setFormActions('.form-action');
});
