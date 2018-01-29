<!DOCTYPE html>
<html>
 <head>
  <meta charset="UTF-8" />
  <title>UmanWeb</title>
  <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes" />
  <link rel="stylesheet prefetch" href="http://fonts.googleapis.com/css?family=Open+Sans" />
  <link rel="stylesheet" href="<?=$__DIR__?>css/login.css" />
  <script type="text/javascript" src="<?=$__DIR__?>jqwidgets/scripts/jquery-1.11.1.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$__DIR__?>css/bootstrap.min.css">
  <script src="<?=$__DIR__?>sweetalert/sweetalert.min.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$__DIR__?>sweetalert/sweetalert.css" />
 </head>
 <body style="background-image:url(<?=$__DIR__?>img/bg_login.jpg); background-size:cover;">
  
  <div class="container">
    <br/>
   <div class="panel panel-danger clearfix" style="width:50%; margin:5% 25%;">
    <div class="panel-heading"><h3 class="bg-danger"><center><?=$__TITLE__?></center></h3></div>
     <div class="panel-body">      
      <?=$__CONTENT__?>
     </div>
    </div>
   </div>
  </div>

  <footer>
  <?=$__FOOTER__?>
  </footer>
    <!-- <div class="panel panel-info clearfix" style="width:50%; margin:5% 25%;">
      <div class="panel-body text-center">      
        <a href="{server}" class="btn btn-info">{server}</a>
      </div>
    </div> -->
  <script type="text/javascript">
    // EVITAR goBack() EN BROWSER
      (function (global) {
        if(typeof (global) === "undefined")
        {
          throw new Error("window is undefined");
        }

          var _hash = "!";
          var noBackPlease = function () {
              global.location.href += "#";

          // making sure we have the fruit available for juice....
          // 50 milliseconds for just once do not cost much (^__^)
              global.setTimeout(function () {
                  global.location.href += "!";
              }, 50);
          };
        
        // Earlier we had setInerval here....
          global.onhashchange = function () {
              if (global.location.hash !== _hash) {
                  global.location.hash = _hash;
              }
          };

          global.onload = function () {
            noBackPlease();

            // disables backspace on page except on input fields and textarea..
            document.body.onkeydown = function (e) {
              var elm = e.target.nodeName.toLowerCase();
              if (e.which === 8 && (elm !== 'input' && elm  !== 'textarea')) {
                e.preventDefault();
              }
              // stopping event bubbling up the DOM tree..
              e.stopPropagation();
            };
          };

      })(window);
  </script>
 </body>
</html>