// Code goes here

    function showDetail(selector){
      // console.log("selector = "+ selector);      
      if($(selector).hasClass("hiddenRow")){
      	$(selector).removeClass("hiddenRow");
      }else {
      	$(selector).addClass("hiddenRow");
      }
      
    };
	

    function showChild(selector){
      // console.log("selector = "+ selector);      
      if($(selector).hasClass("hiddenChild")){
      	$(selector).removeClass("hiddenChild");
      }else {
      	$(selector).addClass("hiddenChild");
      }
      
    };	