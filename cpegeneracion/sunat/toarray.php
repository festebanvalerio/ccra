<?php
function datatoarray($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr = array();
    $arr['certificado'] = $empresa[0]->certificado;
    $arr['clave_certificado'] = $empresa[0]->clave_certificado;
    $arr['usuario_sunat'] = $empresa[0]->usuario_sunat;
    $arr['clave_sunat'] = $empresa[0]->clave_sunat;
    if($tipodoc == 'Invoice'){
        arr_UBLExtensions($header, $detalle, $empresa, $tipodoc);
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_InvoiceTypeCode($header, $detalle, $empresa, $tipodoc);
        arr_Note($header, $detalle, $empresa, $tipodoc);
        arr_DocumentCurrencyCode($header, $detalle, $empresa, $tipodoc);
        arr_DespatchDocumentReference($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_AccountingCustomerParty($header, $detalle, $empresa, $tipodoc);
        arr_PaymentTerms($header, $detalle, $empresa, $tipodoc);
        arr_PrepaidPayment($header, $detalle, $empresa, $tipodoc);
        arr_TaxTotal($header, $detalle, $empresa, $tipodoc);
        arr_LegalMonetaryTotal($header, $detalle, $empresa, $tipodoc);
        arr_InvoiceLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT) .'-'. $header[0]->serie .'-'. $header[0]->numero;
    }elseif ($tipodoc == 'DespatchAdvice'){
        arr_UBLExtensions($header, $detalle, $empresa, $tipodoc);
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_DespatchAdviceTypeCode($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_Note($header, $detalle, $empresa, $tipodoc);
        arr_DespatchSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_DeliveryCustomerParty($header, $detalle, $empresa, $tipodoc);
        arr_Shipment($header, $detalle, $empresa, $tipodoc);
        arr_DespatchLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT) .'-'. $header[0]->serie .'-'. $header[0]->numero;
    }elseif ($tipodoc == 'CreditNote'){
        arr_UBLExtensions($header, $detalle, $empresa, $tipodoc);
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_DocumentCurrencyCode($header, $detalle, $empresa, $tipodoc);
        arr_DiscrepancyResponse($header, $detalle, $empresa, $tipodoc);
        arr_BillingReference($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_AccountingCustomerParty($header, $detalle, $empresa, $tipodoc);
        arr_TaxTotal($header, $detalle, $empresa, $tipodoc);
        arr_LegalMonetaryTotal($header, $detalle, $empresa, $tipodoc);
        arr_CreditNoteLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT) .'-'. $header[0]->serie .'-'. $header[0]->numero;
    }elseif ($tipodoc == 'DebitNote'){
        arr_UBLExtensions($header, $detalle, $empresa, $tipodoc);
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_DocumentCurrencyCode($header, $detalle, $empresa, $tipodoc);
        arr_DiscrepancyResponse($header, $detalle, $empresa, $tipodoc);
        arr_BillingReference($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_AccountingCustomerParty($header, $detalle, $empresa, $tipodoc);
        arr_TaxTotal($header, $detalle, $empresa, $tipodoc);
        arr_RequestedMonetaryTotal($header, $detalle, $empresa, $tipodoc);
        arr_DebitNoteLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT) .'-'. $header[0]->serie .'-'. $header[0]->numero;
    }elseif ($tipodoc == 'SummaryDocuments'){
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_ReferenceDate($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_SummaryDocumentsLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. $header[0]->id;
    }elseif ($tipodoc == 'VoidedDocuments'){
        arr_UBLVersionID($header, $detalle, $empresa, $tipodoc);
        arr_CustomizationID($header, $detalle, $empresa, $tipodoc);
        arr_ID($header, $detalle, $empresa, $tipodoc);
        arr_ReferenceDate($header, $detalle, $empresa, $tipodoc);
        arr_IssueDate($header, $detalle, $empresa, $tipodoc);
        arr_Signature($header, $detalle, $empresa, $tipodoc);
        arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc);
        arr_VoidedDocumentsLine($header, $detalle, $empresa, $tipodoc);
        $arr['filename'] = $empresa[0]->idempresa .'-'. $header[0]->id;
    }
    return $arr;
}
function arr_UBLExtensions($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($tipodoc == 'Invoice'){
        $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][1]['ID'] = '1002';
        $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][1]['Value'] = 'Leyenda TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE';
    }
    if($tipodoc != 'DespatchAdvice'){
        $xnr = 1;
        $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '1001';
        $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->totopgra,2);
        if($header[0]->totopina>0){
            $xnr++;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '1002';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->totopina,2);
        }
        if($header[0]->totopexo>0){
            $xnr++;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '1003';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->totopexo,2);
        }
        if($header[0]->totopgrat>0){
            $xnr++;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '1004';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->totopgrat,2);
        }
        if($header[0]->totdescto>0){
            $xnr++;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '2005';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->totdescto,2);
        }
        if (isset($header[0]->idtoperacion)){
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATTransaction']['ID'] = str_pad($header[0]->idtoperacion, 2, '0', STR_PAD_LEFT);
        }
        if (isset($header[0]->nroplaca)){
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['SUNATCosts']['RoadTransport']['LicensePlateID'] = $header[0]->nroplaca;
        }
        if($header[0]->detmon>0){
            $xnr++;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['ID'] = '2003';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['PayableAmount']['Amount'] = round($header[0]->detmon,2);
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalMonetaryTotal'][$xnr]['Percent'] = round($header[0]->detpor,2);

            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][2]['ID'] = '3000';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][2]['Value'] = $header[0]->detcod;//Otros Servicios Empresariales

            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][3]['ID'] = '3001';
            $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][3]['Value'] = $header[0]->detnumcue;//Cuenta Bando de la Nacion
        }
    }
}
function arr_UBLVersionID($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($tipodoc=='SummaryDocuments' || $tipodoc=='VoidedDocuments'){
        $arr['UBLVersionID'] = '2.0';
    }else{
        $arr['UBLVersionID'] = '2.1';//ACTIVA
    }

}
function arr_CustomizationID($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($tipodoc=='SummaryDocuments'){
        $arr['CustomizationID'] = '1.1';
    }elseif($tipodoc=='VoidedDocuments'){
        $arr['CustomizationID'] = '1.0';
    }else{
        $arr['CustomizationID'] = '2.0';//ACTIVA
    }
}
function arr_ID($header, $detalle, $empresa, $tipodoc){
    global $arr;

    if($tipodoc=='VoidedDocuments' || $tipodoc=='SummaryDocuments' ){
        $arr['ID'] = $header[0]->id;
    }else{
        $arr['ID'] = $header[0]->serie .'-'. $header[0]->numero; //'$header[0]->serie .'-' 'F001-10';
    }
}
function arr_ReferenceDate($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['ReferenceDate'] = $header[0]->referencedate;
}
function arr_IssueDate($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($tipodoc=='SummaryDocuments' || $tipodoc=='VoidedDocuments'){
        $arr['IssueDate'] = $header[0]->issuedate;
    }else{
        $arr['IssueDate'] = $header[0]->fechadoc;
    }
}
function arr_InvoiceTypeCode($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['InvoiceTypeCode'] = str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT); //'01';
}
function arr_DocumentCurrencyCode($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['DocumentCurrencyCode'] = $header[0]->isomoneda;
}
function arr_DespatchAdviceTypeCode($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['DespatchAdviceTypeCode'] = str_pad($header[0]->idcomprobante, 2, '0', STR_PAD_LEFT); //'01';
}

