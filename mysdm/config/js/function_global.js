function CheckMultiAll(ketdivnya) {
    $('#'+ketdivnya+' :checkbox').each(function () {     //loop all checkbox in dvMain div
        $(this).attr('checked', true);                    //This will check the current checkbox
    });
}

function UnCheckMultiAll(ketdivnya) {
    $('#'+ketdivnya+' :checkbox').each(function () {     //loop all checkbox in dvMain div
        $(this).attr('checked', false);                   //This will uncheck the current checkbox
    });
}

