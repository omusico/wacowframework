$(function () {
    $('#roleIdSelector').change(function () {
        var roleId = $(this).val();
        $.post($frontendVars.baseUrl + '/admin/index/permission/roleId/' + roleId, { format: 'html' }, function (data) {
            $('#resourceList').html(data);
        }, 'html');
    });
    $('.checkAll').live('click', function () {
        var resource = this;
        $('.' + this.id).each(function (i) {
            this.checked = resource.checked;
        });
    });
});