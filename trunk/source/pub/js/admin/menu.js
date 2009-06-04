$(function () {
    $('#menu > li > ul').hide();
    $('#menu > li').hover(
        function () { $('ul', this).slideDown('fast'); },
        function () { $('ul', this).slideUp('fast');  }
    );
});