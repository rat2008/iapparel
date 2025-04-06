
//datatable need add the code below to your file, if not it wont work for the img in second page
// $('#tb_detail').dataTable( {
//  "lengthMenu": [[10, 50, 100], [10, 50, 100]],
//  "sPaginationType": "listbox",
//  "fnDrawCallback": function(){
//         exec_dev_enlarge_tooltip();
//     }
// });

$(document).ready(function() {
  exec_dev_enlarge_tooltip();
});

var count_img=0;
function exec_dev_enlarge_tooltip(){
  $(".dev-enlarge-tooltip").each(function() {
    var id="img_auto_id_"+count_img; //if id not define will auto generate id
    if (this.id!==undefined && this.id!=="") {
      id=this.id;
    }else{
      $(this).prop("id",id);

      count_img++;
    }

    // var count_item=id.replace("item_img","");
    // console.log(count_item);
    var src=$(this).attr("src");
    var ele=document.getElementById("img_enlarge_"+id);
    if(ele==null){
      $(this).after("<div id='img_enlarge_"+id+"' style='position:absolute;'></div>");
    }
    
    $('#img_enlarge_'+id).dxTooltip({
        target: '#'+id,
        showEvent: 'mouseenter',
        hideEvent: 'mouseleave',
        hideOnOutsideClick: false,
        position: 'right',
        contentTemplate(data){
          data.html('<img width="300" src="'+src+'">');
        },
    });
  });
}