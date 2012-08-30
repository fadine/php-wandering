/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



    function showLoginDialog(){
        if ($("#login_dialog").html()==null){
            var newdiv = document.createElement('div');
            newdiv.id = "login_dialog";
            newdiv.style.display = false;
            $('body').append(newdiv);
            
            $("#login_dialog").load(baseUrl + "auth/show_login/").dialog({modal:true,width: 600});
        }else {
            $( "#login_dialog" ).dialog( {modal:true, width:600} );
        }
        
    }