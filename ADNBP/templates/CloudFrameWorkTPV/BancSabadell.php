<div class="jumbotron">
	<h1>BancSabadell TPV</h1>
     <div class="row">
          <div class="col-lg-5">
	          <form action='#init' method='post'>
          	  <input type='hidden' name='type' value='BANCSABADELLTPV'>
          	  <input type='hidden' name='initTransaction' value='1'>
              <input type="hidden" name='TPV_URL' class="form-control" placeholder="TPV_URL"  value='<?=htmlentities($_data[TPV_URL])?>'>    
          	  <input type="hidden" name='TPV_Secret' class="form-control" placeholder="TPV_Secret"  value='<?=htmlentities($_data[TPV_Secret])?>'>    
              <input type="hidden" name='Ds_Merchant_MerchantCode' class="form-control" placeholder="Ds_Merchant_MerchantCode"  value='<?=htmlentities($_data[Ds_Merchant_MerchantCode])?>'>    
          	  
	          <p>
	          	<h3>About who is going to pay (client)</h3>
	         <div class="input-group">
	         <label for="Ds_Merchant_ConsumerLanguage" >Ds_Merchant_ConsumerLanguage: </label>
          	 	<select  class="form-control" id='Ds_Merchant_ConsumerLanguage' name='Ds_Merchant_ConsumerLanguage'>
            	<?php foreach ($Ds_Merchant_ConsumerLanguage as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[Ds_Merchant_ConsumerLanguage]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
              </div>
              <br/>
             <h3>About the Product</h3>
 
	         <div class="input-group">
	          <label for="Ds_Merchant_ProductDescription" >Ds_Merchant_ProductDescription: </label>
              <input type=text  class="form-control" id=Ds_Merchant_ProductDescription name=Ds_Merchant_ProductDescription placeholder='Product description to sell' value='<?=htmlentities($_data[Ds_Merchant_ProductDescription])?>'>	
	          </div>              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_Amount" >Ds_Merchant_Amount: </label>
              <input type=text  class="form-control" id=Ds_Merchant_Amount name=Ds_Merchant_Amount placeholder='100 is equal to 1.00 (last 2 digits are decimals)' value='<?=htmlentities($_data[Ds_Merchant_Amount])?>'>	
	          </div>
	          <br/>
	         <div class="input-group">
	         <label for="Ds_Merchant_Currency" >Ds_Merchant_Currency: </label>
          	 	<select  class="form-control" id='Ds_Merchant_Currency' name='Ds_Merchant_Currency'>
            	<?php foreach ($Ds_Merchant_Currency as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[Ds_Merchant_Currency]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
              </div>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_Order" >Ds_Merchant_Order: (4 first chars has to be numbers)</label>
              <input type=text  class="form-control" id=Ds_Merchant_Order name=Ds_Merchant_Order placeholder='Only one use code (max 10 chars) ' value='<?=htmlentities($_data[Ds_Merchant_Order])?>'>	
	          </div>
	          <br/>
	         <div class="input-group">
	         <label for="Ds_Merchant_TransactionType" >Ds_Merchant_TransactionType: </label>
          	 	<select  class="form-control" id='Ds_Merchant_TransactionType' name='Ds_Merchant_TransactionType'>
            	<?php foreach ($Ds_Merchant_TransactionType as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[Ds_Merchant_TransactionType]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
              </div>
              <br/>	 
             <h3>About the Sabadell TPV </h3>
	         <div class="input-group">
	         <label for="Ds_Merchant_Terminal" >Ds_Merchant_Terminal: </label>
          	 	<select  class="form-control" id='Ds_Merchant_Terminal' name='Ds_Merchant_Terminal'>
            	<?php foreach ($Ds_Merchant_Terminal as $key => $value) {?>
                  <option  value='<?=$key?>' <?=($_data[Ds_Merchant_Terminal]==$key)?'selected':''?>><?=$key?> = <?=$value?></option>
            	<?php } ?>
             	</select>
              </div>             
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_MerchantName" >Ds_Merchant_MerchantName: </label>
              <input type=text  class="form-control" id=Ds_Merchant_MerchantName name=Ds_Merchant_MerchantName placeholder='The name to be shown in the TPV' value='<?=htmlentities($_data[Ds_Merchant_MerchantName])?>'>	
	          </div>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_Titular" >Ds_Merchant_Titular: </label>
              <input type=text  class="form-control" id=Ds_Merchant_Titular name=Ds_Merchant_Titular placeholder="I don't know yet" value='<?=htmlentities($_data[Ds_Merchant_Titular])?>'>	
	          </div>
	          <h3>eCommerce Platform Transaction (optional. Only if you have one) </h3>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_MerchantURL" >Ds_Merchant_MerchantURL: </label>
              <input type=text  class="form-control" id=Ds_Merchant_MerchantURL name=Ds_Merchant_MerchantURL placeholder='The Url that the TPV will send call back data' value='<?=htmlentities($_data[Ds_Merchant_MerchantURL])?>'>	
	          </div>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_UrlOK" >Ds_Merchant_UrlOK: </label>
              <input type=text  class="form-control" id=Ds_Merchant_UrlOK name=Ds_Merchant_UrlOK placeholder='The page to show if everything go well' value='<?=htmlentities($_data[Ds_Merchant_UrlOK])?>'>	
	          </div>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_UrlKO" >Ds_Merchant_UrlKO: </label>
              <input type=text  class="form-control" id=Ds_Merchant_UrlKO name=Ds_Merchant_UrlKO placeholder='The page to show if everything go bad' value='<?=htmlentities($_data[Ds_Merchant_UrlKO])?>'>	
	          </div>
              <br/>
	         <div class="input-group">
	          <label for="Ds_Merchant_MerchantData" >Ds_Merchant_MerchantData: </label>
              <input type=text  class="form-control" id=Ds_Merchant_MerchantData name=Ds_Merchant_MerchantData placeholder='Info that the TPV will send back to the eCommerce' value='<?=htmlentities($_data[Ds_Merchant_UrlKO])?>'>	
	          </div>
	          <br/>
	          <p><input type="submit" class="btn btn-lg btn-primary" value='Go to step 2' /></p>
	          </form>
          </div>
          <div class="col-lg-5">
          <?php if($_data[initTransaction]=='1') {?>
          	<h2>Confirm before to pay.</h2>
          <?php if(strlen($errMsg)) {?>
          	<pre>Ups.. Is not posible because: <?=htmlentities($errMsg)?></pre>
          <?php }else{?>
          	
         <form action='<?=(!strlen($_data[TPV_URL]))?'':htmlentities($_data[TPV_URL])?>' method ='post' target='tpv'>
         <input type=hidden name=Ds_Merchant_Terminal value='<?=htmlentities($_data[Ds_Merchant_Terminal])?>'> 
    
         <input type=hidden name=UserName value='<?=htmlentities($_data[UserName])?>'> 
         <input type=hidden name=Ds_Merchant_ProductDescription value='<?=htmlentities($_data[Ds_Merchant_ProductDescription])?>'>
         <input type=hidden name=UserEmail value='<?=htmlentities($_data[UserEmail])?>'>
         <input type=hidden name=Ds_Merchant_ConsumerLanguage value='<?=htmlentities($_data[Ds_Merchant_ConsumerLanguage])?>'>
    
         <input type=hidden name=Ds_Merchant_Amount value='<?=htmlentities($_data[Ds_Merchant_Amount])?>'>
         <input type=hidden name=Ds_Merchant_Currency value='<?=htmlentities($_data[Ds_Merchant_Currency])?>'>
         <input type=hidden name=Ds_Merchant_MerchantCode value='<?=htmlentities($_data[Ds_Merchant_MerchantCode])?>'> 
         <input type=hidden name=Ds_Merchant_MerchantName value='<?=htmlentities($_data[Ds_Merchant_MerchantName])?>'>
         <input type=hidden name=Ds_Merchant_Titular value='<?=htmlentities($_data[Ds_Merchant_Titular])?>'> 
         <input type=hidden name=Ds_Merchant_TransactionType value='<?=htmlentities($_data[Ds_Merchant_TransactionType])?>'> 
         <input type=hidden name=Ds_Merchant_Order value='<?=htmlentities($_data[Ds_Merchant_Order])?>'> 
        
         <input type=hidden name=Ds_Merchant_MerchantSignature value='<?=htmlentities($Ds_Merchant_MerchantSignature)?>'> 
         <input type=hidden name=Ds_Merchant_MerchantURL value='<?=htmlentities($_data[Ds_Merchant_MerchantURL])?>'> 
         <input type=hidden name=Ds_Merchant_UrlOK value='<?=htmlentities($_data[Ds_Merchant_UrlOK])?>'>
         <input type=hidden name=Ds_Merchant_UrlKO value='<?=htmlentities($_data[Ds_Merchant_UrlKO])?>'>
         <input type=hidden name=Ds_Merchant_MerchantData value='<?=htmlentities($_data[Ds_Merchant_MerchantData])?>'> 
         
         <h3><?=htmlentities($_data[TPV_URL])?></h3>
         
         <p>Using the language of 
         <b><?=$Ds_Merchant_ConsumerLanguage[$_data[Ds_Merchant_ConsumerLanguage]]?></b> wants to by:
         <ul>
             <li><b><?=htmlentities($_data[Ds_Merchant_ProductDescription])?></b> 
             <li>With a total cost of 
                 <b><?=substr(htmlentities($_data[Ds_Merchant_Amount]),0,-2)?>.<?=substr(htmlentities($_data[Ds_Merchant_Amount]),-2)?> (<?=$Ds_Merchant_Currency[$_data[Ds_Merchant_Currency]]?>)</b>
             <li>In a <b><?=$Ds_Merchant_TransactionType[$_data[Ds_Merchant_TransactionType]]?></b> Type of transaction.</li>
             <li>And assigned Order: <b><?=htmlentities($_data[Ds_Merchant_Order])?></b></li>
         </ul>
         </p>
         <p>The Store <b><?=htmlentities($_data[Ds_Merchant_MerchantCode])?></b> is selling products under the brand: 
             <b><?=htmlentities($_data[Ds_Merchant_MerchantName])?></b> for the Client: <b><?=htmlentities($_data[Ds_Merchant_Titular])?></b>
         </p>
         
         <p>
          - Type of Terminal: <b><?=htmlentities($_data[Ds_Merchant_Terminal])?></b>
          <br>- Callback: <b><?=htmlentities($_data[Ds_Merchant_MerchantURL])?></b>
          <br>- OK Page: <b><?=htmlentities($_data[Ds_Merchant_UrlOK])?></b>
          <br>- KO Page: <b><?=htmlentities($_data[Ds_Merchant_UrlKO])?></b>
            
         </p>
         <p>This purchase is signed with '<b><?=htmlentities($Ds_Merchant_MerchantSignature)?></b>' secret based code 
            and <?=(strlen($_data[Ds_Merchant_MerchantData]))?'<b>'.htmlentities($_data[Ds_Merchant_MerchantData]).'</b>':'no extra '?> MerchantData
         </p>
       <input type=submit value='Send it to TPV. Step 3/3'>           
         </form>
         <h2>TPV</h2>
   		 <iframe id=tpv name='tpv' width="100%" height="400"></iframe>  
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
          </div>
     </div>	
</div>