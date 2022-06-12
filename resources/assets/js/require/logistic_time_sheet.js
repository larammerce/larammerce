if (window.PAGE_ID === "admin.pages.logistic.edit") {
    require(["jquery", "template", "persian_number"], function (jQuery) {
        jQuery(function () {

           // var cells = JSON.parse('{{ json_encode(cells_array) }}');
            //const checkbox = jQuery('.checkbox');
            const submit_btn = jQuery('#haha');
            const add_column_btn = jQuery('#add-new-column');


            const table_head = jQuery('#logistic-table-head').val();
            const table_row_heads = jQuery('#logistic-table-row-heads').val();
            const table_cells = jQuery('#logistic-table-cells').val();

            const offset_value = jQuery('#offset-value').val();
            const max_items_count = jQuery('#max-items-count-value').val();
            const max_total_price = jQuery('#max-total-price-value').val();




            let cells_array = JSON.parse(table_cells);
            let old_cells_array = JSON.parse(table_cells);
            let hours = JSON.parse(table_head);
            let days_array = JSON.parse(table_row_heads);
            let hours_array = [];
            let columns_count = hours.length;

            for (let col=0;col<columns_count;col++){
                hours_array.push({'start_hour':hours[col]['start_hour'], 'finish_hour':hours[col]['finish_hour'],
                    'order':hours[col]['order'], 'html_index':col.toString()});
            }

            jQuery('#hours').val(JSON.stringify(hours_array));
            console.log(hours_array);
            let rows_count = days_array.length;

            let new_columns_count = 0;



            setInterval(getFreshData, 10000);


            function getFreshData(){
                //console.log('boys in the hood');
                $.ajax({
                    url: "ajax-get-logistic-cells",
                    cache: false,
                    success: function(data){
                        let fresh_cells_array = data['fresh_cells_array'];
                        for (let row=0;row<rows_count;row++){
                            for (let col=0;col<fresh_cells_array[row].length;col++){
                                loadTableCell(fresh_cells_array[row][col]['row'],fresh_cells_array[row][col]['column'],fresh_cells_array[row][col]['items_count']
                                    ,fresh_cells_array[row][col]['total_price'],fresh_cells_array[row][col]['is_enabled'],fresh_cells_array[row][col]['is_enabled_by_admin']);
                            }
                        }
                    }
                });
            }

            function loadTableCell(row,column,items_count,total_price,is_enabled,is_enabled_by_admin){
                //console.log('inside loadCells');
                let cell_doc = jQuery('#logistic-table-cell-'+row+'-'+column);
                let items_count_doc = jQuery('#items-count-'+row+'-'+column);
                let total_price_doc = jQuery('#total-price-'+row+'-'+column);
                let checkbox_doc = jQuery('#checkbox-'+row+'-'+column);
                //console.log(cell_doc);
                /*if (is_enabled){                       no need to enable in the feature's logic
                    cell_doc.removeClass('enabled-cell').removeClass('disabled-cell').addClass('enabled-cell');
                    checkbox_doc.prop('checked', true);
                }*/

                if (is_enabled===0 && old_cells_array[row][column]['is_enabled']===1 && cells_array[row][column]['is_enabled_by_admin']===0){
                    cell_doc.removeClass('disabled-cell').removeClass('enabled-cell').addClass('disabled-cell');
                    checkbox_doc.prop('checked', '');
                    console.log(old_cells_array);
                    console.log(old_cells_array[row][column]);
                    console.log(old_cells_array[row][column]['is_enabled']);
                    items_count_doc.text(items_count);
                    total_price_doc.text(total_price);
                    items_count_doc.persianNumber();
                    total_price_doc.persianNumber();


                    cells_array[row][column]['items_count'] = items_count;
                    cells_array[row][column]['total_price'] = total_price;
                    cells_array[row][column]['is_enabled'] = is_enabled;
                    cells_array[row][column]['is_enabled_by_admin'] = is_enabled_by_admin;
                    jQuery("#cells").val(JSON.stringify(cells_array));
                }


                //console.log(row_heads.length);
                //for (i=0;i<count(row_heads);i++)
            }

            $(document).on('click', '.checkbox', function(){
                let value = this.value;
                let row_col_array = value.split('-');
                let row = row_col_array[0];
                let column = row_col_array[1];
                if(this.checked)
                {
                    cells_array[row][column]['is_enabled'] = 1;
                    jQuery("#logistic-table-cell-"+row+'-'+column).removeClass('disabled-cell').addClass('enabled-cell');
                    //console.log(cells_array);

                    //console.log(max_items_count);
                    if ((row < offset_value)
                        ||(max_items_count>0 && cells_array[row][column]['items_count'] > max_items_count)
                        || (max_total_price>0 && cells_array[row][column]['total_price'] > max_total_price)){
                        //alert('hi');
                        cells_array[row][column]['is_enabled_by_admin'] = 1;
                    }

                    jQuery("#cells").val(JSON.stringify(cells_array));

                }
                else{
                    cells_array[row][column]['is_enabled'] = 0;
                    jQuery("#logistic-table-cell-"+row+'-'+column).removeClass('enabled-cell').addClass('disabled-cell');

                    cells_array[row][column]['is_enabled_by_admin'] = 0;
                    jQuery("#cells").val(JSON.stringify(cells_array));
                }

            });



            add_column_btn.on('click', function (){
                //alert('hi');
                console.log(hours_array);
                let start_hour = jQuery('#add-start-hour').val();
                let finish_hour = jQuery('#add-finish-hour').val();
                console.log(start_hour);
                console.log(finish_hour);
                console.log(compareTime(finish_hour,finish_hour));      //0
                console.log(compareTime(finish_hour,start_hour));       //1
                console.log(compareTime(start_hour,finish_hour));       //-1
                //compareTime(start_hour,finish_hour);
                if (finish_hour > start_hour){
                        if (columns_count === 0){
                            hours_array = [];
                            hours_array.push({'start_hour':start_hour, 'finish_hour':finish_hour,
                                'order':0, 'html_index':'new-'+new_columns_count});
                            addColumn(0);

                        }
                        if (compareTime(hours_array[0]['start_hour'],finish_hour) !== -1){              //state 1
                            for (let col=0;col<columns_count;col++){
                                hours_array[col]['order'] += 1;
                                //columns_html_index_array[col+1]['index'] = columns_html_index_array[col]['index'];
                            }
                            hours_array.unshift({'start_hour':start_hour, 'finish_hour':finish_hour, 'order':0, 'html_index':'new-'+new_columns_count});
                            addColumn(0);

                        }
                        else if (compareTime(start_hour,hours_array[columns_count-1]['finish_hour']) !== -1){   //state 2
                            hours_array[columns_count]= {'start_hour':start_hour, 'finish_hour':finish_hour, 'order':columns_count, 'html_index':'new-'+new_columns_count};
                            addColumn(columns_count);

                        }
                        else{
                            for (let i=0;i<columns_count-1;i++){
                                if (compareTime(start_hour,hours_array[i]['finish_hour']) !== -1
                                    && compareTime(hours_array[i+1]['start_hour'],finish_hour) !== -1){
                                    for (let col=i+1;col<columns_count;col++){
                                        hours_array[col]['order'] += 1;
                                    }
                                    hours_array.splice(i+1,0,{'start_hour':start_hour, 'finish_hour':finish_hour, 'order':i+1, 'html_index':'new-'+new_columns_count});

                                    addColumn(i+1);
                                }
                            }
                        }
                }

                else
                    alert("Time invalid");
            })



            function addColumn(array_index){
                let cell_html = ``;

                for (let row=0;row<rows_count;row++){

                    cell_html = `<td id="logistic-table-cell-${row}-${hours_array[array_index]['html_index']}" class="new-cell">
                        <div style="float: right;margin-right: 5%;margin-left: 5%">
                            <p style="float: left">-------</p>
                            <br>
                            <p style="float: left">مجموع قیمت</p>

                        </div>
                        <div style="float: right;margin-right: 10%;margin-left: 10%">
                            <p style="float: left">-------</p>
                            <br>
                            <p style="float: left">تعداد کالا</p>

                        </div>
                    </td>`;

                    if (columns_count === 0){
                        let table_element = '#logistic-table-row-head-'+row;
                        jQuery(table_element).after(cell_html);
                    }
                    else if (array_index>0){

                        let table_element = '#logistic-table-cell-'+row+'-'+hours_array[array_index-1]['html_index'];
                        jQuery(table_element).after(cell_html);

                    }
                    else if (array_index === 0) {
                        let table_element = '#logistic-table-cell-'+row+'-'+hours_array[array_index+1]['html_index'];
                        jQuery(table_element).before(cell_html);
                    }

                }

                if (columns_count === 0){
                    let th_id = "logistic-table-column-head-"+hours_array[array_index]['html_index'];
                    let th_text_id = "logistic-table-column-head-text-"+hours_array[array_index]['html_index'];
                    let column_head = `<th scope="col" style="text-align: right" id="${th_id}">
                    <p id="${th_text_id}" act="persian-number" style="float: right;">
                    ${ hours_array[array_index]["start_hour"]}-${hours_array[array_index]["finish_hour"] }
                    </p>
                    <a class="btn btn-danger btn-sm remove-col-btn" href="#" style="border-radius: 50%;float: left;margin-left: 5%;" id="remove-column-btn-${hours_array[array_index]['html_index']}">
                    <i class="fa fa-remove"></i>
                    </a>
                    </th>`;
                    jQuery('#logistic-table-column-head').after(column_head);
                    let th_text_element = jQuery('#'+th_text_id);
                    th_text_element.persianNumber();
                }
                else if (array_index>0){
                    let th_id = "logistic-table-column-head-"+hours_array[array_index]['html_index'];
                    let th_text_id = "logistic-table-column-head-text-"+hours_array[array_index]['html_index'];
                    let column_head = `<th scope="col" style="text-align: right" id="${th_id}">
                    <p id="${th_text_id}" act="persian-number" style="float: right;">
                    ${ hours_array[array_index]["start_hour"]}-${hours_array[array_index]["finish_hour"] }
                    </p>
                    <a class="btn btn-danger btn-sm remove-col-btn" href="#" style="border-radius: 50%;float: left;margin-left: 5%;" id="remove-column-btn-${hours_array[array_index]['html_index']}">
                    <i class="fa fa-remove"></i>
                    </a>
                    </th>`;
                    jQuery('#logistic-table-column-head-'+hours_array[array_index-1]['html_index']).after(column_head);
                    let th_text_element = jQuery('#'+th_text_id);
                    th_text_element.persianNumber();
                }
                else if(array_index === 0){
                    let th_id = "logistic-table-column-head-"+hours_array[array_index]['html_index'];
                    let th_text_id = "logistic-table-column-head-text-"+hours_array[array_index]['html_index'];
                    let column_head = `<th scope="col" style="text-align: right" id="${th_id}">
                    <p id="${th_text_id}" act="persian-number" style="float: right;">
                    ${ hours_array[array_index]["start_hour"]}-${hours_array[array_index]["finish_hour"] }
                    </p>
                    <a class="btn btn-danger btn-sm remove-col-btn" href="#" style="border-radius: 50%;float: left;margin-left: 5%;" id="remove-column-btn-${hours_array[array_index]['html_index']}">
                    <i class="fa fa-remove"></i>
                    </a>
                    </th>`;
                    jQuery('#logistic-table-column-head-'+hours_array[array_index+1]['html_index']).before(column_head);
                    let th_text_element = jQuery('#'+th_text_id);
                    th_text_element.persianNumber();
                }
                columns_count += 1;
                new_columns_count +=1;
                jQuery('#hours').val(JSON.stringify(hours_array));

                //jQuery(th_id).persianNumber();

            }


            $(document).on('click', '.remove-col-btn', function(){
                let btn_id = (this.id);
                let html_index = btn_id.match('[\n\r]*remove-column-btn-\s*([^\n\r]*)')[1];
                let array_index = 0;

                for(let i=0;i<columns_count;i++){
                    if (hours_array[i]['html_index'] === html_index){
                        array_index = i;
                    }
                }


                if (array_index !== columns_count){
                    for (let i=array_index;i<columns_count;i++){
                        hours_array[i]['order'] -=1;
                    }
                }
                hours_array.splice(array_index,1);
                for (let row=0;row<rows_count;row++){
                    jQuery('#logistic-table-cell-'+row+'-'+html_index).remove();
                }
                jQuery('#logistic-table-column-head-'+html_index).remove();
                jQuery('#hours').val(JSON.stringify(hours_array));
                columns_count -=1;

            });


            function compareTime(str1, str2){
                if(str1 === str2){
                    return 0;
                }
                var time1 = str1.split(':');
                var time2 = str2.split(':');
                if(eval(time1[0]) > eval(time2[0])){
                    return 1;
                } else if(eval(time1[0]) === eval(time2[0]) && eval(time1[1]) > eval(time2[1])) {
                    return 1;
                } else {
                    return -1;
                }
            }


            jQuery("#update-form").submit(function (){

                let old_cells = cells_array;
                //console.log(rows_available);
                //console.log(old_cells);
                let cells = [];
                let new_col_index = 0;
                let cell = [];

                for (let row=0;row<rows_count;row++){
                    let temp = [];
                    for (let col=0;col<columns_count;col++){

                        if (hours_array[col]['html_index'].includes('new')) {
                            cell = {'row' : row, 'column' : col, 'is_enabled' : 1, 'is_enabled_by_admin' : 0,
                                'items_count' : 0, 'total_price' : 0};
                            new_col_index +=1;
                            //alert('new');
                        }
                        else {
                            let old_col = col-new_col_index;
                            cell = {'row' : row, 'column' : col, 'is_enabled' : old_cells[row][old_col]['is_enabled'], 'is_enabled_by_admin' : old_cells[row][old_col]['is_enabled_by_admin'],
                                'items_count' : old_cells[row][old_col]['items_count'], 'total_price' : old_cells[row][old_col]['total_price']};

                        }
                        //array_push(temp,cell);
                        temp.push(cell);
                    }
                    new_col_index = 0;
                    //array_push(cells_array,temp);
                    cells.push(temp);
                }
                //alert(cells)
                cells_array = cells;
                console.log(cells);
                jQuery("#cells").val(JSON.stringify(cells_array));
            })


        });

    });
}

