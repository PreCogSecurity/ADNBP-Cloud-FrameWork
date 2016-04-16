<div class="jumbotron">
	<h1>ADNBP TPV CloudServices</h1>
	<h3>In partnership with <a href='https://www.bancsabadell.com/cs/Satellite/SabAtl/Terminales-punto-de-venta-(TPV)/1191332198568/en/' target=_blank >BancSabadell TPV</a>. The perfect Team.</h3>
    
          <?php if(strlen($errForm)) {?>
          	
          	<pre>Ups.. Is not posible because: <?=htmlentities($errForm)?></pre>
          	
          <?php } else {?>
          	
          	     <div class="row">
          <div class="col-lg-5">
	          <form action='#init' method='post'>
          	  <input type='hidden' name='type' value='ADNBPTPV'>
          	  <input type='hidden' name='initTransaction' value='1'>
          	  <input type="hidden" name='ADNBPTPV_MerchantId' class="form-control" placeholder="ADNBPTPV_MerchantId"  value='<?=htmlentities($_data[ADNBPTPV_MerchantId])?>'>    
          	  <input type="hidden" name='ADNBPTPV_Production' class="form-control" placeholder="ADNBPTPV_Production"  value='<?=($_data[ADNBPTPV_Production]=='1')?'1':'0'?>'>    
              <input type="hidden" name='ADNBPTPV_MerchantSecret' class="form-control" placeholder="ADNBPTPV_MerchantSecret"  value='<?=htmlentities($_data[ADNBPTPV_MerchantSecret])?>'>    
              <input type="hidden" name='ADNBPTPV_ProductCurrency' class="form-control" placeholder="ADNBPTPV_ProductCurrency"  value='<?=htmlentities($_data[ADNBPTPV_ProductCurrency])?>'>    
          	  
          	  <div class="panel panel-default">
          	  <div class="panel-heading"> <h2><?=htmlentities($TPV_merchant[Name])?><img width='90' src='<?=$TPV_merchant[Photo]?>' alt="<?=htmlentities($TPV_merchant[Name])?>" class="img-circle navbar-right" ></h2></div>
  			  <div class="panel-body">
	          <?php if(strlen($TPV_merchant[Photo])) {?>
	          	
		      <?php } ?>

	         <div class="input-group">
	          <label for="TPV_ProductId" >TPV_ProductId: </label>
            <select class="form-control" name='TPV_ProductId'>
            <?php foreach ($TPV_products as $key => $value) {?>
                  <option  value='<?=$value[CRMProduct_Id]?>' <?=($_data[TPV_ProductId]==$value[CRMProduct_Id])?'selected':''?>><?=$value[CRMProduct_Name]?> - <?=$value[CRMProduct_Price]?> <?=$value[CRMProduct_Currency]?></option>
            <?php } ?>        
            </select>
            <br>TPV_ProductUnits:  <input size='3' class="form-control" type=text name=TPV_ProductUnits value='<?=(strlen($_data[TPV_ProductUnits]))?htmlentities($_data[TPV_ProductUnits]):1?>'>

	          </div><br/>
	          </div>
	          </div>
		      

		      <div class="panel panel-default">
          	  <div class="panel-heading"> <h2>About who is going to pay (client)</h2></div>
  			  <div class="panel-body">
  			  	  			  	  
	         <div class="input-group">
		       <?php if($this->getConf("TPV_requireAuth") && !strlen($this->getConf("TPV_ClientId"))) {?>
		       <a href='/CloudFrameWorkOauth/google?ret=<?=urlencode($_SERVER['REQUEST_URI'])?>'><img src ='<?=$this->getConf('urlPrefix')?>/adnbp-public/static/v1/img/sign-in-with-google.png' alt='Google Auth' width=220 ></a>	
			   <?php } else { ?>
	       	
           <?php if(!strlen($this->getConf("TPV_ClientId")))  {?>
              <input type=hidden  class="form-control" id=TPV_ClientId name=TPV_ClientId placeholder='Client Id' value='<?=htmlentities($_data[TPV_ClientId])?>'>	
		   <?php } ?>
	          <label for="TPV_ClientName" >TPV_ClientName: </label>
           <?php if(strlen($this->getConf("TPV_ClientName"))) echo htmlentities($_data[TPV_ClientName]);
				 else {?>
              <input type=text  class="form-control" id=TPV_ClientName name=TPV_ClientName placeholder='Client Name' value='<?=htmlentities($_data[TPV_ClientName])?>'>	
		   <?php } ?>
	          </div><br/>
	         <div class="input-group">
	          <label for="TPV_ClientName" >TPV_ClientEmail: </label>
           <?php if(strlen($this->getConf("TPV_ClientEmail"))) echo htmlentities($_data[TPV_ClientEmail]);
				 else {?>
              <input type=text  class="form-control" id=TPV_ClientEmail name=TPV_ClientEmail placeholder='Client Email' value='<?=htmlentities($_data[TPV_ClientEmail])?>'>	
		   <?php } ?>
	          </div><br/>
	         <div class="input-group">
	         <label for="TPV_ConsumerLanguage" >TPV_ConsumerLanguage: </label>
           <?php if(strlen($this->getConf("TPV_ConsumerLanguage"))) echo $Ds_Merchant_ConsumerLanguage[$_data[TPV_ConsumerLanguage]];
				 else {?>          	 		
          	 	<select  class="form-control" id='TPV_ConsumerLanguage' name='TPV_ConsumerLanguage'>
            	<?php foreach ($Ds_Merchant_ConsumerLanguage as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[TPV_ConsumerLanguage]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
            	<?php } ?>
              </div>
              <br/>
            
	          <p><input type="submit" class="btn btn-lg btn-primary" value='Go to step 2' /></p>
	          
	   <?php } ?>
	         </div>
	         </div>
	          
	          </form>
          </div>
          <div class="col-lg-5">
          <?php if($_data[initTransaction]=='1') {?>
          <?php if(strlen($errMsg)) {?>
          	    <pre>Ups.. Is not posible because: <?=htmlentities($errMsg)?></pre>
          <?php } else {?>
            	          	
         <form action='<?=htmlentities($_dataTransaction[TPV_URL])?>' method ='post' target='tpv'>
         <?=$_dataHTML?>
         
         
     	  <div class="panel panel-default">
      	  <div class="panel-heading"> <h2>Confirm Payment</h2></div>
		  <div class="panel-body">          	
	      	  <div class="input-group">
	          <label ><?=htmlentities($_dataTransaction[Ds_Merchant_ProductDescription])?> </label>
	          <h3>TOTAL:  <b><?=substr(htmlentities($_dataTransaction[Ds_Merchant_Amount]),0,-2)?>.<?=substr(htmlentities($_dataTransaction[Ds_Merchant_Amount]),-2)?> 
	          	         (<?=$Ds_Merchant_Currency[$_dataTransaction[Ds_Merchant_Currency]]?>)
	          </h3>
	          <p>Your order number is: <b><?=htmlentities($_dataTransaction[Ds_Merchant_Order])?></b></p>
	          </div>      	 
      	 </div>
      	 </div>
      	 
     	  <div class="panel panel-default">
      	  <div class="panel-heading"> <p><input type="submit" class="btn btn-lg btn-primary" value='Init Payment' /></p></div>
		  <div class="panel-body">          	
			<iframe id=tpv name='tpv' width="100%" height="400"></iframe> 
      	 </div>
      	 </div>          	
         </form>

 <?php if($_GET[debug]==1) {?>
         <h2>Debug Data.</h2>
         <h3><?=htmlentities($_dataTransaction[TPV_URL])?></h3>
         <p>Using the language of 
         <b><?=$Ds_Merchant_ConsumerLanguage[$_dataTransaction[Ds_Merchant_ConsumerLanguage]]?></b> wants to by:
         <ul>
             <li><b><?=htmlentities($_dataTransaction[Ds_Merchant_ProductDescription])?></b> 
             <li>With a total cost of 
                 <b><?=substr(htmlentities($_dataTransaction[Ds_Merchant_Amount]),0,-2)?>.<?=substr(htmlentities($_dataTransaction[Ds_Merchant_Amount]),-2)?> (<?=$Ds_Merchant_Currency[$_dataTransaction[Ds_Merchant_Currency]]?>)</b>
             <li>In a <b><?=$Ds_Merchant_TransactionType[$_dataTransaction[Ds_Merchant_TransactionType]]?></b> Type of transaction.</li>
             <li>And assigned Order: <b><?=htmlentities($_dataTransaction[Ds_Merchant_Order])?></b></li>
         </ul>
         </p>
         <p>The Store <b><?=htmlentities($_dataTransaction[Ds_Merchant_MerchantCode])?></b> is selling products under the brand: 
             <b><?=htmlentities($_dataTransaction[Ds_Merchant_MerchantName])?></b> for the Client: <b><?=htmlentities($_data[Ds_Merchant_Titular])?></b>
         </p>
         
         <p>
          - Type of Terminal: <b><?=htmlentities($_dataTransaction[Ds_Merchant_Terminal])?></b>
          <br>- Callback: <b><?=htmlentities($_dataTransaction[Ds_Merchant_MerchantURL])?></b>
          <br>- OK Page: <b><?=htmlentities($_dataTransaction[Ds_Merchant_UrlOK])?></b>
          <br>- KO Page: <b><?=htmlentities($_dataTransaction[Ds_Merchant_UrlKO])?></b>
            
         </p>
         <p>This purchase is signed with '<b><?=htmlentities($_dataTransaction[Ds_Merchant_MerchantSignature])?></b>' secret based code 
            and <?=(strlen($_dataTransaction[Ds_Merchant_MerchantData]))?'<b>'.htmlentities($_dataTransaction[Ds_Merchant_MerchantData]).'</b>':'no extra '?> MerchantData
         </p>
         <h2>TPV</h2>
   		  
    	<p>    <b>SOPORTE TECNICO</b><br/>
        De lunes a domingo de 8 h a 22 h
        Teléfono: 902 365 650 (opc. 2)
        Correo electrónico:
        tpvvirtual@bancsabadell.com
        <br/> 
        incidencias sobre comunicaciones, inestabilidad del  sistema y similares <br/>
        teléfono 902 198 747, en activo las 24 horas</p>       
          <?php }?>
          <?php }?>
<?php } ?>
          </div>
     </div>	
     <?php } ?>
</div>