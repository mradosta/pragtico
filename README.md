1-      Generación txt. para la transferencia de cualquier liquidación (sueldo, anticipo, SAC, etc.).
HECHO

2-      Modulo Ganancias

-          Importación del formulario 572 web (es el formulario que cada empleado declara sus deducción en AFIP).
NATY
-          Cálculo del impuesto a retener a cada empleado para incluir en la liquidación de haberes.
NATY
-          Generación txt. SICORE (pago de Tecnomyl del impuesto al fisco)
EMI
-          Generación del formulario 1357 (antiguo F649).
NATY
3-      Generación de asientos para la contabilidad de sueldos con cálculo de las provisiones (SAC, Vacaciones, etc.)
PTT / EMI

4-      Otros desarrollos:

a.       Visualización de fecha de antigüedad reconocida en el recibo (hoy tenemos reclamos por este tema)
HECHO

b.       Visualización de los conceptos de algunas licencias (ejemplo lic. sin goce de sueldo, gremial, etc.) y visualización correcta de licencias que restan sueldo (maternidad, ausencia injustificada).



c.       Carga masiva de sueldo básico, hoy Emiliano carga uno por uno.
NO CORRESPONDE

d.       Guardar salario histórico de los trabajadores, hoy cuando se carga uno nuevo pisa el anterior y se pierde el registro.
NO CORRESPONDE

e.       Posibilidad de liquidar un misma novedad en ciertos legajos seleccionados (vacaciones por ejemplo), hoy liquida solo un legajo o nomina completa.
HECHO





                5- Diseño Recibo de Sueldo: Por cuestiones legales se necesitan los siguientes cambios ( Revisado con Norma Flores)

·         En el encabezado se debe incluir la leyenda “Recibo de Haberes”
HECHO

·         En la actual disposición, se debe incluir sobre el domicilio la Razón Social y Tipo societario (TECNOMYL SA)
HECHO

·         En el domicilio se debe detallar el domicilio completo: Rio Grande-Tierra del Fuego CP 9200

·         A los campos de periodo y fecha, se les debe agregar el tipo de liquidación (Ej: Mes, Vacaciones, Final, Especial, etc)
HECHO

·         No es necesario el campo del DNI. Solo CUIL
HECHO

·         En el campo “Banco Deposito” se debe incluir el CBU destinatario del pago.
HECHO

·         El campo “Antig” no corresponde. Si debe visualizarse la fecha de antigüedad reconocida como detalla Cesar en el mail precedente.
HECHO

·         Incluir campo con detalle de salario bruto mensual.
HECHO



6- Formato del documento: Debido a la inminente implementación de la gestión digital se necesita:



·         Impresión recibos formato PDF. Actualmente el sistema genera un archivo Excel que luego debe ser impreso en PDF. Es necesario que se genere directamente en PDF
SE ADAPTO EL SISTEMA PARA QUE IMPRIME EN DIFERENTES HOJAS DE EXCEL y DESDE EXCEL A PDF (https://www.ilovepdf.com/split_pdf)

·         Impresión de nomina completa. Actualmente la nomina del sur debe ser impresa por lotes.
ES UNA LIMITACION DE NUESTRO SERVIDOR, NO DEL SISTEMA. PUEDEN USAR SU PROPIO SERVIDOR Y SE PODRA IMPRIMIR TODO

·         El proveedor del portal de recibos asegura el correcto funcionamiento del mismo si los PDF tienen un tamaño de hasta 129KB. Los recibos que genera Pragtico en su mayoría lo superan. Por lo que se solita la reducción máxima aplicable.
DE DEJO CADA ARCHIVO ENTRE 32K y 38K









ALTER TABLE `conceptos` ADD COLUMN `codigo_afip` VARCHAR(6) NULL AFTER `orden`, ADD COLUMN `cotizaciones` INT UNSIGNED NULL AFTER `remuneracion`;



# pragtico

Sistema de sueldos con interface web. Simplifica la liquidacion de sueldos y jornales y esta pensado para un entorno multiusuario y multiempresa. Especialmente diseñado para estudios contables o empresas de servicios de contratacion eventual.


#####################################################################################################
## BORRAR UNA LIQUIDACION
#####################################################################################################

set foreign_key_checks=0;

begin;

CREATE TEMPORARY TABLE t_l as
    SELECT id
		FROM liquidaciones
		WHERE id in (335528)
		AND estado =  'Confirmada';
CREATE TEMPORARY TABLE t_d as
select id from descuentos where id in (
	select descuento_id from descuentos_detalles where liquidacion_id in (
		select id from t_l
	)
);
delete from liquidaciones_errores where liquidacion_id in (
    select id from t_l
);
delete from `liquidaciones_auxiliares` where liquidacion_id in (
    select id from t_l
);
delete from `liquidaciones_detalles` where liquidacion_id in (
    select id from t_l
);

delete from `horas` where liquidacion_id in (
    select id from t_l
);

delete from descuentos_detalles where descuento_id in (
    select id from t_d
);
update ausencias_seguimientos set estado = 'Confirmado', liquidacion_id = null where liquidacion_id in (
    select id from t_l
);
delete from pagos where liquidacion_id in (
    select id from t_l
);
delete from pagos where descuento_id in (
    select id from t_d
);
delete from novedades where liquidacion_id in (
    select id from t_l
);
delete from descuentos where id in (select id from t_d);
delete from liquidaciones where id in (select id from t_l);

commit;