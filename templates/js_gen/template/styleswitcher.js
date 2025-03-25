if($.cookie("reason-color")) {
    $("link[href|='../css/style']").attr("href","../css/" + $.cookie("reason-color"));
}

if($.cookie("reason-width")) {
    $("link[href|='../css/width']").attr("href","../css/" + $.cookie("reason-width"));
}

$(document).ready(function() { 
    $("#color-options .color-box").click(function() { 
        $("link[href|='../css/style']").attr("href", "../css/" + $(this).attr('rel'));
        $("link[href|='css/style']").attr("href", "css/" + $(this).attr('rel'));
        $.cookie("reason-color",$(this).attr('rel'), {expires: 7, path: '/'});
        return false;
    });

    $("#width-options .container-option").click(function() { 
        $("link[href|='../css/width']").attr("href", "../css/" + $(this).attr('rel'));;
        $.cookie("reason-width",$(this).attr('rel'), {expires: 7, path: '/'});
        return false;
    });
});
