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
//                allFields.removeClass( "ui-state-error" );
//                bValid = bValid && checkLength( email, "email", 6, 80 );

                

                if ( bValid ) {
                    //TODO
                    
                    var $form = $("#formEditCat");
                    $inputs = $form.find("input, select, button, textarea");
                    serializedData = $form.serialize();
                    $inputs.attr("disabled", "disabled");

                    mUrl = baseUrl + "bookfeed/commit_cat/";
                    
                    alert(JSON.stringify(serializedData));
                    $.ajax({
                        url: mUrl,
                        type: "post",
                        data: serializedData,
                        // callback handler that will be called on success
                        success: function(response, textStatus, jqXHR){
                            // log a message to the console
                            console.log("Hooray, it worked!");
                            $( this ).dialog( "close" );
                        },
                        // callback handler that will be called on error
                        error: function(jqXHR, textStatus, errorThrown){
                            // log the error to the console
                            console.log(
                                "The following error occured: "+
                                textStatus, errorThrown
                            );
                        },
                        // callback handler that will be called on completion
                        // which means, either on success or error
                        complete: function(){
                            // enable the inputs
                            $inputs.removeAttr("disabled");
                        }
                    });

                    
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
//            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });
    
    function catFormReset() {
        $("#ebookcat_id").val("");
        $("#ebookcat_name").val("");
        $("#ebookcat_description").val("");
        $("#ebookcat_language").val("0");
    }

    $( "#btnAddCat" )
    .button()
    .click(function() {
        catFormReset();
        $( "#divEditCat" ).dialog( "open" );
    });
    
    
    
    function catGetCatInfor(cat_id) {
        mUrl = baseUrl + "bookfeed/get_cat/catid/"+cat_id;
        $.ajax({
          url: mUrl,
          dataType:"json",
          beforeSend: function ( xhr ) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
          }
        }).done(function ( data ) {
          if (data.return_arr==true) {
                $("#ebookcat_id").val(data.data.ebookcat_id);
                $("#ebookcat_name").val(data.data.ebookcat_name);
                $("#ebookcat_description").val(data.data.ebookcat_description);
                $("#ebookcat_language").val(data.data.ebookcat_language);
                $( "#divEditCat" ).dialog( "open" );
          }else {
              alert("Error in get data");
          }
        });
    }
    
    $( "#btnEditCat" )
    .button()
    .click(function() {
        
    
        var text =  $( ".ui-selected", $( "#selectable" ) ).get(0).id;
            
//            var text = this.id;
        if(text!=undefined && text!=null && text.length > 4) {
            cat_id = text.substring(4, text.length);
            catGetCatInfor(cat_id);
        }
        
        
    
    });
    
    
    
            
    $( "#bookfeed_left" ).resizable({
            //alsoResize: "#bookfeed_right",

            resize: function(event, ui) {         
                    ui.size.height = ui.originalSize.height;
                    myDeltaOriginal = ui.size.width  - leftWidthOriginal;
                    $( "#bookfeed_right" ).width(rightWidthOriginal - myDeltaOriginal) ;
            }

    });
    /*
    $( "#bookfeed_right" ).resizable({
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
        $("#bookfeed_book_content").html("");
        $.post(baseUrl + "bookfeed/get_books/", {
            catids : catIds
        },
        function(data){
            //alert(data);
            jsonData = $.parseJSON(data);
            alert(JSON.stringify(jsonData.return_arr));
            if (jsonData.return_arr!=null && jsonData.return_arr != false) {
                var htmlApp = "<ol id='selectable2'>";
                $.each(jsonData.return_arr, function(i, obj) {
                    //alert(JSON.stringify(obj));
                    htmlApp += "<div id = '"+obj.ebook_id+"' class = 'data_row'>" + obj.ebook_name + "</div>";
                });
                htmlApp += "</ol>";
                $("#bookfeed_book_content").html(htmlApp);
//                $("#selectable2").html(htmlApp);
            }


        })
    }
    
    
    
    function showbookfeedDialog(){
        if ($("#bookfeed_dialog").html()==null){
            var newdiv = document.createElement('div');
            newdiv.id = "bookfeed_dialog";
            newdiv.style.display = false;
            $('body').append(newdiv);
            
            $("#bookfeed_dialog").load(baseUrl + "bookfeed/show_bookfeed/").dialog({modal:true,width: 600});
        }else {
            $( "#bookfeed_dialog" ).dialog( {modal:true, width:600} );
        }
        
        
         
        
    }
    
    
    function bookFormReset() {
        $("#ebook_id").val("");
        $("#ebook_cat").val("0");
        $("#ebook_name").val("");
        $("#ebook_description").val("");
        $("#ebook_auth_name").val("");
        $("#ebook_isbn").val("");
        $("#ebook_publisher").val("");
        $("#ebook_issued").val("");
    }
    
    
    function bookGetCatInfor(book_id) {
        mUrl = baseUrl + "bookfeed/get_book/bookid/"+book_id;
        $.ajax({
          url: mUrl,
          dataType:"json",
          beforeSend: function ( xhr ) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
          }
        }).done(function ( data ) {
          if (data.return_arr==true) {
                $("#ebook_id").val(data.data.ebook_id);
                $("#ebook_cat").val(data.data.ebook_cat);
                $("#ebook_name").val(data.data.ebook_name);
                $("#ebook_description").val(data.data.ebook_description);
                
                $("#ebook_auth_name").val(data.data.ebook_auth_name);
                $("#ebook_isbn").val(data.data.ebook_isbn);
                $("#ebook_publisher").val(data.data.ebook_publisher);
                $("#ebook_issued").val(data.data.ebook_issued);
                
                $( "#divEditEbook" ).dialog( "open" );
          }else {
              alert("Error in get data");
          }
        });
    }
    
    
    $( "#btnEditBook" )
    .button()
    .click(function() {
        var text =  $( ".ui-selected", $( "#selectable2" ) ).get(0).id;
    
        if(text!=undefined && text!=null && text.length > 5) {
            book_id = text.substring(5, text.length);
            bookGetCatInfor(book_id);
        }
        
        
    
    });
    
    
    
    $( "#selectable2" ).selectable({
            stop: function() {
                catIds = "";
                //Do nothing
            }
    });
    
    
    $(document).ready(function() { 
        // bind 'myForm' and provide a simple callback function 
        $('#formEditEbook').ajaxForm(function() { 
            alert("Thank you for your comment!"); 
        }); 
    }); 
    
    
    
    
    $( "#divEditEbook" ).dialog({
        autoOpen: false,
        height: 500,
        width: 500,
        modal: true,
        close: function() {
//            allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });

    $( "#btnAddBook" )
    .button()
    .click(function() {
        bookFormReset();
        $( "#divEditEbook" ).dialog( "open" );
    });
    
    
    
    
    //Test
    $( "#show_address" )
    .button()
    .click(function() {
        showbookfeedDialog();
    });
    
    
    
    
    
    
    
    
    
});