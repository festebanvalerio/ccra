<?php
require_once("/var/www/html/ccra/cpegeneracion/sunat/funciones.php");
function run($arr, $dirxml, $dircdr, $nodo="", $tipodoc="", $enviar=false) {
    global $xml, $cadena_original, $sello, $texto, $ret;
    $arrRe = array();
    error_reporting(E_ALL & ~(E_WARNING | E_NOTICE));
    genera_xml($arr, $nodo, $tipodoc);
    $ok = true;
    file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Tipo Documento : " . $tipodoc . "\n", FILE_APPEND);
    if($tipodoc=='DespatchAdvice' || $tipodoc=='SummaryDocuments'){
        $xml = save($arr, $dirxml);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - XML : " . implode($xml) . "\n", FILE_APPEND);
        $xml = firmar($xml, $dirxml, $arr);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - XML : " . implode($xml) . "\n", FILE_APPEND);
        $signvalue = get_signaturevalue($xml);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - SIGNVALUE : " . $signvalue . "\n", FILE_APPEND);
        $digvalue = get_DigestValue($xml);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - DIGVALUE : " . $digvalue . "\n", FILE_APPEND);
    }else{
        $ok = valida($tipodoc);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - OK : " . $ok . "\n", FILE_APPEND);
        if ($ok) {
            $xml = save($arr, $dirxml);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - XML : " . implode($xml) . "\n", FILE_APPEND);
            $xml = firmar($xml, $dirxml, $arr);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - XML : " . implode($xml) . "\n", FILE_APPEND);
            $signvalue = get_signaturevalue($xml);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - SIGNVALUE : " . $signvalue . "\n", FILE_APPEND);
            $digvalue = get_DigestValue($xml);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - DIGVALUE : " . $digvalue . "\n", FILE_APPEND);
        }
    }
    if($tipodoc!="SummaryDocuments"){
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Tipo Documento : " . $tipodoc . "\n", FILE_APPEND);
        $ok = valida($tipodoc);
        file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - OK : " . $ok . "\n", FILE_APPEND);
    }
    file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - OK : " . $ok . "\n", FILE_APPEND);
    if (!$ok) {
        $arrRe = array('valid' => (($ok) ? '1' : '0'), 'faultcode' => '', 'signvalue' => '', 'digvalue' => '');
    }else{
        if($enviar){
            $nomfuncion = "sendBill";
            if($tipodoc == "VoidedDocuments" || $tipodoc == "SummaryDocuments"){
                $nomfuncion = "sendSummary";
            }
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Directorio XML : " . $dirxml . "\n", FILE_APPEND);
            file_put_contents("info" . date("Ymd") . ".log", "Envio Electronico - Nombre Archivo : " . $arr["filename"] . "\n", FILE_APPEND);
            $faultcode = send_sunat($dirxml .$arr['filename']. ".zip", $arr['filename'] .".zip", $arr, $dircdr, $nomfuncion, $tipodoc);
        }else{
            $faultcode = '-1';
        }
        if(is_array($faultcode)){
            $arrRe = array('valid' => '1', 'faultcode' => $faultcode['faultcode'], 'signvalue' => $signvalue, 'digvalue' => $digvalue, 'ticket' => $faultcode['ticket']);
        }else{
            $arrRe = array('valid' => '1', 'faultcode' => $faultcode, 'signvalue' => $signvalue, 'digvalue' => $digvalue);
        }

    }
    return $arrRe;
}
function genera_xml($arr, $nodo, $tipodoc) {
    global $xml, $ret;
    $xml = new DOMdocument('1.0', 'ISO-8859-1');
    $xml->standalone=false;
    if ($tipodoc == 'Invoice'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_InvoiceTypeCode($arr,$nodo,$tipodoc);
        add_DocumentCurrencyCode($arr,$nodo,$tipodoc);
        add_DespatchDocumentReference($arr,$nodo,$tipodoc);
        add_Signature($arr,$nodo,$tipodoc);
        add_AccountingSupplierParty($arr,$nodo,$tipodoc);
        add_AccountingCustomerParty($arr,$nodo,$tipodoc);
        add_PrepaidPayment($arr,$nodo,$tipodoc);
        add_TaxTotal($arr,$nodo,$tipodoc);
        add_LegalMonetaryTotal($arr,$nodo,$tipodoc);
        add_InvoiceLine($arr,$nodo,$tipodoc);
    }elseif ($tipodoc == 'DespatchAdvice'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_DespatchAdviceTypeCode($arr,$nodo,$tipodoc);
        add_Note($arr,$nodo,$tipodoc);
        add_DespatchSupplierParty($arr,$nodo,$tipodoc);
        add_DeliveryCustomerParty($arr,$nodo,$tipodoc);
        add_Shipment($arr,$nodo,$tipodoc);
        add_DespatchLine($arr,$nodo,$tipodoc);
    }elseif ($tipodoc == 'CreditNote'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_DocumentCurrencyCode($arr,$nodo,$tipodoc);
        add_DiscrepancyResponse($arr,$nodo,$tipodoc);
        add_BillingReference($arr,$nodo,$tipodoc);
        /*if (isset($arr['DespatchDocumentReference']['ID'])){
            add_DespatchDocumentReference($arr,$nodo,$tipodoc);
        }
        if (isset($arr['AdditionalDocumentReference']['ID'])){
            add_AdditionalDocumentReference ($arr,$nodo,$tipodoc);
        }*/
        add_Signature($arr,$nodo,$tipodoc);
        add_AccountingSupplierParty($arr,$nodo,$tipodoc);
        add_AccountingCustomerParty($arr,$nodo,$tipodoc);
        add_TaxTotal($arr,$nodo,$tipodoc);
        add_LegalMonetaryTotal($arr,$nodo,$tipodoc);
        add_CreditNoteLine($arr,$nodo,$tipodoc);
    }elseif ($tipodoc == 'DebitNote'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_DocumentCurrencyCode($arr,$nodo,$tipodoc);
        add_DiscrepancyResponse($arr,$nodo,$tipodoc);
        add_BillingReference($arr,$nodo,$tipodoc);
        /*if (isset($arr['DespatchDocumentReference']['ID'])){
            add_DespatchDocumentReference($arr,$nodo,$tipodoc);
        }
        if (isset($arr['AdditionalDocumentReference']['ID'])){
            add_AdditionalDocumentReference ($arr,$nodo,$tipodoc);
        }*/
        add_Signature($arr,$nodo,$tipodoc);
        add_AccountingSupplierParty($arr,$nodo,$tipodoc);
        add_AccountingCustomerParty($arr,$nodo,$tipodoc);
        add_TaxTotal($arr,$nodo,$tipodoc);
        add_RequestedMonetaryTotal($arr,$nodo,$tipodoc);
        add_DebitNoteLine($arr,$nodo,$tipodoc);
    }elseif ($tipodoc == 'SummaryDocuments'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_ReferenceDate($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_Signature($arr,$nodo,$tipodoc);
        add_AccountingSupplierParty($arr,$nodo,$tipodoc);
        add_SummaryDocumentsLine($arr,$nodo,$tipodoc);
    }elseif ($tipodoc == 'VoidedDocuments'){
        add_generales($arr,$nodo,$tipodoc);
        add_UBLExtensions($arr,$nodo,$tipodoc);
        add_UBLVersionID($arr,$nodo,$tipodoc);
        add_CustomizationID($arr,$nodo,$tipodoc);
        add_ID($arr,$nodo,$tipodoc);
        add_ReferenceDate($arr,$nodo,$tipodoc);
        add_IssueDate($arr,$nodo,$tipodoc);
        add_Signature($arr,$nodo,$tipodoc);
        add_AccountingSupplierParty($arr,$nodo,$tipodoc);
        add_VoidedDocumentsLine($arr,$nodo,$tipodoc);
    }
}
function add_generales($arr, $nodo, $tipodoc) {
    global $root, $xml;
    $root = $xml->createElement($tipodoc);
    $root = $xml->appendChild($root);
    if ($tipodoc=='Invoice') {
        xml_AddAtt($root, array("xmlns" => "urn:oasis:names:specification:ubl:schema:xsd:Invoice-2",
        "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
        "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
        "xmlns:ccts" => "urn:un:unece:uncefact:documentation:2",
        "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
        "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
        "xmlns:qdt" => "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2",
        "xmlns:sac" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1",
        "xmlns:udt" => "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2",
        "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance"));
    }elseif ($tipodoc=='DespatchAdvice') {
        xml_AddAtt($root, array("xmlns" => "urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2",
        "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
        "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
        "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
        "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"));
    }elseif ($tipodoc=='CreditNote') {
        xml_AddAtt($root, array("xmlns" => "urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2",
        "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
        "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
        "xmlns:ccts" => "urn:un:unece:uncefact:documentation:2",
        "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
        "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
        "xmlns:qdt" => "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2",
        "xmlns:sac" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1",
        "xmlns:udt" => "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2",
        "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance"));
    }elseif ($tipodoc=='DebitNote') {
         xml_AddAtt($root, array("xmlns" => "urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2",
         "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
         "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
         "xmlns:ccts" => "urn:un:unece:uncefact:documentation:2",
         "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
         "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
         "xmlns:qdt" => "urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2",
         "xmlns:sac" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1",
         "xmlns:udt" => "urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2",
         "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance"));
    }elseif ($tipodoc=='SummaryDocuments') {
        xml_AddAtt($root, array("xmlns" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1",
        "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
        "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
        "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
        "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
        "xmlns:sac" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1",
        "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance"));
    }elseif ($tipodoc=='VoidedDocuments') {
       xml_AddAtt($root, array("xmlns" => "urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1",
       "xmlns:cac" => "urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2",
       "xmlns:cbc" => "urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2",
       "xmlns:ds" => "http://www.w3.org/2000/09/xmldsig#",
       "xmlns:ext" => "urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2",
       "xmlns:sac" => "urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1",
       "xmlns:xsi" => "http://www.w3.org/2001/XMLSchema-instance"));
    }

}
//ext:UBLExtensions
function add_UBLExtensions($arr,$nodo,$tipodoc){
    global $root, $xml;
    $UBLExtensions = $xml->createElement("ext:UBLExtensions");
    $UBLExtensions = $root->appendChild($UBLExtensions);
        //<ext:UBLExtension>
        if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent'])){
            $UBLExtension = $xml->createElement("ext:UBLExtension");
            $UBLExtension = $UBLExtensions->appendChild($UBLExtension);
        }
            //<ext:ExtensionContent>
            if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent'])){
                $ExtensionContent = $xml->createElement("ext:ExtensionContent");
                $ExtensionContent = $UBLExtension->appendChild($ExtensionContent);
            }
                    //<sac:AdditionalInformation>
                if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation'])){
                    $AdditionalInformation = $xml->createElement("sac:AdditionalInformation");
                    $AdditionalInformation = $ExtensionContent->appendChild($AdditionalInformation);

                        for ($i=1; $i<=sizeof($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal']); $i++) {
                            //<sac:AdditionalMonetaryTotal>
                            $AdditionalMonetaryTotal = $xml->createElement("sac:AdditionalMonetaryTotal");
                            $AdditionalMonetaryTotal = $AdditionalInformation->appendChild($AdditionalMonetaryTotal);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['ID']);
                                $ID = $AdditionalMonetaryTotal->appendChild($ID);
                                //sac:ReferencAmount
                                if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['ReferencAmount'])){
                                    $ReferencAmount = $xml->createElement("sac:ReferencAmount", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['ReferencAmount']['Amount']);
                                    $ReferencAmount = $AdditionalMonetaryTotal->appendChild($ReferencAmount);
                                    $ReferencAmount->SetAttribute("currencyID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['ReferencAmount']['currencyID']);
                                }
                                //<cbc:PayableAmount
                                $PayableAmount = $xml->createElement("cbc:PayableAmount", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['PayableAmount']['Amount']);
                                $PayableAmount = $AdditionalMonetaryTotal->appendChild($PayableAmount);
                                $PayableAmount->SetAttribute("currencyID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['PayableAmount']['currencyID']);
                                //<sac:Percent>
                                if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['Percent'])){
                                    $Percent = $xml->createElement("cbc:Percent", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$i]['Percent'].".00");
                                    $Percent = $AdditionalMonetaryTotal->appendChild($Percent);
                                }
                        }
                        for ($i=1; $i<=sizeof($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty']); $i++) {
                            //<sac:AdditionalProperty>
                            $AdditionalProperty = $xml->createElement("sac:AdditionalProperty");
                            $AdditionalProperty = $AdditionalInformation->appendChild($AdditionalProperty);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][$i]['ID']);
                                $ID = $AdditionalProperty->appendChild($ID);
                                //<cbc:Value
                                $Value = $xml->createElement("cbc:Value", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][$i]['Value']);
                                $Value = $AdditionalProperty->appendChild($Value);
                        }
                        //<sac:SUNATTransaction>
                        if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATTransaction']['ID'])){
                            $SUNATTransaction = $xml->createElement("sac:SUNATTransaction");
                            $SUNATTransaction = $AdditionalInformation->appendChild($SUNATTransaction);
                                //<cbc:ID
                                $ID = $xml->createElement("cbc:ID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATTransaction']['ID']);
                                $ID = $SUNATTransaction->appendChild($ID);
                        }
                        //sac:SUNATCosts
                        if(isset($arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATCosts']['RoadTransport']['LicensePlateID'])){
                            $SUNATCosts = $xml->createElement("sac:SUNATCosts");
                            $SUNATCosts = $AdditionalInformation->appendChild($SUNATCosts);
                                //cac:RoadTransport
                                $RoadTransport = $xml->createElement("cac:RoadTransport");
                                $RoadTransport = $SUNATCosts->appendChild($RoadTransport);
                                    //cbc:LicensePlateID
                                    $LicensePlateID = $xml->createElement("cbc:LicensePlateID", $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATCosts']['RoadTransport']['LicensePlateID']);
                                    $LicensePlateID = $RoadTransport->appendChild($LicensePlateID);
                        }
                }
                //<ext:UBLExtension>
                $UBLExtension = $xml->createElement("ext:UBLExtension");
                $UBLExtension = $UBLExtensions->appendChild($UBLExtension);
                    //<ext:ExtensionContent>
                    $ExtensionContent = $xml->createElement("ext:ExtensionContent");
                    $ExtensionContent = $UBLExtension->appendChild($ExtensionContent);
}
//<cbc:UBLVersionID>
function add_UBLVersionID($arr,$nodo,$tipodoc) {
    global $root, $xml;
    $UBLVersionID = $xml->createElement("cbc:UBLVersionID", $arr['UBLVersionID']);
    $UBLVersionID = $root->appendChild($UBLVersionID);
}
//<cbc:CustomizationID>
function add_CustomizationID($arr,$nodo,$tipodoc) {
    global $root, $xml;
    $CustomizationID = $xml->createElement("cbc:CustomizationID", $arr['CustomizationID']);
    $CustomizationID = $root->appendChild($CustomizationID);
}

