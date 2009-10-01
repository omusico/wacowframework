$(function () {
    $('#menu > li > ul').hide();
    $('#menu > li').hover(
        function () { $('ul', this).slideDown('fast'); },
        function () { var $menu = this; setTimeout(function () { $('ul', $menu).slideUp('fast'); }, 300) }
    );
});