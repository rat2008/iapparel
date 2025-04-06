// http://jsfiddle.net/mekwall/sgxKJ/

$.widget("ui.autocomplete", $.ui.autocomplete, {
    options : $.extend({}, this.options, {
        multiselect: false
    }),
    _create: function(){
        this._super();

        var self = this,
            o = self.options;

        if (o.multiselect) {
            console.log('multiselect true');

            self.selectedItems = {};           
            self.multiselect = $("<div></div>")
                .addClass("ui-autocomplete-multiselect ui-state-default ui-widget")
                .css("width", self.element.width())
                .insertBefore(self.element)
                .append(self.element)
                .bind("click.autocomplete", function(){
                    self.element.focus();
                });
            
            var fontSize = parseInt(self.element.css("fontSize"), 10);
            function autoSize(e){
                // Hackish autosizing
                var $this = $(this);
                $this.width(1).width(this.scrollWidth+fontSize-1);
            };

            var kc = $.ui.keyCode;
            self.element.bind({
                "keydown.autocomplete": function(e){
                    if ((this.value === "") && (e.keyCode == kc.BACKSPACE)) {
                        var prev = self.element.prev();
                        delete self.selectedItems[prev.text()];
                        prev.remove();
                    }
                },
                // TODO: Implement outline of container
                "focus.autocomplete blur.autocomplete": function(){
                    self.multiselect.toggleClass("ui-state-active");
                },
                "keypress.autocomplete change.autocomplete focus.autocomplete blur.autocomplete": autoSize
            }).trigger("change");

            // TODO: There's a better way?
            o.select = o.select || function(e, ui) {
				var this_id = e.target.id;
				var str_this = $('#'+this_id+"_hidden").val();
				var arr_this = str_this.split(",");
				arr_this.push(ui.item.value);
				
				
				var str_this_arr = $('#'+this_id+"_hidden_arr").val();
				//var arr_assoc = str_this_arr.split("##^^**");
				var arr_assoc = JSON.parse(str_this_arr);
				arr_assoc[ui.item.value] = ui.item.label;
				
                $("<div></div>")
                    .addClass("ui-autocomplete-multiselect-item")
                    .text(ui.item.label)
                    .append(
                        $("<span></span>")
                            .addClass("ui-icon ui-icon-close")
                            .click(function(){
                                var item = $(this).parent();
                                delete self.selectedItems[item.text()];
                                item.remove();
								
								var str_this_arr = $('#'+this_id+"_hidden_arr").val();
								var arr_assoc = JSON.parse(str_this_arr);
								var this_key = "";
								for(var key in arr_assoc){
									if(arr_assoc[key]==item.text())
										this_key = key;
								}
								
								delete arr_assoc[""+this_key];
								//alert(this_key); 
								
								var this_str = $('#'+this_id+"_hidden").val();
								var arr_this = this_str.split("##^^**");
								arr_this.remove(this_key.toString());
								$('#'+this_id+"_hidden").val(arr_this.join("##^^**")+"");//*/
								$('#'+this_id+"_hidden_arr").val(JSON.stringify(arr_assoc));//*/
                            })
                    )
                    .insertBefore(self.element);
                
                self.selectedItems[ui.item.label] = ui.item;
                self._value("");
				$('#'+this_id+"_hidden").val(arr_this.join("##^^**")+"");
				$('#'+this_id+"_hidden_arr").val(JSON.stringify(arr_assoc));
                return false;
            }

            /*self.options.open = function(e, ui) {
                var pos = self.multiselect.position();
                pos.top += self.multiselect.height();
                self.menu.element.position(pos);
            }*/
        }

        return this;
    }
});