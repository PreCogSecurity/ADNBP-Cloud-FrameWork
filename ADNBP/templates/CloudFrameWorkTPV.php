<?php if(!strlen($service)) {?>
    <div class="jumbotron">
    <h1>TPV Integration</h1>
    <b>The following TPVs, have been tested:</b>
    <ul>
        <li><a href='CloudFrameWorkTPV/SABADELL'>Sabadell May 2014</a>
    </ul>
    </div>
        
<?php } else if($service=='SABADELL') {?>
	<?php if( !strlen($this->getConf("ADNBPTPV_MerchantId"))
	         || !strlen($this->getConf("ADNBPTPV_MerchantSecret"))
	         || !strlen($this->getConf("ADNBPTPV_ProductCurrency"))
	         || !strlen($this->getConf("ADNBPTPV_Production")) ) { ?>

    <div class="jumbotron">
    <h1>BancSabadell Integration</h1>
    <p>The following implementation follows the requirements that you cand find in 
        <a hreh='https://drive.google.com/file/d/0B5Hz2q4YesjSLVFmZzhDVEFrQlAxakhFNzdIV0xwU01kM1dz/edit?usp=sharing' target_blank>Sabadell DOC in Mat 2014.</a> 
        You can use the ADNBP Payment System Built with <a href='https://www.bancsabadell.com/cs/Satellite/SabAtl/Terminales-punto-de-venta-(TPV)/1191332198568/en/' target=_blank>Sabadell Technology</a>
        
        or you can manage your own Sabadell TPV clicking on the follow links: Development(<a href='https://sis-t.redsys.es:25443/canales/' target='_blank'>https://sis-t.redsys.es:25443/canales/</a>)
        , Production (<a href='https://sis.redsys.es/canales/' target='_blank'>https://sis.redsys.es/canales/</a>)
     </p>
     <p>For Development Testing use the following fake credit card: Credit Card: 4548812049400004 Expire:12/12 CVV2:123 123456 
     </p>
     <p>You can use ADNBP Cloud TPV Services poiting: <pre> $this->setConf("CloudServiceUrl","http://cloud.adnbp.com/CloudFrameWorkService");</pre>
     </p>
     <p>Or you implement your own personal Services in other location. You only need to know how to create it.
        to do that, change the CloudServiceUrl config var to the URL where you are implementing it.
     </p>

     <h2>CloudServiceUrl is: <a href='<?=$this->getCloudServiceURL()?>' target='_blank'><?=$this->getCloudServiceURL()?></a></h2></p>
     <?php if(strlen($warning)) { ?>
     	<pre><?=htmlentities($warning)?></pre>
     <?php } ?>
     </div>
     <div class="jumbotron">
     <div class="row">
          <div class="col-lg-5">
          <h2>I don't have a Sabadell TPV activated</h2>
          <p>Good.. no problem.<br/> We will provide you everything to faciliate sellings and 
              payments processing for your company.
          </p>
          <p>You need to be signed-up in <a href='http://cloud.adnbp.com' target=_blank>ADNBP Cloud Services</a>, and to have activated Instant Check-Out Services. 
              If yes, just provide only 2 fields:</p>
          <form action='#init' method='post'>
          <p>
          <div class="input-group">
           <input type='hidden' name='type' value='ADNBPTPV'>
           <?php if(strlen($this->getConf("ADNBPTPV_MerchantId"))) echo "ADNBPTPV_MerchantId configured";
				 else {?>
           <input type="text" name='ADNBPTPV_MerchantId' class="form-control" placeholder="ADNBPTPV_MerchantId" value='<?=htmlentities($_data[ADNBPTPV_MerchantId])?>'>    
           <?php } ?>
           <?php if(strlen($this->getConf("ADNBPTPV_MerchantSecret"))) echo "ADNBPTPV_MerchantSecret configured";
				 else {?>
           <input type="text" name='ADNBPTPV_MerchantSecret' class="form-control" placeholder="ADNBPTPV_MerchantSecret"  value='<?=htmlentities($_data[ADNBPTPV_MerchantSecret])?>'>    
           <?php } ?>
           <?php if(strlen($this->getConf("ADNBPTPV_ProductCurrency"))) echo "ADNBPTPV_ProductCurrency configured";
				 else {?>
          	 	<select  class="form-control" id='ADNBPTPV_ProductCurrency' name='ADNBPTPV_ProductCurrency'>
            	<?php foreach ($ADNBPTPV_ProductCurrency as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[ADNBPTPV_ProductCurrency]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
           <?php } ?>
           <?php if(strlen($this->getConf("ADNBPTPV_Production"))) echo "ADNBPTPV_Production configured";
				 else {?>
          </div>
          <div class="radio">
           <label  class="checkbox-inline">
           <input type="checkbox" name='ADNBPTPV_Production' id='ADNBPTPV_Production'  value='1' <?=($_data[ADNBPTPV_Production]=='1')?'checked':''?>>  
           Use Production
           </label> 
           <?php } ?>
          </div>    

          </p>
          <p><input type="submit" class="btn btn-lg btn-primary" value='Start Here' /></p>
          </form>
          </div>  
          <div class="col-lg-5">
          <h2>I DO have a Sabadell TPV</h2>
          <p>Let's start then. Please, check if you have received the following information from your bank:</p>
          <p><ul>
              <li>Dev.&Prod. User/Password TPV managment</li>
              <li>Dev.&Prod. URL to send the payments Process</li>
              <li>Your Merchant Code</li>
              <li>The <b>Personal Secret Word</b> that you will use to hashing verification.</li>
          </ul>
          <p>If you have all this information we can show you the basics to create your own TPV. 
          </p>
          <form action='#init' method='post'>
          <input type='hidden' name='type' value='BANCSABADELLTPV'>
          <p>
          <div class="input-group">
           <input type="text" name='TPV_URL' class="form-control" placeholder="TPV_URL"  value='<?=htmlentities($_data[TPV_URL])?>'>    
           <input type="text" name='TPV_Secret' class="form-control" placeholder="TPV_Secret"  value='<?=htmlentities($_data[TPV_Secret])?>'>    
           <input type="text" name='Ds_Merchant_MerchantCode' class="form-control" placeholder="Ds_Merchant_MerchantCode"  value='<?=htmlentities($_data[Ds_Merchant_MerchantCode])?>'>    
          </div>    
          </p>
          <p> 
          <p><input type="submit" class="btn btn-lg btn-primary" value='Start Here' /></p>
          </p>
          </form>
          </div>             
     </div>
     You can store the default values for the variables below in configVars. Ex. in {yourapp}/config/config.php: 
     <pre>$this->setConfig("TPV_URL","https://sis-t.redsys.es:25443/sis/realizarPago");
     </div>
     
     <?php } ?>
    
     <a name='init'></a>
     <?php if($_data[type] == 'ADNBPTPV') include dirname(__FILE__).'/CloudFrameWorkTPV/ADNBPTPV.php';
           else  if($_data[type] == 'BANCSABADELLTPV') include dirname(__FILE__).'/CloudFrameWorkTPV/BancSabadell.php';
     ?>
   
 <?php } ?>