function add_ID($arr,$nodo,$tipodoc){
    global $root, $xml;
    $ID = $xml->createElement("cbc:ID", $arr['ID']);
    $ID = $root->appendChild($ID);
}

function add_IssueDate($arr,$nodo,$tipodoc) {
    global $root, $xml;
    $IssueDate = $xml->createElement("cbc:IssueDate", xml_fecha($arr['IssueDate']));
    $IssueDate = $root->appendChild($IssueDate);
    //xml_AddAtt($IssueDate, array("IssueDate"=>xml_fecha($arr['IssueDate'])));
}
function add_InvoiceTypeCode($arr,$nodo,$tipodoc){
    global $root, $xml;
    $InvoiceTypeCode = $xml->createElement("cbc:InvoiceTypeCode", $arr['InvoiceTypeCode']);
    $InvoiceTypeCode = $root->appendChild($InvoiceTypeCode);
}
function add_DespatchAdviceTypeCode($arr,$nodo,$tipodoc){
    global $root, $xml;
    $DespatchAdviceTypeCode = $xml->createElement("cbc:DespatchAdviceTypeCode", $arr['DespatchAdviceTypeCode']);
    $DespatchAdviceTypeCode = $root->appendChild($DespatchAdviceTypeCode);
}
function add_Note($arr,$nodo,$tipodoc){
    global $root, $xml;
    $Note = $xml->createElement("cbc:Note", $arr['Note']);
    $Note = $root->appendChild($Note);
}


//cac:AccountingCustomerParty
function add_AccountingSupplierParty($arr,$nodo,$tipodoc){
    global $root, $xml;
    $AccountingSupplierParty = $xml->createElement("cac:AccountingSupplierParty");
    $AccountingSupplierParty = $root->appendChild($AccountingSupplierParty);

        $CustomerAssignedAccountID = $xml->createElement("cbc:CustomerAssignedAccountID", $arr['AccountingSupplierParty']['CustomerAssignedAccountID']);
        $CustomerAssignedAccountID = $AccountingSupplierParty->appendChild($CustomerAssignedAccountID);

        $AdditionalAccountID = $xml->createElement("cbc:AdditionalAccountID", $arr['AccountingSupplierParty']['AdditionalAccountID']);
        $AdditionalAccountID = $AccountingSupplierParty->appendChild($AdditionalAccountID);

        $Party = $xml->createElement("cac:Party");
        $Party = $AccountingSupplierParty->appendChild($Party);
            if(strlen($arr['AccountingSupplierParty']['Party']['PartyName']['Name'])>0){
                $PartyName = $xml->createElement("cac:PartyName");
                $PartyName = $Party->appendChild($PartyName);

                    $Name = $xml->createElement("cbc:Name");
                    $Name = $PartyName->appendChild($Name);
                    $texto = $Name->appendChild(new DOMCdataSection($arr['AccountingSupplierParty']['Party']['PartyName']['Name']));
            }

            if(isset($arr['AccountingSupplierParty']['Party']['PostalAddress'])){

                $PostalAddress = $xml->createElement("cac:PostalAddress");
                $PostalAddress = $Party->appendChild($PostalAddress);

                    $ID = $xml->createElement("cbc:ID", $arr['AccountingSupplierParty']['Party']['PostalAddress']['ID']);
                    $ID = $PostalAddress->appendChild($ID);

                    $StreetName =  $xml->createElement("cbc:StreetName", $arr['AccountingSupplierParty']['Party']['PostalAddress']['StreetName']);
                    $StreetName = $PostalAddress->appendChild($StreetName);

                    $CitySubdivisionName =  $xml->createElement("cbc:CitySubdivisionName", $arr['AccountingSupplierParty']['Party']['PostalAddress']['CitySubdivisionName']);
                    $CitySubdivisionName = $PostalAddress->appendChild($CitySubdivisionName);

                    $CityName =  $xml->createElement("cbc:CityName", $arr['AccountingSupplierParty']['Party']['PostalAddress']['CityName']);
                    $CityName = $PostalAddress->appendChild($CityName);

                    $CountrySubentity = $xml->createElement("cbc:CountrySubentity", $arr['AccountingSupplierParty']['Party']['PostalAddress']['CountrySubentity']);
                    $CountrySubentity = $PostalAddress->appendChild($CountrySubentity);

                    $District = $xml->createElement("cbc:District", $arr['AccountingSupplierParty']['Party']['PostalAddress']['District']);
                    $District = $PostalAddress->appendChild($District);

                    //<cac:Country>
                    $Country = $xml->createElement("cac:Country");
                    $Country = $PostalAddress->appendChild($Country);
                        //<cbc:IdentificationCode>
                        $IdentificationCode = $xml->createElement("cbc:IdentificationCode", $arr['AccountingSupplierParty']['Party']['PostalAddress']['Country']['IdentificationCode']);
                        $IdentificationCode = $Country->appendChild($IdentificationCode);
            }
            $PartyLegalEntity = $xml->createElement("cac:PartyLegalEntity");
            $PartyLegalEntity = $Party->appendChild($PartyLegalEntity);

                $RegistrationName = $xml->createElement("cbc:RegistrationName", $arr['AccountingSupplierParty']['Party']['PartyLegalEntity']['RegistrationName']);
                $RegistrationName = $PartyLegalEntity->appendChild($RegistrationName);
}
//cac:DespatchSupplierParty/
function add_DespatchSupplierParty($arr,$nodo,$tipodoc){
    global $root, $xml;
    $DespatchSupplierParty = $xml->createElement("cac:DespatchSupplierParty");
    $DespatchSupplierParty = $root->appendChild($DespatchSupplierParty);

        $CustomerAssignedAccountID = $xml->createElement("cbc:CustomerAssignedAccountID", $arr['DespatchSupplierParty']['CustomerAssignedAccountID']['AccountID']);
        $CustomerAssignedAccountID = $DespatchSupplierParty->appendChild($CustomerAssignedAccountID);
        $CustomerAssignedAccountID->SetAttribute("schemeID", $arr['DespatchSupplierParty']['CustomerAssignedAccountID']['schemeID']);

        $Party = $xml->createElement("cac:Party");
        $Party = $DespatchSupplierParty->appendChild($Party);
            if(strlen($arr['DespatchSupplierParty']['Party']['PartyLegalEntity']['RegistrationName'])>0){

                //<cac:PartyLegalEntity>
                $PartyLegalEntity = $xml->createElement("cac:PartyLegalEntity");
                $PartyLegalEntity = $Party->appendChild($PartyLegalEntity);
                    //<cbc:RegistrationName>
                    $RegistrationName = $xml->createElement("cbc:RegistrationName");
                    $RegistrationName = $PartyLegalEntity->appendChild($RegistrationName);
                    $texto = $RegistrationName->appendChild(new DOMCdataSection($arr['DespatchSupplierParty']['Party']['PartyLegalEntity']['RegistrationName']));
            }
}

