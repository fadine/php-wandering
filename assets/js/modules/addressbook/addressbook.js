$(function() {


    function checkLength( o, n, min, max ) {
        if ( o.val().length > max || o.val().length < min ) {
            o.addClass( "ui-state-error" );
            updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
            return false;
        } else {
            return true;
        }
    }

    function checkRegexp( o, regexp, n ) {
        if ( !( regexp.test( o.val() ) ) ) {
            o.addClass( "ui-state-error" );
            updateTips( n );
            return false;
        } else {
            return true;
        }
    }
		
    $( "#divEditCat" ).dialog({
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        buttons: {
            "Save": function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( email, "email", 6, 80 );

                if ( bValid ) {
                    //TODO
                    
                    $( this ).dialog( "close" );
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $( "#btnAddCat" )
    .button()
    .click(function() {
        $( "#divEditCat" ).dialog( "open" );
    });
    
    
    
    
    
    
    
            
    $( "#addressbook_left" ).resizable({
            //alsoResize: "#addressbook_right",

            resize: function(event, ui) {         
                    ui.size.height = ui.originalSize.height;
                    myDeltaOriginal = ui.size.width  - leftWidthOriginal;
                    $( "#addressbook_right" ).width(rightWidthOriginal - myDeltaOriginal) ;
            }

    });
    /*
    $( "#addressbook_right" ).resizable({
            resize: function(event, ui) {         
                    ui.size.height = ui.originalSize.height;     
                    ui.size.width = ui.originalSize.width + myDeltaOriginal;
            }
    });
    */

   var catIds = "";
   $( "#selectable" ).selectable({
            stop: function() {
                catIds = "";
                $( ".ui-selected", this ).each(function() {
                    var text = this.id;
                    if(text.length > 4) {
                        text = text.substring(4, text.length);
                        catIds = catIds + (catIds!=""?"-":"") + text;
                    }
                });
                showBooksOfCats();
            }
    });

    
    
    
    function showBooksOfCats(){
        $("#addressbook_book_content").html("");
        $.post(baseUrl + "addressbook/get_books/", {
            catids : catIds
        },
        function(data){
            //alert(data);
            jsonData = $.parseJSON(data);
            alert(JSON.stringify(jsonData.return_arr));
            if (jsonData.return_arr!=null && jsonData.return_arr != false) {
                var htmlApp = "";
                $.each(jsonData.return_arr, function(i, obj) {
                    //alert(JSON.stringify(obj));
                    htmlApp += "<div class = 'data_row'>" + obj.addr_first_name + "</div>";
                });

                $("#addressbook_book_content").html(htmlApp);
            }


        })
    }
    
    
    
    function showAddressBookDialog(){
        if ($("#addressbook_dialog").html()==null){
            var newdiv = document.createElement('div');
            newdiv.id = "addressbook_dialog";
            newdiv.style.display = false;
            $('body').append(newdiv);
            
            $("#addressbook_dialog").load(baseUrl + "addressbook/show_addressbook/").dialog({modal:true,width: 600});
        }else {
            $( "#addressbook_dialog" ).dialog( {modal:true, width:600} );
        }
        
    }
    
    
    
    
    
    //Test
    $( "#show_address" )
    .button()
    .click(function() {
        showAddressBookDialog();
    });
    
    
});