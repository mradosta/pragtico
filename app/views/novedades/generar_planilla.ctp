<?php
/**
 * Este archivo contiene la presentacion.
 *
 * PHP versions 5
 *
 * @filesource
 * @copyright       Copyright 2007-2008, Pragmatia de RPB S.A.
 * @link            http://www.pragmatia.com
 * @package         pragtico
 * @subpackage      app.views
 * @since           Pragtico v 1.0.0
 * @version         $Revision$
 * @modifiedby      $LastChangedBy$
 * @lastmodified    $Date$
 * @author          Martin Radosta <mradosta@pragmatia.com>
 */
 
if(!empty($registros)) {
    $documento->create(array('password' => true));
    $fila = $filaInicio = 8;

    /**
    * Oculto la columna donde tengo los identificadores de la relacion.
    */
    $documento->doc->getActiveSheet()->getColumnDimension('A')->setVisible(false);

    /**
    * Pongo el titulo de la planilla.
    */
    $documento->setCellValue('E1:M3', 'Novedades - ' . date('Y-m-d'),
        array('style' => array('font'       => array('bold' => true, 'size' => 14),
                            'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER))
            ));

    /**
    * Agrego el logo de Pragtico.
    */
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName('Pragtico');
    $objDrawing->setDescription('Pragtico');
    $objDrawing->setPath(WWW_ROOT . 'img' . DS . 'logo_pragtico.jpg');
    $objDrawing->setCoordinates('B1');
    $objDrawing->setHeight(130);
    $objDrawing->setWidth(260);
    $objDrawing->getShadow()->setVisible(true);
    $objDrawing->setWorksheet($documento->doc->getActiveSheet());

    /**
    * Pongo las columnas en auto ajuste del ancho.
    */
    $documento->doc->getActiveSheet()->getColumnDimensionByColumn('B')->setWidth(8);
    $documento->doc->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
    $documento->doc->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);

    /**
    * Pongo los titulos de las columnas.
    */
    $estiloTituloColumna =
        array(
            'font'    => array(
                'bold'      => true
            ),
            'alignment' => array(
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'fill' => array(
                'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation'   => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0'
                ),
                'endcolor'   => array(
                    'argb' => 'FFFFFFFF'
                )
            )
        );

    $documento->setCellValue('A' . $fila . ':A' . ($fila+1), 'Relacion');
    $documento->setCellValue('B' . $fila . ':B' . ($fila+1), 'Empleador', array('style' => $estiloTituloColumna));
    $documento->setCellValue('C' . $fila . ':C' . ($fila+1), 'Trabajador', array('style' => $estiloTituloColumna));
    $documento->setCellValue('D' . $fila . ':D' . ($fila+1), 'Categoria', array('style' => $estiloTituloColumna));
    $documento->setCellValue('E' . $fila . ':E' . ($fila+1), 'Ingreso', array('style' => $estiloTituloColumna));
    $documento->setCellValue('F' . $fila . ':F' . ($fila+1), 'Egreso', array('style' => $estiloTituloColumna));
    $columna = $columnaInicioConceptosDinamicos = 5;
    
    /**
    * Las horas.
    */
    if (in_array('Horas', $tipos)) {
        $columna++;
        $documento->setCellValue($columna . ',' . $fila . ':' . ($columna+2) . ',' . $fila, 'Horas', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Normal', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '50%', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '100%', array('style' => $estiloTituloColumna));
        
        $columna++;
        $documento->setCellValue($columna . ',' . $fila . ':' . ($columna+2) . ',' . $fila, 'Horas Ajuste', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Normal', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '50%', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '100%', array('style' => $estiloTituloColumna));
        
        $columna++;
        $documento->setCellValue($columna . ',' . $fila . ':' . ($columna+2) . ',' . $fila, 'Horas Nocturna', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Normal', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '50%', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '100%', array('style' => $estiloTituloColumna));
        
        $columna++;
        $documento->setCellValue($columna . ',' . $fila . ':' . ($columna+2) . ',' . $fila, 'Horas Ajuste Nocturna', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Normal', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '50%', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(8);
        $columna++;
        $documento->setCellValue($columna . ',' . ($fila+1), '100%', array('style' => $estiloTituloColumna));
    }
    
    /**
    * Las ausencias.
    */
    if (in_array('Ausencias', $tipos)) {
        $columna++;
        $documento->setCellValue($columna . ',' . $fila . ':' . ($columna+2) . ',' . $fila, 'Ausencias', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(27);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Motivo', array('style' => $estiloTituloColumna));
        $columnaMotivo = $columna;
        $columna++;
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(10);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Desde', array('style' => $estiloTituloColumna));
        $columna++;
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(6);
        $documento->setCellValue($columna . ',' . ($fila+1), 'Dias', array('style' => $estiloTituloColumna));
    }
    
    
    /**
    * Los vales.
    */
    if (in_array('Vales', $tipos)) {
        $columna++;
        $documento->setCellValue($columna . ',' . $fila, 'Vales', array('style' => $estiloTituloColumna));
        $documento->setCellValue($columna . ',' . ($fila+1), '$', array('style' => $estiloTituloColumna));
        $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(9);
    }
    
    /**
    * Trabajo con los genericos si hay alguno.
    */
    foreach ($tipos as $concepto) {
        if (!in_array($concepto, $tiposPredefinidos)) {
            $columna++;
            $documento->doc->getActiveSheet()->getColumnDimensionByColumn($columna)->setWidth(30);
            $documento->setCellValue($columna . ',' . $fila . ':' . $columna . ',' . ($fila+1), $concepto, array('style' => $estiloTituloColumna));
        }
    }
    
    /**
    * Recorro cada registro ahora que yatengo los encabezados.
    */
    $fila++;
    $initialRow = $fila + 1;
    foreach ($registros as $registro) {
        $fila++;
        $documento->setCellValue('A' . $fila, $registro['Relacion']['id']);
        $documento->setCellValue('B' . $fila, $registro['Empleador']['nombre']);
        $documento->setCellValue('C' . $fila, $registro['Relacion']['legajo'] . ' - ' . $registro['Trabajador']['apellido'] . ' ' . $registro['Trabajador']['nombre']);
        $documento->setCellValue('D' . $fila, $registro['ConveniosCategoria']['nombre']);
        $documento->setCellValue('E' . $fila, $registro['Relacion']['ingreso']);
        $documento->setCellValue('F' . $fila, ($registro['Relacion']['egreso'] !== '0000-00-00')?$registro['Relacion']['egreso']:'');

        for($i = $columnaInicioConceptosDinamicos; $i < $columna; $i++) {
            $documento->setDataValidation($i . ',' . $fila, 'decimal');
        }
        
        /**
        * El combo con los posibles motivos.
        */
        if (isset($columnaMotivo)) {
            $documento->setDataValidation(($columnaMotivo+1) . ',' . $fila, 'date');
            $documento->setDataValidation($columnaMotivo . ',' . $fila, 'list', array('valores'=>$motivos));
        }
    }
    $documento->doc->getActiveSheet()->freezePane('G10');
    $fila++;
    for ($i = $columnaInicioConceptosDinamicos + 1; $i <= $columna; $i++) {
        $documento->setCellValue($i . ',' . $fila,
            '=SUM(' . PHPExcel_Cell::stringFromColumnIndex($i) . $initialRow . ':' . PHPExcel_Cell::stringFromColumnIndex($i) . ($fila - 1) . ')', array('right', 'bold'));
    }
    
    $documento->save($formatoDocumento);
} else {
    /**
    * Especifico los campos para ingresar las condiciones.
    */
    $condiciones['Condicion.Relacion-trabajador_id'] = array(   
            'lov'=>array(   'controller'        =>  'trabajadores',
                            'separadorRetorno'  =>  ' ',
                            'camposRetorno'     => array('Trabajador.apellido', 'Trabajador.nombre')));

    $condiciones['Condicion.Relacion-empleador_id'] = array(    
            'lov'=>array(   'controller'        => 'empleadores',
                            'camposRetorno'     => array('Empleador.nombre')));

    $condiciones['Condicion.Relacion-id'] = array(  
            'label' => 'Relacion',
            'lov'   => array(   'controller'    => 'relaciones',
                                'camposRetorno' => array('Empleador.nombre', 'Trabajador.apellido')));
    
    $condiciones['Condicion.Novedad-tipo'] = array('type' => 'select', 'multiple' => 'checkbox', 'options'=>$tiposIngreso);
    $condiciones['Condicion.Novedad-formato'] = array('type' => 'radio');
    $fieldsets[] = array('campos' => $condiciones);
    
    $fieldset = $appForm->pintarFieldsets($fieldsets, array('fieldset' => array('legend' => 'Novedades', 'imagen' => 'novedades.gif')));
    $opcionesTabla['tabla']['omitirMensajeVacio'] = true;
    $accionesExtra['opciones'] = array('acciones'=>array($appForm->link('Generar', null, array('class' => 'link_boton', 'id' => 'confirmar', 'title' => 'Confirma las liquidaciones seleccionadas'))));
    $botonesExtra['opciones'] = array('botones'=>array('limpiar', $appForm->submit('Generar', array('title' => 'Genera la planilla base para importar novedades'))));
    echo $this->element('index/index', array('botonesExtra'=>$botonesExtra, 'condiciones'=>$fieldset, 'opcionesForm'=>array('action' => 'generar_planilla'), 'opcionesTabla'=>$opcionesTabla));
}

?>