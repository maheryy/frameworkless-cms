const INFO_PRIMARY = 0;
const INFO_SUCCESS = 1;
const INFO_WARNING = 2;
const INFO_DANGER = 3;

const INFO_DATA = [
    {
        class: 'info-primary',
        icon: "fas fa-info-circle"
    },
    {
        class: 'info-success',
        icon: "fas fa-check-circle"
    },
    {
        class: 'info-warning',
        icon: "fas fa-exclamation-triangle"
    },
    {
        class: 'info-danger',
        icon: "fas fa-exclamation-circle"
    },
];

function bindRoles(root_element = null) {
    let elements = root_element != null
        ? $(root_element).find('[data-role]')
        : $('* [data-role]');

    $.each(elements, function () {
        var role = $(this).attr('data-role');
        if (roleFunctions[role] != undefined) {
            roleFunctions[role].call(this);
        } else {
            alert('bindRoles: role[' + role + '] is ' + roleFunctions[role]);
            return false;
        }
    });
}

function getOptions(el) {
    let opts = $(el).data('options');
    return opts != null && opts != undefined && opts != '' ? opts : false;
}

function getId(el) {
    var id = $(el).data('id');
    return id != null && id != undefined && id != '' ? id : false;
}

function getUrl(el) {
    var url = $(el).data('url');
    return url != null && url != undefined && url != '' ? url : false;
}

