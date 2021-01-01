$(document).ready(function(){
    $(".mydecimal").inputmask('decimal',{rightAlign: true});
    $(".mycurrency").inputmask('currency',{rightAlign: true  });
    $(".maskpersen").inputmask({ mask: "**[.**]", greedy: false, definitions: { '*': { validator: "[0-9]" } }, rightAlign : true });
    $(".maskpersen2").inputmask({ mask: "**[.**]", greedy: false, definitions: { '*': { validator: "[0-9]" } }, rightAlign : false });
    $(".inputmaskrp").inputmask({ 'alias' : 'decimal', rightAlign: true, 'groupSeparator': '.','autoGroup': true });
    $(".inputmaskrp2").inputmask({ 'alias' : 'decimal', rightAlign: false, 'groupSeparator': '.','autoGroup': true });
	$(".inputmaskrp3").inputmask({ 'alias' : 'decimal', rightAlign: false, 'groupSeparator': '.','autoGroup': false });
    $(".mycustom").inputmask({ 'alias' : 'decimal', 'mask' : "**[.**]", rightAlign: true});
});
