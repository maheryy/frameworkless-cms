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
    return opts != null && opts != undefined && opts != '' ? JSON.parse(opts) : false;
}

function getId(el) {
    var id = $(el).data('id');
    return id != null && id != undefined && id != '' ? id : false;
}

function getUrl(el) {
    var url = $(el).data('url');
    return url != null && url != undefined && url != '' ? url : false;
}


function setInfo(type, text) {
    $('#info-box').removeClass();
    $('#info-icon').removeClass();
    $('#info-description').html(text);

    $('#info-box').addClass(INFO_DATA[type].class + ' active');
    $('#info-icon').addClass(INFO_DATA[type].icon);
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

    addData: function () {
        $(this).click(function (e) {
            e.preventDefault();

            setInfo(INFO_SUCCESS, "Ajouté avec succès");
        });
    },
    deleteData: function () {
        $(this).click(function (e) {
            e.preventDefault();

            setInfo(INFO_DANGER, "Ce voyage a été supprimé");
        });
    },
    saveData: function () {
        $(this).click(function (e) {
            e.preventDefault();

            setInfo(INFO_PRIMARY, "Sauvegardé avec succès");
        });
    }
};


const ajaxFunctions = {
    debug: function (res) {
        if (typeof res === 'string' || res.success) {
            console.log('success');
            console.log(res);
        } else {
            console.log('fail');
            console.log(res);
        }
    },
    errorDefault: function (error) {
        console.log('An error occured : ', error.responseText);
    }
}

$(document).ready(function () {
    bindRoles();
});
