<?php
	require_once('fr3d\xmldsig\Adapter\AdapterInterface.php');
	require_once('robrichards\xmlseclibs\XMLSecurityDSig.php');
	require_once('robrichards\xmlseclibs\XMLSecurityKey.php');
	require_once('fr3d\xmldsig\Adapter\XmlseclibsAdapter.php');

	$DTE_TIMBRE = new DOMDocument();
	$DTE_TIMBRE->formatOutput = FALSE;
	$DTE_TIMBRE->preserveWhiteSpace = TRUE;

	$DTE_TIMBRE->load("factura.xml");
	$DTE_TIMBRE->encoding = "ISO-8859-1";

	$xmlTool = new FR3D\XmlDSig\Adapter\XmlseclibsAdapter();
	$pfx = file_get_contents("chiuhnos_TemporaryKey.pfx");
	openssl_pkcs12_read($pfx, $key, "chiuhnos");
	$xmlTool->setPrivateKey($key["pkey"]);
	$xmlTool->setpublickey($key["cert"]);


	$xmlTool->addTransform(FR3D\XmlDSig\Adapter\XmlseclibsAdapter::ENVELOPED);
	$xmlTool->sign($DTE_TIMBRE);
	// segundo valor puede ser ,DTE, LIBRO, ENVIO, O VACIO PARA SEMILLAS.

	$DTE_TIMBRE->save("factura_firmada_chiu16162016.xml");

?>
