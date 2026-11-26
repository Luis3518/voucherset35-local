# 0004. Generación e impresión de vouchers

Estado: vigente, documentado retrospectivamente.

## Contexto
Se necesitan vouchers imprimibles para las comidas asignadas a alumnos con becas vigentes.

## Decisión
Generar un voucher por alumno seleccionado, fecha hábil dentro de la vigencia y comida asignada. Ofrecer vista previa y PDF A4 con hasta doce vouchers por página; el lote se conserva descargando el PDF.

Cada alumno comienza en una página nueva y sus vouchers pueden ocupar varias páginas. Se agrupa por identificador de alumno para distinguir homónimos; se descartó compartir páginas entre alumnos para facilitar la distribución, a costa de utilizar más papel.

## Alternativas
No consta una evaluación histórica de alternativas.

## Consecuencias
La generación depende de los alumnos activos seleccionados, sus asignaciones y las exclusiones del calendario. El PDF descargado permite conservar el lote.

En el entorno local de Docker se asignan 512 MB de memoria al servidor PHP: Dompdf construye el documento completo en memoria y el límite predeterminado de 128 MB se agotaba con lotes mensuales. Se eligió aumentar el límite frente a dividir el PDF, para conservar una única descarga. El límite sigue siendo finito y lotes mayores pueden requerir otra estrategia de generación.

Durante la generación se muestra un indicador de progreso indeterminado y se pide mantener la página abierta. Se eligió frente a un porcentaje estimado porque la petición no comunica avances reales; desaparece al terminar o ante un error.
