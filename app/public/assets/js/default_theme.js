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


const INFO_PRIMARY = 0;
const INFO_SUCCESS = 1;
const INFO_WARNING = 2;
const INFO_DANGER = 3;
const INFO_DATA = [
    {
        class: 'info-primary',
    },
    {
        class: 'info-success',
    },
    {
        class: 'info-warning',
    },
    {
        class: 'info-danger',
    },
];

function setInfo(parent, type, text, delay = null) {
    $(parent).attr('class', INFO_DATA[type].class + ' active info-box');
    $(parent).find('.info-description').html(text);

    if (delay) {
        setTimeout(function () {
            $(parent).removeClass('active');
        }, delay * 1000);
    }
}

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


function startLoadingButton(button) {
    $(button).addClass('btn-loading');
    $(button).attr('disabled', true);
}

function resetLoadingButton(button) {
    $(button).removeClass('btn-loading');
    $(button).removeAttr('disabled');
}


function setHero(urlImage) {
    $('.hero-header').css('background-image', `url('${urlImage}')`);
    $('.hero-header').css('opacity', '1');
}

function setFormActions(inputElement) {
    $(inputElement).click(function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        let data = getFormData(form, null, false);

        $.ajax({
            method: 'POST',
            url: '/send-action?action=' + $(form).attr('action'),
            data: data,
            success: function (res) {
                if (res.success) {
                    setInfo($(form).find('.info-box'), INFO_SUCCESS, res.message, 2);
                } else {
                    setInfo($(form).find('.info-box'), INFO_DANGER, res.message, 2);
                }
            },
            beforeSend: function () {
                startLoadingButton(e.currentTarget);
            },
            complete: function () {
                resetLoadingButton(e.currentTarget);
            }
        });
    });
}


$(document).ready(function () {
    setHero($('#hero-img').data('url'));
    setFormActions('.form-action');
});
