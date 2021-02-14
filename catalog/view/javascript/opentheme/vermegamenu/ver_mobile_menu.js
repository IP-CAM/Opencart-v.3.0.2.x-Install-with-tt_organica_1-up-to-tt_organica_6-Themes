$(document).ready(function () {
	$("#vermegamenu-mobilemenu .had_child, ul.ver_mobilemenu li span.button-view1, ul.ver_mobilemenu li span.button-view2").each(function(){
        $(this).append('<span class="ttclose_v"><a href="javascript:void(0)"></a></span>');
      });
	$("ul.ver_mobilemenu li.active").each(function(){
		$(this).children().next("ul").css('display', 'block');
	});
	$("ul.ver_mobilemenu li ul").hide();
	
	// only category menu
	$('#vermegamenu-mobilemenu span.button-view1 span').click(function() { 
	if ($(this).hasClass('ttopen_v')) {varche = true} else {varche = false};
	if(varche == false){
		$(this).addClass("ttopen_v");
		$(this).removeClass("ttclose_v");
		$(this).parent().parent().find('ul.level2').slideDown();
		varche = true;
	} else 
	{	
		$(this).removeClass("ttopen_v");
		$(this).addClass("ttclose_v");
		$(this).parent().parent().find('ul.level2').slideUp();
		varche = false;
	}
	});
	$('#vermegamenu-mobilemenu span.button-view2 span').click(function() { 
		if ($(this).hasClass('ttopen_v')) {varche = true} else {varche = false};
		if(varche == false){
			$(this).addClass("ttopen_v");
			$(this).removeClass("ttclose_v");
			$(this).parent().parent().find('ul.level3').slideDown();
			varche = true;
		} else 
		{	
			$(this).removeClass("ttopen_v");
			$(this).addClass("ttclose_v");
			$(this).parent().parent().find('ul.level3').slideUp();
			varche = false;
		}
	});
	
	// origin menu
	$('#vermegamenu-mobilemenu li.had_child').click(function(){
		var chk = 0;
		if ( $(this).children('span').hasClass('ttclose_v') && ( chk==0 ) ) {
			$(this).children('span').removeClass('ttclose_v');
			$(this).children('span').addClass('ttopen_v');
			$(this).children('ul').slideDown();
			chk = 1;
		}
		if ( $(this).children('span').hasClass('ttopen_v') && ( chk==0 ) ) {
			$(this).children('span').removeClass('ttopen_v');
			$(this).children('span').addClass('ttclose_v');
			$(this).children('ul').slideUp();
			chk = 1;
		}
	});
	//mobile
	$('.btn-navbar_v').click(function() {
		var chk = 0;
		if ( $('#navbar-inner_v').hasClass('navbar-inactive') && ( chk==0 ) ) {
			$('#navbar-inner_v').removeClass('navbar-inactive');
			$('#navbar-inner_v').addClass('navbar-active');
			$('#vermegamenu-mobilemenu').slideDown();
			chk = 1;
		}
		if ($('#navbar-inner_v').hasClass('navbar-active') && ( chk==0 ) ) {
			$('#navbar-inner_v').removeClass('navbar-active');
			$('#navbar-inner_v').addClass('navbar-inactive');			
			$('#vermegamenu-mobilemenu').slideUp();
			chk = 1;
		}
		//$('#vermegamenu-mobilemenu').slideToggle();
	});    
});