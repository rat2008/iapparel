/*
    1. documentation for datagrid can view this link:
    https://js.devexpress.com/Documentation/ApiReference/UI_Widgets/dxDataGrid/
    
    2. more info about PHP datasource can view this link:
    https://github.com/DevExpress/DevExtreme-PHP-Data
*/


$(function(){
    //var order_data = new DevExpress.data.DataSource("http://localhost/devexpress/data.php");
    
    // for Excel highlight max value
    // i cannot found any solution by using devexpress function to find max amount in Excel. so using this way
    var max = 0;
    
    // data source setting, determine CRUD URL
    var order_data = new DevExpress.data.DataSource({
        // for get table data
        load: function (loadOptions) {
            return $.getJSON("service.php?mode=0");
        },
        
        // for insert row
        insert: function (values) {
            return $.ajax({
                url: "service.php?mode=1",
                method: "POST",
                data: values
            })
        },
        
        // for delete row
        remove: function (key) {           
            // this loop will put all value for this row into a array.
            // if just need to pass a 'ID' key, can used &&ID="+key["ID"] instead (like update) 
            var out = [];

            for (var mydata in key) {
                if (key.hasOwnProperty(mydata)) {
                    out.push(mydata + '=' + encodeURIComponent(key[mydata]));
                }
            }
            
            out = out.join('&');

            return $.ajax({
                url: "https://www.apparelezi.com/devexpress/service.php?mode=2&&"+out,

                //url: "http://mydomain.com/MyDataService/" + encodeURIComponent(key),
                method: "POST",
            })
        },
        
        // for update row
        update: function (key, values) {
            return $.ajax({
                url: "https://www.apparelezi.com/devexpress/service.php?mode=3&&ID="+key["ID"],
                method: "POST",
                data: values
            })            
        }
    });
    
    
    
    var dataGrid = $("#gridContainer").dxDataGrid({
        // dataSource: orders,
        dataSource: order_data, //data source determined on above
               
        keyExpr: "ID",  // primary key
        allowColumnReordering: true,
        showBorders: true,
        
        // default grouping is expand or not
        grouping: {
            autoExpandAll: true,
        },
        
        // allow search on top-right of page
        searchPanel: {
            visible: true
        },
        
        // determine show how many row in a page
        paging: {
            pageSize: 10
        },  
        
        // allow grouping by column
        groupPanel: {
            visible: true
        },
        
        // allow key in search box for every column
        filterRow: { 
            visible: true 
        },
        
        /* allow header filter. will create a filter box with checkbox to search by data in column
        this function can be customize in columns part for each column, can check:
        https://js.devexpress.com/Demos/WidgetsGallery/Demo/DataGrid/Filtering/jQuery/Light/
        */
        headerFilter:{
            visible: true
        },
        
        // allow to edit setting
        editing: {
            mode: "row",
            allowUpdating: true,
            allowDeleting: true,
            allowAdding: true,
            useIcons: true  // add icon to button
        },
        
        // column chooser
        columnChooser: {
            enabled: true,
            mode: "select" // or "dragAndDrop"
        },
        
        // column setting. if remove this part, table will display column by using key in data source array
        columns: [            
            // columns in table
            {
                dataField: "OrderNumber",
                width: 130,
                caption: "Invoice Number"
            }, {
                dataField: "OrderDate",
                dataType: "date",
                width: 160
            }, 
            "Employee", {
                caption: "Term",
                dataField: "Terms",
                visible: false          // hidden as default, will appear in the column chooser
                // allowHiding: false // cannot be hidden
                // showInColumnChooser: false // does not appear in the column chooser even when hidden
            },{
                caption: "City",
                dataField: "CustomerStoreCity"
            }, {
                caption: "State",
                dataField: "CustomerStoreState"  
            }, {
                dataField: "SaleAmount",
                alignment: "right",
                format: "currency"
            }, {
                dataField: "TotalAmount",
                alignment: "right",
                format: "currency"
            },
            
            // button control
            {
                type: "buttons",
                width: 110,
                buttons: ["edit", "delete"]
            }
        ],
        
        // summarty
        sortByGroupSummaryInfo: [{
            summaryItem: "count"
        }],
        
        // determine what need to show in group summary
        summary: {
            groupItems: [{
                column: "OrderNumber",
                summaryType: "count",
                displayFormat: "{0} orders",
            }, {
                column: "SaleAmount",
                summaryType: "max",
                valueFormat: "currency",
                showInGroupFooter: false,
                alignByColumn: true
            }, {
                column: "TotalAmount",
                summaryType: "max",
                valueFormat: "currency",
                showInGroupFooter: false,
                alignByColumn: true
            }, {
                column: "TotalAmount",
                summaryType: "sum",
                valueFormat: "currency",
                displayFormat: "Total: {0}",
                showInGroupFooter: true
            }]
            
            // totalItems: [{
                // column: "OrderNumber",
                // summaryType: "count",
                // displayFormat: "{0} orders",
            // },  {
                // column: "SaleAmount",
                // summaryType: "sum",
                // valueFormat: "currency"
            // }]
        },
        
        // export to excel
        export: {
            enabled: true,
            customizeExcelCell: function(options) {
                var gridCell = options.gridCell;
                
                if(!gridCell) {
                    return;
                }
                
                // data row
                if(gridCell.rowType === "data") {
                    // if(gridCell.data.OrderDate < new Date(2014, 2, 3)) {
                        // options.font.color = "#AAAAAA";
                    // }
                    
                    // if(gridCell.data.SaleAmount > 15000) {
                        // if(gridCell.column.dataField === "OrderNumber") {
                            // options.font.bold = true;
                        // }
                        // if(gridCell.column.dataField === "SaleAmount") {
                            // options.backgroundColor = "#FFBB00";
                            // options.font.color = "#000";
                        // }
                    // } 
                    // this example will highlight max amount for SaleAmount for each group
                    if(gridCell.column.dataField === "SaleAmount") {
                        // console.log(max);
                        if(options.value === max) {
                            
                            options.backgroundColor = "#FFBB00";
                        }
                    }
                }

                // group summary row
                if(gridCell.rowType === "group") {
                    if(gridCell.groupIndex === 0) {
                        options.backgroundColor = "#BEDFE6";
                    }
                    if(gridCell.groupIndex === 1) {
                        options.backgroundColor = "#C9ECD7";
                    }
                    // if(gridCell.column.dataField === "Employee") {
                        // options.value = gridCell.value + " (" + gridCell.groupSummaryItems[0].value + " items)";
                        // options.font.bold = false;
                    // }
                    // if(gridCell.column.dataField === "SaleAmount") {
                        // options.value = gridCell.groupSummaryItems[0].value;
                        // options.numberFormat = "&quot;Max: &quot;$0.00";
                    // }
                    
                    // to determine max amount for SaleAmount for each group
                    if(gridCell.column.dataField === "SaleAmount") {
                        // console.log(gridCell.groupSummaryItems[0].value);
                        max = gridCell.groupSummaryItems[0].value;                       
                    }
                    
                }
                
                // group footer row
                if(gridCell.rowType === "groupFooter" && gridCell.column.dataField === "SaleAmount") {
                    options.value = options.gridCell.value;
                    options.numberFormat = "&quot;Sum: &quot;$0.00";
                    options.font.italic = true;
                }
                
                // footer row
                if(gridCell.rowType === "totalFooter" && gridCell.column.dataField === "SaleAmount") {
                    options.value = options.gridCell.value;
                    options.numberFormat = "&quot;Total Sum: &quot;$0.00";
                }                    
            }
        } 
      
    }).dxDataGrid("instance");
    
    // group expand checkbox on bottom of page
    $("#autoExpand").dxCheckBox({
        value: true,
        text: "Expand All Groups",
        onValueChanged: function(data) {
            dataGrid.option("grouping.autoExpandAll", data.value);
        }
    });
});