function redirect(url, delay = null) {
    if (!delay) {
        window.location.href = url;
        return;
    }
    setTimeout(function () {
        window.location.href = url;
    }, delay * 1000);
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


function displayErrorFields(fields) {
    fields.forEach(function (el) {
        if ($('#' + el.name).length) setErrorField('#' + el.name, el.error);
    });
}

function setErrorField(input, message) {
    $(input).parent().addClass('error');

    /* Remove existing error and recreate */
    const element = "<p class=\"error-container\"><span class=\"error-message\">" + message + "</span></p>"
    $(input).parent().find('.error-container').remove();
    $(input).parent().append(element);

    /* Remove error when user has edited the input */
    $(input).focusout(function () {
        $(this).parent().removeClass('error')
        $(this).parent().find('.error-container').remove();
        $(this).off('focusout');
    })
}

let delay_info;

function setInfo(type, text, delay = null) {
    let extra_classes = $('#info-box').hasClass('center') ? ' center' : '';

    $('#info-box').removeClass();
    $('#info-icon').removeClass();
    $('#info-description').html('');
    $('#info-description').html(text);

    $('#info-box').addClass(INFO_DATA[type].class + ' active' + extra_classes);
    $('#info-icon').addClass(INFO_DATA[type].icon);

    if (delay) {
        clearTimeout(delay_info);
        delay_info = setTimeout(function () {
            $('#info-box').removeClass('active');
        }, delay * 1000);
    }
}

/* .table-list > (.list-count-container > .list-count + .list-count-text) + (.li-actions)*/
function updateTableListCounter() {
    let updated = updateListCounter({
        countElement: '.table-list .table-row-check',
        countTag: ':checked',
        updateElement: '.list-count-container',
        defaultText: 'éléments',
        updatedText: 'séléctionnés',
    });

    if (updated > 0) {
        $('.list-action-toggle').show();
    } else {
        $('.list-action-toggle').hide();
    }

    return updated;
}

function updateListCounter(data) {
    let elt = `${data.countElement}${data.countTag}`;
    let count_selected = countElement(elt);
    if (count_selected > 0) {
        $(data.updateElement).find('.list-count').html(count_selected);
        $(data.updateElement).find('.list-count-text').html(' ' + data.updatedText);
    } else {
        count_total = countElement(data.countElement);
        $(data.updateElement).find('.list-count').html(count_total);
        $(data.updateElement).find('.list-count-text').html(' ' + data.defaultText);
    }

    return count_selected;
}

function countElement(element) {
    return $(element).length;
}

/* Include every data-role functions declared in the HTML code below */
const roleFunctions = {
    setActiveLink: function () {
        $(this).parents('li.sidebar-link').addClass('selected');
    },
    initTableList: function () {
        //  Check every table row
        let total_checkbox = countElement('.table-list .table-row-check');
        updateTableListCounter();
        $('#table-check-all').change(function () {
            let all_checked = $(this).is(':checked');
            $('.table-list .table-row-check').each(function () {
                this.checked = all_checked;
            });

            if (all_checked) {
                $('.table-list tbody tr').addClass('selected')
            } else {
                $('.table-list tbody tr').removeClass('selected');
            }

            updateTableListCounter();
        })

        //  Check a row on click
        $('.table-list tbody tr').click(function (e) {
            let checkbox = $(this).find('.table-row-check');

            if (!$(e.target).is(checkbox) && !$(e.target).is('a')) {
                checkbox[0].checked = !checkbox[0].checked;
            }

            if (checkbox[0].checked) {
                $(checkbox).closest('tr').addClass('selected')
            } else {
                $(checkbox).closest('tr').removeClass('selected')
            }


            $('#table-check-all')[0].checked = total_checkbox - updateTableListCounter() > 0 ? false : true;

            // checkboxes_count = updateTableListCounter();
        })

        // Delete row(s) from the list
        $('#list-remove').click(function () {
            let id_list = [];
            $('.table-list tr.selected').each(function () {
                id_list.push(getId(this));
            });

            if (id_list.length) {
                $('.table-list tr.selected').addClass('remove-animation');
                setTimeout(function () {
                    $('.table-list tr.selected').remove();
                    $('#table-check-all')[0].checked = total_checkbox - updateTableListCounter() > 0 ? false : true;

                    if (id_list.length == 1) {
                        setInfo(INFO_DANGER, '1 élément a été supprimé');
                    } else {
                        setInfo(INFO_DANGER, id_list.length + ' éléments ont été supprimés');
                    }
                }, 650);
            }
        })

    },
    initInfoBox: function () {
        $('#info-close').click(function () {
            $('#info-box').removeClass('active');
        });
    },
    initDataTable: function () {
        $(this).DataTable();
    },
    initTinyMCE: function () {
        tinymce.init({
            selector : '#post-text-editor',
        });
    },
    submitDefault: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
            let data = getFormData(form, getOptions(this), true);

            /* Some input are not valid */
            if (!data) return;

            $.ajax({
                method: $(form).attr('method'),
                headers: $('meta[name="csrf-token"]').length ? {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} : null,
                url: $(form).attr('action'),
                data: data,
                success: ajaxFunctions.submitDefault
            });
        });
    },
    submitTextEditor: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
            let data = getFormData(form, getOptions(this));

            if ($('#post-text-editor').length) {
               data.push({
                   name: $('#post-text-editor').attr('name'),
                   value: tinymce.get("post-text-editor").getContent()
               })
            }

            $.ajax({
                method: $(form).attr('method'),
                headers: $('meta[name="csrf-token"]').length ? {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')} : null,
                url: $(form).attr('action'),
                data: data,
                success: ajaxFunctions.submitDefault
            });
        });
    },
    deleteItem: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const url = $(this).attr('href') ?? getUrl(this);
            if (!url) return;
            $.ajax({
                method: 'GET',
                url: url,
                success: ajaxFunctions.submitDefault,
            });
        });
    },
    switchPasswordUpdate: function () {
        $(this).click(function (e) {
            e.preventDefault();
            if ($('#change-password').hasClass('hidden')) {
                $('#change-password').removeClass('hidden');
                $('#change-password input#password').removeAttr('disabled');
            } else {
                $('#change-password').addClass('hidden');
                $('#change-password input#password').attr('disabled', 'disabled');
            }
        })
    }
};

let delay_function;
const ajaxFunctions = {
    debug: function (res) {
        if (res.success) {
            console.log('success');
            console.log(res);
        } else {
            console.log('fail');
            console.log(res);
        }
    },
    submitDefault: function (res) {
        if (res.success) {
            setInfo(INFO_SUCCESS, res.message, 1);
            if (res.data && res.data.url_next) {
                redirect(res.data.url_next, res.data.delay_url_next ?? 1.5);
            }
        } else {
            if (res.data) {
                displayErrorFields(res.data);
            }
            setInfo(INFO_DANGER, res.message, 4);
        }
    },
    errorDefault: function (error) {
        console.log('An error occured : ', error.responseText);
    },
    loginSuccessful: function (res) {
        setInfo(INFO_SUCCESS, res.message);
        setTimeout(function () {
            $('#info-box').removeClass('active');

            if (res.data.url_next) {
                window.location.href = res.data.url_next;
            }
        }, 1000);
    },
    loginError: function (error) {
        setInfo(INFO_DANGER, error.responseText);
        clearTimeout(delay_function);
        delay_function = setTimeout(function () {
            $('#info-box').removeClass('active');
        }, 5000);
    }
};

$(document).ready(function () {
    bindRoles();
});
