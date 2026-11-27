# 0007. Importación de feriados nacionales

Estado: vigente.

## Contexto
La carga anual de feriados es manual y debe convivir con exclusiones agregadas por el usuario.

## Decisión
Consultar ArgentinaDatos para el año elegido y agregar cada fecha que aún no esté cubierta por una exclusión. Validar la respuesta completa antes de guardar y conservar los registros existentes. Los feriados importados son exclusiones editables.

## Alternativas
Reemplazar o actualizar los registros existentes facilitaría la sincronización, pero podría sobrescribir cambios manuales.

## Consecuencias
La consulta requiere conexión y disponibilidad del proveedor. Repetirla no duplica fechas cubiertas; al volver a consultar se pueden incorporar fechas eliminadas manualmente.
