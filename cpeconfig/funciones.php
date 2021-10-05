<?php 
function estado_sunat($est,$resbol,$combaj)
{
    $data['color'] = '#3D3D3D';

	//estado envio SUNAT y procesamiento
	if($est=='')
	{
		$data['estado_sunat']='-';
		$data['envio_sunat']=1;
	}
	if($est=='0')
	{
		$data['estado_sunat']='NO PROCESADO';
		$data['envio_sunat']=1;
        $data['color'] = '#0288D1';
	}
	if($est=='1')
	{
		$data['estado_sunat']='ACEPTADO';
		$data['anular']=1;
	}
	if($est=='2')
	{
		$data['estado_sunat']='ACEPTADO(!)';
		$data['anular']=1;
	}
	if($est=='3')
	{
		$data['estado_sunat']='RECHAZADO';
        $data['color'] = '#FF6F00';
	}
	if($est=='4')
	{
		$data['estado_sunat']='EXC. SUNAT';
		$data['envio_sunat']=1;
        $data['color'] = '#6200EA';
	}
	if($est=='5')
	{
		$data['estado_sunat']='EXC. LOCAL';
		$data['envio_sunat']=1;
        $data['color'] = '#6200EA';
	}

	//resumen de boleta
    if($resbol=='1')
    {
      	$data['estado_sunat']='RESUMEN';
      	$data['envio_sunat']=1;
      	$data['anular']=1;
        $data['color'] = '#3D3D3D';
    }
    //comunicación de baja
    if($combaj=='1')
    {
        $data['estado_sunat'].=' CB';
        $data['color'] = '#3D3D3D';
    }
    
    if($est=='10')
	{
		$data['estado_sunat']='(ACEPTADO) RESUMEN';
		$data['envio_sunat']=0;
	}


	return $data;
}

?>