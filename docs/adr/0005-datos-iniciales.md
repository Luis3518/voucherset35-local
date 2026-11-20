# 0005. Datos iniciales de ejemplo

Estado: vigente.

## Contexto
Se necesitan datos para comenzar a recorrer el sistema y probar la generación de vouchers.

## Decisión
Una migración agrega 40 alumnos ficticios y les asigna desayuno o merienda de lunes a viernes si la tabla de alumnos está vacía. Las becas comienzan el primer día del año de ejecución y no tienen fecha de fin.

## Alternativas
Un seeder manual requeriría un paso adicional al iniciar el proyecto.

## Consecuencias
Los datos de ejemplo se cargan una sola vez al aplicar la migración. Si ya existen alumnos, no se agregan; revertir la migración no los elimina porque podrían haber sido modificados.
