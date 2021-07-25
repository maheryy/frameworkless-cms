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

function startLoadingButton(button) {
    $(button).addClass('btn-loading');
    $(button).attr('disabled', true);
}

function resetLoadingButton(button) {
    $(button).removeClass('btn-loading');
    $(button).removeAttr('disabled');
}

function getMenuItemBaseElement(data) {
    return $(
        '<li class="transferable-element">' +
        '<div class="element-content">' +
        `<input type="hidden" name="menu_items[icons][]" value="${data.icon ?? ''}">` +
        `<input type="hidden" name="menu_items[pages][]" value="${data.page_id ?? ''}">` +
        `<input class="label" type="text" name="menu_items[labels][]" value="${data.page_title ?? 'Mon lien'}" ${data.label_readonly ? 'readonly' : ''} placeholder="Nom du lien">` +
        (
            data.type === 1
                ? `<span class="description">Page ${data.page_title} : <input type="text" class="link" name="menu_items[links][]" value="${data.page_link}" readonly></span>`
                : `<span class="description">${data.icon !== undefined ? '<i class="' + data.icon + ' px-0.25"></i>' : 'Lien personnalisé :'} <input type="text" class="link" name="menu_items[links][]" placeholder="www.example.com" value="${data.page_link ?? ''}" ${data.link_readonly ? 'readonly' : ''}></span>`
        ) +
        '</div>' +
        '</li>'
    );
}

function createMenuItemElement(data, actionUp, actionDown, actionDel) {
    const element = getMenuItemBaseElement(data);

    const up = $('<span class="element-up"><i class="fas fa-sort-up"></i></span>').click(actionUp);
    const down = $('<span class="element-down"><i class="fas fa-sort-down"></i></span>').click(actionDown);
    const del = $('<span class="element-delete"><i class="fas fa-times"></i></span>').click(actionDel);

    return $(element).append($('<div class="element-actions"></div>').append(up, down, del));
}


function createFooterTextElement() {
    return $(
        '<li class="transferable-element">' +
        '<div class="element-content">' +
        `<input type="hidden" name="footer_items[types][]" value="1">` +
        `<input type="hidden" name="footer_items[menus][]" value="">` +
        `<input class="label" type="text" name="footer_items[labels][]" value="A propos" placeholder="A propos">` +
        `<textarea class="form-control" name="footer_items[data][]" rows="4" style="resize: none" placeholder="Entrez votre texte..."></textarea>` +
        '</div>' +
        '</li>'
    );
}

function createFooterLinkElement(data) {
    let options = "";

    if (data.data) {
        for (const [key, value] of Object.entries(data.data)) {
            options += `<option value=${value.id}>Menu - ${value.title}</option>`;
        }
    }

    return $(
        '<li class="transferable-element">' +
        '<div class="element-content">' +
        `<input type="hidden" name="footer_items[types][]" value="2">` +
        `<input type="hidden" name="footer_items[data][]" value="">` +
        `<input class="label" type="text" name="footer_items[labels][]" value="Liens utiles" placeholder="Liens utiles">` +
        `<select class="form-control" name="footer_items[menus][]">` + options + `</select>` +
        '</div>' +
        '</li>'
    );
}

function createFooterContactElement() {
    return $(
        '<li class="transferable-element">' +
        '<div class="element-content">' +
        `<input type="hidden" name="footer_items[types][]" value="3">` +
        `<input type="hidden" name="footer_items[data][]" value="">` +
        `<input type="hidden" name="footer_items[menus][]" value="">` +
        `<input class="label" type="text" name="footer_items[labels][]" value="Contactez-nous" placeholder="Contactez-nous">` +
        '<span class="description">Formulaire rapide - Contact</span>' +
        '</div>' +
        '</li>'
    );
}

function createFooterNewsletterElement() {
    return $(
        '<li class="transferable-element">' +
        '<div class="element-content">' +
        `<input type="hidden" name="footer_items[types][]" value="4">` +
        `<input type="hidden" name="footer_items[data][]" value="">` +
        `<input type="hidden" name="footer_items[menus][]" value="">` +
        `<input class="label" type="text" name="footer_items[labels][]" value="Newsletter" placeholder="Newsletter">` +
        '<span class="description">Formulaire rapide - Newsletter</span>' +
        '</div>' +
        '</li>'
    );
}