function add_AccountingCustomerParty($arr,$nodo,$tipodoc){
    global $root, $xml;
    $AccountingCustomerParty = $xml->createElement("cac:AccountingCustomerParty");
    $AccountingCustomerParty = $root->appendChild($AccountingCustomerParty);
        //<cbc:CustomerAssignedAccountID>
        $CustomerAssignedAccountID = $xml->createElement("cbc:CustomerAssignedAccountID", $arr['AccountingCustomerParty']['CustomerAssignedAccountID']);
        $CustomerAssignedAccountID = $AccountingCustomerParty->appendChild($CustomerAssignedAccountID);
        //<cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
        $AdditionalAccountID = $xml->createElement("cbc:AdditionalAccountID", $arr['AccountingCustomerParty']['AdditionalAccountID']);
        $AdditionalAccountID = $AccountingCustomerParty->appendChild($AdditionalAccountID);
        //<cac:Party>
        $Party = $xml->createElement("cac:Party");
        $Party = $AccountingCustomerParty->appendChild($Party);
            //<cac:PartyLegalEntity>
            $PartyLegalEntity = $xml->createElement("cac:PartyLegalEntity");
            $PartyLegalEntity = $Party->appendChild($PartyLegalEntity);
                //<cbc:RegistrationName>
                $RegistrationName = $xml->createElement("cbc:RegistrationName");
                $RegistrationName = $PartyLegalEntity->appendChild($RegistrationName);
                $texto = $RegistrationName->appendChild(new DOMCdataSection($arr['AccountingCustomerParty']['Party']['PartyLegalEntity']['RegistrationName']));

}
function add_DeliveryCustomerParty($arr,$nodo,$tipodoc){
    global $root, $xml;
    $DeliveryCustomerParty = $xml->createElement("cac:DeliveryCustomerParty");
    $DeliveryCustomerParty = $root->appendChild($DeliveryCustomerParty);
        //<cbc:CustomerAssignedAccountID>
        $CustomerAssignedAccountID = $xml->createElement("cbc:CustomerAssignedAccountID", $arr['DeliveryCustomerParty']['CustomerAssignedAccountID']['AccountID']);
        $CustomerAssignedAccountID = $DeliveryCustomerParty->appendChild($CustomerAssignedAccountID);
        $CustomerAssignedAccountID->SetAttribute("schemeID", $arr['DeliveryCustomerParty']['CustomerAssignedAccountID']['schemeID']);

        //<cac:Party>
        $Party = $xml->createElement("cac:Party");
        $Party = $DeliveryCustomerParty->appendChild($Party);
            //<cac:PartyLegalEntity>
            $PartyLegalEntity = $xml->createElement("cac:PartyLegalEntity");
            $PartyLegalEntity = $Party->appendChild($PartyLegalEntity);
                //<cbc:RegistrationName>
                $RegistrationName = $xml->createElement("cbc:RegistrationName");
                $RegistrationName = $PartyLegalEntity->appendChild($RegistrationName);
                $texto = $RegistrationName->appendChild(new DOMCdataSection($arr['DeliveryCustomerParty']['Party']['PartyLegalEntity']['RegistrationName']));

}

