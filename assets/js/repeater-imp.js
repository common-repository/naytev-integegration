    jQuery(document).ready(function() {
        jQuery(".add-repeater").click(function() {
            var parent  =   jQuery(this).parent('.repeater-container');
            var parentID =  parent.attr('id');
            var elementID = parent.attr('for');

            naytevAccordShrink(parentID);

            var elementRow = jQuery('#'+parentID + " #" + elementID).clone();
			      var elementCounter = parseInt(jQuery('#'+parentID + " .repeater_count").val());
			      //alert(elementCounter);
            var newID = elementID + "-" + elementCounter;
            //console.log(elementRow);
            elementRow.attr("id", newID);
            elementRow.show();

            jQuery('.fieldcounter', elementRow).each(function (index){
              jQuery(this).remove();
            });
            jQuery('.heading-content', elementRow).each(function (index){
              jQuery(this).html('');
            });
            jQuery('input', elementRow).each(function (index) {

                var input_index_num_begins_at = jQuery(this).attr("name").indexOf("element-num-");
                var input_stringStarts = jQuery(this).attr("name").substring(0,input_index_num_begins_at);
                var input_stringEnds = jQuery(this).attr("wpn_subfield");
                var input_stringEnds_complete = "["+input_stringEnds+"]";
                var input_string = input_stringStarts + "element-num-" + elementCounter +"]"+input_stringEnds_complete;
                jQuery(this).attr("name", input_string);
                jQuery(this).attr("value", '');
                jQuery(this).attr('wpn_active', "false");

            });

            jQuery('label', elementRow).each(function (index) {

                var label_index_num_begins_at = jQuery(this).attr("for").indexOf("element-num-");
                var label_stringStarts = jQuery(this).attr("for").substring(0,label_index_num_begins_at);
                var label_stringEnds = jQuery(this).attr("class");
                var label_stringEnds_complete = "["+label_stringEnds+"]";
                var label_string = label_stringStarts + "element-num-" + elementCounter +"]"+label_stringEnds_complete;
                jQuery(this).attr("for", label_string);

            });

            elementCounter++;
            jQuery("#counter-for-"+elementID).val(elementCounter);

            jQuery("#"+parentID+' .add-repeater').before(elementRow);

            naytevHandleMetaFields();
            naytevAccordExpand(newID);
            jQuery('#'+newID+' input').removeAttr('readonly');

            return false;
        });


        jQuery('body').on('click', '.repeat-element-remover', function() {
            var parentID  =   jQuery(this).parent('li').attr('id');
            //alert(parentID);
            jQuery('#'+parentID).remove();
             return false;
        });

    });
