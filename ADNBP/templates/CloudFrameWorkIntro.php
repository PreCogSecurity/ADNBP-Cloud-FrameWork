
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <!-- ADNBP Site version: 0.2 (pre-release) -->
        <!--
                :::     :::::::::  ::::    ::: :::::::::  :::::::::  
              :+: :+:   :+:    :+: :+:+:   :+: :+:    :+: :+:    :+: 
             +:+   +:+  +:+    +:+ :+:+:+  +:+ +:+    +:+ +:+    +:+ 
            +#++:++#++: +#+    +:+ +#+ +:+ +#+ +#++:++#+  +#++:++#+  
            +#+     +#+ +#+    +#+ +#+  +#+#+# +#+    +#+ +#+        
            #+#     #+# #+#    #+# #+#   #+#+# #+#    #+# #+#        
            ###     ### #########  ###    #### #########  ###               
        -->
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>ADNBP Business Performance | Welcome</title>
        
        <link rel="shortcut icon" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/favicon.ico">  
        <link rel="apple-touch-icon" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/apple-touch-icon-iphone.png" /> 
        <link rel="apple-touch-icon" sizes="72x72" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-ipad.png" /> 
        <link rel="apple-touch-icon" sizes="114x114" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-iphone4.png" />
        <link rel="apple-touch-icon" sizes="144x144" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-ipad3.png" />    
            
        <link rel="stylesheet" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/css/foundation.css" />
        <link rel="stylesheet" type="text/css" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/css/jquery.vegas.css" />
        <link rel="stylesheet" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/css/adnbp.css">
        <link rel="stylesheet" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/css/animate.css">
            
        <script src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/modernizr.js"></script>
    </head>
  
<body id="home">
    
            <section class="section-home">  
                <div class="row">
                    <div class="large-12 columns text-center  animated fadeInDownBig"  style="margin-bottom:-50px;">
                        <img class="logo" src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/logo.png"><br />
                        <a href="/CloudFrameWork">[Go to ADNBP Cloud FrameWork <?=$this->version();?>]</a> 
                    </div>
                </div>
            </section>          
    
    <script src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/jquery.js"></script>
    <script src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/foundation.min.js"></script>
    <!--
    <script src="js/foundation/foundation.js"></script>
    <script src="js/foundation/foundation.topbar.js"></script>
    <script src="js/foundation/foundation.offcanvas.js"></script>
    <script src="js/foundation/foundation.tab.js"></script>
    -->
    <script>
      $(document).foundation();
      $('ul.off-canvas-list li a').click(function(e){
        $('.off-canvas-wrap').removeClass('move-right');
    });
    </script>
    <script type="text/javascript" src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/jquery.vegas.js"></script>
    
    <script type="text/javascript">
        var _urlPrefix = '<?=$this->getConf('urlPrefix')?>';
        $.vegas('slideshow', {
            delay:10000,
          backgrounds:[
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg0.jpg', fade:1000 },
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg1.jpg', fade:1000 },
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg2.jpg', fade:1000 },
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg3.jpg', fade:1000 },
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg4.jpg', fade:1000 },
            { src:_urlPrefix+'/adnbp-public/static/v1/img/bg5.jpg', fade:1000 }
          ]
        })('overlay', {
          /** src:'/overlays/05.png' **/
        });
        
        $( "#target" ).click(function() {
          $.vegas('jump', 1);
        });
        
        $("#buttons a").click(function() {
            var id = $(this).attr("id");
            var idd = $(this).attr("id")-6;
            $("#buttons a").css("opacity", "0.4");
            $("#buttons a#" + id + "").css("opacity", "1");
            $.vegas('jump', idd);
        });
        
        $('body').bind('vegaswalk',
          function(e, bg, step) {
            var step6 = step+6;
            $("#pages div").css("display", "none");
            $("#pages div#" +step+ "").css("display", "block");
            $("#buttons a").css("opacity", "0.4");
            $("#buttons a#" + step6 + "").css("opacity", "1");
          }
        );
        
        /** inserta video o no 
                
        $('body').bind('vegascomplete', 
          function(e, bg, step) {
            var img = $(bg).attr('src');
            if (step == 1){
                $("#vid1").css("display", "none");
            
                
            } else {
                $("#vid1").css("display", "none");  
                
            }
          }
        );  
        **/
</script>

    <!-- JS para Scroll suave desde .mainmenu -->
    <script type="text/javascript">
        $(function () {
            $('a[href*=#]:not([href=#])').click(function () {
                if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {

                    var target = $(this.hash);
                    target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                    if (target.length) {
                        $('html,body').animate({
                            scrollTop: target.offset().top
                        }, 1000);
                        return false;
                    }
                }
            });
        });
    </script>
    <!-- FIN JS para Scroll suave desde .mainmenu -->
<script type="text/javascript" src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/formy.js"></script>
<script type="text/javascript" src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/jquery.simplyscroll.min.js"></script>

  </body>
</html>