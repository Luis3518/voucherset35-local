# Cantina La 35

Sistema de becas alimentarias para una sola persona. Permite administrar alumnos, asignar comidas por día, registrar fechas sin actividad y generar vouchers listos para imprimir. Usa Laravel 13, SQLite, Blade, Tabulator.js, Bootstrap 5 y DomPDF.

## Desarrollo con Docker Compose

Se requiere Docker con el complemento Compose. Desde la raíz del repositorio, ejecutá:

```bash
docker compose up --build
```

Abrí [http://127.0.0.1:8000](http://127.0.0.1:8000). El contenedor instala las dependencias con Composer y npm, crea `.env` si falta, aplica las migraciones de SQLite y compila los recursos. Las dependencias, los recursos compilados y la base de desarrollo se guardan en volúmenes de Docker. `docker compose down` conserva esos volúmenes.

En una base sin alumnos, una migración carga 40 alumnos ficticios con comidas asignadas para probar el sistema. Si ya hay alumnos, no agrega datos de ejemplo. Los alumnos cargados pueden editarse o eliminarse desde la aplicación; revertir la migración no los borra.

Los cambios en PHP y Blade se ven al recargar la página. Después de editar JavaScript o CSS, volvé a compilar dentro del contenedor:

```bash
docker compose exec app npm run build
```

Para correr las pruebas:

```bash
docker compose exec app composer test
```

Las pruebas usan una base SQLite en memoria y cubren el flujo de generación, la baja definitiva y la paginación del PDF. La instalación local en la PC del cliente queda para una etapa posterior.

## Uso

1. En **Alumnos**, registrá los datos escolares y las fechas de vigencia de la beca. Desde **Editar** podés activar, desactivar o eliminar definitivamente al alumno. La eliminación también borra sus asignaciones.
2. En **Comidas**, marcá las casillas de lunes a viernes para desayuno, almuerzo, merienda y cena. Presioná **Guardar cambios**.
3. En **Calendario**, registrá feriados, días sin actividad, vacaciones o recesos. Para excluir un día, dejá vacía la fecha de fin. Los extremos de un rango se incluyen.
4. En **Vouchers**, elegí el mes actual, el siguiente o un rango personalizado y seleccioná los alumnos activos. **Generar vouchers** muestra el total y la vista previa. Desde allí podés descargar el PDF o abrirlo en el visor e imprimir el lote en tamaño A4.

Se emite un voucher por cada combinación de alumno seleccionado, fecha hábil dentro de la vigencia de su beca y comida asignada. Se excluyen sábados, domingos y fechas registradas en el calendario. Las fechas de inicio y fin de la beca se incluyen. La hoja A4 tiene dos columnas y seis filas, con hasta 12 vouchers. Para conservar un lote, descargá el PDF.

El alcance del MVP y las funcionalidades previstas para etapas posteriores están en [la especificación](funcionalidades_sistema_becas_vouchers.md).
