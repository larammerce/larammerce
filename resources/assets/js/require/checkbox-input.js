if (window.HAS_CHECKBOX_INPUT === true) {
    require(["jquery"], function (jQuery) {
        let checkbox_input_class_names="";
        let checkbox_check_all_ids="";
        let checkbox_classes = [];
        let checkbox_class = class {
            constructor(enabled_inputs,enabled_inputs_element,all_inputs,output_id) {
                this.enabled_inputs = enabled_inputs;
                this.enabled_inputs_element = enabled_inputs_element;
                this.all_inputs = all_inputs;
                this.output_id = output_id;
            }
        };
        jQuery('.check-box-inputs-container').each(function () {
            let output_id = $(this).data('output-id');
            let check_all_element = $(this).find('#check-all');
            check_all_element.attr('id', "check-all-"+output_id);
            let check_all_label_element = $(this).find('label[for="check-all"]');
            check_all_label_element.attr('for',"check-all-"+output_id);
            let check_all_hidden_element = $(this).find('#check-all_hidden');
            check_all_hidden_element.attr('id',"check-all-"+output_id+'_hidden');

            let inputs_element_list = $(this).find('.checkbox-input');
            let hidden_input_element_list = $(this).find('.checkbox-input-hidden');
            let labels = $(this).find(".checkbox-input-label");
            let enabled_inputs_array = [];
            let all_inputs_array = [];
            let checkbox_inputs_class = 'checkbox-input-'+output_id;
            inputs_element_list.removeClass('checkbox-input').addClass(checkbox_inputs_class);
            for(let i=0;i<inputs_element_list.length;i++){
                let last_id = inputs_element_list[i].id;
                labels[i].htmlFor = output_id+'-'+last_id;
                inputs_element_list[i].id = output_id+'-'+last_id;
                hidden_input_element_list[i].id = output_id+'-'+last_id+'_hidden';

                all_inputs_array.push(inputs_element_list[i].id.replace(output_id+'-',''));
                inputs_element_list[i].output_id = output_id;
                if (inputs_element_list[i].checked){
                    enabled_inputs_array.push(inputs_element_list[i].id.replace(output_id+'-',''));
                }
            }
            const enabled_inputs_element = jQuery('#'+output_id);
            enabled_inputs_element.val(JSON.stringify(enabled_inputs_array));

            checkbox_classes[output_id] = new checkbox_class(enabled_inputs_array,enabled_inputs_element,all_inputs_array,output_id);

            checkbox_input_class_names += ".checkbox-input-"+output_id+",";
            checkbox_check_all_ids += "#check-all-"+output_id+",";


        });

        checkbox_input_class_names = checkbox_input_class_names.slice(0, -1);
        checkbox_check_all_ids = checkbox_check_all_ids.slice(0, -1);

        jQuery(checkbox_input_class_names).on('change', function (){
            let output_id = this.output_id;
            let field_name = this.id.replace(output_id+'-','');
            let checkbox_input_object = checkbox_classes[output_id];
            let enabled_inputs_array = checkbox_input_object.enabled_inputs;
            let enabled_inputs_element = checkbox_input_object.enabled_inputs_element;
            if (this.checked){
                enabled_inputs_array.push(field_name);
            }
            else {
                enabled_inputs_array = enabled_inputs_array.filter(function(value) { return value !== field_name });
            }
            checkbox_input_object.enabled_inputs=enabled_inputs_array;
            enabled_inputs_element.val(JSON.stringify(enabled_inputs_array));
        });

        jQuery(checkbox_check_all_ids).on('change' ,function (){
            let output_id = this.id.replace('check-all-','');
            let checkbox_input_object = checkbox_classes[output_id];
            let enabled_inputs_array = checkbox_input_object.enabled_inputs;
            let enabled_inputs_element = checkbox_input_object.enabled_inputs_element;
            let all_inputs_array = checkbox_input_object.all_inputs;
            if(this.checked){
                enabled_inputs_array = all_inputs_array;
                jQuery('.checkbox-input-'+output_id).prop('checked',true);
            }
            else {
                enabled_inputs_array = [];
                jQuery('.checkbox-input-'+output_id).prop('checked',false);
            }
            checkbox_input_object.enabled_inputs=enabled_inputs_array;
            enabled_inputs_element.val(JSON.stringify(enabled_inputs_array));
        });
    });
}

