
$(document).ready(function(){$(".alert").addClass("in").fadeOut(500);

/* swap open/close side menu icons */
$('[data-toggle=collapse]').click(function(){
  	// toggle icon
	//alert("here");
  	//$(this).find("i").toggleClass("glyphicon-chevron-right glyphicon-chevron-down");
  	$(this).find("i").toggleClass("gly-chev-down gly-chev-right");
});
});