function add_Shipment($arr,$nodo,$tipodoc){
    global $root, $xml;
    $Shipment = $xml->createElement("cac:Shipment");
    $Shipment = $root->appendChild($Shipment);
        //<cbc:ID>
        $ID = $xml->createElement("cbc:ID", "1");
        $Shipment->appendChild($ID);
        //<cbc:HandlingCode>
        $HandlingCode = $xml->createElement("cbc:HandlingCode", $arr['Shipment']['HandlingCode']);
        $Shipment->appendChild($HandlingCode);
        //<cbc:Information>
        $Information = $xml->createElement("cbc:Information", $arr['Shipment']['Information']);
        $Shipment->appendChild($Information);
        //<cbc:GrossWeightMeasure>
        $GrossWeightMeasure = $xml->createElement("cbc:GrossWeightMeasure", $arr['Shipment']['GrossWeightMeasure']['Measure']);
        $GrossWeightMeasure = $Shipment->appendChild($GrossWeightMeasure);
        $GrossWeightMeasure->SetAttribute("unitCode", $arr['Shipment']['GrossWeightMeasure']['unitCode']);
        //<cbc:TotalTransportHandlingUnitQuantity>
        $TotalTransportHandlingUnitQuantity = $xml->createElement("cbc:TotalTransportHandlingUnitQuantity", $arr['Shipment']['TotalTransportHandlingUnitQuantity']);
        $Shipment->appendChild($TotalTransportHandlingUnitQuantity);
        //<cbc:SplitConsignmentIndicator>
        $SplitConsignmentIndicator = $xml->createElement("cbc:SplitConsignmentIndicator", $arr['Shipment']['SplitConsignmentIndicator']);
        $Shipment->appendChild($SplitConsignmentIndicator);
        //<cac:ShipmentStage>
        $ShipmentStage = $xml->createElement("cac:ShipmentStage");
        $ShipmentStage = $Shipment->appendChild($ShipmentStage);
            //<cbc:TransportModeCode>
            $TransportModeCode = $xml->createElement("cbc:TransportModeCode", $arr['Shipment']['ShipmentStage']['TransportModeCode'] );
            $ShipmentStage->appendChild($TransportModeCode);
            //<cac:TransitPeriod>
            $TransitPeriod = $xml->createElement("cac:TransitPeriod");
            $TransitPeriod = $ShipmentStage->appendChild($TransitPeriod);
                //<cbc:StartDate>
                $StartDate = $xml->createElement("cbc:StartDate", $arr['Shipment']['ShipmentStage']['TransitPeriod']['StartDate'] );
                $TransitPeriod->appendChild($StartDate);
            //<cac:CarrierParty>
            $CarrierParty = $xml->createElement("cac:CarrierParty");
            $CarrierParty = $ShipmentStage->appendChild($CarrierParty);
                //<cac:PartyIdentification>
                $PartyIdentification = $xml->createElement("cac:PartyIdentification");
                $PartyIdentification = $CarrierParty->appendChild($PartyIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyIdentification']['ID']['ID'] );
                    $ID = $PartyIdentification->appendChild($ID);
                    $ID->SetAttribute("schemeID", $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyIdentification']['ID']['schemeID']);
                //<cac:PartyName>
                $PartyName = $xml->createElement("cac:PartyName");
                $PartyName = $CarrierParty->appendChild($PartyName);
                    //<cbc:Name>
                    $Name = $xml->createElement("cbc:Name", $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyName']['Name'] );
                    $PartyName->appendChild($Name);

            //<cac:TransportMeans>
            $TransportMeans = $xml->createElement("cac:TransportMeans");
            $TransportMeans = $ShipmentStage->appendChild($TransportMeans);
                //<cac:RoadTransport>
                $RoadTransport = $xml->createElement("cac:RoadTransport");
                $RoadTransport = $TransportMeans->appendChild($RoadTransport);
                    //<cbc:LicensePlateID>
                    $LicensePlateID = $xml->createElement("cbc:LicensePlateID", $arr['Shipment']['ShipmentStage']['TransportMeans']['RoadTransport']['LicensePlateID']);
                    $RoadTransport->appendChild($LicensePlateID);

            //<cac:DriverPerson>
            $DriverPerson = $xml->createElement("cac:DriverPerson");
            $DriverPerson = $ShipmentStage->appendChild($DriverPerson);
                //<cbc:ID>
                $ID = $xml->createElement("cbc:ID", $arr['Shipment']['ShipmentStage']['DriverPerson']['ID']['ID']);
                $ID = $DriverPerson->appendChild($ID);
                $ID->SetAttribute("schemeID", $arr['Shipment']['ShipmentStage']['DriverPerson']['ID']['schemeID']);
        //<cac:Delivery>
        $Delivery = $xml->createElement("cac:Delivery");
        $Delivery = $Shipment->appendChild($Delivery);
            //<cac:DeliveryAddress>
            $DeliveryAddress = $xml->createElement("cac:DeliveryAddress");
            $DeliveryAddress = $Delivery->appendChild($DeliveryAddress);
                //<cbc:ID>
                $ID = $xml->createElement("cbc:ID", $arr['Shipment']['Delivery']['DeliveryAddress']['ID']);
                $DeliveryAddress->appendChild($ID);
                //<cbc:StreetName>
                $StreetName = $xml->createElement("cbc:StreetName", $arr['Shipment']['Delivery']['DeliveryAddress']['StreetName']);
                $DeliveryAddress->appendChild($StreetName);
        //<cac:OriginAddress>
        $OriginAddress = $xml->createElement("cac:OriginAddress");
        $OriginAddress = $Shipment->appendChild($OriginAddress);
            //<cbc:ID>
            $ID = $xml->createElement("cbc:ID", $arr['Shipment']['OriginAddress']['ID']);
            $OriginAddress->appendChild($ID);
            //<cbc:StreetName>
            $StreetName = $xml->createElement("cbc:StreetName", $arr['Shipment']['OriginAddress']['StreetName']);
            $OriginAddress->appendChild($StreetName);
}
//<cac:PrepaidPayment>
function add_PrepaidPayment($arr,$nodo,$tipodoc){
    global $root, $xml;
    if(isset($arr['PrepaidPayment']['PaidAmount']['Amount'])){
        $PrepaidPayment = $xml->createElement("cac:PrepaidPayment");
        $PrepaidPayment = $root->appendChild($PrepaidPayment);
            //cbc:ID
            $ID = $xml->createElement("cbc:ID", $arr['PrepaidPayment']['ID']['ID']);
            $ID = $PrepaidPayment->appendChild($ID);
            $ID->SetAttribute("schemeID", $arr['PrepaidPayment']['ID']['schemeID']);
            //<cbc:PaidAmount
            $PaidAmount = $xml->createElement("cbc:PaidAmount", $arr['PrepaidPayment']['PaidAmount']['Amount']);
            $PaidAmount = $PrepaidPayment->appendChild($PaidAmount);
            $PaidAmount->SetAttribute("currencyID", $arr['PrepaidPayment']['PaidAmount']['currencyID']);
            //cbc:InstructionID
            $InstructionID = $xml->createElement("cbc:InstructionID", $arr['PrepaidPayment']['InstructionID']['InstructionID']);
            $InstructionID = $PrepaidPayment->appendChild($InstructionID);
            $InstructionID->SetAttribute("schemeID", $arr['PrepaidPayment']['InstructionID']['schemeID']);
    }
}
//cac:InvoiceLine
function add_InvoiceLine($arr,$nodo,$tipodoc) {
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['InvoiceLine']); $i++) {
        $InvoiceLine = $xml->createElement("cac:InvoiceLine");
        $InvoiceLine = $root->appendChild($InvoiceLine);
            //<cbc:ID>
            $ID = $xml->createElement("cbc:ID", $arr['InvoiceLine'][$i]['ID']);
            $ID = $InvoiceLine->appendChild($ID);
            //cbc:InvoicedQuantity
            $InvoicedQuantity = $xml->createElement("cbc:InvoicedQuantity", $arr['InvoiceLine'][$i]['InvoicedQuantity']['Quantity']);
            $InvoicedQuantity = $InvoiceLine->appendChild($InvoicedQuantity);
            $InvoicedQuantity->SetAttribute("unitCode", $arr['InvoiceLine'][$i]['InvoicedQuantity']['unitCode']);
            //cbc:LineExtensionAmount
            $LineExtensionAmount = $xml->createElement("cbc:LineExtensionAmount", $arr['InvoiceLine'][$i]['LineExtensionAmount']['Amount']);
            $LineExtensionAmount = $InvoiceLine->appendChild($LineExtensionAmount);
            $LineExtensionAmount->SetAttribute("currencyID", $arr['InvoiceLine'][$i]['LineExtensionAmount']['currencyID']);
            //cac:PricingReference
            $PricingReference = $xml->createElement("cac:PricingReference");
            $PricingReference = $InvoiceLine->appendChild($PricingReference);

            for ($ii=1; $ii<=sizeof($arr['InvoiceLine'][$i]['PricingReference']['AlternativeConditionPrice']); $ii++) {
                //cac:AlternativeConditionPrice
                $AlternativeConditionPrice = $xml->createElement("cac:AlternativeConditionPrice");
                $AlternativeConditionPrice = $PricingReference->appendChild($AlternativeConditionPrice);
                    //cbc:PriceAmount
                    $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['InvoiceLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['Price']);
                    $PriceAmount = $AlternativeConditionPrice->appendChild($PriceAmount);
                    $PriceAmount->setAttribute("currencyID", $arr['InvoiceLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['currencyID']);
                    //cbc:PriceTypeCode
                    $PriceTypeCode = $xml->createElement("cbc:PriceTypeCode", $arr['InvoiceLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceTypeCode']);
                    $PriceTypeCode = $AlternativeConditionPrice->appendChild($PriceTypeCode);
            }
            //<cac:AllowanceCharge>
            if( isset($arr['InvoiceLine'][$i]['AllowanceCharge'])){
                $AllowanceCharge = $xml->createElement("cac:AllowanceCharge");
                $AllowanceCharge = $InvoiceLine->appendChild($AllowanceCharge);
                    //<cbc:ChargeIndicator>
                    $ChargeIndicator = $xml->createElement("cbc:ChargeIndicator", $arr['InvoiceLine'][$i]['AllowanceCharge']['ChargeIndicator']);
                    $ChargeIndicator = $AllowanceCharge->appendChild($ChargeIndicator);
                    //<cbc:Amount
                    $Amount = $xml->createElement("cbc:Amount", $arr['InvoiceLine'][$i]['AllowanceCharge']['Amount']['Amount']);
                    $Amount = $AllowanceCharge->appendChild($Amount);
                    $Amount->SetAttribute("currencyID", $arr['InvoiceLine'][$i]['AllowanceCharge']['Amount']['currencyID']);
            }

            for ($iii=1; $iii<=sizeof($arr['InvoiceLine'][$i]['TaxTotal']); $iii++) {
                $TaxTotal = $xml->createElement("cac:TaxTotal");
                $TaxTotal = $InvoiceLine->appendChild($TaxTotal);
                    //<cbc:TaxAmount
                    $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxAmount']['Amount']);
                    $TaxAmount = $TaxTotal->appendChild($TaxAmount);
                    $TaxAmount->SetAttribute("currencyID", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxAmount']['currencyID']);
                    //<cac:TaxSubtotal>
                    $TaxSubtotal = $xml->createElement("cac:TaxSubtotal");
                    $TaxSubtotal = $TaxTotal->appendChild($TaxSubtotal);
                        //cbc:TaxAmount
                        $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['Amount']);
                        $TaxAmount = $TaxSubtotal->appendChild($TaxAmount);
                        $TaxAmount->SetAttribute("currencyID", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['currencyID']);
                        //<cac:TaxCategory>
                        $TaxCategory = $xml->createElement("cac:TaxCategory");
                        $TaxCategory = $TaxSubtotal->appendChild($TaxCategory);
                            //<cbc:TaxExemptionReasonCode>10</cbc:TaxExemptionReasonCode>
                            if(isset($arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'])){
                                $TaxExemptionReasonCode = $xml->createElement("cbc:TaxExemptionReasonCode", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode']);
                                $TaxExemptionReasonCode = $TaxCategory->appendChild($TaxExemptionReasonCode);
                            }else{
                                //<cbc:TierRange>02</cbc:TierRange>
                                $TierRange = $xml->createElement("cbc:TierRange", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TierRange']);
                                $TierRange = $TaxCategory->appendChild($TierRange);
                            }
                            //<cac:TaxScheme>
                            $TaxScheme = $xml->createElement("cac:TaxScheme");
                            $TaxScheme = $TaxCategory->appendChild($TaxScheme);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']);
                                $ID = $TaxScheme->appendChild($ID);
                                //<cbc:Name>
                                $Name = $xml->createElement("cbc:Name", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']);
                                $Name = $TaxScheme->appendChild($Name);
                                //<cbc:TaxTypeCode>
                                $TaxTypeCode = $xml->createElement("cbc:TaxTypeCode", $arr['InvoiceLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']);
                                $TaxTypeCode = $TaxScheme->appendChild($TaxTypeCode);
            }

            //cac:Item
            $Item = $xml->createElement("cac:Item");
            $Item = $InvoiceLine->appendChild($Item);
                //cbc:Description
                $Description = $xml->createElement("cbc:Description");
                $Description = $Item->appendChild($Description);
                $texto = $Description->appendChild(new DOMCdataSection($arr['InvoiceLine'][$i]['Item']['Description']));
                //<cac:SellersItemIdentification>
                $SellersItemIdentification = $xml->createElement("cac:SellersItemIdentification");
                $SellersItemIdentification = $Item->appendChild($SellersItemIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['InvoiceLine'][$i]['Item']['SellersItemIdentification']['ID']);
                    $ID = $SellersItemIdentification->appendChild($ID);
                //<cac:AdditionalItemIdentification>
                $AdditionalItemIdentification = $xml->createElement("cac:AdditionalItemIdentification");
                $AdditionalItemIdentification = $Item->appendChild($AdditionalItemIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['InvoiceLine'][$i]['Item']['AdditionalItemIdentification']['ID']);
                    $ID = $AdditionalItemIdentification->appendChild($ID);

            //cac:Price
            $price = $xml->createElement("cac:Price");
            $price = $InvoiceLine->appendChild($price);
                //cbc:PriceAmount
                $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['InvoiceLine'][$i]['Price']['PriceAmount']['Price']);
                $PriceAmount = $price->appendChild($PriceAmount);
                $PriceAmount->setAttribute("currencyID", $arr['InvoiceLine'][$i]['Price']['PriceAmount']['currencyID']);

    }
}
//cac::DespatchLine
function add_DespatchLine($arr,$nodo,$tipodoc) {
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['DespatchLine']); $i++) {
        $DespatchLine = $xml->createElement("cac:DespatchLine");
        $DespatchLine = $root->appendChild($DespatchLine);
            //<cbc:ID>
            $ID = $xml->createElement("cbc:ID", $arr['DespatchLine'][$i]['ID']);
            $ID = $DespatchLine->appendChild($ID);
            //cbc:DeliveredQuantity
            $DeliveredQuantity = $xml->createElement("cbc:DeliveredQuantity", $arr['DespatchLine'][$i]['DeliveredQuantity']['Quantity']);
            $InvoicedQuantity = $DespatchLine->appendChild($DeliveredQuantity);
            $DeliveredQuantity->SetAttribute("unitCode", $arr['DespatchLine'][$i]['DeliveredQuantity']['unitCode']);
            //<cac:OrderLineReference>
            $OrderLineReference = $xml->createElement("cac:OrderLineReference");
            $OrderLineReference = $DespatchLine->appendChild($OrderLineReference);
                //<cbc:LineID>
                $LineID = $xml->createElement("cbc:LineID", $arr['DespatchLine'][$i]['OrderLineReference']['LineID']);
                $OrderLineReference->appendChild($LineID);

            //cac:Item
            $Item = $xml->createElement("cac:Item");
            $Item = $DespatchLine->appendChild($Item);
                //cbc:Name
                $Name = $xml->createElement("cbc:Name");
                $Name = $Item->appendChild($Name);
                $texto = $Name->appendChild(new DOMCdataSection($arr['DespatchLine'][$i]['Item']['Name']));
                //<cac:SellersItemIdentification>
                $SellersItemIdentification = $xml->createElement("cac:SellersItemIdentification");
                $SellersItemIdentification = $Item->appendChild($SellersItemIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['DespatchLine'][$i]['Item']['SellersItemIdentification']['ID']);
                    $SellersItemIdentification->appendChild($ID);
    }
}
//<cac:TaxTotal>
function add_TaxTotal($arr,$nodo,$tipodoc){
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['TaxTotal']); $i++) {

        $TaxTotal = $xml->createElement("cac:TaxTotal");
        $TaxTotal = $root->appendChild($TaxTotal);
            //<cbc:TaxAmount
            $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['TaxTotal'][$i]['TaxAmount']['Amount']);
            $TaxAmount = $TaxTotal->appendChild($TaxAmount);
            $TaxAmount->SetAttribute("currencyID", $arr['TaxTotal'][$i]['TaxAmount']['currencyID']);
            //<cac:TaxSubtotal>
            $TaxSubtotal = $xml->createElement("cac:TaxSubtotal");
            $TaxSubtotal = $TaxTotal->appendChild($TaxSubtotal);
                //cbc:TaxAmount
                $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['TaxTotal'][$i]['TaxSubtotal']['TaxAmount']['Amount']);
                $TaxAmount = $TaxSubtotal->appendChild($TaxAmount);
                $TaxAmount->SetAttribute("currencyID", $arr['TaxTotal'][$i]['TaxSubtotal']['TaxAmount']['currencyID']);
                //<cac:TaxCategory>
                $TaxCategory = $xml->createElement("cac:TaxCategory");
                $TaxCategory = $TaxSubtotal->appendChild($TaxCategory);
                    //<cac:TaxScheme>
                    $TaxScheme = $xml->createElement("cac:TaxScheme");
                    $TaxScheme = $TaxCategory->appendChild($TaxScheme);
                        //<cbc:ID>
                        $ID = $xml->createElement("cbc:ID", $arr['TaxTotal'][$i]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']);
                        $ID = $TaxScheme->appendChild($ID);
                        //<cbc:Name>
                        $Name = $xml->createElement("cbc:Name", $arr['TaxTotal'][$i]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']);
                        $Name = $TaxScheme->appendChild($Name);
                        //<cbc:TaxTypeCode>
                        $TaxTypeCode = $xml->createElement("cbc:TaxTypeCode", $arr['TaxTotal'][$i]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']);
                        $TaxTypeCode = $TaxScheme->appendChild($TaxTypeCode);
    }
}
//<cac:LegalMonetaryTotal>
function add_LegalMonetaryTotal($arr,$nodo,$tipodoc){
    global $root, $xml;
    $LegalMonetaryTotal = $xml->createElement("cac:LegalMonetaryTotal");
    $LegalMonetaryTotal = $root->appendChild($LegalMonetaryTotal);
        //<cbc:AllowanceTotalAmount
        if(isset($arr['LegalMonetaryTotal']['AllowanceTotalAmount'])){
            $AllowanceTotalAmount = $xml->createElement("cbc:AllowanceTotalAmount", $arr['LegalMonetaryTotal']['AllowanceTotalAmount']['TotalAmount']);
            $AllowanceTotalAmount = $LegalMonetaryTotal->appendChild($AllowanceTotalAmount);
            $AllowanceTotalAmount->SetAttribute("currencyID", $arr['LegalMonetaryTotal']['AllowanceTotalAmount']['currencyID']);
        }
        //<cbc:ChargeTotalAmount
        if(isset($arr['LegalMonetaryTotal']['ChargeTotalAmount'])){
            $ChargeTotalAmount = $xml->createElement("cbc:ChargeTotalAmount", $arr['LegalMonetaryTotal']['ChargeTotalAmount']['TotalAmount']);
            $ChargeTotalAmount = $LegalMonetaryTotal->appendChild($ChargeTotalAmount);
            $ChargeTotalAmount->SetAttribute("currencyID", $arr['LegalMonetaryTotal']['ChargeTotalAmount']['currencyID']);
        }
        //cbc:PrepaidAmount
        if(isset($arr['LegalMonetaryTotal']['PrepaidAmount']['Amount'])){
            $PrepaidAmount = $xml->createElement("cbc:PrepaidAmount", $arr['LegalMonetaryTotal']['PrepaidAmount']['Amount']);
            $PrepaidAmount = $LegalMonetaryTotal->appendChild($PrepaidAmount);
            $PrepaidAmount->SetAttribute("currencyID", $arr['LegalMonetaryTotal']['PrepaidAmount']['currencyID']);
        }
        //<cbc:PayableAmount
        if(isset($arr['LegalMonetaryTotal']['PayableAmount'])){
            $PayableAmount = $xml->createElement("cbc:PayableAmount", $arr['LegalMonetaryTotal']['PayableAmount']['TotalAmount']);
            $PayableAmount = $LegalMonetaryTotal->appendChild($PayableAmount);
            $PayableAmount->SetAttribute("currencyID", $arr['LegalMonetaryTotal']['PayableAmount']['currencyID']);
        }
}
//<cbc:DocumentCurrencyCode>
function add_DocumentCurrencyCode($arr,$nodo,$tipodoc){
    global $root, $xml;
    $DocumentCurrencyCode = $xml->createElement("cbc:DocumentCurrencyCode", $arr['DocumentCurrencyCode']);
    $DocumentCurrencyCode = $root->appendChild($DocumentCurrencyCode);
}
//<cac:DespatchDocumentReference>
function add_DespatchDocumentReference($arr,$nodo,$tipodoc){
    global $root, $xml;
    if(isset($arr['DespatchDocumentReference'])){
            for ($i=1; $i<=sizeof($arr['DespatchDocumentReference']); $i++) {
                $DespatchDocumentReference = $xml->createElement("cac:DespatchDocumentReference");
                $DespatchDocumentReference = $root->appendChild($DespatchDocumentReference);

                    $ID = $xml->createElement("cbc:ID", $arr['DespatchDocumentReference'][$i]['ID']);
                    $DespatchDocumentReference->appendChild($ID);
                    $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode", $arr['DespatchDocumentReference'][$i]['DocumentTypeCode']);
                    $DespatchDocumentReference->appendChild($DocumentTypeCode);
            }
        }
}
//<cac:AdditionalDocumentReference>
function add_AdditionalDocumentReference($arr,$nodo,$tipodoc){
    global $root, $xml;
    $AdditionalDocumentReference = $xml->createElement("cac:AdditionalDocumentReference");
    $AdditionalDocumentReference = $root->appendChild($AdditionalDocumentReference);
        //<cbc:ID>
        $ID = $xml->createElement("cbc:ID");
        $ID = $AdditionalDocumentReference->appendChild($ID);
        xml_AddAtt($ID, array('ID' => $arr['AdditionalDocumentReference']['ID'] ));
        //<cbc:DocumentTypeCode>
        $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode");
        $DocumentTypeCode = $AdditionalDocumentReference->appendChild($DocumentTypeCode);
        xml_AddAtt($DocumentTypeCode, array('DocumentTypeCode' => $arr['AdditionalDocumentReference']['DocumentTypeCode'] ));
}
//cac:Signature
function add_Signature($arr,$nodo,$tipodoc){
    global $root, $xml;
    $Signature = $xml->createElement("cac:Signature");
    $Signature = $root->appendChild($Signature);
        //cbc:ID
        $ID = $xml->createElement("cbc:ID", $arr['Signature']['ID2']);
        $ID = $Signature->appendChild($ID);
        //<cac:SignatoryParty>
        $SignatoryParty = $xml->createElement("cac:SignatoryParty");
        $SignatoryParty = $Signature->appendChild($SignatoryParty);
            //<cac:PartyIdentification>
            $PartyIdentification = $xml->createElement("cac:PartyIdentification");
            $PartyIdentification = $SignatoryParty->appendChild($PartyIdentification);
                //<cbc:ID
                $ID = $xml->createElement("cbc:ID", $arr['Signature']['SignatoryParty']['PartyIdentification']['ID']);
                $ID = $PartyIdentification->appendChild($ID);
                //<cac:PartyName>
            $PartyName = $xml->createElement("cac:PartyName");
            $PartyName = $SignatoryParty->appendChild($PartyName);
                //<cbc:Name>
                $Name = $xml->createElement("cbc:Name");
                $Name = $PartyName->appendChild($Name);
                $textto = $Name->appendChild(new DOMCdataSection($arr['Signature']['SignatoryParty']['PartyName']['Name']));
        //<cac:DigitalSignatureAttachment>
        $DigitalSignatureAttachment = $xml->createElement("cac:DigitalSignatureAttachment");
        $DigitalSignatureAttachment = $Signature->appendChild($DigitalSignatureAttachment);
            //<cac:ExternalReference>
            $ExternalReference = $xml->createElement("cac:ExternalReference");
            $ExternalReference = $DigitalSignatureAttachment->appendChild($ExternalReference);
                //<cbc:URI>
                $URI = $xml->createElement("cbc:URI", $arr['Signature']['DigitalSignatureAttachment']['ExternalReference']['URI']);
                $URI = $ExternalReference->appendChild($URI);
}
// Debaja
function add_ReferenceDate($arr,$nodo,$tipodoc){
    global $root,$xml;
    $ReferenceDate = $xml->createElement("cbc:ReferenceDate", $arr['ReferenceDate']);
    $ReferenceDate = $root->appendChild($ReferenceDate);
}
function add_VoidedDocumentsLine($arr,$nodo,$tipodoc){
    global $root,$xml;
    for ($i=1; $i<=sizeof($arr['VoidedDocumentsLine']); $i++) {
        $VoidedDocumentsLine = $xml->createElement("sac:VoidedDocumentsLine");
        $VoidedDocumentsLine = $root->appendChild($VoidedDocumentsLine);

            $LineID = $xml->createElement("cbc:LineID", $arr['VoidedDocumentsLine'][$i]['LineID']);
            $LineID = $VoidedDocumentsLine->appendChild($LineID);

            $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode", $arr['VoidedDocumentsLine'][$i]['DocumentTypeCode']);
            $DocumentTypeCode = $VoidedDocumentsLine->appendChild($DocumentTypeCode);

            $DocumentSerialID = $xml->createElement("sac:DocumentSerialID", $arr['VoidedDocumentsLine'][$i]['DocumentSerialID']);
            $DocumentSerialID = $VoidedDocumentsLine->appendChild($DocumentSerialID);

            $DocumentNumberID = $xml->createElement("sac:DocumentNumberID", $arr['VoidedDocumentsLine'][$i]['DocumentNumberID']);
            $DocumentNumberID = $VoidedDocumentsLine->appendChild($DocumentNumberID);

            $VoidReasonDescription = $xml->createElement("sac:VoidReasonDescription", $arr['VoidedDocumentsLine'][$i]['VoidReasonDescription']);
            $VoidReasonDescription = $VoidedDocumentsLine->appendChild($VoidReasonDescription);
    }
}

/*Nota de credito*/
function add_DiscrepancyResponse($arr,$nodo,$tipodoc){
    global $root, $xml;
    $DiscrepancyResponse = $xml->createElement("cac:DiscrepancyResponse");
    $DiscrepancyResponse = $root->appendChild($DiscrepancyResponse);

      $ReferenceID = $xml->createElement("cbc:ReferenceID", $arr['DiscrepancyResponse']['ReferenceID']);
      $ReferenceID = $DiscrepancyResponse->appendChild($ReferenceID);

      $ResponseCode = $xml->createElement("cbc:ResponseCode", $arr['DiscrepancyResponse']['ResponseCode']);
      $ResponseCode = $DiscrepancyResponse->appendChild($ResponseCode);

      $Description = $xml->createElement("cbc:Description", $arr['DiscrepancyResponse']['Description']);
      $Description = $DiscrepancyResponse->appendChild($Description);
}
function add_BillingReference($arr,$nodo,$tipodoc){
  global $root, $xml;
  $BillingReference = $xml->createElement("cac:BillingReference");
  $BillingReference = $root->appendChild($BillingReference);

    $InvoiceDocumenteReference = $xml->createElement("cac:InvoiceDocumentReference");
    $InvoiceDocumenteReference = $BillingReference->appendChild($InvoiceDocumenteReference);

      $ID = $xml->createElement("cbc:ID", $arr['BillingReference']['InvoiceDocumentReference']['ID']);
      $ID = $InvoiceDocumenteReference->appendChild($ID);

      $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode", $arr['BillingReference']['InvoiceDocumentReference']['DocumentTypeCode']);
      $DocumentTypeCode = $InvoiceDocumenteReference->appendChild($DocumentTypeCode);
}
function add_CreditNoteLine($arr,$nodo,$tipodoc) {
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['CreditNoteLine']); $i++) {
        $CreditNoteLine = $xml->createElement("cac:CreditNoteLine");
        $CreditNoteLine = $root->appendChild($CreditNoteLine);
            //<cbc:ID>
            $ID = $xml->createElement("cbc:ID", $arr['CreditNoteLine'][$i]['ID']);
            $ID = $CreditNoteLine->appendChild($ID);
            //cbc:InvoicedQuantity
            $InvoicedQuantity = $xml->createElement("cbc:CreditedQuantity", $arr['CreditNoteLine'][$i]['CreditedQuantity']['Quantity']);
            $InvoicedQuantity = $CreditNoteLine->appendChild($InvoicedQuantity);
            $InvoicedQuantity->SetAttribute("unitCode", $arr['CreditNoteLine'][$i]['CreditedQuantity']['unitCode']);
            //cbc:LineExtensionAmount
            $LineExtensionAmount = $xml->createElement("cbc:LineExtensionAmount", $arr['CreditNoteLine'][$i]['LineExtensionAmount']['Amount']);
            $LineExtensionAmount = $CreditNoteLine->appendChild($LineExtensionAmount);
            $LineExtensionAmount->SetAttribute("currencyID", $arr['CreditNoteLine'][$i]['LineExtensionAmount']['currencyID']);
            //cac:PricingReference
            $PricingReference = $xml->createElement("cac:PricingReference");
            $PricingReference = $CreditNoteLine->appendChild($PricingReference);

            for ($ii=1; $ii<=sizeof($arr['CreditNoteLine'][$i]['PricingReference']['AlternativeConditionPrice']); $ii++) {
                //cac:AlternativeConditionPrice
                $AlternativeConditionPrice = $xml->createElement("cac:AlternativeConditionPrice");
                $AlternativeConditionPrice = $PricingReference->appendChild($AlternativeConditionPrice);
                    //cbc:PriceAmount
                    $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['CreditNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['Price']);
                    $PriceAmount = $AlternativeConditionPrice->appendChild($PriceAmount);
                    $PriceAmount->setAttribute("currencyID", $arr['CreditNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['currencyID']);
                    //cbc:PriceTypeCode
                    $PriceTypeCode = $xml->createElement("cbc:PriceTypeCode", $arr['CreditNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceTypeCode']);
                    $PriceTypeCode = $AlternativeConditionPrice->appendChild($PriceTypeCode);
            }
            //<cac:AllowanceCharge>
            if( isset($arr['CreditNoteLine'][$i]['AllowanceCharge'])){
                $AllowanceCharge = $xml->createElement("cac:AllowanceCharge");
                $AllowanceCharge = $CreditNoteLine->appendChild($AllowanceCharge);
                    //<cbc:ChargeIndicator>
                    $ChargeIndicator = $xml->createElement("cbc:ChargeIndicator", $arr['CreditNoteLine'][$i]['AllowanceCharge']['ChargeIndicator']);
                    $ChargeIndicator = $AllowanceCharge->appendChild($ChargeIndicator);
                    //<cbc:Amount
                    $Amount = $xml->createElement("cbc:Amount", $arr['CreditNoteLine'][$i]['AllowanceCharge']['Amount']['Amount']);
                    $Amount = $AllowanceCharge->appendChild($Amount);
                    $Amount->SetAttribute("currencyID", $arr['CreditNoteLine'][$i]['AllowanceCharge']['Amount']['currencyID']);
            }

            for ($iii=1; $iii<=sizeof($arr['CreditNoteLine'][$i]['TaxTotal']); $iii++) {
                $TaxTotal = $xml->createElement("cac:TaxTotal");
                $TaxTotal = $CreditNoteLine->appendChild($TaxTotal);
                    //<cbc:TaxAmount
                    $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxAmount']['Amount']);
                    $TaxAmount = $TaxTotal->appendChild($TaxAmount);
                    $TaxAmount->SetAttribute("currencyID", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxAmount']['currencyID']);
                    //<cac:TaxSubtotal>
                    $TaxSubtotal = $xml->createElement("cac:TaxSubtotal");
                    $TaxSubtotal = $TaxTotal->appendChild($TaxSubtotal);
                        //cbc:TaxAmount
                        $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['Amount']);
                        $TaxAmount = $TaxSubtotal->appendChild($TaxAmount);
                        $TaxAmount->SetAttribute("currencyID", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['currencyID']);
                        //<cac:TaxCategory>
                        $TaxCategory = $xml->createElement("cac:TaxCategory");
                        $TaxCategory = $TaxSubtotal->appendChild($TaxCategory);
                            //<cbc:TaxExemptionReasonCode>10</cbc:TaxExemptionReasonCode>
                            if(isset($arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'])){
                                $TaxExemptionReasonCode = $xml->createElement("cbc:TaxExemptionReasonCode", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode']);
                                $TaxExemptionReasonCode = $TaxCategory->appendChild($TaxExemptionReasonCode);
                            }else{
                                //<cbc:TierRange>02</cbc:TierRange>
                                $TierRange = $xml->createElement("cbc:TierRange", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TierRange']);
                                $TierRange = $TaxCategory->appendChild($TierRange);
                            }
                            //<cac:TaxScheme>
                            $TaxScheme = $xml->createElement("cac:TaxScheme");
                            $TaxScheme = $TaxCategory->appendChild($TaxScheme);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']);
                                $ID = $TaxScheme->appendChild($ID);
                                //<cbc:Name>
                                $Name = $xml->createElement("cbc:Name", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']);
                                $Name = $TaxScheme->appendChild($Name);
                                //<cbc:TaxTypeCode>
                                $TaxTypeCode = $xml->createElement("cbc:TaxTypeCode", $arr['CreditNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']);
                                $TaxTypeCode = $TaxScheme->appendChild($TaxTypeCode);
            }

            //cac:Item
            $Item = $xml->createElement("cac:Item");
            $Item = $CreditNoteLine->appendChild($Item);
                //cbc:Description
                $Description = $xml->createElement("cbc:Description");
                $Description = $Item->appendChild($Description);
                $texto = $Description->appendChild(new DOMCdataSection($arr['CreditNoteLine'][$i]['Item']['Description']));
                //<cac:SellersItemIdentification>
                $SellersItemIdentification = $xml->createElement("cac:SellersItemIdentification");
                $SellersItemIdentification = $Item->appendChild($SellersItemIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['CreditNoteLine'][$i]['Item']['SellersItemIdentification']['ID']);
                    $ID = $SellersItemIdentification->appendChild($ID);
            //cac:Price
            $price = $xml->createElement("cac:Price");
            $price = $CreditNoteLine->appendChild($price);
                //cbc:PriceAmount
                $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['CreditNoteLine'][$i]['Price']['PriceAmount']['Price']);
                $PriceAmount = $price->appendChild($PriceAmount);
                $PriceAmount->setAttribute("currencyID", $arr['CreditNoteLine'][$i]['Price']['PriceAmount']['currencyID']);

    }
}
function add_DebitNoteLine($arr,$nodo,$tipodoc) {
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['DebitNoteLine']); $i++) {
        $DebitNoteLine = $xml->createElement("cac:DebitNoteLine");
        $DebitNoteLine = $root->appendChild($DebitNoteLine);
            //<cbc:ID>
            $ID = $xml->createElement("cbc:ID", $arr['DebitNoteLine'][$i]['ID']);
            $ID = $DebitNoteLine->appendChild($ID);
            //cbc:InvoicedQuantity
            $InvoicedQuantity = $xml->createElement("cbc:DebitedQuantity", $arr['DebitNoteLine'][$i]['DebitedQuantity']['Quantity']);
            $InvoicedQuantity = $DebitNoteLine->appendChild($InvoicedQuantity);
            $InvoicedQuantity->SetAttribute("unitCode", $arr['DebitNoteLine'][$i]['DebitedQuantity']['unitCode']);
            //cbc:LineExtensionAmount
            $LineExtensionAmount = $xml->createElement("cbc:LineExtensionAmount", $arr['DebitNoteLine'][$i]['LineExtensionAmount']['Amount']);
            $LineExtensionAmount = $DebitNoteLine->appendChild($LineExtensionAmount);
            $LineExtensionAmount->SetAttribute("currencyID", $arr['DebitNoteLine'][$i]['LineExtensionAmount']['currencyID']);
            //cac:PricingReference
            $PricingReference = $xml->createElement("cac:PricingReference");
            $PricingReference = $DebitNoteLine->appendChild($PricingReference);

            for ($ii=1; $ii<=sizeof($arr['DebitNoteLine'][$i]['PricingReference']['AlternativeConditionPrice']); $ii++) {
                //cac:AlternativeConditionPrice
                $AlternativeConditionPrice = $xml->createElement("cac:AlternativeConditionPrice");
                $AlternativeConditionPrice = $PricingReference->appendChild($AlternativeConditionPrice);
                    //cbc:PriceAmount
                    $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['DebitNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['Price']);
                    $PriceAmount = $AlternativeConditionPrice->appendChild($PriceAmount);
                    $PriceAmount->setAttribute("currencyID", $arr['DebitNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['currencyID']);
                    //cbc:PriceTypeCode
                    $PriceTypeCode = $xml->createElement("cbc:PriceTypeCode", $arr['DebitNoteLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceTypeCode']);
                    $PriceTypeCode = $AlternativeConditionPrice->appendChild($PriceTypeCode);
            }
            //<cac:AllowanceCharge>
            if( isset($arr['DebitNoteLine'][$i]['AllowanceCharge'])){
                $AllowanceCharge = $xml->createElement("cac:AllowanceCharge");
                $AllowanceCharge = $DebitNoteLine->appendChild($AllowanceCharge);
                    //<cbc:ChargeIndicator>
                    $ChargeIndicator = $xml->createElement("cbc:ChargeIndicator", $arr['DebitNoteLine'][$i]['AllowanceCharge']['ChargeIndicator']);
                    $ChargeIndicator = $AllowanceCharge->appendChild($ChargeIndicator);
                    //<cbc:Amount
                    $Amount = $xml->createElement("cbc:Amount", $arr['DebitNoteLine'][$i]['AllowanceCharge']['Amount']['Amount']);
                    $Amount = $AllowanceCharge->appendChild($Amount);
                    $Amount->SetAttribute("currencyID", $arr['DebitNoteLine'][$i]['AllowanceCharge']['Amount']['currencyID']);
            }

            for ($iii=1; $iii<=sizeof($arr['DebitNoteLine'][$i]['TaxTotal']); $iii++) {
                $TaxTotal = $xml->createElement("cac:TaxTotal");
                $TaxTotal = $DebitNoteLine->appendChild($TaxTotal);
                    //<cbc:TaxAmount
                    $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxAmount']['Amount']);
                    $TaxAmount = $TaxTotal->appendChild($TaxAmount);
                    $TaxAmount->SetAttribute("currencyID", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxAmount']['currencyID']);
                    //<cac:TaxSubtotal>
                    $TaxSubtotal = $xml->createElement("cac:TaxSubtotal");
                    $TaxSubtotal = $TaxTotal->appendChild($TaxSubtotal);
                        //cbc:TaxAmount
                        $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['Amount']);
                        $TaxAmount = $TaxSubtotal->appendChild($TaxAmount);
                        $TaxAmount->SetAttribute("currencyID", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['currencyID']);
                        //<cac:TaxCategory>
                        $TaxCategory = $xml->createElement("cac:TaxCategory");
                        $TaxCategory = $TaxSubtotal->appendChild($TaxCategory);
                            //<cbc:TaxExemptionReasonCode>10</cbc:TaxExemptionReasonCode>
                            if(isset($arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'])){
                                $TaxExemptionReasonCode = $xml->createElement("cbc:TaxExemptionReasonCode", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode']);
                                $TaxExemptionReasonCode = $TaxCategory->appendChild($TaxExemptionReasonCode);
                            }else{
                                //<cbc:TierRange>02</cbc:TierRange>
                                $TierRange = $xml->createElement("cbc:TierRange", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TierRange']);
                                $TierRange = $TaxCategory->appendChild($TierRange);
                            }
                            //<cac:TaxScheme>
                            $TaxScheme = $xml->createElement("cac:TaxScheme");
                            $TaxScheme = $TaxCategory->appendChild($TaxScheme);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']);
                                $ID = $TaxScheme->appendChild($ID);
                                //<cbc:Name>
                                $Name = $xml->createElement("cbc:Name", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']);
                                $Name = $TaxScheme->appendChild($Name);
                                //<cbc:TaxTypeCode>
                                $TaxTypeCode = $xml->createElement("cbc:TaxTypeCode", $arr['DebitNoteLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']);
                                $TaxTypeCode = $TaxScheme->appendChild($TaxTypeCode);
            }

            //cac:Item
            $Item = $xml->createElement("cac:Item");
            $Item = $DebitNoteLine->appendChild($Item);
                //cbc:Description
                $Description = $xml->createElement("cbc:Description");
                $Description = $Item->appendChild($Description);
                $texto = $Description->appendChild(new DOMCdataSection($arr['DebitNoteLine'][$i]['Item']['Description']));
                //<cac:SellersItemIdentification>
                $SellersItemIdentification = $xml->createElement("cac:SellersItemIdentification");
                $SellersItemIdentification = $Item->appendChild($SellersItemIdentification);
                    //<cbc:ID>
                    $ID = $xml->createElement("cbc:ID", $arr['DebitNoteLine'][$i]['Item']['SellersItemIdentification']['ID']);
                    $ID = $SellersItemIdentification->appendChild($ID);
            //cac:Price
            $price = $xml->createElement("cac:Price");
            $price = $DebitNoteLine->appendChild($price);
                //cbc:PriceAmount
                $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['DebitNoteLine'][$i]['Price']['PriceAmount']['Price']);
                $PriceAmount = $price->appendChild($PriceAmount);
                $PriceAmount->setAttribute("currencyID", $arr['DebitNoteLine'][$i]['Price']['PriceAmount']['currencyID']);

    }
}
function add_SummaryDocumentsLine($arr,$nodo,$tipodoc) {
    global $root, $xml;
    for ($i=1; $i<=sizeof($arr['SummaryDocumentsLine']); $i++) {
        $SummaryDocumentsLine = $xml->createElement("sac:SummaryDocumentsLine");
        $SummaryDocumentsLine = $root->appendChild($SummaryDocumentsLine);
            //<cbc:LineID>
            $LineID = $xml->createElement("cbc:LineID", $arr['SummaryDocumentsLine'][$i]['LineID']);
            $LineID = $SummaryDocumentsLine->appendChild($LineID);
            //DocumentTypeCode
            $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode", $arr['SummaryDocumentsLine'][$i]['DocumentTypeCode']);
            $DocumentTypeCode = $SummaryDocumentsLine->appendChild($DocumentTypeCode);
            //<sac:DocumentSerialID>
            /*$DocumentSerialID = $xml->createElement("sac:DocumentSerialID", $arr['SummaryDocumentsLine'][$i]['DocumentSerialID']);
            $DocumentSerialID = $SummaryDocumentsLine->appendChild($DocumentSerialID);*/

			//<cbc:ID>
			$ID = $xml->createElement("cbc:ID", $arr['SummaryDocumentsLine'][$i]['ID']);
            $SummaryDocumentsLine->appendChild($ID);
			//</cbc:ID>

			//<cac:AccountingCustomerParty>
			$AccountingCustomerParty = $xml->createElement("cac:AccountingCustomerParty");
            $AccountingCustomerParty = $SummaryDocumentsLine->appendChild($AccountingCustomerParty);
				$CustomerAssignedAccountID = $xml->createElement("cbc:CustomerAssignedAccountID", $arr['SummaryDocumentsLine'][$i]['AccountingCustomerParty']['CustomerAssignedAccountID']);
				$AccountingCustomerParty->appendChild($CustomerAssignedAccountID);

				$AdditionalAccountID = $xml->createElement("cbc:AdditionalAccountID", $arr['SummaryDocumentsLine'][$i]['AccountingCustomerParty']['AdditionalAccountID']);
				$AccountingCustomerParty->appendChild($AdditionalAccountID);
			//</cac:AccountingCustomerParty>

            //cac:BillingReference
            if(isset($arr['SummaryDocumentsLine'][$i]['BillingReference'])){
                $BillingReference = $xml->createElement("cac:BillingReference");
                $BillingReference = $SummaryDocumentsLine->appendChild($BillingReference);
                    $InvoiceDocumentReference = $xml->createElement("cac:InvoiceDocumentReference");
                    $InvoiceDocumentReference = $BillingReference->appendChild($InvoiceDocumentReference);
                        //cbc:ID
                        $ID = $xml->createElement("cbc:ID", $arr['SummaryDocumentsLine'][$i]['BillingReference']['InvoiceDocumentReference']['ID']);
                        $InvoiceDocumentReference->appendChild($ID);

                        $DocumentTypeCode = $xml->createElement("cbc:DocumentTypeCode", $arr['SummaryDocumentsLine'][$i]['BillingReference']['InvoiceDocumentReference']['DocumentTypeCode']);
                        $InvoiceDocumentReference->appendChild($DocumentTypeCode);
            }

			//<cac:Status>
			$Status = $xml->createElement("cac:Status");
			$Status = $SummaryDocumentsLine->appendChild($Status);

				$ConditionCode = $xml->createElement("cbc:ConditionCode", $arr['SummaryDocumentsLine'][$i]['Status']['ConditionCode']);
				$Status->appendChild($ConditionCode);
			//</cac:Status>


            //sac:StartDocumentNumberID
            /*$StartDocumentNumberID = $xml->createElement("sac:StartDocumentNumberID", $arr['SummaryDocumentsLine'][$i]['StartDocumentNumberID']);
            $StartDocumentNumberID = $SummaryDocumentsLine->appendChild($StartDocumentNumberID);*/
            //sac:EndDocumentNumberID

            /*$EndDocumentNumberID = $xml->createElement("sac:EndDocumentNumberID", $arr['SummaryDocumentsLine'][$i]['EndDocumentNumberID']);
            $EndDocumentNumberID = $SummaryDocumentsLine->appendChild($EndDocumentNumberID);*/

            $TotalAmount = $xml->createElement("sac:TotalAmount", $arr['SummaryDocumentsLine'][$i]['TotalAmount']['Amount']);
            $TotalAmount = $SummaryDocumentsLine->appendChild($TotalAmount);
            $TotalAmount->SetAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['TotalAmount']['currencyID']);

            for ($ii=1; $ii<=sizeof($arr['SummaryDocumentsLine'][$i]['BillingPayment']); $ii++) {
                //sac:BillingPayment
                $BillingPayment = $xml->createElement("sac:BillingPayment");
                $BillingPayment = $SummaryDocumentsLine->appendChild($BillingPayment);
                    //cbc:PaidAmount
                    $PaidAmount = $xml->createElement("cbc:PaidAmount", $arr['SummaryDocumentsLine'][$i]['BillingPayment'][$ii]['PaidAmount']['Amount']);
                    $PaidAmount = $BillingPayment->appendChild($PaidAmount);
                    $PaidAmount->SetAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['BillingPayment'][$ii]['PaidAmount']['currencyID']);
                    //cbc:InstructionID
                    $InstructionID = $xml->createElement("cbc:InstructionID", $arr['SummaryDocumentsLine'][$i]['BillingPayment'][$ii]['InstructionID']);
                    $InstructionID = $BillingPayment->appendChild($InstructionID);
            }

            for ($ii=1; $ii<=sizeof($arr['SummaryDocumentsLine'][$i]['PricingReference']['AlternativeConditionPrice']); $ii++) {
                //cac:AlternativeConditionPrice
                $AlternativeConditionPrice = $xml->createElement("cac:AlternativeConditionPrice");
                $AlternativeConditionPrice = $PricingReference->appendChild($AlternativeConditionPrice);
                    //cbc:PriceAmount
                    $PriceAmount = $xml->createElement("cbc:PriceAmount", $arr['SummaryDocumentsLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['Price']);
                    $PriceAmount = $AlternativeConditionPrice->appendChild($PriceAmount);
                    $PriceAmount->setAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceAmount']['currencyID']);
                    //cbc:PriceTypeCode
                    $PriceTypeCode = $xml->createElement("cbc:PriceTypeCode", $arr['SummaryDocumentsLine'][$i]['PricingReference']['AlternativeConditionPrice'][$ii]['PriceTypeCode']);
                    $PriceTypeCode = $AlternativeConditionPrice->appendChild($PriceTypeCode);
            }

            //<cac:AllowanceCharge>
            if( isset($arr['SummaryDocumentsLine'][$i]['AllowanceCharge'])){
                $AllowanceCharge = $xml->createElement("cac:AllowanceCharge");
                $AllowanceCharge = $SummaryDocumentsLine->appendChild($AllowanceCharge);
                    //<cbc:ChargeIndicator>
                    $ChargeIndicator = $xml->createElement("cbc:ChargeIndicator", $arr['SummaryDocumentsLine'][$i]['AllowanceCharge']['ChargeIndicator']);
                    $ChargeIndicator = $AllowanceCharge->appendChild($ChargeIndicator);
                    //<cbc:Amount
                    $Amount = $xml->createElement("cbc:Amount", $arr['SummaryDocumentsLine'][$i]['AllowanceCharge']['Amount']['Amount']);
                    $Amount = $AllowanceCharge->appendChild($Amount);
                    $Amount->SetAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['AllowanceCharge']['Amount']['currencyID']);
            }

            for ($iii=1; $iii<=sizeof($arr['SummaryDocumentsLine'][$i]['TaxTotal']); $iii++) {
                $TaxTotal = $xml->createElement("cac:TaxTotal");
                $TaxTotal = $SummaryDocumentsLine->appendChild($TaxTotal);
                    //<cbc:TaxAmount
                    $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxAmount']['Amount']);
                    $TaxAmount = $TaxTotal->appendChild($TaxAmount);
                    $TaxAmount->SetAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxAmount']['currencyID']);
                    //<cac:TaxSubtotal>
                    $TaxSubtotal = $xml->createElement("cac:TaxSubtotal");
                    $TaxSubtotal = $TaxTotal->appendChild($TaxSubtotal);
                        //cbc:TaxAmount
                        $TaxAmount = $xml->createElement("cbc:TaxAmount", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['Amount']);
                        $TaxAmount = $TaxSubtotal->appendChild($TaxAmount);
                        $TaxAmount->SetAttribute("currencyID", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxAmount']['currencyID']);
                        //<cac:TaxCategory>
                        $TaxCategory = $xml->createElement("cac:TaxCategory");
                        $TaxCategory = $TaxSubtotal->appendChild($TaxCategory);
                            //<cac:TaxScheme>
                            $TaxScheme = $xml->createElement("cac:TaxScheme");
                            $TaxScheme = $TaxCategory->appendChild($TaxScheme);
                                //<cbc:ID>
                                $ID = $xml->createElement("cbc:ID", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']);
                                $ID = $TaxScheme->appendChild($ID);
                                //<cbc:Name>
                                $Name = $xml->createElement("cbc:Name", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']);
                                $Name = $TaxScheme->appendChild($Name);
                                //<cbc:TaxTypeCode>
                                $TaxTypeCode = $xml->createElement("cbc:TaxTypeCode", $arr['SummaryDocumentsLine'][$i]['TaxTotal'][$iii]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']);
                                $TaxTypeCode = $TaxScheme->appendChild($TaxTypeCode);
            }
    }
}
//<cac:RequestedMonetaryTotal>
function add_RequestedMonetaryTotal($arr,$nodo,$tipodoc){
    global $root, $xml;
    $RequestedMonetaryTotal = $xml->createElement("cac:RequestedMonetaryTotal");
    $RequestedMonetaryTotal = $root->appendChild($RequestedMonetaryTotal);
        //<cbc:AllowanceTotalAmount
        if(isset($arr['RequestedMonetaryTotal']['AllowanceTotalAmount'])){
            $AllowanceTotalAmount = $xml->createElement("cbc:AllowanceTotalAmount", $arr['RequestedMonetaryTotal']['AllowanceTotalAmount']['TotalAmount']);
            $AllowanceTotalAmount = $RequestedMonetaryTotal->appendChild($AllowanceTotalAmount);
            $AllowanceTotalAmount->SetAttribute("currencyID", $arr['RequestedMonetaryTotal']['AllowanceTotalAmount']['currencyID']);
        }
        //<cbc:ChargeTotalAmount
        if(isset($arr['RequestedMonetaryTotal']['ChargeTotalAmount'])){
            $ChargeTotalAmount = $xml->createElement("cbc:ChargeTotalAmount", $arr['RequestedMonetaryTotal']['ChargeTotalAmount']['TotalAmount']);
            $ChargeTotalAmount = $RequestedMonetaryTotal->appendChild($ChargeTotalAmount);
            $ChargeTotalAmount->SetAttribute("currencyID", $arr['RequestedMonetaryTotal']['ChargeTotalAmount']['currencyID']);
        }
        //cbc:PrepaidAmount
        if(isset($arr['RequestedMonetaryTotal']['PrepaidAmount']['Amount'])){
            $PrepaidAmount = $xml->createElement("cbc:PrepaidAmount", $arr['RequestedMonetaryTotal']['PrepaidAmount']['Amount']);
            $PrepaidAmount = $RequestedMonetaryTotal->appendChild($PrepaidAmount);
            $PrepaidAmount->SetAttribute("currencyID", $arr['RequestedMonetaryTotal']['PrepaidAmount']['currencyID']);
        }
        //<cbc:PayableAmount
        if(isset($arr['RequestedMonetaryTotal']['PayableAmount'])){
            $PayableAmount = $xml->createElement("cbc:PayableAmount", $arr['RequestedMonetaryTotal']['PayableAmount']['TotalAmount']);
            $PayableAmount = $RequestedMonetaryTotal->appendChild($PayableAmount);
            $PayableAmount->SetAttribute("currencyID", $arr['RequestedMonetaryTotal']['PayableAmount']['currencyID']);
        }
}
function save($arr, $dirxml) {
    global $xml;
    //$xml->formatOutput = true;
    //var_dump($xml);
    $todo = null;

    if ($dirxml != "/dev/null") {
        $file = $dirxml .$arr['filename']. '.xml';
        $xml->save($file);
        $todo = new DOMDocument();
        $todo->formatOutput = FALSE;
        $todo->preserveWhiteSpace = TRUE;
        $todo->encoding = "ISO-8859-1";
        $todo->load($file);

    }
    return($todo);
}


function xml_AddAtt(&$nodo, $attr) {
    global $xml, $sello;
    $quitar = array('sello'=>1,'noCertificado'=>1,'certificado'=>1);
    foreach ($attr as $key => $val) {
        for ($i=0;$i<strlen($val); $i++) {
            $a = substr($val,$i,1);
            if ($a > chr(127) && $a !== chr(219) && $a !== chr(211) && $a !== chr(209)) {
                $val = substr_replace($val, ".", $i, 1);
            }
        }
        $val = preg_replace('/\s\s+/', ' ', $val);   // Regla 5a y 5c
        $val = trim($val);                           // Regla 5b
        if (strlen($val)>0) {   // Regla 6
            $val = str_replace(array('"','>','<'),"'",$val);  // &...;
            $val = utf8_encode(str_replace("|","/",$val)); // Regla 1
            $nodo->setAttribute($key,$val);
        }
    }
}
function xml_fecha($fech) {
    $ano = substr($fech,0,4);
    $mes = substr($fech,5,2);
    $dia = substr($fech,8,2);
    $hor = substr($fech,8,2);
    $min = substr($fech,10,2);
    $seg = substr($fech,12,2);
    $aux = $ano."-".$mes."-".$dia;
    if ($aux == "--T::")
        $aux = "";
    return ($aux);
}
function satxmlsv32_xml_fix_fech($fech) {
    $dia = substr($fech,0,2);
    $mes = substr($fech,3,2);
    $ano = substr($fech,6,4);
    $aux = $ano."-".$mes."-".$dia;
    return ($aux);
}
?>