function arr_Note($header, $detalle, $empresa, $tipodoc){
    global $arr;
    //$arr['Note'] = $header[0]->note; //'01';
    $arr['Note'] = $header[0]->AdditionalProperty_Value;
    $arr['LineCount'] = $header[0]->LineCount;
}

function arr_DespatchDocumentReference($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $DespatchDocument = (isset($header[0]->DespatchDocument) ? $header[0]->DespatchDocument: []);
    foreach ($DespatchDocument as $row => $item) {
        $arr['DespatchDocumentReference'][$row+1]['ID'] = $item['ID'];
        $arr['DespatchDocumentReference'][$row+1]['DocumentTypeCode'] = $item['DocumentTypeCode'];
    }
}

function arr_Signature($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['Signature']['ID'] = $empresa[0]->signature_id;
    $arr['Signature']['ID2'] = $empresa[0]->signature_id2;
    $arr['Signature']['SignatoryParty']['PartyIdentification']['ID'] = $empresa[0]->idempresa;
    $arr['Signature']['SignatoryParty']['PartyName']['Name'] = $empresa[0]->razon;
    $arr['Signature']['DigitalSignatureAttachment']['ExternalReference']['URI'] = '#' .$empresa[0]->signature_id;
}
function arr_AccountingSupplierParty($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['AccountingSupplierParty']['CustomerAssignedAccountID'] = $empresa[0]->idempresa;
    $arr['AccountingSupplierParty']['AdditionalAccountID'] = $empresa[0]->idtipodni;
    $arr['AccountingSupplierParty']['Party']['PartyName']['Name'] = $empresa[0]->nomcomercial;
    $arr['AccountingSupplierParty']['Party']['PartyLegalEntity']['RegistrationName'] = $empresa[0]->razon;
    if($tipodoc != 'VoidedDocuments'){
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['ID'] = $empresa[0]->iddistrito;
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['StreetName'] = $empresa[0]->direccion;
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['CitySubdivisionName'] = $empresa[0]->subdivision;
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['CityName'] = strtoupper($empresa[0]->departamento);
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['CountrySubentity'] = strtoupper($empresa[0]->provincia);
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['District'] = strtoupper($empresa[0]->distrito);
        $arr['AccountingSupplierParty']['Party']['PostalAddress']['Country']['IdentificationCode'] = 'PE';
    }
}
function arr_DespatchSupplierParty($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['DespatchSupplierParty']['CustomerAssignedAccountID']['AccountID'] = $empresa[0]->idempresa;
    $arr['DespatchSupplierParty']['CustomerAssignedAccountID']['schemeID'] = $empresa[0]->idtipodni;
    $arr['DespatchSupplierParty']['Party']['PartyName']['Name'] = $empresa[0]->nomcomercial;
    $arr['DespatchSupplierParty']['Party']['PartyLegalEntity']['RegistrationName'] = $empresa[0]->razon;
}

