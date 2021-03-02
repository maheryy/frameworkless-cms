function bindRoles(root_element = null) {
    let elements = root_element != null 
                    ? $(root_element).find('[data-role]') 
                    : $('* [data-role]');

    $.each(elements, function(el){
        var role = $(this).attr('data-role');
        if(roleFunctions[role] != undefined) {
            roleFunctions[role].call(this);
        } else {
            alert('bindRoles: functions[' + role + '] is ' + roleFunctions[role]);
            return false;
        }
    });
}

function getOptions(el) {
    let opts = $(el).data('options');
    return opts != null && opts != undefined && opts != '' ? JSON.parse(opts) : false;
}


/* Include every data-role functions declared in the HTML code below */

const roleFunctions = {
    'testFunction': function() {
        console.log(this);
    },
};



$(document).ready(function () {
    bindRoles();
});
