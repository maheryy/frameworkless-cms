const INFO_PRIMARY = 0;
const INFO_SUCCESS = 1;
const INFO_WARNING = 2;
const INFO_DANGER = 3;
const URI_PAGE_LINK_LIST = '/admin/page-link-list';

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
    const opts = $(el).data('options');
    return opts != null && opts != undefined && opts != '' ? opts : false;
}

function getId(el) {
    const id = $(el).data('id');
    return id != null && id != undefined ? id : false;
}

function getUrl(el) {
    const url = $(el).data('url');
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

/* Include every data-role functions declared in the HTML code below */
const roleFunctions = {
    setActiveLink: function () {
        $(this).parents('li.sidebar-link').addClass('selected');
    },
    initInfoBox: function () {
        $('#info-close').click(function () {
            $('#info-box').removeClass('active');
        });
    },
    initDataTable: function () {
        $(this).DataTable({
            columnDefs: [
                {className: 'text-center', targets: '_all'}
            ]
        });
    },
    initTinyMCE: function () {
        tinymce.init({
            selector: '#post-text-editor',
            width: "100%",
            height: "70vh",
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
                "save table directionality template paste"
            ],
            link_list: URI_PAGE_LINK_LIST,
            link_title: false,
            toolbar: "styleselect | bold italic forecolor | bullist numlist alignment | link image | fullscreen preview media",
            toolbar_groups: {
                alignment: {
                    icon: 'align-left',
                    tooltip: 'Alignement',
                    items: 'alignleft aligncenter alignright alignjustify'
                }
            }
        });
    },
    initTabs: function () {
        const options = getOptions(this);
        $('.tabs-container .tab-view').click(function (e) {
            e.preventDefault();
            if (!options.url_tab_view) return;

            $.ajax({
                method: 'GET',
                url: options.url_tab_view,
                data: {ref: getId(this)},
                success: function (res) {
                    // Detach all events of this container childrens
                    $('#' + options.container_id + ' *').off();
                    // Change container html content
                    $('#' + options.container_id).html(res);
                    // bind new elements data-roles
                    bindRoles('#' + options.container_id);
                }
            });
        })
    },
    initSelectTabs: function () {
        $(this).change(function () {
            const options = getOptions(this);
            const id = $(this).val();
            if (!options.url_tab_view) return;
            $.ajax({
                method: 'GET',
                url: options.url_tab_view,
                data: {ref: id},
                beforeSend: function () {
                    // Update url to keep track of the current tab view id (for reload purpose)
                    let index = window.location.href.indexOf('?id=');
                    let current_url = index > 0 ? window.location.href.substr(0, index) : window.location.href;
                    window.history.replaceState("", "", `${current_url}?id=${id}`);
                },
                success: function (res) {
                    // Detach all events of this container childrens
                    $('#' + options.container_id + ' *').off();
                    // Change container html content
                    $('#' + options.container_id).html(res);
                    // bind new elements data-roles
                    bindRoles('#' + options.container_id);


                }
            });
        })
    },
    initTransposable: function () {
        const container = $(this).attr('id') === 'transposable' ? $(this) : $(this).find('#transposable');
        const source_list = $(container).find('#transpose-source .list-elements');
        const target_list = $(container).find('#transpose-target .list-elements');

        const sourceClick = e => {
            $(e.currentTarget).clone().click(targetClick).appendTo(target_list);
            $(e.currentTarget).remove();
        };

        const targetClick = e => {
            $(e.currentTarget).clone().click(sourceClick).appendTo(source_list);
            $(e.currentTarget).remove();
        };

        $(container).find('#transpose-source .transpose-element').click(sourceClick);
        $(container).find('#transpose-target .transpose-element').click(targetClick);
    },
    initNavigationTransferable: function () {
        const target_list = $(this).find('#transferable-target .list-elements');

        const sourceClick = e => {
            const data = getOptions($(e.currentTarget).find('input.element-data'));
            // Base element
            const element =
                '<li class="transferable-element">' +
                '<div class="element-content">' +
                `<input type="hidden" name="nav_items[]" value="${data.page_id}">` +
                `<input type="text" name="nav_labels[]" value="${data.page_title}">` +
                `<span class="description">Page ${data.page_title} | <a target="_blank" href="${data.page_link}">${data.page_link}</a></span>` +
                '</div>' +
                '</li>';

            // Attach event to item actions
            const up = $('<span class="element-up"><i class="fas fa-sort-up"></i></span>').click(moveUp);
            const down = $('<span class="element-down"><i class="fas fa-sort-down"></i></span>').click(moveDown);
            const del = $('<span class="element-delete"><i class="fas fa-times"></i></span>').click(remove);

            // Create a container for the actions to append to base element
            const actions = $('<div class="element-actions"></div>').append(up, [down, del]);
            $(element).append(actions).appendTo(target_list);
        };

        const remove = e => {
            $(e.currentTarget).closest('li').remove();
        }

        const moveUp = e => {
            const current = $(e.currentTarget).closest('li');
            const prev = $(current).prev();
            if (prev.length) {
                $(prev).before(current);
            }
        }
        const moveDown = e => {
            const current = $(e.currentTarget).closest('li');
            const next = $(current).next();
            if (next.length) {
                $(next).after(current);
            }
        }

        $(this).find('#transferable-source .transferable-element').click(sourceClick);
        $(this).find('#transferable-target .transferable-element .element-up').click(moveUp);
        $(this).find('#transferable-target .transferable-element .element-down').click(moveDown);
        $(this).find('#transferable-target .transferable-element .element-delete').click(remove);
    },
    submitDefault: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
            let data = getFormData(form, getOptions(this));

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
    submitPermissions: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const form = $(this).closest('form');
            let data = getFormData(form, getOptions(this), false);

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
    },
    addRoleTabView: function () {
        $(this).click(function (e) {
            e.preventDefault();
            if ($('#tab_view').val() == -1) return;
            $('#tab_view').val(-1).change();
        })
    }
};

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
};

$(document).ready(function () {
    bindRoles();
});
