<!DOCTYPE html>
<html lang="en">
    <head>
    <meta charset="utf-8">
    <title><?=(strlen($title))?htmlentities($title).' - ':''?><?=$this->getConf("portalTitle")?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=$this->getConf("portalDescription")?>">
    <meta name="author" content="ADNBP Business & IT Solutions.">

    <!-- Bootstrap theme -->
    <link href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/css/bootstrap.min.css" rel="stylesheet" media="screen">

    <style type="text/css">
		body {
		  padding-top: 60px;
		  padding-bottom: 40px;
		}

		.theme-dropdown .dropdown-menu {
		  position: static;
		  display: block;
		  margin-bottom: 20px;
		}

		.theme-showcase > p > .btn {
		  margin: 5px 0;
		}
    </style>

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/js/adnbpcloudframework.js"></script>
    <script>
        _adnbpKeepSession=0;  // Change this milliseconds value >0 to activate keepSession
        keepSession();
    </script>

        <?php if(strlen($this->getConf("GooglePublicAPICredential")) && $this->getConf("GoogleMapsAPI")) {?>
            <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
            <style type="text/css">
              #map-canvas { height: 100% }
            </style>
            <script type="text/javascript"src="https://maps.googleapis.com/maps/api/js?key=<?=$this->getConf("GooglePublicAPICredential")?>&sensor=true"></script>
         <?php } ?>

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-ipad3.png" />
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-iphone4.png" />
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-ipad.png" />
    <link rel="apple-touch-icon-precomposed" href="<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/ico/apple-touch-icon-iphone.png" />
    <link rel="shortcut icon" href="<?=((strlen($this->getConf('favicon')))?$this->getConf('favicon'):$this->getConf('urlPrefix').'/adnbp-public/static/v1/img/ico/favicon.ico')?>">


    </head>
    <body role="document">
     <!-- Fixed navbar -->
    <div class="navbar navbar-inverse <?=$this->getConf("portalNavColor")?> navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Cloud Framework <?=date("Y")?></a>
        </div>
        <div class="navbar-collapse collapse">

           <?php if($this->isAuth()) { ?>
            <p class="navbar-text pull-right">
              Logged in as <?=$this->getAuthUserData("name")?> <a href="?logout=1" class="navbar-link">[log-out]</a>
            </p>
            <?php } ?>
              <ul class="nav navbar-nav">
              <li <?=($this->getConf("pageCode")=="home")?"class='active'":""?>><a href="/CloudFrameWork">Home</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Services <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li <?=($this->getConf("pageCode")=="GeoLocation")?"class='active'":""?>><a href="/CloudFrameWork/GeoLocation">GeoLocation</a></li>
                    <li <?=($this->getConf("pageCode")=="CloudSQL")?"class='active'":""?>><a href="/CloudFrameWork/CloudSQL">CloudSQL/Mysql Access</a></li>
                    <li <?=($this->getConf("pageCode")=="Email")?"class='active'":""?>><a href="/CloudFrameWork/Email">Email Function</a></li>
                    <li <?=($this->getConf("pageCode")=="SMS")?"class='active'":""?>><a href="/CloudFrameWork/SMS">SMS Function</a></li>
                    <li <?=($this->getConf("pageCode")=="File")?"class='active'":""?>><a href="/CloudFrameWork/File">I/O functions</a></li>
                    <li <?=($this->getConf("pageCode")=="oauth")?"class='active'":""?>><a href="/CloudFrameWorkOauth">Testing Oauth</a></li>
                    <li <?=($this->getConf("pageCode")=="TPVSabadell")?"class='active'":""?>><a href="/CloudFrameWorkTPV/SABADELL">TPV Sabadell</a></li>
                    <li <?=($this->getConf("pageCode")=="socialnetworks")?"class='active'":""?>><a href="/socialnetworks">Social Networks Logins</a></li>
                    <li <?=($this->getConf("pageCode")=="pushMessages")?"class='active'":""?>><a href="/pushMessages">Mobile Push Messages</a></li>
                    <li <?=($this->getConf("pageCode")=="datastore")?"class='active'":""?>><a href="/datastore">DataStore Help</a></li>
                  </ul>
            </ul>
            <?php if(!$this->isAuth()) { ?>
           <form class="navbar-form navbar-right" role="form"  method=post>
              <input class="form-control" type="text" placeholder="Email" name='CloudUser'>
              <input class="form-control" type="password" placeholder="Password" name='CloudPassword'>
              <button type="submit" class="btn btn-success">Sign in</button>
            </form>
            <?php } ?>

        </div><!--/.nav-collapse -->
      </div>
    </div>

     <div class="container theme-showcase" role="main">