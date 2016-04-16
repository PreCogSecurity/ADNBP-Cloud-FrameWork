    <h1>OAUTH Login</h1>
    <b>config/config.php</b>
    <pre>
    $this->setConf("AllowOauth",true);
     // GOOGLE CREDENTIALS: https://console.developers.google.com/project/apps~{YOUR-PROJECT}/apiui/credential
    $this->setConf("GoogleOauth",true); // To allow Google auth 
        $this->setConf("GoogleOauth_CLIENTE_ID",'TAKE FROM CREDENTIALS' ); // CLIENT_ID
        $this->setConf("GoogleOauth_CLIENTE_SECRET",'TAKE FROM CREDENTIALS'); // CLIENTE_SECRET
    
     // FACEBOOK CREDENTIALS: https://developers.facebook.com/x/apps/{ID}/dashboard/      
    $this->setConf("FacebookOauth",true); // To allow FaceBook auth 
        $this->setConf("FacebookOauth_APP_ID",'TAKE FROM CREDENTIALS' ); // APP_ID
        $this->setConf("FacebookOauth_APP_SECRET",'TAKE FROM CREDENTIALS'); // APP_SECRET
        
    $this->setConf("TwitterOauth",true); // To allow Twitter auth 
    </pre>
    <b>$this->getConf("AllowOauth") == <?=($this->getConf("AllowOauth"))?"true":"false"?></b>
    
    <?php if($this->getConf("AllowOauth")) {?>
    <p>Log in with: The Callback will assign in <b>opauth</b> session all the information, and the framework stores it
        <b>$this->setAuthUserData("opauth",$_SESSION[opauth]).</b></p>
    <ul>
        <?php if($this->getConf("FacebookOauth",true)) {?>
        <li><a href="CloudFrameWorkOauth/facebook">Facebook</a>
        <br> APP_ID: <?=(strlen($this->getConf("FacebookOauth_APP_ID")))?$this->getConf("FacebookOauth_APP_ID"):"missing. It is required"?>
        <br> APP_SECRET: <?=(strlen($this->getConf("FacebookOauth_APP_SECRET")))?$this->getConf("FacebookOauth_APP_SECRET"):"missing. It is required"?>
        <?php } ?>
        <?php if($this->getConf("GoogleOauth",true)) {?>
        <li><a href="CloudFrameWorkOauth/google">Google</a>
        <br> CLIENT_ID: <?=(strlen($this->getConf("GoogleOauth_CLIENT_ID")))?$this->getConf("GoogleOauth_CLIENT_ID"):"missing. It is required"?>
        <br> CLIENT_SECRET: <?=(strlen($this->getConf("GoogleOauth_CLIENT_SECRET")))?$this->getConf("GoogleOauth_CLIENT_SECRET"):"missing. It is required"?>
        <br> The <b>Authorized JavaScript origins</b> has to be: <?=((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']=='on')?'https':'http').'://'.$_SERVER['SERVER_NAME']?>
        <br> The <b>Authorized redirect URI</b> has to be: <?=((array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS']=='on')?'https':'http').'://'.$_SERVER['HTTP_HOST']?>/CloudFrameWorkOauth/google/oauth2callback
        <?php } ?>
        <?php if($this->getConf("TwitterOauth",true)) {?>
        <li><a href="CloudFrameWorkOauth/twitter">Twitter</a></li>
        <?php } ?>
    </ul>
    <?php } ?>
    <b>$this->getAuthUserNameSpace()</b>
    <pre>
        <?=print_r($this->getAuthUserNameSpace())?>
        <?=print_r($_SESSION["opauth"])?>
    </pre>