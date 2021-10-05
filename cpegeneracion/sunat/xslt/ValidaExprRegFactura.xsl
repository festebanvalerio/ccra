<?xml version="1.0"? encoding="ISO-8859-1">
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:regexp="http://exslt.org/regular-expressions" xmlns:gemfunc="http://www.sunat.gob.pe/gem/functions" xmlns:func="http://exslt.org/functions" xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:dp="http://www.datapower.com/extensions" extension-element-prefixes="dp" exclude-result-prefixes="dp" version="1.0">
  <xsl:include href="local:///sistemagem/util/ThrowError.xsl" dp:ignore-multiple="yes"/>
  <xsl:key name="by-document-despatch-reference" match="cac:DespatchDocumentReference" use="concat(cbc:DocumentTypeCode,' ', cbc:ID)"/>
  <xsl:key name="by-document-additional-reference" match="cac:AdditionalDocumentReference" use="concat(cbc:DocumentTypeCode,' ', cbc:ID)"/>
  <xsl:key name="by-pricingReference-alternativeConditionPrice-priceTypeCode" match="./cac:PricingReference/cac:AlternativeConditionPrice" use="cbc:PriceTypeCode"/>
  <xsl:template match="/*">
    <xsl:variable name="sacAdditionalInformation" select="ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation"/>
    <xsl:variable name="additionalMonetaryTotal" select="$sacAdditionalInformation/sac:AdditionalMonetaryTotal"/>
    <xsl:variable name="additionalMonetaryTotalID" select="$additionalMonetaryTotal/cbc:ID"/>
    <xsl:variable name="sacSUNATTransactionID" select="$sacAdditionalInformation/sac:SUNATTransaction/cbc:ID"/>
    <xsl:if test="count($sacAdditionalInformation)&gt;1">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2427'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2427)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="$sacSUNATTransactionID[text() != '06']">
      <xsl:if test="$sacAdditionalInformation/sac:SUNATEmbededDespatchAdvice">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'4047'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 4047)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:if test="$sacSUNATTransactionID">
      <xsl:if test="not($sacSUNATTransactionID[text() = '01' or text() = '02' or text() = '03' or text() = '04' or text() = '05' or text() = '06'])">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'4042'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 4042)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="$sacSUNATTransactionID[text() = '04'] and not(cac:InvoiceLine/cac:Item/cbc:Description and cac:InvoiceLine/cbc:LineExtensionAmount)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2500'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2500)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="$sacSUNATTransactionID[text() = '04'] and (cac:InvoiceLine/cbc:LineExtensionAmount[text()&lt;=0])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2501'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2501)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="$sacSUNATTransactionID[text() = '04'] and $additionalMonetaryTotalID[text()='1001'] and (cac:TaxTotal/cbc:TaxAmount[text() &lt;=0] or cac:LegalMonetaryTotal/cbc:PayableAmount[text() &lt;=0])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2502'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2502)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:for-each select="cac:PrepaidPayment">
      <xsl:if test="./cbc:PaidAmount and ./cbc:PaidAmount &lt;= 0 ">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'2503'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2503)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="./cbc:PaidAmount and not(./cbc:ID and ./cbc:InstructionID)">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'2504'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2504)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="./cbc:ID/@schemeID and not(./cbc:ID/@schemeID != '')">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'1041'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 1041)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="./cbc:ID and ./cbc:ID/@schemeID !='02' and ./cbc:ID/@schemeID !='03'">
        <xsl:call-template name="addWarning">
          <xsl:with-param name="codigo" select="'2505'"/>
        </xsl:call-template>
        <xsl:message terminate="no" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2505)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="./cbc:InstructionID/@schemeID">
          <xsl:if test="not(./cbc:InstructionID/@schemeID != '')">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'1042'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 1042)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cbc:ID/@schemeID ='02' and not(regexp:match(./cbc:ID,'^[F][A-Z0-9]{3}-[0-9]{1,8}$|^(E001)-[0-9]{1,8}$|^[0-9]{1,4}-[0-9]{1,8}$'))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2521'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2521)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cbc:ID/@schemeID ='03' and not(regexp:match(./cbc:ID,'^[B][A-Z0-9]{3}-[0-9]{1,8}$|^[0-9]{1,4}-[0-9]{1,8}$'))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2521'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2521)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cbc:InstructionID/@schemeID != '6'">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2520'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2520)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cbc:InstructionID = ''">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2529'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2529)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="not(regexp:match(./cbc:InstructionID,'^[0-9]{11}$'))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2521'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2521)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="./cbc:InstructionID">
            <xsl:if test="./cbc:InstructionID = ''">
              <xsl:call-template name="addWarning">
                <xsl:with-param name="codigo" select="'2508'"/>
              </xsl:call-template>
              <xsl:message terminate="no" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2508)'"/>
              </xsl:message>
            </xsl:if>
            <xsl:if test="not(regexp:match(./cbc:InstructionID,'^[F][A-Z0-9]{3}-[0-9]{1,8}$|^(E001)-[0-9]{1,8}$|^[0-9]{1,4}-[0-9]{1,8}$'))">
              <xsl:call-template name="addWarning">
                <xsl:with-param name="codigo" select="'1044'"/>
              </xsl:call-template>
              <xsl:message terminate="no" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 1044)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
    <xsl:variable name="PrepaidPaymentId02GreaterThan0" select="count(cac:PrepaidPayment/cbc:ID[text()='02'])&gt;0"/>
    <xsl:variable name="acumPrepaid">
      <xsl:call-template name="getPrepaidPaymentAcum">
        <xsl:with-param name="Items" select="cac:PrepaidPayment"/>
      </xsl:call-template>
    </xsl:variable>
    <xsl:choose>
      <xsl:when test="$PrepaidPaymentId02GreaterThan0">
        <xsl:if test="cac:LegalMonetaryTotal/cbc:PrepaidAmount and not(regexp:match(cac:LegalMonetaryTotal/cbc:PrepaidAmount,'^[0-9]{1,12}(\.[0-9]{1,2})?$'))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2509'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2509 -1)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="number(cac:LegalMonetaryTotal/cbc:PrepaidAmount) != number(round(100*$acumPrepaid) div 100)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2509'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2509 -2)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="cac:LegalMonetaryTotal/cbc:PrepaidAmount and cac:LegalMonetaryTotal/cbc:PrepaidAmount &lt;= 0">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2508'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2508)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="cac:LegalMonetaryTotal/cbc:PrepaidAmount and not(cac:LegalMonetaryTotal/cbc:PrepaidAmount !='')">
      <xsl:call-template name="addWarning">
        <xsl:with-param name="codigo" select="'1048'"/>
      </xsl:call-template>
      <xsl:message terminate="no" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 1048)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="cac:LegalMonetaryTotal/cbc:PrepaidAmount and cac:LegalMonetaryTotal/cbc:PrepaidAmount &lt;= 0">
      <xsl:call-template name="addWarning">
        <xsl:with-param name="codigo" select="'2527'"/>
      </xsl:call-template>
      <xsl:message terminate="no" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2527)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="not(string(cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'1006'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 1006)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID,&quot;^[0-9]{11}$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'1005'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 1005)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cbc:ID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'1002'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 1002)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cbc:ID,&quot;^[F][A-Z0-9]{3}-[0-9]{1,8}?$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'1001'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 1001)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cbc:InvoiceTypeCode))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'1004'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 1004)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cbc:InvoiceTypeCode,&quot;^[0]{1}[1]{1}$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'1003'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 1003)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cbc:CustomizationID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2073'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2073)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cbc:CustomizationID,&quot;^[0-9]{1,7}(\.[0-9]{1,2})?$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2072'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2072)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cbc:UBLVersionID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2075'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2075)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cbc:UBLVersionID,&quot;^[2]{1}(\.[0]{1})$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2074'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2074)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="cac:SellerSupplierParty/cac:Party/cac:PostalAddress/cac:Country/cbc:IdentificationCode and cac:SellerSupplierParty/cac:Party/cac:PostalAddress/cac:Country/cbc:IdentificationCode != 'PE'">
      <xsl:call-template name="addWarning">
        <xsl:with-param name="codigo" select="'4041'"/>
      </xsl:call-template>
      <xsl:message terminate="no" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 4041)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="not(string(cac:AccountingSupplierParty/cbc:AdditionalAccountID))">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'1008'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 1008)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="not(regexp:match(cac:AccountingSupplierParty/cbc:AdditionalAccountID,&quot;^[6]{1}$&quot;))">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'1007'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 1007)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="count(cac:AccountingSupplierParty/cbc:AdditionalAccountID)&gt;1">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2362'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2362)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="not(string(cbc:DocumentCurrencyCode))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2070'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2070)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cbc:DocumentCurrencyCode,&quot;^[A-Z]{3}$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2069'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2069)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'1037'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 1037)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:AccountingSupplierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName,&quot;^(?!\s*$).{3,1000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'1038'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 1038)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="not(string(cac:AccountingCustomerParty/cbc:AdditionalAccountID))">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2015'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2015)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="count(cac:AccountingCustomerParty/cbc:AdditionalAccountID)&gt;1">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2363'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2363)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="cac:InvoiceLine/cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode[text()='40']">
        <xsl:if test="not(regexp:match(cac:AccountingCustomerParty/cbc:AdditionalAccountID,&quot;^[01467A\-]{1}$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2016'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2016)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(cac:AccountingCustomerParty/cbc:AdditionalAccountID[text()=6])">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2016'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2016)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(string(cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2021'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2021)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:AccountingCustomerParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName,&quot;^(?!\s*$).{3,1000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2022'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2022)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(cac:LegalMonetaryTotal/cbc:PayableAmount)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2063'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2063)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:LegalMonetaryTotal/cbc:PayableAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,10})?$&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2062'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2062)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="cac:LegalMonetaryTotal/cbc:ChargeTotalAmount">
      <xsl:if test="not(regexp:match(cac:LegalMonetaryTotal/cbc:ChargeTotalAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2064'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2064)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:if test="cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount">
      <xsl:if test="not(regexp:match(cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2065'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2065)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="not(regexp:match(cac:LegalMonetaryTotal/cbc:AllowanceTotalAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2419'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2419)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:for-each select="cac:DespatchDocumentReference">
      <xsl:if test="./cbc:DocumentTypeCode">
        <xsl:choose>
          <xsl:when test="not(regexp:match(./cbc:DocumentTypeCode,&quot;^[0-9]{2}$&quot;))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'4004'"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not((./cbc:DocumentTypeCode=09) or (./cbc:DocumentTypeCode=31))">
              <xsl:call-template name="addWarning">
                <xsl:with-param name="codigo" select="'4005'"/>
              </xsl:call-template>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:if>
      <xsl:if test="./cbc:DocumentTypeCode">
        <xsl:choose>
          <xsl:when test="not(./cbc:ID)">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'4007'"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not(regexp:match(./cbc:ID,&quot;^(.){1,}-[0-9]{1,}$&quot;))">
              <xsl:call-template name="addWarning">
                <xsl:with-param name="codigo" select="'4006'"/>
              </xsl:call-template>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:if>
    </xsl:for-each>
    <xsl:for-each select="cac:DespatchDocumentReference">
      <xsl:if test="count(key('by-document-despatch-reference', concat(./cbc:DocumentTypeCode,' ',./cbc:ID))) &gt; 1">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2364'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura  (codigo: 2364)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:for-each>
    <xsl:for-each select="cac:AdditionalDocumentReference">
      <xsl:if test="./cbc:DocumentTypeCode">
        <xsl:choose>
          <xsl:when test="not(regexp:match(./cbc:DocumentTypeCode,&quot;^[0-9]{2}$&quot;))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'4008'"/>
            </xsl:call-template>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not((./cbc:DocumentTypeCode=04) or (./cbc:DocumentTypeCode=05) or (./cbc:DocumentTypeCode=99) or (./cbc:DocumentTypeCode=01))">
              <xsl:call-template name="addWarning">
                <xsl:with-param name="codigo" select="'4009'"/>
              </xsl:call-template>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="./cbc:DocumentTypeCode and not(./cbc:ID)">
          <xsl:call-template name="addWarning">
            <xsl:with-param name="codigo" select="'4011'"/>
          </xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="./cbc:DocumentTypeCode and not(regexp:match(./cbc:ID,&quot;^(?!\s*$).{1,100}&quot;))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'4010'"/>
            </xsl:call-template>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
    <xsl:for-each select="cac:AdditionalDocumentReference">
      <xsl:if test="count(key('by-document-additional-reference', concat(./cbc:DocumentTypeCode,' ',./cbc:ID))) &gt; 1">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2365'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura  (codigo: 2365)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:for-each>
    <xsl:if test="cac:TaxTotal/cbc:TaxAmount">
      <xsl:choose>
        <xsl:when test="not(string(cac:TaxTotal/cbc:TaxAmount))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2049'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2049)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match(cac:TaxTotal/cbc:TaxAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2048'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2048)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:for-each select="cac:TaxTotal">
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000">
          <xsl:if test="count(./cbc:TaxAmount)&gt;1">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2352'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2352)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=2000">
          <xsl:if test="count(./cbc:TaxAmount)&gt;1">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2353'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2353)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=9999">
          <xsl:if test="count(./cbc:TaxAmount)&gt;1">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2354'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2354)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
      </xsl:for-each>
    </xsl:if>
    <xsl:variable name="taxSubtotalTaxCategoryTaxScheme" select="cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme"/>
    <xsl:if test="$taxSubtotalTaxCategoryTaxScheme">
      <xsl:choose>
        <xsl:when test="not($taxSubtotalTaxCategoryTaxScheme/cbc:ID)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2052'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2052)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match($taxSubtotalTaxCategoryTaxScheme/cbc:ID,&quot;^[0-9]{4}$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2050'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2050)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:if test="not((($taxSubtotalTaxCategoryTaxScheme/cbc:ID)='1000') or (($taxSubtotalTaxCategoryTaxScheme/cbc:ID)='2000') or (($taxSubtotalTaxCategoryTaxScheme/cbc:ID)='9999'))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2051'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2051)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:choose>
        <xsl:when test="not($taxSubtotalTaxCategoryTaxScheme/cbc:Name)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2054'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2054)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match($taxSubtotalTaxCategoryTaxScheme/cbc:Name,&quot;^(?!\s*$)[^\s]{3,1000}&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2053'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2053)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:if test="$taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode and not(regexp:match($taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode,&quot;^(\s*$)|(.*)?[^\s]{3,100}&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2055'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2055)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="(($taxSubtotalTaxCategoryTaxScheme/cbc:ID=1000) and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode !='' and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode and not($taxSubtotalTaxCategoryTaxScheme/cbc:Name='IGV'))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2057'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2057)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="(($taxSubtotalTaxCategoryTaxScheme/cbc:ID=1000) and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode !='' and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode and not($taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode='VAT'))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2057'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2057)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="(($taxSubtotalTaxCategoryTaxScheme/cbc:ID=2000) and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode !='' and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode and not($taxSubtotalTaxCategoryTaxScheme/cbc:Name='ISC'))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2058'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2058)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="(($taxSubtotalTaxCategoryTaxScheme/cbc:ID=2000) and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode !='' and $taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode and not($taxSubtotalTaxCategoryTaxScheme/cbc:TaxTypeCode='EXC'))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2058'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2058)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:if test="cac:TaxTotal/cac:TaxSubtotal/cbc:TaxAmount">
      <xsl:for-each select="cac:TaxTotal">
        <xsl:choose>
          <xsl:when test="not(string(./cac:TaxSubtotal/cbc:TaxAmount))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2060'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2060)'"/>
            </xsl:message>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not(regexp:match(./cac:TaxSubtotal/cbc:TaxAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2059'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2059)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="number(./cac:TaxSubtotal/cbc:TaxAmount)!=number(./cbc:TaxAmount)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2061'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2061)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="$sacSUNATTransactionID[text() = '04'] and (./cac:TaxSubtotal/cbc:TaxAmount[text()&lt;=0])">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2502'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2502)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:for-each>
    </xsl:if>
    <xsl:if test="(cbc:DocumentCurrencyCode!=$sacAdditionalInformation/sac:AdditionalMonetaryTotal/cbc:ID[text()!='2001']/../cbc:PayableAmount/@currencyID)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2071'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2071)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="not(string(cac:AccountingCustomerParty/cbc:CustomerAssignedAccountID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2014'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2014)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="cac:AccountingCustomerParty/cbc:AdditionalAccountID[text()='6'] and not(regexp:match(cac:AccountingCustomerParty/cbc:CustomerAssignedAccountID,'^[0-9]{11}$|^[-]{1}$'))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2017'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular FACTURA (codigo: 2017)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not((cac:Signature/cbc:ID))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2076'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2076)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:Signature/cbc:ID,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2077'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2077)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="not(cac:Signature/cac:SignatoryParty/cac:PartyIdentification/cbc:ID)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2079'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2079)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="(cac:Signature/cac:SignatoryParty/cac:PartyIdentification/cbc:ID != cac:AccountingSupplierParty/cbc:CustomerAssignedAccountID)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2078'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2078)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:choose>
      <xsl:when test="not(cac:Signature/cac:SignatoryParty/cac:PartyName/cbc:Name)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2081'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2081)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:Signature/cac:SignatoryParty/cac:PartyName/cbc:Name,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2080'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2080)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(cac:Signature/cac:DigitalSignatureAttachment/cac:ExternalReference/cbc:URI)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2083'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2083)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(cac:Signature/cac:DigitalSignatureAttachment/cac:ExternalReference/cbc:URI,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2082'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2082)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not((ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/@Id))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2085'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2085)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/@Id,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2084'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2084)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:CanonicalizationMethod/@Algorithm)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2087'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2087)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:CanonicalizationMethod/@Algorithm,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2086'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2086)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:SignatureMethod/@Algorithm)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2089'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2089)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:SignatureMethod/@Algorithm,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2088'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2088)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/@URI)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2091'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2091)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="string(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/@URI)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2090'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2090)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:Transforms/ds:Transform/@Algorithm)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2093'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2093)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:Transforms/ds:Transform/@Algorithm,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2092'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2092)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestMethod/@Algorithm)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2095'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2095)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestMethod/@Algorithm,&quot;^(?!\s*$).{2,3000}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2094'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2094)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignedInfo/ds:Reference/ds:DigestValue)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2097'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2097)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise/>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignatureValue)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2099'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2099)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:SignatureValue,&quot;[A-Za-z0-9+/=\s]{2,}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2098'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2098)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:choose>
      <xsl:when test="not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509Certificate)">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2101'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2101)'"/>
        </xsl:message>
      </xsl:when>
      <xsl:otherwise>
        <xsl:if test="not(regexp:match(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509Certificate,&quot;[A-Za-z0-9+/=\s]{2,}&quot;))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2100'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2100)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:otherwise>
    </xsl:choose>
    <xsl:if test="$sacSUNATTransactionID[text()='02']">
      <xsl:if test="count(cac:InvoiceLine[cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceTypeCode='02'])&gt;0">
        <xsl:if test="number(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:AdditionalMonetaryTotal[cbc:ID=1004]/cbc:PayableAmount) = 0       or not(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:AdditionalMonetaryTotal[cbc:ID=1004]/cbc:PayableAmount)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2641'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Operacion gratuita,  debe consignar Total valor venta - operaciones gratuitas  mayor a cero (codigo: 2641)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:if>
    </xsl:if>
    <xsl:if test="ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:SUNATTransaction/cbc:ID=07">
      <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cbc:TaxAmount=0])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2643'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta IVAP debe consignar Monto de impuestos por item(codigo: 2643)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode!=17])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2644'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta IVAP solo debe tener &#xED;tems con c&#xF3;digo afectaci&#xF3;n IGV 17(codigo: 2644)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID!=1000])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2645'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta a IVAP debe consignar items con codigo de tributo 1000(codigo: 2645)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name!='IVAP'])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2646'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta a IVAP debe consignar  items con nombre  de tributo IVAP(codigo: 2646)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:TaxTypeCode!='VAT'])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2647'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'C&#xF3;digo tributo  UN/ECE debe ser VAT(codigo: 2647)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="sum(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:AdditionalMonetaryTotal[cbc:ID!='1001']/cbc:PayableAmount)&gt;0">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2648'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta al IVAP, solo puede consignar informacion para operacion gravadas(codigo: 2648)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="sum(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:AdditionalMonetaryTotal[cbc:ID=1001]/cbc:PayableAmount)=0">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2649'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Operaci&#xF3;n sujeta al IVAP, debe consignar monto en total operaciones gravadas(codigo: 2649)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme[cbc:ID=2000]/../../cbc:TaxAmount&gt;0">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2650'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta al IVAP , no debe consignar valor para ISC o debe ser 0(codigo: 2650)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="string(ext:UBLExtensions/ext:UBLExtension/ext:ExtensionContent/sac:AdditionalInformation/sac:AdditionalProperty/cbc:ID)!='2007'">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2651'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Factura de operacion sujeta al IVAP , debe registrar mensaje 2007(codigo: 2651)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:variable name="itemsExportacion" select="        cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode='40'     and cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID='1000'     and cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name='IGV'     and cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:TaxTypeCode='VAT']"/>
    <xsl:if test="count($itemsExportacion)&gt;0">
      <xsl:if test="cac:AccountingCustomerParty/cbc:AdditionalAccountID=0">
        <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID!='1000'])&gt;0">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2654'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Servicios prestados No domiciliados. C&#xF3;digo tributo a consignar debe ser 1000(codigo: 2654)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode!='40'])&gt;0">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2655'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Servicios prestados No domiciliados.  El c&#xF3;digo de afectaci&#xF3;n debe ser 40(codigo: 2655)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="count(cac:InvoiceLine[cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:TaxTypeCode!='VAT'])&gt;0">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2656'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Servicios prestados No domiciliados. C&#xF3;digo tributo  UN/ECE debe ser VAT (codigo: 2656)'"/>
          </xsl:message>
        </xsl:if>
      </xsl:if>
    </xsl:if>
    <xsl:for-each select="cac:InvoiceLine">
      <xsl:choose>
        <xsl:when test="$sacSUNATTransactionID[text()='02']">
          <xsl:if test="cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode[text() != '40']">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2642'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Operaciones de exportacion, deben consignar Tipo Afectacion igual a 40 (codigo: 2642)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceTypeCode = 02">
            <xsl:if test="cac:Price/cbc:PriceAmount&gt;0">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2640'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Operacion gratuita, solo debe consignar un monto referencial  (codigo: 2640)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:if>
        </xsl:when>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="not(./cbc:ID)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2023'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2023)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match(./cbc:ID,&quot;^[0-9]{1,3}?$&quot;)) or ./cbc:ID &lt;= 0">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2023'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2023)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="not(string(./cbc:InvoicedQuantity))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2024'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2024)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match(./cbc:InvoicedQuantity,&quot;^[0-9]{1,12}(\.[0-9]{1,10})?$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2025'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2025)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="not(./cac:Item/cbc:Description)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2026'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2026)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match(./cac:Item/cbc:Description,&quot;^(?!\s*$).{1,250}&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2027'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2027)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:for-each select="./cac:PricingReference/cac:AlternativeConditionPrice">
        <xsl:choose>
          <xsl:when test="(./cbc:PriceTypeCode)=01 and (not(string(./cbc:PriceAmount)))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2028'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2028)'"/>
            </xsl:message>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="(./cbc:PriceTypeCode)=01 and not(regexp:match(./cbc:PriceAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,10})?$&quot;))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2367'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2367)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="(count(./cbc:PriceTypeCode[text()='01'])&gt;1 or count(./cbc:PriceTypeCode[text()='02'])&gt;1)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2409'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2409)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="not(./cbc:PriceTypeCode=01 or ./cbc:PriceTypeCode=02)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2410'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2410)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:choose>
          <xsl:when test="(./cbc:PriceTypeCode)=02 and (not(string(./cbc:PriceAmount)))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2417'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2417)'"/>
            </xsl:message>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="(./cbc:PriceTypeCode)=02 and not(regexp:match(./cbc:PriceAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,10})?$&quot;))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2408'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2408)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
      <xsl:choose>
        <xsl:when test="not(./cac:Price/cbc:PriceAmount)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2068'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="concat('Error Expr Regular Factura (codigo: 2068) line: ', cbc:ID)"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="regexp:match(./cac:Price/cbc:PriceAmount,&quot;^[\s]+$&quot;)">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2067'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="concat('Error Expr Regular Factura (codigo: 2067) line: ', cbc:ID)"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="not(regexp:match(./cac:Price/cbc:PriceAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,10})?$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2369'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="concat('Error Expr Regular Factura (codigo: 2369) line: ', cbc:ID)"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:choose>
        <xsl:when test="not(./cbc:LineExtensionAmount)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2032'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2032)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="regexp:match(./cbc:LineExtensionAmount,&quot;^[\s]+$&quot;)">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2031'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2031)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="not(regexp:match(./cbc:LineExtensionAmount,&quot;(-?[0-9]+){1,12}(\.[0-9]{1,2})?$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2370'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2370)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
      <xsl:if test="$sacSUNATTransactionID[text() = '04'] and (not(./cbc:LineExtensionAmount) or not(./cac:Item/cbc:Description))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2500'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2500)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="$sacSUNATTransactionID[text() = '04'] and (./cbc:LineExtensionAmount[text()&lt;=0])">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2501'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2501)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:if test="count(./cac:PricingReference/cac:AlternativeConditionPrice/cbc:PriceAmount[text()&gt;0 and parent::node()/cbc:PriceTypeCode=02])&gt;0      and ./cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode[text() = '10' or text() = '20' or text() = '30']">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2425'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2425)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:for-each select="./cac:TaxTotal">
        <xsl:if test="./cbc:TaxAmount">
          <xsl:choose>
            <xsl:when test="not(string(./cbc:TaxAmount))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2034'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2034)'"/>
              </xsl:message>
            </xsl:when>
            <xsl:otherwise>
              <xsl:if test="not(regexp:match(./cbc:TaxAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
                <xsl:call-template name="throwError">
                  <xsl:with-param name="codigo" select="'2033'"/>
                </xsl:call-template>
                <xsl:message terminate="yes" dp:priority="debug">
                  <xsl:value-of select="'Error Expr Regular Factura (codigo: 2033)'"/>
                </xsl:message>
              </xsl:if>
            </xsl:otherwise>
          </xsl:choose>
          <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000 and count(./cbc:TaxAmount)&gt;1">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2355'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2355)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=2000 and count(./cbc:TaxAmount)&gt;1">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2356'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2356)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="number(./cbc:TaxAmount)!=number(./cac:TaxSubtotal/cbc:TaxAmount)">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2372'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2372)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:choose>
          <xsl:when test="not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID)">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2037'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2037)'"/>
            </xsl:message>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not(regexp:match(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID,&quot;^[0-9]{4}$&quot;))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2035'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2035)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID[text()=1000 or text()=2000 or text()=9999])">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2036'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2036)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000">
          <xsl:choose>
            <xsl:when test="not(./cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode)">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2371'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2371)'"/>
              </xsl:message>
            </xsl:when>
            <xsl:otherwise>
              <xsl:if test="not(./cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode[text() = '10' or text() = '11' or text() = '12' or text() = '13' or text() = '14' or text() = '15' or text() = '16' or text() = '17' or text() = '20' or text() = '21' or text() = '30' or text() = '31' or text() = '32' or text() = '33' or text() = '34' or text() = '35' or text() = '36' or text() = '40'])">
                <xsl:call-template name="throwError">
                  <xsl:with-param name="codigo" select="'2040'"/>
                </xsl:call-template>
                <xsl:message terminate="yes" dp:priority="debug">
                  <xsl:value-of select="'Error Expr Regular Factura (codigo: 2040)'"/>
                </xsl:message>
              </xsl:if>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:if>
        <xsl:choose>
          <xsl:when test="not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name)">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2038'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2038)'"/>
            </xsl:message>
          </xsl:when>
          <xsl:otherwise>
            <xsl:if test="not(regexp:match(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name,&quot;^[A-Z]{3,1000}?$&quot;))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2038'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2038)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID">
          <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000">
            <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name and ./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name !='' and (not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name='IGV') and not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:TaxTypeCode='VAT'))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2377'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2377)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:if>
          <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=2000">
            <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name and ./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name !='' and (not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:Name='ISC') and not(./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:TaxTypeCode='EXC'))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2378'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2378)'"/>
              </xsl:message>
            </xsl:if>
          </xsl:if>
        </xsl:if>
        <xsl:if test="./cac:TaxSubtotal/cbc:TaxAmount">
          <xsl:choose>
            <xsl:when test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000 and not(string(./cac:TaxSubtotal/cbc:TaxAmount))">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2042'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2042)'"/>
              </xsl:message>
            </xsl:when>
            <xsl:otherwise>
              <xsl:if test="not(regexp:match(./cac:TaxSubtotal/cbc:TaxAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
                <xsl:call-template name="throwError">
                  <xsl:with-param name="codigo" select="'2368'"/>
                </xsl:call-template>
                <xsl:message terminate="yes" dp:priority="debug">
                  <xsl:value-of select="'Error Expr Regular Factura (codigo: 2368)'"/>
                </xsl:message>
              </xsl:if>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:if>
        <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=2000">
          <xsl:if test="not(string(./cac:TaxSubtotal/cac:TaxCategory/cbc:TierRange))  and ./cac:TaxTotal/cbc:TaxAmount &gt; 0">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2373'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2373)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cac:TaxSubtotal/cac:TaxCategory/cbc:TierRange and not(./cac:TaxSubtotal/cac:TaxCategory/cbc:TierRange[text()='' or text()=01 or text()=02 or text()=03])">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2041'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2041)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
      </xsl:for-each>
    </xsl:for-each>
    <xsl:for-each select="$sacAdditionalInformation/sac:SUNATEmbededDespatchAdvice">
      <xsl:if test="$sacSUNATTransactionID[text() = '06']">
        <xsl:variable name="todoNulo" select="not(./cac:DeliveryAddress/cbc:ID) and not(./cac:DeliveryAddress/cbc:StreetName) and not(./cac:DeliveryAddress/cbc:CityName)        and not(./cac:DeliveryAddress/cbc:CountrySubentity) and not(./cac:DeliveryAddress/cbc:District) and not(./cac:DeliveryAddress/cac:Country/cbc:IdentificationCode)        and not(./cac:OriginAddress/cbc:ID) and not(./cac:OriginAddress/cbc:StreetName) and not(./cac:OriginAddress/cbc:CityName) and not(./cac:OriginAddress/cbc:CountrySubentity)        and not(./cac:OriginAddress/cbc:District) and not(./cac:OriginAddress/cac:Country/cbc:IdentificationCode) "/>
        <xsl:if test="not($todoNulo)">
          <xsl:if test="not(./cac:DeliveryAddress/cbc:ID != '' and ./cac:DeliveryAddress/cbc:StreetName != '' and ./cac:DeliveryAddress/cbc:CityName != ''         and ./cac:DeliveryAddress/cbc:CountrySubentity != '' and ./cac:DeliveryAddress/cbc:District != '' and ./cac:DeliveryAddress/cac:Country/cbc:IdentificationCode != ''         and ./cac:OriginAddress/cbc:ID != '' and ./cac:OriginAddress/cbc:StreetName != '' and ./cac:OriginAddress/cbc:CityName != '' and ./cac:OriginAddress/cbc:CountrySubentity != ''         and ./cac:OriginAddress/cbc:District != '' and ./cac:OriginAddress/cac:Country/cbc:IdentificationCode != '')">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2421'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2421)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:if test="not(./cbc:TransportModeCode)">
          <xsl:call-template name="addWarning">
            <xsl:with-param name="codigo" select="'2532'"/>
          </xsl:call-template>
          <xsl:message terminate="no" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2532)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="./cbc:TransportModeCode and ./cbc:TransportModeCode[text() != '01'] and ./cbc:TransportModeCode[text() != '02']">
          <xsl:call-template name="addWarning">
            <xsl:with-param name="codigo" select="'4043'"/>
          </xsl:call-template>
          <xsl:message terminate="no" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 4043)'"/>
          </xsl:message>
        </xsl:if>
        <xsl:if test="./cbc:TransportModeCode and ./cbc:TransportModeCode[text() = '02']">
          <xsl:variable name="Tags1" select="./sac:SUNATRoadTransport/cbc:LicensePlateID and ./sac:SUNATRoadTransport/cbc:TransportAuthorizationCode and ./sac:SUNATRoadTransport/cbc:BrandName and ./sac:DriverParty/cac:Party/cac:PartyIdentification/cbc:ID"/>
          <xsl:if test="not($Tags1)">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2533'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2533)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./sac:SUNATCarrierParty/cbc:CustomerAssignedAccountID and ./sac:SUNATCarrierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'4045'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 4045)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:if test="./cbc:TransportModeCode and ./cbc:TransportModeCode[text() = '01']">
          <xsl:if test="not(./sac:SUNATCarrierParty/cbc:CustomerAssignedAccountID and ./sac:SUNATCarrierParty/cac:Party/cac:PartyLegalEntity/cbc:RegistrationName)">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2534'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2534)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
        <xsl:if test="./cbc:GrossWeightMeasure">
          <xsl:if test="./cbc:GrossWeightMeasure/@unitCode and ./cbc:GrossWeightMeasure/@unitCode = ''">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'0306'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 0306 - 1)'"/>
            </xsl:message>
          </xsl:if>
          <xsl:if test="./cbc:GrossWeightMeasure &lt;= 0 or not(regexp:match(./cbc:GrossWeightMeasure,'^[0-9]{1,12}(\.[0-9]{1,2})?$'))">
            <xsl:call-template name="addWarning">
              <xsl:with-param name="codigo" select="'2523'"/>
            </xsl:call-template>
            <xsl:message terminate="no" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2523)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:if>
      </xsl:if>
    </xsl:for-each>
    <xsl:if test="($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID=1002     and cac:InvoiceLine/cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cac:TaxScheme/cbc:ID=1000)     and cac:InvoiceLine/cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:TaxExemptionReasonCode[text()='10' or text()='20' or text()='30' or text()='40']">
      <xsl:call-template name="addWarning">
        <xsl:with-param name="codigo" select="'4025'"/>
      </xsl:call-template>
    </xsl:if>
    <xsl:if test="$sacAdditionalInformation/sac:AdditionalProperty/cbc:ID">
      <xsl:if test="not(regexp:match($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID,&quot;^[^\s]{4}$&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2366'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2366)'"/>
        </xsl:message>
      </xsl:if>
      <xsl:variable name="AdditionalPropertyId1000GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='1000'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId1001GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='1001'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId1002GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='1002'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId2000GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='2000'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId2001GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='2001'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId2002GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='2002'])&gt;1"/>
      <xsl:variable name="AdditionalPropertyId2003GreaterThan1" select="count($sacAdditionalInformation/sac:AdditionalProperty/cbc:ID[text()='2003'])&gt;1"/>
      <xsl:if test="$AdditionalPropertyId1000GreaterThan1 or $AdditionalPropertyId1001GreaterThan1 or $AdditionalPropertyId1002GreaterThan1 or $AdditionalPropertyId2000GreaterThan1 or $AdditionalPropertyId2001GreaterThan1 or $AdditionalPropertyId2002GreaterThan1 or $AdditionalPropertyId2003GreaterThan1">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2407'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2407)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:if test="$sacAdditionalInformation/sac:AdditionalProperty/cbc:Value">
      <xsl:if test="not(regexp:match($sacAdditionalInformation/sac:AdditionalProperty/cbc:Value,&quot;^(?!\s*$).{1,100}&quot;))">
        <xsl:call-template name="throwError">
          <xsl:with-param name="codigo" select="'2066'"/>
        </xsl:call-template>
        <xsl:message terminate="yes" dp:priority="debug">
          <xsl:value-of select="'Error Expr Regular Factura (codigo: 2066)'"/>
        </xsl:message>
      </xsl:if>
    </xsl:if>
    <xsl:for-each select="$additionalMonetaryTotal">
      <xsl:choose>
        <xsl:when test="not(string(./cbc:ID))">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2046'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2046)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:variable name="respParametro">
            <xsl:call-template name="parametro741">
              <xsl:with-param name="codigo" select="./cbc:ID"/>
            </xsl:call-template>
          </xsl:variable>
          <xsl:choose>
            <xsl:when test="gemfunc:is-blank($respParametro)">
              <xsl:call-template name="throwError">
                <xsl:with-param name="codigo" select="'2045'"/>
              </xsl:call-template>
              <xsl:message terminate="yes" dp:priority="debug">
                <xsl:value-of select="'Error Expr Regular Factura (codigo: 2045)'"/>
              </xsl:message>
            </xsl:when>
            <xsl:otherwise>
              <xsl:if test="$sacSUNATTransactionID[text() = '04'] and (./cbc:ID[text() = '1001']) and (./cbc:PayableAmount[text()&lt;=0])">
                <xsl:call-template name="throwError">
                  <xsl:with-param name="codigo" select="'2502'"/>
                </xsl:call-template>
                <xsl:message terminate="yes" dp:priority="debug">
                  <xsl:value-of select="'Error Expr Regular Factura (codigo: 2502)'"/>
                </xsl:message>
              </xsl:if>
              <xsl:if test="(./cbc:ID[text() = '2005']) and (not(./cbc:PayableAmount) or not(regexp:match(./cbc:PayableAmount,'^[0-9]{1,12}(\.[0-9]{1,2})?$')))">
                <xsl:call-template name="throwError">
                  <xsl:with-param name="codigo" select="'2065'"/>
                </xsl:call-template>
                <xsl:message terminate="yes" dp:priority="debug">
                  <xsl:value-of select="'Error Expr Regular Factura (codigo: 2065)'"/>
                </xsl:message>
              </xsl:if>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
    <xsl:if test="not(($additionalMonetaryTotalID[text()='1001' or text()='1002' or text()='1003' or text()='1004' or text()='3001']))">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2047'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2047)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="(count($additionalMonetaryTotalID[text()='1001'])&gt;1)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2349'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2349)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="(count($additionalMonetaryTotalID[text()='1002'])&gt;1)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2350'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2350)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="(count($additionalMonetaryTotalID[text()='1003'])&gt;1)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2351'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2351)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:if test="(count($additionalMonetaryTotalID[text()='1001'])&gt;1 or     count($additionalMonetaryTotalID[text()='1002'])&gt;1 or      count($additionalMonetaryTotalID[text()='1003'])&gt;1 or      count($additionalMonetaryTotalID[text()='1004'])&gt;1 or      count($additionalMonetaryTotalID[text()='3001'])&gt;1)">
      <xsl:call-template name="throwError">
        <xsl:with-param name="codigo" select="'2406'"/>
      </xsl:call-template>
      <xsl:message terminate="yes" dp:priority="debug">
        <xsl:value-of select="'Error Expr Regular Factura (codigo: 2406)'"/>
      </xsl:message>
    </xsl:if>
    <xsl:for-each select="$additionalMonetaryTotal">
      <xsl:choose>
        <xsl:when test="not(./cbc:PayableAmount)">
          <xsl:call-template name="throwError">
            <xsl:with-param name="codigo" select="'2044'"/>
          </xsl:call-template>
          <xsl:message terminate="yes" dp:priority="debug">
            <xsl:value-of select="'Error Expr Regular Factura (codigo: 2044)'"/>
          </xsl:message>
        </xsl:when>
        <xsl:otherwise>
          <xsl:if test="not(regexp:match(./cbc:PayableAmount,&quot;^[0-9]{1,12}(\.[0-9]{1,2})?$&quot;))">
            <xsl:call-template name="throwError">
              <xsl:with-param name="codigo" select="'2043'"/>
            </xsl:call-template>
            <xsl:message terminate="yes" dp:priority="debug">
              <xsl:value-of select="'Error Expr Regular Factura (codigo: 2043)'"/>
            </xsl:message>
          </xsl:if>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
    <xsl:copy-of select="."/>
  </xsl:template>
  <xsl:template name="comprobante_anticipado"/>
  <xsl:template name="getPrepaidPaymentAcum">
    <xsl:param name="Items"/>
    <xsl:variable name="Item1" select="$Items[1]"/>
    <xsl:variable name="RemainingItems" select="$Items[position() &gt; 1]"/>
    <xsl:variable name="acumulador" select="$Item1/cbc:PaidAmount"/>
    <xsl:choose>
      <xsl:when test="$RemainingItems">
        <xsl:if test="$Item1/cbc:PaidAmount and (regexp:match($Item1/cbc:PaidAmount,'^[0-9]{1,12}(\.[0-9]{1,2})?$') and $Item1/cbc:PaidAmount &gt; 0 )">
          <xsl:variable name="subAcum">
            <xsl:call-template name="getPrepaidPaymentAcum">
              <xsl:with-param name="Items" select="$RemainingItems"/>
            </xsl:call-template>
          </xsl:variable>
          <xsl:value-of select="$acumulador + $subAcum"/>
        </xsl:if>
      </xsl:when>
      <xsl:otherwise>
        <xsl:value-of select="$acumulador"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  <xsl:template name="parametro741">
    <xsl:param name="codigo"/>
    <xsl:variable name="descripcionParam" select="document('local:///sistemagem/catalogos/Catalogo741.xml')"/>
    <xsl:value-of select="$descripcionParam/catalogo741/item[@numero=$codigo]"/>
  </xsl:template>
  <func:function name="gemfunc:is-blank">
    <xsl:param name="data" select="''"/>
    <func:result select="regexp:match($data,'^[\s]*$')"/>
  </func:function>
</xsl:stylesheet>