function arr_AccountingCustomerParty($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['AccountingCustomerParty']['CustomerAssignedAccountID'] = $header[0]->identidad;
    $arr['AccountingCustomerParty']['AdditionalAccountID'] = $header[0]->idtipodni;
    $arr['AccountingCustomerParty']['Party']['PartyLegalEntity']['RegistrationName'] = $header[0]->razon;
}
function arr_DeliveryCustomerParty($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['DeliveryCustomerParty']['CustomerAssignedAccountID']['AccountID'] = $header[0]->identidad;
    $arr['DeliveryCustomerParty']['CustomerAssignedAccountID']['schemeID'] = $header[0]->idtipodni;
    $arr['DeliveryCustomerParty']['Party']['PartyLegalEntity']['RegistrationName'] = $header[0]->razon;
}
function arr_Shipment($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['Shipment']['HandlingCode'] = str_pad($header[0]->idmotivo, 2, '0', STR_PAD_LEFT);
    $arr['Shipment']['Information'] = $header[0]->motivo;
    $arr['Shipment']['SplitConsignmentIndicator'] = (($header[0]->programado=='1') ? 'true' : 'false');
    $arr['Shipment']['GrossWeightMeasure']['Measure'] = $header[0]->pesobruto;
    $arr['Shipment']['GrossWeightMeasure']['unitCode'] = 'KGM';
    $arr['Shipment']['TotalTransportHandlingUnitQuantity'] = $header[0]->bultos;

    $arr['Shipment']['ShipmentStage']['TransportModeCode'] = str_pad($header[0]->idmodetra, 2, '0', STR_PAD_LEFT);
    $arr['Shipment']['ShipmentStage']['TransitPeriod']['StartDate'] = $header[0]->fectra;

    //transporte publico
    $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyIdentification']['ID']['ID'] = $header[0]->idtransporte;
    $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyIdentification']['ID']['schemeID'] = $header[0]->idtipodnit;
    $arr['Shipment']['ShipmentStage']['CarrierParty']['PartyName']['Name'] = $header[0]->transporte;

    //transporte privado
    $arr['Shipment']['ShipmentStage']['DriverPerson']['ID']['ID'] = $header[0]->idconductor;
    $arr['Shipment']['ShipmentStage']['DriverPerson']['ID']['schemeID'] = $header[0]->idtipodnic;
    //solo si es privado
    //placa vehiculo
    $arr['Shipment']['ShipmentStage']['TransportMeans']['RoadTransport']['LicensePlateID'] = $header[0]->placa;
    $arr['Shipment']['ShipmentStage']['DriverPerson']['ID']['schemeID'] = $header[0]->idtipodnic;
    //Punto de llegada
    $arr['Shipment']['Delivery']['DeliveryAddress']['ID'] = $header[0]->iddistritod;
    $arr['Shipment']['Delivery']['DeliveryAddress']['StreetName'] = $header[0]->direcciond;

    //Punto de partida
    $arr['Shipment']['OriginAddress']['ID'] = $header[0]->iddistritop;
    $arr['Shipment']['OriginAddress']['StreetName'] = $header[0]->direccionp;

}

