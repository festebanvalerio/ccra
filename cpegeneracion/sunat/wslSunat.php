<?php
class feedSoap extends SoapClient
{
    var $XMLStr = "";
    function setXMLStr ($value){$this->XMLStr = $value; }
    function getXMLStr(){return $this->XMLStr; }

    function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        $request = $this -> XMLStr;
        $dom = new DOMDocument('1.0');

        try
        {
            $dom->loadXML($request);
        }
        catch (DOMException $e)
        {
            die($e->code);
        }

        $request = $dom->saveXML();

        //doRequest
        return parent::__doRequest($request, $location, $action, $version, $one_way = 0);
    }

    function SoapClientCall($SOAPXML)
    {
        return $this -> setXMLStr ($SOAPXML);
    }
}
 //i just copied the soap request they provided and dropped in my values (removed for stackoverflow
function getSopMessage($file, $file_name, $user, $pass, $nomfuncion="sendBill"){
    $XMLString= '<?xml version="1.0" encoding="UTF-8"?>
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
     <soapenv:Header>
    	 <wsse:Security>
    		 <wsse:UsernameToken>
    			 <wsse:Username>' .$user. '</wsse:Username>
    			 <wsse:Password>' .$pass. '</wsse:Password>
    		 </wsse:UsernameToken>
    	 </wsse:Security>
     </soapenv:Header>
     <soapenv:Body>
    	 <ser:' .$nomfuncion. '>';
         if($nomfuncion=='getStatus'){
             $XMLString = $XMLString .'<ticket>'. $file_name .'</ticket>';
         }else {
             $XMLString = $XMLString .'<fileName>' .$file_name. '</fileName>
     	     <contentFile>'. base64_encode(file_get_contents($file)) .'</contentFile>';
         }
    	 $XMLString = $XMLString .'</ser:'. $nomfuncion. '>
     </soapenv:Body>
    </soapenv:Envelope>';
    return $XMLString;
}

function soapCall($wsdlURL, $callFunction="SendBill", $XMLString, $file_name, $dircdr)
{
    $faultcode = '0';
    $endpoint  = $wsdlURL;//

    /*descomentar en produccion*/
    $uri            = 'http://service.sunat.gob.pe';
    $options=array(
        'trace'         => true,
        'location'      => $endpoint,
        'uri'           => $uri
    );
    /*$options=array(
         'trace'         => true
    );*/

    try {
        $client = new feedSoap(null, $options);
        //$client = new feedSoap($endpoint, $options);
        $reply = $client->SoapClientCall($XMLString);
        $client->__call("$callFunction", array(), array());
    }catch (SoapFault $f) {
        $array = explode('.', $f->faultcode);
        if($array[0]=='soap-env:Client'){
            $faultcode =  $array[1];
        }else{
            if($f->faultcode=='WSDL'){
                $faultcode = '-1';
            }else{
                $faultcode =  $f->faultcode;
            }
        }
    }
    //echo $faultcode;
    //var_dump($client);
    //exit();

    $ticket = '0';
    if($faultcode=='0'){
        $result = $client -> __getLastResponse();
        $doc = new DOMDocument();
        $doc->loadXML($result);
        $doc->save('mirespuesta.xml');
        $data = $doc->getElementsByTagName('ticket');
        if($data->length>0){
            $ticket = $data->item(0)->nodeValue;
            $faultcode = array('faultcode' => '0' ,'ticket'=> $ticket);
        }else{
            $data = $doc->getElementsByTagName('applicationResponse');
            if($data->length>0){
                $databin = $data->item(0)->nodeValue;
            }else{
                $data = $doc->getElementsByTagName('content');

                if($data->length>0){
                    $databin = $data->item(0)->nodeValue;
                }
            }
            if(isNotData($databin)){

                $zip= base64_decode($databin);
                if(strrpos($file_name, '.zip')===false){
                    $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($zip,0,30));
                    $filename = substr($zip,30,$head['namelen']);
                    $newfile_name = str_replace(".xml", "", $filename). ".zip";
                    $file_name_xml = substr($zip,30,$head['namelen']);
                }else{
                    $newfile_name = 'R-' .$file_name;
                    $file_name_xml = 'R-' .str_replace(".zip", "", $file_name). ".xml";
                }
                $file = fopen($dircdr .$newfile_name,"w");
                fwrite($file,$zip);
                fclose($file);
                $enzipado = new ZipArchive();
                //Abrimos el archivo a descomprimir
                $enzipado->open($dircdr .$newfile_name);
                //Extraemos el contenido del archivo dentro de la carpeta especificada
                $extraido = $enzipado->extractTo($dircdr, array($file_name_xml));
                //$doc->save($dircdr ."R-" .str_replace(".zip", "", $file_name). ".xml");
                //print_r $pasrew;
                $doc2 = new DOMDocument();
                $doc2->load($dircdr .$file_name_xml);

                $tt = $doc2->getElementsByTagName("ResponseCode");
                if($tt->length>0){
                    $faultcode = $tt->item(0)->nodeValue;
                }
                $tt = $doc2->getElementsByTagName("statusCode");
                if($tt->length>0){
                    $faultcode = $tt->item(0)->nodeValue;
                }

                if($callFunction=="getStatus"){
                    $enzipado->close();
                    @unlink($dircdr .$file_name_xml);
                    /*@unlink($dircdr .$newfile_name);*/
                }

            }else{
                $faultcode = "0127";
            }
        }

    }
    return $faultcode;
}

