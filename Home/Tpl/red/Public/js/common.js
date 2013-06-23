function minimsg(content){
	$(function(){
		$("body").append('<table cellspacing="0" cellpadding="0" class="popupcredit"><tr><td class="pc_l">&nbsp;</td><td class="pc_c"><div class="pc_inner">' + content +
			'</td><td class="pc_r">&nbsp;</td></tr></table>');
		$('.popupcredit').show().delay(1000).animate({opacity: 'hide',top:'100px'}, "slow",'',function(){$(".popupcredit").remove();});
	});
}