function arr_PaymentTerms($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($header[0]->idcomprobante == 1) {
        $arr['PaymentTerms']['ID']['ID'] = "FormaPago";
        $arr['PaymentTerms']['PaymentMeansID']['PaymentMeansID'] = "Contado";
    }
}

function arr_PrepaidPayment($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($header[0]->totanti>0){
        if($tipodoc == 'DebitNote'){
            $arr['RequestedMonetaryTotal']['PrepaidAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['RequestedMonetaryTotal']['PrepaidAmount']['Amount'] = round($header[0]->totanti,2);
        }else{
            $arr['LegalMonetaryTotal']['PrepaidAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['LegalMonetaryTotal']['PrepaidAmount']['Amount'] = round($header[0]->totanti,2);
        }

        $arr['PrepaidPayment']['ID']['schemeID'] = str_pad($header[0]->iddoctributario, 2, '0', STR_PAD_LEFT);
        $arr['PrepaidPayment']['ID']['ID'] = $header[0]->iddoctriref;
        $arr['PrepaidPayment']['PaidAmount']['Amount'] = round($header[0]->totanti,2);
        $arr['PrepaidPayment']['PaidAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['PrepaidPayment']['InstructionID']['schemeID'] = $empresa[0]->idtipodni;
        $arr['PrepaidPayment']['InstructionID']['InstructionID'] = $empresa[0]->idempresa;
    }
}
function arr_TaxTotal($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $xnr = 0;
    if($header[0]->totigv>0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxAmount']['Amount'] = round($header[0]->totigv,2);

        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxableAmount']['Amount'] = round($header[0]->totopgra,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round($header[0]->totigv,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['ID']= 'S';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '1000';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'IGV';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'VAT';
    }
    if($header[0]->totopexo > 0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxAmount']['Amount'] = round($header[0]->totigv,2);

        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxableAmount']['Amount'] = round($header[0]->totopexo,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round(0.00,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['ID']= 'E';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '9997';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'EXO';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'VAT';
    }
    if($header[0]->totopina > 0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxAmount']['Amount'] = round($header[0]->totigv,2);

        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxableAmount']['Amount'] = round($header[0]->totopina,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round(0.00,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['ID']= 'O';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '9998';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'INA';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'FRE';
    }
    if($header[0]->totopgrat > 0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxableAmount']['Amount'] = round($header[0]->totopgrat,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round(0.00,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['ID']= 'Z';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '9996';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'GRA';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'FRE';
    }
    /*if($header[0]->totisc>0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxAmount']['Amount'] = round($header[0]->totisc,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round($header[0]->totisc,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '2000';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'ISC';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'EXC';
    }
    if($header[0]->tototh>0){
        $xnr++;
        $arr['TaxTotal'][$xnr]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxAmount']['Amount'] = round($header[0]->tototh,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxAmount']['Amount'] = round($header[0]->tototh,2);
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID']= '9999';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name']= 'OTROS';
        $arr['TaxTotal'][$xnr]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode']= 'OTH';
    }*/
}
function arr_LegalMonetaryTotal($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($header[0]->desctoglobal>0){
        $arr['LegalMonetaryTotal']['AllowanceTotalAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['LegalMonetaryTotal']['AllowanceTotalAmount']['TotalAmount'] = round($header[0]->desctoglobal,2);
    }
    if($header[0]->tototroca>0){
        $arr['LegalMonetaryTotal']['ChargeTotalAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['LegalMonetaryTotal']['ChargeTotalAmount']['TotalAmount'] = round($header[0]->tototroca,2);
    }

    $arr['LegalMonetaryTotal']['LineExtensionAmount']['currencyID'] = $header[0]->isomoneda;
    $arr['LegalMonetaryTotal']['LineExtensionAmount']['TotalAmount'] = round($header[0]->totopgra,2);
    
    $arr['LegalMonetaryTotal']['TaxInclusiveAmount']['currencyID'] = $header[0]->isomoneda;
    $arr['LegalMonetaryTotal']['TaxInclusiveAmount']['TotalAmount'] = round($header[0]->importetotal,2);
    
    $arr['LegalMonetaryTotal']['PayableAmount']['currencyID'] = $header[0]->isomoneda;
    $arr['LegalMonetaryTotal']['PayableAmount']['TotalAmount'] = round($header[0]->importetotal,2);

    if($header[0]->totanti>0){
        $arr['LegalMonetaryTotal']['PrepaidAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['LegalMonetaryTotal']['PrepaidAmount']['Amount'] = round($header[0]->totanti,2);
    }
}

function arr_VoidedDocumentsLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    foreach ($detalle as $row => $item) {
        $arr['VoidedDocumentsLine'][$row+1]['LineID'] = $item->lineid;
        $arr['VoidedDocumentsLine'][$row+1]['DocumentTypeCode'] = str_pad($item->idcomprobante, 2, '0', STR_PAD_LEFT); //'01';
        $arr['VoidedDocumentsLine'][$row+1]['DocumentSerialID'] = $item->serie;
        $arr['VoidedDocumentsLine'][$row+1]['DocumentNumberID'] = $item->numero;
        $arr['VoidedDocumentsLine'][$row+1]['VoidReasonDescription'] = $item->voidreasondescription;
    }
}
function arr_InvoiceLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if(count($detalle)>0){
        foreach ($detalle as $row => $item) {
            if($item->idafectaciond == '10' || $item->idafectaciond == '20' || $item->idafectaciond == '30' || $item->idafectaciond == '40'){
                $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][1]['ID'] = '1000';
                $arr['UBLExtensions']['UBLExtension']['ExtensionContent']['AdditionalInformation']['AdditionalProperty'][1]['Value'] = $header[0]->AdditionalProperty_Value;
            }

            $arr['InvoiceLine'][$row+1]['ID'] = $item->nro;
            $arr['InvoiceLine'][$row+1]['InvoicedQuantity']['unitCode'] = $item->idmedida;
            $arr['InvoiceLine'][$row+1]['InvoicedQuantity']['Quantity'] = $item->cantidad;
            
            //$arr['InvoiceLine'][$row+1]['Item']['SellersItemIdentification']['ID'] = (($item->idproducto=='0') ? $item->nro : $item->codigo);
            if($item->codigo!="")$arr['InvoiceLine'][$row+1]['Item']['SellersItemIdentification']['ID'] = $item->codigo;

            //$arr['InvoiceLine'][$row+1]['Item']['Description'] = format_description((($item->detalle==null) ? $item->cdsc : $item->detalle));
            $arr['InvoiceLine'][$row+1]['Item']['Description'] = format_description($item->detalle);
            
            $arr['InvoiceLine'][$row+1]['Price']['PriceAmount']['Price'] = round($item->valorunitario, 2);
            $arr['InvoiceLine'][$row+1]['Price']['PriceAmount']['currencyID'] = $header[0]->isomoneda;
            
            if($item->idafectaciond == 10)//gravado
            {
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->preciounitario,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }
            elseif ($item->idafectaciond>=11 && $item->idafectaciond<=17)//gravado retiro
            {
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorrefunitario,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '02';
            }
            elseif ($item->idafectaciond==20)//exonerado
            {
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->preciounitario,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }
            elseif ($item->idafectaciond==21)//exo-gratuito
            {
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorrefunitario,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '02';
            }
            elseif ($item->idafectaciond==30)//inafecto
            {
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->preciounitario,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }
            elseif ($item->idafectaciond>=31 && $item->idafectaciond<=36)//faceto - retiro
            {
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                //$arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round(0.00,2);
                $arr['InvoiceLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '02';
            }
            elseif ($item->idafectaciond==40)//exportacion
            {

            }
            
            $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxAmount']['Amount'] = round($item->igv,2);
            $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->igv,2);;


            if ($item->idafectaciond==10){
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '1000';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'IGV';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'VAT';
            }elseif ($item->idafectaciond==20){
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '9997';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'EXO';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'VAT';
            }elseif ($item->idafectaciond==30){
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '9998';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'INA';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'FRE';
            }else{
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '9996';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'GRA';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'FRE';
            }
            

            if($item->idtiposcisc !=0){
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TierRange'] = $item->idtiposcisc;
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '200';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'ISC';
                $arr['InvoiceLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'EXC';
            }
            
            $arr['InvoiceLine'][$row+1]['LineExtensionAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['InvoiceLine'][$row+1]['LineExtensionAmount']['Amount'] = round($item->valorventa,2);
            
            if($item->descto > 0){
                $arr['InvoiceLine'][$row+1]['AllowanceCharge']['ChargeIndicator'] = 'false';
                $arr['InvoiceLine'][$row+1]['AllowanceCharge']['Amount']['currencyID'] = $header[0]->isomoneda;
                $arr['InvoiceLine'][$row+1]['AllowanceCharge']['Amount']['Amount'] = round($item->descto,2);
            }

            //if ($item->nroplaca > 0){
            //    $arr['InvoiceLine'][$row+1]['Item']['AdditionalItemIdentification']['ID'] = $item->nroplaca;
            //}
        }
    }
}
function arr_DespatchLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if(count($detalle)>0){
        foreach ($detalle as $row => $item) {
            $arr['DespatchLine'][$row+1]['ID'] = $item->nro;
            $arr['DespatchLine'][$row+1]['OrderLineReference']['LineID'] = $item->nro;
            $arr['DespatchLine'][$row+1]['DeliveredQuantity']['unitCode'] = $item->idmedida;
            $arr['DespatchLine'][$row+1]['DeliveredQuantity']['Quantity'] = $item->cantidad;
            $arr['DespatchLine'][$row+1]['Item']['Name'] = $item->cdsc;
            $arr['DespatchLine'][$row+1]['Item']['SellersItemIdentification']['ID'] = (($item->idproducto=='0') ? $item->nro : $item->codigo);
        }
    }
}
function arr_DiscrepancyResponse($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['DiscrepancyResponse']['ReferenceID'] = $header[0]->referenceid;
    $arr['DiscrepancyResponse']['ResponseCode'] = str_pad($header[0]->idtiponotacredito, 2, '0', STR_PAD_LEFT);
    $arr['DiscrepancyResponse']['Description'] = $header[0]->description;
}
function arr_BillingReference($header, $detalle, $empresa, $tipodoc){
    global $arr;
    $arr['BillingReference']['InvoiceDocumentReference']['ID'] = $header[0]->referenceid;
    $arr['BillingReference']['InvoiceDocumentReference']['DocumentTypeCode'] = str_pad($header[0]->referencedocumenttypecode, 2, '0', STR_PAD_LEFT);
}
function arr_CreditNoteLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if(count($detalle)>0){
        foreach ($detalle as $row => $item) {
            $arr['CreditNoteLine'][$row+1]['ID'] = $item->nro;
            $arr['CreditNoteLine'][$row+1]['CreditedQuantity']['unitCode'] = $item->idmedida;
            $arr['CreditNoteLine'][$row+1]['CreditedQuantity']['Quantity'] = $item->cantidad;
            $arr['CreditNoteLine'][$row+1]['Item']['SellersItemIdentification']['ID'] = (($item->idproducto=='0') ? $item->nro : $item->codigo);
            $arr['CreditNoteLine'][$row+1]['Item']['Description'] = format_description((($item->detalle==null) ? $item->cdsc : $item->detalle));
            $arr['CreditNoteLine'][$row+1]['Price']['PriceAmount']['Price'] = round($item->valorunitario, 2);
            $arr['CreditNoteLine'][$row+1]['Price']['PriceAmount']['currencyID'] = $header[0]->isomoneda;
            if($item->idafectaciond == 10){
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->preciounitario,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond>=11 && $item->idafectaciond<=17) {
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';

            }elseif ($item->idafectaciond==20) {
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond==21) {
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';
            }elseif ($item->idafectaciond==30) {
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond>=31 && $item->idafectaciond<=36) {
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['CreditNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';
            }
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxAmount']['Amount'] = round($item->igv,2);
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->igv,2);;

            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '1000';
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'IGV';
            $arr['CreditNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'VAT';

            if($item->idtiposcisc !=0){
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TierRange'] = $item->idtiposcisc;
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '200';
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'ISC';
                $arr['CreditNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'EXC';
            }
            $arr['CreditNoteLine'][$row+1]['LineExtensionAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['CreditNoteLine'][$row+1]['LineExtensionAmount']['Amount'] = round($item->valorventa,2);
            if($item->descto > 0){
                $arr['CreditNoteLine'][$row+1]['AllowanceCharge']['ChargeIndicator'] = 'false';
                $arr['CreditNoteLine'][$row+1]['AllowanceCharge']['Amount']['currencyID'] = $header[0]->isomoneda;
                $arr['CreditNoteLine'][$row+1]['AllowanceCharge']['Amount']['Amount'] = round($item->descto,2);
            }
        }
    }
}
function arr_DebitNoteLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if(count($detalle)>0){
        foreach ($detalle as $row => $item) {
            $arr['DebitNoteLine'][$row+1]['ID'] = $item->nro;
            $arr['DebitNoteLine'][$row+1]['DebitedQuantity']['unitCode'] = $item->idmedida;
            $arr['DebitNoteLine'][$row+1]['DebitedQuantity']['Quantity'] = $item->cantidad;
            $arr['DebitNoteLine'][$row+1]['Item']['SellersItemIdentification']['ID'] = (($item->idproducto=='0') ? $item->nro : $item->codigo);
            $arr['DebitNoteLine'][$row+1]['Item']['Description'] = format_description((($item->detalle==null) ? $item->cdsc : $item->detalle));
            $arr['DebitNoteLine'][$row+1]['Price']['PriceAmount']['Price'] = round($item->precio, 2);
            $arr['DebitNoteLine'][$row+1]['Price']['PriceAmount']['currencyID'] = $header[0]->isomoneda;
            if($item->idafectaciond == 10){
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond>=11 && $item->idafectaciond<=17) {
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';

            }elseif ($item->idafectaciond==20) {
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond==21) {
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';
            }elseif ($item->idafectaciond==30) {
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';
            }elseif ($item->idafectaciond>=31 && $item->idafectaciond<=36) {
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceAmount']['Price'] = '0.00';
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][1]['PriceTypeCode'] = '01';

                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceAmount']['Price'] = round($item->valorref,2);
                $arr['DebitNoteLine'][$row+1]['PricingReference']['AlternativeConditionPrice'][2]['PriceTypeCode'] = '02';
            }
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxAmount']['Amount'] = round($item->igv,2);
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->igv,2);;

            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxExemptionReasonCode'] = $item->idafectaciond;
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '1000';
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'IGV';
            $arr['DebitNoteLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'VAT';

            if($item->idtiposcisc !=0){
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->isc,2);
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TierRange'] = $item->idtiposcisc;
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '200';
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'ISC';
                $arr['DebitNoteLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'EXC';
            }
            $arr['DebitNoteLine'][$row+1]['LineExtensionAmount']['currencyID'] = $header[0]->isomoneda;
            $arr['DebitNoteLine'][$row+1]['LineExtensionAmount']['Amount'] = round($item->valorventa,2);
            if($item->descto > 0){
                $arr['DebitNoteLine'][$row+1]['AllowanceCharge']['ChargeIndicator'] = 'false';
                $arr['DebitNoteLine'][$row+1]['AllowanceCharge']['Amount']['currencyID'] = $header[0]->isomoneda;
                $arr['DebitNoteLine'][$row+1]['AllowanceCharge']['Amount']['Amount'] = round($item->descto,2);
            }
        }
    }
}
function arr_RequestedMonetaryTotal($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if($header[0]->desctoglobal>0){
        $arr['RequestedMonetaryTotal']['AllowanceTotalAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['RequestedMonetaryTotal']['AllowanceTotalAmount']['TotalAmount'] = round($header[0]->desctoglobal,2);
    }
    if($header[0]->tototroca>0){
        $arr['RequestedMonetaryTotal']['ChargeTotalAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['RequestedMonetaryTotal']['ChargeTotalAmount']['TotalAmount'] = round($header[0]->tototroca,2);
    }

    $arr['RequestedMonetaryTotal']['PayableAmount']['currencyID'] = $header[0]->isomoneda;
    $arr['RequestedMonetaryTotal']['PayableAmount']['TotalAmount'] = round($header[0]->importetotal,2);

    if($header[0]->totanti>0){
        $arr['RequestedMonetaryTotal']['PrepaidAmount']['currencyID'] = $header[0]->isomoneda;
        $arr['RequestedMonetaryTotal']['PrepaidAmount']['Amount'] = round($header[0]->totanti,2);
    }
}
function arr_SummaryDocumentsLine($header, $detalle, $empresa, $tipodoc){
    global $arr;
    if(count($detalle)>0){
        foreach ($detalle as $row => $item) {
            $arr['SummaryDocumentsLine'][$row+1]['LineID'] = $item->nro;
            $arr['SummaryDocumentsLine'][$row+1]['DocumentTypeCode'] = str_pad($item->idcomprobante, 2, '0', STR_PAD_LEFT);
            $arr['SummaryDocumentsLine'][$row+1]['ID'] = $item->serie.'-'.$item->numero;
            //$arr['SummaryDocumentsLine'][$row+1]['DocumentSerialID'] = $item->serie;
            //$arr['SummaryDocumentsLine'][$row+1]['StartDocumentNumberID'] = $item->startdocumentnumberid;
            //$arr['SummaryDocumentsLine'][$row+1]['EndDocumentNumberID'] = $item->enddocumentnumberid;
            $arr['SummaryDocumentsLine'][$row+1]['AccountingCustomerParty']['CustomerAssignedAccountID'] = $item->identidad;
            $arr['SummaryDocumentsLine'][$row+1]['AccountingCustomerParty']['AdditionalAccountID'] = $item->idtipodni;

            if($item->idcomprobante == '7' || $item->idcomprobante == '8' ){
                //sac:SummaryDocumentsLine/cac:BillingReference/cac:InvoiceDocumentReference/cbc:ID
                $arr['SummaryDocumentsLine'][$row+1]['BillingReference']['InvoiceDocumentReference']['ID']  = $item->invoicedocumentreference;
                //sac:SummaryDocumentsLine/cac:BillingReference/cac:InvoiceDocumentReference/cbc:DocumentTypeCode
                $arr['SummaryDocumentsLine'][$row+1]['BillingReference']['InvoiceDocumentReference']['DocumentTypeCode']  = str_pad($item->documenttypecode, 2, '0', STR_PAD_LEFT);
            }

            $arr['SummaryDocumentsLine'][$row+1]['Status']['ConditionCode'] = $item->conditioncode;

            $arr['SummaryDocumentsLine'][$row+1]['TotalAmount']['Amount'] = round($item->importetotal,2);
            $arr['SummaryDocumentsLine'][$row+1]['TotalAmount']['currencyID'] = $item->isomoneda;
            $nBi=0;
            if($item->totopgra>0){
            $nBi++;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['Amount'] = round($item->totopgra,2);
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['InstructionID'] = '01';
            }
            if($item->totopexo>0){
                $nBi++;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['Amount'] = round($item->totopexo,2);
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['InstructionID'] = '02';
            }
            if($item->totopina>0){
                $nBi++;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['Amount'] = round($item->totopina,2);
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['PaidAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['BillingPayment'][$nBi]['InstructionID'] = '03';
            }
            if($item->tototroca>0){
            $arr['SummaryDocumentsLine'][$row+1]['AllowanceCharge']['ChargeIndicator'] = 'true';
            $arr['SummaryDocumentsLine'][$row+1]['AllowanceCharge']['Amount']['Amount'] = round($item->tototroca,2);
            $arr['SummaryDocumentsLine'][$row+1]['AllowanceCharge']['Amount']['currencyID'] = $item->isomoneda;
            }
            
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxAmount']['Amount'] = round($item->totisc,2);

            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->totisc,2);
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '2000';
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'ISC';
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][1]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'EXC';

            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxAmount']['Amount'] = round($item->totigv,2);
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['currencyID'] = $item->isomoneda;
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxAmount']['Amount'] = round($item->totigv,2);;

            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['ID'] = '1000';
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['Name'] = 'IGV';
            $arr['SummaryDocumentsLine'][$row+1]['TaxTotal'][2]['TaxSubtotal']['TaxCategory']['TaxScheme']['TaxTypeCode'] = 'VAT';
        }
    }
}
function format_description($cadena){
    $description = preg_replace('/\s+/', ' ', $cadena);
    return $description;
}
?>