function isNotData($test_string){
    return (bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $test_string);
}

function getSopMessage_getStatus($user, $pass, $ruc, $tipo, $serie, $numero)
{
    $numero=$numero*1;
    $XMLString= '<?xml version="1.0" encoding="UTF-8"?>
    <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ser="http://service.sunat.gob.pe" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
     <soapenv:Header xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope">
         <wsse:Security>
             <wsse:UsernameToken>
                 <wsse:Username>'.$user.'</wsse:Username>
                 <wsse:Password>'.$pass.'</wsse:Password>
             </wsse:UsernameToken>
         </wsse:Security>
     </soapenv:Header>
     <soapenv:Body>
         <m:getStatusCdr xmlns:m="http://service.sunat.gob.pe">
            <rucComprobante>'.$ruc.'</rucComprobante>
            <tipoComprobante>'.$tipo.'</tipoComprobante>
            <serieComprobante>'.$serie.'</serieComprobante>
            <numeroComprobante>'.$numero.'</numeroComprobante>
         </m:getStatusCdr>
     </soapenv:Body>
    </soapenv:Envelope>';
    return $XMLString;
}

function soapCall_getStatus($XMLString, $file_name, $dircdr)
{

    $faultcode = '0';
    $endpoint       = 'https://e-factura.sunat.gob.pe/ol-ti-itcpfegem/billService';
    //$endpoint       = 'https://e-beta.sunat.gob.pe/ol-ti-itcpfegem-beta/billService';

    $uri            = 'http://service.sunat.gob.pe';
    $options=array(
        'trace'         => true,
        'location'      => $endpoint,
        'uri'           => $uri
    );

    try {
        $client = new feedSoap(null, $options);
        $reply = $client->SoapClientCall($XMLString);
        $client->__call("getStatusCdr", array(), array());
    }catch (SoapFault $f) {
        if($f->faultcode=='WSDL'){
            $faultcode = '-1';
        }else{
            $faultcode =  $f->faultcode;
        }
    }
    //echo $faultcode;
    var_dump($faultcode);
    exit();
    
    $ticket = '0';
    if($faultcode=='0'){
        $result = $client -> __getLastResponse();
        $doc = new DOMDocument();
        $doc->loadXML($result);

            $data = $doc->getElementsByTagName('statusCdr');
            if($data->length>0){
                $databin = $data->item(0)->nodeValue;
            }
            // $zip= base64_decode($databin);
            // if(strrpos($file_name, '.zip')===false){
            //     $head = unpack("Vsig/vver/vflag/vmeth/vmodt/vmodd/Vcrc/Vcsize/Vsize/vnamelen/vexlen", substr($zip,0,30));
            //     $filename = substr($zip,30,$head['namelen']);
            //     $newfile_name = str_replace(".xml", "", $filename). ".zip";
            //     $file_name_xml = $filename;
            // }else{
            //     $newfile_name = 'CDR-' .$file_name;
            //     $file_name_xml = 'CDR-' .str_replace(".zip", "", $file_name). ".xml";
            // }
            // $file = fopen($dircdr .$newfile_name,"w");
            // fwrite($file,$zip);
            // fclose($file);
            // $enzipado = new ZipArchive();
            // //Abrimos el archivo a descomprimir
            // $enzipado->open($dircdr .$newfile_name);
            // //Extraemos el contenido del archivo dentro de la carpeta especificada
            // $extraido = $enzipado->extractTo($dircdr, array($file_name_xml));
            // //$doc->save($dircdr ."R-" .str_replace(".zip", "", $file_name). ".xml");
            // //print_r $pasrew;
            // $doc2 = new DOMDocument();
            // $doc2->load($dircdr .$file_name_xml);

            // $tt = $doc2->getElementsByTagName("ResponseCode");
            // if($tt->length>0){
            //     $faultcode = $tt->item(0)->nodeValue;
            // }
            // $tt = $doc2->getElementsByTagName("statusCode");
            // if($tt->length>0){
            //     $faultcode = $tt->item(0)->nodeValue;
            // }

    }
    return $faultcode;
}
?>