function createItemElement(data, actionUp, actionDown, actionDel) {
    let element;
    switch (data.element) {
        case 1:
            element = createFooterTextElement();
            break;
        case 2:
            element = createFooterLinkElement(data);
            break;
        case 3:
            element = createFooterContactElement();
            break;
        case 4:
            element = createFooterNewsletterElement();
            break;
    }
    const up = $('<span class="element-up"><i class="fas fa-sort-up"></i></span>').click(actionUp);
    const down = $('<span class="element-down"><i class="fas fa-sort-down"></i></span>').click(actionDown);
    const del = $('<span class="element-delete"><i class="fas fa-times"></i></span>').click(actionDel);

    return $(element).append($('<div class="element-actions"></div>').append(up, down, del));
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
    initClassicalTabs: function () {
        $('#tab-list .tab-btn').click(function () {
            const ref_id = getId(this);
            $('#tab-list .tab-btn').each((key, el) => {
                let id = getId(el);
                if (ref_id === id) {
                    $(el).addClass('active');
                    $(`#content-${id}`).attr('class', 'tab-content active');
                } else {
                    $(el).removeClass('active');
                    $(`#content-${id}`).attr('class', 'tab-content hidden');
                }
            });
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
    initMenuTransferable: function () {
        const target_list = $(this).find('#transferable-target .list-elements');

        const sourceClick = e => {
            const data = getOptions($(e.currentTarget).find('input.element-data'));

            const element = createMenuItemElement(data, moveUp, moveDown, remove);
            $(target_list).append(element);
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

        $(this).find('.transferable-source .transferable-element').click(sourceClick);
        $(this).find('#transferable-target .transferable-element .element-up').click(moveUp);
        $(this).find('#transferable-target .transferable-element .element-down').click(moveDown);
        $(this).find('#transferable-target .transferable-element .element-delete').click(remove);
    },
    initFooterCustomization: function () {
        const target_list = $(this).find('#transferable-target .list-elements');

        const sourceClick = e => {
            if ($('#transferable-target .transferable-element').length >= 4) return;
            const data = getOptions($(e.currentTarget));

            const element = createItemElement(data, moveUp, moveDown, remove);
            $(target_list).append(element);
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

        $(this).find('.transferable-source .transferable-element').click(sourceClick);
        $(this).find('#transferable-target .transferable-element .element-up').click(moveUp);
        $(this).find('#transferable-target .transferable-element .element-down').click(moveDown);
        $(this).find('#transferable-target .transferable-element .element-delete').click(remove);
    },
    'initVisitorsChart': function () {
        const options = getOptions(this);
        const ctx = document.getElementById('visitor-chart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: options.x_axis,
                datasets: [{
                    label: 'Visites',
                    data: options.y_axis,
                    borderColor: 'rgb(54, 162, 235)',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
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
                success: ajaxFunctions.submitDefault,
                beforeSend: function () {
                    startLoadingButton(e.currentTarget);
                },
                complete: function () {
                    resetLoadingButton(e.currentTarget);
                },
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
                success: ajaxFunctions.submitDefault,
                beforeSend: function () {
                    startLoadingButton(e.currentTarget);
                },
                complete: function () {
                    resetLoadingButton(e.currentTarget);
                }
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
                success: ajaxFunctions.submitDefault,
                beforeSend: function () {
                    startLoadingButton(e.currentTarget);
                },
                complete: function () {
                    resetLoadingButton(e.currentTarget);
                }
            });
        });
    },
    actionItem: function () {
        $(this).click(function (e) {
            e.preventDefault();
            const url = $(this).attr('href') ?? getUrl(this);
            if (!url) return;
            const options = getOptions(this);
            let additional_data = [];
            if (options && options.add_data) {
                for (const [k, v] of Object.entries(options.add_data)) {
                    additional_data.push({name: k, value: v});
                }
            }

            $.ajax({
                method: 'POST',
                url: url,
                data: additional_data,
                success: ajaxFunctions.submitDefault,
                beforeSend: function () {
                    !$(this).attr('href') && startLoadingButton(e.currentTarget);
                },
                complete: function () {
                    !$(this).attr('href') && resetLoadingButton(e.currentTarget);
                }
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
    },
    refreshMenuSource: function () {
        let storage = {};
        $(this).change(function () {
            if ($(this).val() === '1') {
                $('.source-links').removeClass('hidden');
                $('.source-socials').addClass('hidden');
            } else {
                $('.source-socials').removeClass('hidden');
                $('.source-links').addClass('hidden');

            }
            $('#transferable-target .list-elements').html('');
        });
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
            if (res.data && res.data.url_next) {
                if (res.data.url_next_delay) setInfo(INFO_SUCCESS, res.message, res.data.url_next_delay);
                redirect(res.data.url_next, res.data.url_next_delay ?? null);
                return;
            }
            setInfo(INFO_SUCCESS, res.message, 1);
        } else {
            if (res.data) displayErrorFields(res.data);
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
