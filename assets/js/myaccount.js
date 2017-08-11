jQuery(document).ready(function($) {
    $("#MassiliaJudo_Firstname").focusout(function () {
        console.log($(this).val());
    });
});