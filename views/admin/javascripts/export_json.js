jQuery(document).ready(function () {
    var $ = jQuery;
    $("a.export-json").on("click", function () {
        var href = $(this).attr('href');
        if ($('#export_sub').prop("checked")) {
            href = href.concat('/1');
        }
        else {
            href = href.concat('/0');
        }
        $(this).attr('href', href);
        return true;
    });
});
