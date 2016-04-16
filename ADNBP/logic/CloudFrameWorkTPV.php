<?php
// local Functions
// This service has to be implemented in your <document_root>/logic/CloudFrameWorkService.php
    list($foo,$script,$service,$params) = split('/',$this->_url,4);
	  
    
    switch ($service) {
        case "SABADELL":
            $this->setConf("pageCode","TPVSabadell");
			$errMsg ='';
            $this->checkRequestParameter($_data,'type');
			
			
            if(!strlen($params)) {
                
                // $_data = $_POST;
				$this->checkRequestParameter($_data,'ADNBPTPV_MerchantId',true,$_data[type]=='ADNBPTPV');
				$this->checkRequestParameter($_data,'ADNBPTPV_MerchantSecret',true,$_data[type]=='ADNBPTPV');				
				$this->checkRequestParameter($_data,'ADNBPTPV_ProductCurrency',true,$_data[type]=='ADNBPTPV');				
				$this->checkRequestParameter($_data,'ADNBPTPV_Production',true,$_data[type]=='ADNBPTPV');	
				$this->checkRequestParameter($_data,'initTransaction');				

				if( strlen($this->getConf("ADNBPTPV_MerchantId"))
		         && strlen($this->getConf("ADNBPTPV_MerchantSecret"))
		         && strlen($this->getConf("ADNBPTPV_ProductCurrency"))
		         && strlen($this->getConf("ADNBPTPV_Production")) ) $_data[type] = 'ADNBPTPV';
				 
                // If I am going to use ADNBPTPV
				if($_data[type]=='ADNBPTPV') {
					
					if(!strlen($_data[ADNBPTPV_MerchantId])) $errForm .= "Missing ADNBPTPV_MerchantId field\n";
					if(!strlen($_data[ADNBPTPV_MerchantSecret])) $errForm .= "Missing ADNBPTPV_MerchantSecret field\n";
					if(!strlen($_data[ADNBPTPV_ProductCurrency])) $errForm .= "Missing ADNBPTPV_ProductCurrency field\n";
                    $this->setConf("TPV_ClientName",$this->getAuthUserData("name"));
                    $this->setConf("TPV_ClientEmail",$this->getAuthUserData("email"));
                    $this->setConf("TPV_ClientId",$this->getAuthUserData("id"));
                    $this->checkRequestParameter($_data,'TPV_ClientName');
                    $this->checkRequestParameter($_data,'TPV_ClientEmail');
                    $this->checkRequestParameter($_data,'TPV_ClientId');
                    $this->checkRequestParameter($_data,'TPV_ConsumerLanguage');
                    $this->checkRequestParameter($_data,'TPV_ProductId');
                    $this->checkRequestParameter($_data,'TPV_ProductUnits');
                    				
					// Getting products from service
					if(!strlen($errMsg)) {
						$serviceData[ADNBPTPV_MerchantId] = $_data[ADNBPTPV_MerchantId];
	                    $serviceData[ADNBPTPV_Signature] = strtoupper(sha1(
									                    $_data[ADNBPTPV_MerchantId]
									                   .$_data[ADNBPTPV_MerchantSecret]
									                   ));	
					    $merchantInfo = unserialize($this->getCloudServiceResponse('TPV/getMerchantInfo',$serviceData));
								
						$serviceData[ADNBPTPV_MerchantId] = $_data[ADNBPTPV_MerchantId];
						$serviceData[ADNBPTPV_ProductCurrency] = $_data[ADNBPTPV_ProductCurrency];
	                    $serviceData[ADNBPTPV_Signature] = strtoupper(sha1(
									                    $_data[ADNBPTPV_MerchantId]
									                   .$_data[ADNBPTPV_ProductCurrency]
									                   .$_data[ADNBPTPV_MerchantSecret]
									                   ));	
													   						
					
					    $products = unserialize($this->getCloudServiceResponse('TPV/getProducts',$serviceData));
						if($result[error]) $errForm = 'Error retreiving Products from: '.$this->getCloudServiceURL('/TPV/getProducts');
						else {
							$TPV_products = $products[data];
							$TPV_merchant = $products[merchant];
						}
						
						unset($products);
					
					}

					// Force init transaction if we have all the data 
				    if(!strlen($errMsg) && $_data[initTransaction] != '1'
						&& strlen($_data[ADNBPTPV_MerchantId])
						&& strlen($_data[ADNBPTPV_ProductCurrency])
						&& strlen($_data[TPV_ClientName])
						&& strlen($_data[TPV_ClientEmail])
						&& strlen($_data[TPV_ProductId])
						&& strlen($_data[TPV_ProductUnits])
						&& strlen($_data[ADNBPTPV_MerchantSecret])
                        && strlen($_data[TPV_ConsumerLanguage])
					) $_data[initTransaction] = '1';
					
                    // Preparing information if initTrasaction is sent
				    if(!strlen($errMsg) && $_data[initTransaction] == '1') {
						if(!strlen($_data[ADNBPTPV_MerchantId])) $errMsg .= "Missing ADNBPTPV_MerchantId field\n";
						if(!strlen($_data[ADNBPTPV_ProductCurrency])) $errMsg .= "Missing ADNBPTPV_ProductCurrency field\n";
						if(!strlen($_data[TPV_ClientName])) $errMsg .= "Missing TPV_ClientName field\n";
						if(!strlen($_data[TPV_ClientEmail])) $errMsg .= "Missing TPV_ClientEmail field\n";
						if(!strlen($_data[TPV_ProductId])) $errMsg .= "Missing TPV_ProductId field\n";
						if(!strlen($_data[TPV_ProductUnits])) $errMsg .= "Missing TPV_ProductUnits field\n";
						if(!strlen($_data[ADNBPTPV_MerchantSecret])) $errMsg .= "Missing ADNBPTPV_MerchantSecret field\n";
                        if(!strlen($_data[TPV_ConsumerLanguage])) $errMsg .= "Missing TPV_ConsumerLanguage field\n";
												
						
						if(!strlen($errMsg)) {
							unset($serviceData);
                            $serviceData[ADNBPTPV_MerchantId] = $_data[ADNBPTPV_MerchantId];
                            $serviceData[ADNBPTPV_Production] = $_data[ADNBPTPV_Production];
                            $serviceData[TPV_ConsumerLanguage] = $_data[TPV_ConsumerLanguage];
                            $serviceData[TPV_ClientId] = $_data[TPV_ClientId];
                            $serviceData[TPV_ClientName] = $_data[TPV_ClientName];
                            $serviceData[TPV_ClientEmail] = $_data[TPV_ClientEmail];
                            $serviceData[ADNBPTPV_ProductCurrency] = $_data[ADNBPTPV_ProductCurrency];
                            $serviceData[TPV_ProductId] = $_data[TPV_ProductId];
                            $serviceData[TPV_ProductUnits] = $_data[TPV_ProductUnits];
                            
                            
							$serviceData[ADNBPTPV_Signature] = strtoupper(sha1(
										                    $_data[ADNBPTPV_MerchantId]
										                   .$_data[ADNBPTPV_Production]
                                                           .$_data[TPV_ConsumerLanguage]
										                   .$_data[TPV_ClientId]
										                   .$_data[TPV_ClientName]
										                   .$_data[TPV_ClientEmail]
										                   .$_data[ADNBPTPV_ProductCurrency]
										                   .$_data[TPV_ProductId]
										                   .$_data[TPV_ProductUnits]
										                   .$_data[ADNBPTPV_MerchantSecret]
										                   ));	
							
							$result = unserialize($this->getCloudServiceResponse('TPV/initTransaction',$serviceData));
							if($result[error]) {
							    $errMsg = 'Error initTransaction  from: '.$this->getCloudServiceURL('TPV/initTransaction')."\n";
                                $errMsg .= $result[msgError];
                            }
							
                        }
						
						if(!strlen($errMsg)) {
						    $_dataTransaction = $result[data];
                            $_dataHTML = $result[html];
                            // _print($result[sign],$result[data]);
						}						
    						
					}
					
				} 
                // If I am going to use directoy BANCSABADELLTPV
				elseif($_data[type]=='BANCSABADELLTPV') {
					
					$this->checkRequestParameter($_data,'Ds_Merchant_ConsumerLanguage');
					$this->checkRequestParameter($_data,'TPV_URL',true,$_data[type]=='BANCSABADELLTPV');
					$this->checkRequestParameter($_data,'TPV_Secret',true,$_data[type]=='BANCSABADELLTPV');
					$this->checkRequestParameter($_data,'Ds_Merchant_MerchantCode',true,$_data[type]=='BANCSABADELLTPV');
					$this->checkRequestParameter($_data,'Ds_Merchant_ProductDescription');
					$this->checkRequestParameter($_data,'Ds_Merchant_Amount');
					$this->checkRequestParameter($_data,'Ds_Merchant_TransactionType');
					$this->checkRequestParameter($_data,'Ds_Merchant_Currency');
					$this->checkRequestParameter($_data,'Ds_Merchant_Terminal');
					$this->checkRequestParameter($_data,'Ds_Merchant_Order');
					$this->checkRequestParameter($_data,'Ds_Merchant_MerchantData');
					$this->checkRequestParameter($_data,'Ds_Merchant_MerchantName');
					$this->checkRequestParameter($_data,'Ds_Merchant_Titular');
					$this->checkRequestParameter($_data,'Ds_Merchant_MerchantURL');
					$this->checkRequestParameter($_data,'Ds_Merchant_UrlOK');
					$this->checkRequestParameter($_data,'Ds_Merchant_UrlKO');
					
                    //Optional
                    if(is_array($TPV_products) && strlen($_POST[TPV_product]) && strlen($_POST[TPV_ProductUnits])
                        && !strlen($_data[Ds_Merchant_Amount]) && is_numeric($_POST[TPV_ProductUnits]) 
                    ) {
                        $_data[CRMProduct_Id] = $TPV_products[$_POST[TPV_product]][CRMProduct_Id];
                        $_data[Ds_Merchant_Amount] = $TPV_products[$_POST[TPV_product]][CRMProduct_Price] * $_POST[TPV_ProductUnits]*100;
                        $_data[Ds_Merchant_ProductDescription] = $_POST[TPV_ProductUnits].' x '.$TPV_products[$_POST[TPV_product]][CRMProduct_Name].' - '.$TPV_products[$_POST[TPV_product]][CRMProduct_Price].' '.$TPV_products[$_POST[TPV_product]][CRMProduct_Currency] ;
                    }
                    
                    // Preparing data for a init transaction
                    if($_data[initTransaction]=='1') {
                        
                        if(!strlen($_data[Ds_Merchant_Amount])) $errMsg .= "Missing Ds_Merchant_Amount field\n";
                        if(!strlen($_data[Ds_Merchant_MerchantCode])) $errMsg .= "Missing Ds_Merchant_MerchantCode field\n";
                        if(!strlen($_data[Ds_Merchant_Currency])) $errMsg .= "Missing Ds_Merchant_Currency field\n";
                        if(!strlen($_data[Ds_Merchant_TransactionType])) $errMsg .= "Missing Ds_Merchant_TransactionType field\n";
                        // if(!strlen($_data[Ds_Merchant_MerchantURL])) $errMsg .= "Missing Ds_Merchant_MerchantURL field\n";
                        if(!strlen($_data[TPV_Secret])) $errMsg .= "Missing TPV_Secret field\n";
                        $Ds_Merchant_MerchantSignature = strtoupper(sha1(
                        $_data[Ds_Merchant_Amount]
                       .$_data[Ds_Merchant_Order]
                       .$_data[Ds_Merchant_MerchantCode]
                       .$_data[Ds_Merchant_Currency]
                       .$_data[Ds_Merchant_TransactionType]
                       .$_data[Ds_Merchant_MerchantURL]
                       .$_data[TPV_Secret]
                       ));
                       $_data[Ds_Merchant_MerchantSignature] =   $Ds_Merchant_MerchantSignature;                         
                    }                     

				}
				

                // Begining a transaction
                if($_data[initTransaction]=='1' && !strlen($errMsg)) {
                    
                    $_ready = true;
                    if(!strlen($_data[TPV_URL])) { $_ready = false; $msgError.='Missing TPV_URL';}
                    if(!strlen($_data[Ds_Merchant_MerchantCode])) { $_ready = false; $msgError.='Missing Ds_Merchant_MerchantCode';}
                    if(!strlen($_data[Ds_Merchant_MerchantName])) { $_ready = false; $msgError.='Missing Ds_Merchant_MerchantName';}
                    if(!strlen($_data[Ds_Merchant_Titular])) { $_ready = false; $msgError.='Missing Ds_Merchant_Titular';}
                    if(!strlen($_data[Ds_Merchant_ProductDescription])) { $_ready = false; $msgError.='Missing Ds_Merchant_ProductDescription';}
                    if(!strlen($_data[Ds_Merchant_Amount])) { $_ready = false; $msgError.='Missing Ds_Merchant_Amount';}
                    if(!strlen($_data[Ds_Merchant_TransactionType])) { $_ready = false; $msgError.='Missing Ds_Merchant_TransactionType';}
                    if(!strlen($_data[Ds_Merchant_Currency])) { $_ready = false; $msgError.='Missing Ds_Merchant_Currency';}
                    if(!strlen($_data[Ds_Merchant_Terminal])) { $_ready = false; $msgError.='Missing Ds_Merchant_Terminal';}
                    
                    if($_data[type]=='BANCSABADELLTPV')
                       if(!strlen($_data[Ds_Merchant_MerchantURL])) { $_ready = false; $msgError.='Missing Ds_Merchant_MerchantURL';}
                    if($_data[type]=='BANCSABADELLTPV')
                        if(!strlen($_data[Ds_Merchant_UrlOK])) { $_ready = false; $msgError.='Missing Ds_Merchant_UrlOK';}
                    if($_data[type]=='BANCSABADELLTPV')
                        if(!strlen($_data[Ds_Merchant_UrlKO])) { $_ready = false; $msgError.='Missing Ds_Merchant_UrlKO';}

                    if(!strlen($_data[Ds_Merchant_Amount])) { $_ready = false; $msgError.='Missing Ds_Merchant_Amount';}
                    if(!strlen($_data[Ds_Merchant_Order])) { $_ready = false; $msgError.='Missing Ds_Merchant_Order';}
    
               }

            } else {
                die("unknown action");
            }
            
            break;
        default:
            break;
    }

