<?php
    function firmar($xml, $dirxml, $arr) {
        $ruta = "/var/www/html/ccra/";
        require_once($ruta.'cpegeneracion/xmlseclibs/fr3d/xmldsig/Adapter/AdapterInterface.php');
        require_once($ruta.'cpegeneracion/xmlseclibs/robrichards/xmlseclibs/XMLSecurityDSig.php');
        require_once($ruta.'cpegeneracion/xmlseclibs/robrichards/xmlseclibs/XMLSecurityKey.php');
        require_once($ruta.'cpegeneracion/xmlseclibs/fr3d/xmldsig/Adapter/XmlseclibsAdapter.php');
        $xmlTool = new FR3D\XmlDSig\Adapter\XmlseclibsAdapter($arr['Signature']['ID']);
        $pfx = file_get_contents($ruta.'cpegeneracion/sunat/certificado/'. $arr['certificado']);
        
        openssl_pkcs12_read($pfx, $key, $arr['clave_certificado']);
        $xmlTool->setPrivateKey($key["pkey"]);
        $xmlTool->setpublickey($key["cert"]);
        $xmlTool->addTransform(FR3D\XmlDSig\Adapter\XmlseclibsAdapter::ENVELOPED);
        $xmlTool->sign($xml);
        $filename = $arr['filename'];
        $xml->save($dirxml .$filename. ".xml");
        create_zip(array($dirxml .$filename. ".xml"), $dirxml .$filename. ".zip", $overwrite = false);
        return($xml);
    }

    function send_sunat($file, $file_name, $arr, $dircdr, $nomfuncion="sendBill", $tipodoc="Invoice"){
        global $name_rec;

        $faultcode = '0';
        require_once('wslSunat.php');

        //------------------------------------
        //envio pruebas
        //$wsdlURL = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';

        //------------------------------------
        //envio en produccion
        if($tipodoc=='DespatchAdvice')
        {
            $wsdlURL = 'https://e-guiaremision.sunat.gob.pe/ol-ti-itemision-guia-gem/billService';
        }else{
            $wsdlURL = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
        }
        //$arr['usuario_sunat'] = "LURSTRES";
        //make the call, and set the soap function that I'll be using
        $XMLString = getSopMessage($file, $file_name, $arr['usuario_sunat'], $arr['clave_sunat'], $nomfuncion);
        $faultcode = soapCall($wsdlURL, $nomfuncion, $XMLString, $file_name, $dircdr);
        return $faultcode;
    }

    function consultar_sunat($file_name, $dircdr, $ruc, $tipo, $serie, $numero){
        global $name_rec;

        $faultcode = '0';
        require_once('wslSunat.php');

        $user="20552171579FACTURA1";
        $pass="Lima201911";
         //$user="10447868186";
        //$pass="123456";
        $XMLString = getSopMessage_getStatus($user, $pass, $ruc, $tipo, $serie, $numero);
        $faultcode = soapCall_getStatus($XMLString, $file_name, $dircdr);
        return $faultcode;
    }

    function unzipByteArray($data){
        global $name_rec;
      /*this firts is a directory*/
      $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($data,0,30));
      $filename = substr($data,30,$head['namelen']);
      $if=30+$head['namelen']+$head['exlen']+$head['csize'];
     /*this second is the actua file*/
      $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($data,$if,30));
      $name_rec = substr($data,strrpos($data,'R-'),$head['namelen']);
      $raw = gzinflate(substr($data,$if+$head['namelen']+$head['exlen']+30,$head['csize']));
      /*you can create a loop and continue decompressing more files if the were*/
      return $raw;
    }

    function get_SignatureValue($xml){
        $signvalue = null;
        $SignatureValue = $xml->getElementsByTagName('SignatureValue');
        if($SignatureValue->length>0){
            $signvalue = $SignatureValue->item(0)->nodeValue;
        }
        return $signvalue;
    }

    function get_DigestValue($xml){
        $digvalue = null;
        $DigestValue = $xml->getElementsByTagName('DigestValue');
        if($DigestValue->length>0){
            $digvalue = $DigestValue->item(0)->nodeValue;
        }
        return $digvalue;
    }

    function create_zip($files = array(),$destination = '',$overwrite = false) {
    	//if the zip file already exists and overwrite is false, return false
    	if(file_exists($destination) && !$overwrite) { return false; }
    	//vars
    	$valid_files = array();
    	//if files were passed in...
    	if(is_array($files)) {
    		//cycle through each file
    		foreach($files as $file) {
    			//make sure the file exists
    			if(file_exists($file)) {
    				$valid_files[] = $file;
    			}
    		}
    	}
    	//if we have good files...
    	if(count($valid_files)) {
    		//create the archive
    		$zip = new ZipArchive();
    		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
    			return false;
    		}
    		//add the files
    		foreach($valid_files as $file) {
    			$zip->addFile($file, substr($file,strrpos($file,'/') + 1));
    		}
    		//debug
    		//echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

    		//close the zip -- done!
    		$zip->close();
    		//check to make sure the file exists
    		return file_exists($destination);
    	}
    	else
    	{
    		return false;
    	}
    }

    function valida($tipodoc) {
        global $xml, $texto;
        $rutaGeneral = "/var/www/html/ccra/";
        $paso = new DOMDocument("1.0","ISO-8859-1");
        $texto = $xml->saveXML();
        $paso->loadXML($texto);
        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $ruta = $rutaGeneral."cpegeneracion/sunat/ubl/2.1/maindoc/";
        $ruta2= $rutaGeneral."cpegeneracion/sunat/ubl/20/maindoc/";
        if($tipodoc == 'VoidedDocuments'){
            $file=$ruta2."UBLPE-VoidedDocuments-1.0.xsd";
        }elseif($tipodoc == 'CreditNote'){
            $file=$ruta."UBL-CreditNote-2.1.xsd";
        }elseif($tipodoc == 'DebitNote'){
            $file=$ruta."UBLPE-DebitNote-1.0.xsd";
        }elseif($tipodoc == 'SummaryDocuments'){
            $file=$ruta2."UBLPE-SummaryDocuments-1.0.xsd";
        }elseif($tipodoc == 'DespatchAdvice'){
            $file=$ruta2."UBL-DespatchAdvice-2.1.xsd";
        }else{
            $file=$ruta."UBL-Invoice-2.1.xsd";
        }
        $ok = $paso->schemaValidate($file);
        return $ok;
    }
    function display_xml_errors() {
        global $texto;
        $lineas = explode("\n", $texto);
        $errors = libxml_get_errors();

        foreach ($errors as $error) {
            echo display_xml_error($error, $lineas);
        }
        libxml_clear_errors();
    }
    function display_xml_error($error, $lineas) {
        $return  = $lineas[$error->line - 1]. "\n";
        $return .= str_repeat('-', $error->column) . "^\n";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;

            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message) .
                   "\n  Linea: $error->line" .
                   "\n  Columna: $error->column";
        echo "$return\n\n--------------------------------------------\n\n";
    }
?>
