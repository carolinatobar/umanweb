$(document).ready(function() {
  
  var animating = false,
      submitPhase1 = 1100,
      submitPhase2 = 400,
      logoutPhase1 = 800,
      $login = $(".login"),
      $app = $(".app");
  
  function ripple(elem, e) {
    $(".ripple").remove();
    var elTop = elem.offset().top,
        elLeft = elem.offset().left,
        x = e.pageX - elLeft,
        y = e.pageY - elTop;
    var $ripple = $("<div class='ripple'></div>");
    $ripple.css({top: y, left: x});
    elem.append($ripple);
  };
  
  $(document).on("click", ".login__submit", function(e) {
    if (animating) return;
    animating = true;
    var that = this;
    ripple($(that), e);
    $(that).addClass("processing");
    setTimeout(function() {
      $(that).addClass("success");
      setTimeout(function() {
        $app.show();
        $app.css("top");
        $app.addClass("active");
      }, submitPhase2 - 70);
      setTimeout(function() {
        $login.hide();
        $login.addClass("inactive");
        animating = false;
        $(that).removeClass("success processing");
      }, submitPhase2);
    }, submitPhase1);
    
    console.log('42');
    
    
     var usuariox=$('#user1').val();
     
     console.log('47: usuariox:'+usuariox);
     
     var clavex=$('#pass1').val();
     
     console.log('47: clavex:'+clavex);
     
    	//Ahora buscar con ajax si corresponde a algun usuario:
    	$.ajaxSetup({async:false});
    	//$.ajaxSetup({async:true});
    	
    		$.ajax({
							
							url:   'ajax/login_ajax.php?usr='+usuariox+'&clave='+clavex,
							//url:   '../ajax/login_ajax.php?usr='+usuariox+'&clave='+clavex,
							type:  'post',
								
								success:  function (data) {
								
									var json = eval("(" + data + ")");
									var i=0;
    							
									
									console.log('68');
									
							 		user_asd=json.usuario_asd[i];
							 		
							 		console.log('73');
							 		
							 		
							 		clave_asd=json.clave_asd[i];	
									
									console.log('70: userx:'+user_asd+' clavex:'+clave_asd);								
								
								}
    							
			
			
			
			
			});    	
    
    
    
    
    
    
  });
  
  $(document).on("click", ".app__logout", function(e) {
    if (animating) return;
    $(".ripple").remove();
    animating = true;
    var that = this;
    $(that).addClass("clicked");
    setTimeout(function() {
      $app.removeClass("active");
      $login.show();
      $login.css("top");
      $login.removeClass("inactive");
    }, logoutPhase1 - 120);
    setTimeout(function() {
      $app.hide();
      animating = false;
      $(that).removeClass("clicked");
    }, logoutPhase1);
  });
  
});