// Data Arrays
        $Ds_Merchant_ConsumerLanguage['0'] = 'Cliente';
        $Ds_Merchant_ConsumerLanguage['1'] = 'Castellano';
        $Ds_Merchant_ConsumerLanguage['2'] = 'Inglés';
        $Ds_Merchant_ConsumerLanguage['3'] = 'Catalán';
        $Ds_Merchant_ConsumerLanguage['4'] = 'Francés';
        $Ds_Merchant_ConsumerLanguage['5'] = 'Alemán';
        $Ds_Merchant_ConsumerLanguage['6'] = 'Holandés';
        $Ds_Merchant_ConsumerLanguage['7'] = 'Italiano';
        $Ds_Merchant_ConsumerLanguage['8'] = 'Sueco';
        $Ds_Merchant_ConsumerLanguage['9'] = 'Portugués';
        $Ds_Merchant_ConsumerLanguage['10'] = 'Valenciano';
        $Ds_Merchant_ConsumerLanguage['11'] = 'Polaco';
        $Ds_Merchant_ConsumerLanguage['12'] = 'Gallego';
        $Ds_Merchant_ConsumerLanguage['13'] = 'Euskera';

        $Ds_Merchant_TransactionType['0'] = 'Normal';
        $Ds_Merchant_TransactionType['1'] = 'Preautorización (sólo Hoteles, Agencia y Alquiler vehículos)';
        $Ds_Merchant_TransactionType['2'] = 'Conformación de Preautorización (sólo Hoteles, Agencia y Alquiler vehículos)';
        $Ds_Merchant_TransactionType['9'] = 'Conformación de Preautorización (sólo Hoteles, Agencia y Alquiler vehículos)';
        $Ds_Merchant_TransactionType['L'] = 'Pago por subscripciones. Pago inicial';
        $Ds_Merchant_TransactionType['M'] = 'Pago por subscripciones. Siguientes cargos';

        $Ds_Merchant_Currency['978'] = 'EUR';
        $Ds_Merchant_Currency['840'] = 'USD';
        $Ds_Merchant_Currency['392'] = 'YEN';
		
		$Ds_Merchant_Terminal[1] = 'EURO Terminal';
		$Ds_Merchant_Terminal[99] = 'For other terminals talk with the bank ';
		
		$ADNBPTPV_ProductCurrency[EUR] = 'Euros';
		$ADNBPTPV_ProductCurrency[USD] = 'USA Dollar';
        


?>