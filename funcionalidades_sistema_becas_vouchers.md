# Sistema de gestión de becas alimentarias y generación de vouchers

## Objetivo

Reemplazar el proceso actual basado en una planilla de Excel y sus macros por un sistema local y sencillo, utilizado por una sola persona, que permita cargar y administrar información de los alumnos, asignar comidas, generar vouchers automáticamente y obtener documentos listos para imprimir.

## Flujo general

1. **Carga y administración de alumnos:** datos personales, curso, fechas de vigencia y comidas asignadas.
2. **Configuración del período:** selección de fechas, feriados, días sin actividad y alumnos.
3. **Procesamiento automático:** generación de vouchers según las asignaciones y reglas establecidas.
4. **Vouchers e impresión:** vista previa, exportación a PDF e impresión por lote.
5. **Reportes:** totales y estadísticas de alumnos, servicios y vouchers.

---

## Funcionalidades a desarrollar

### Módulo 1. Gestión de alumnos

**Prioridad: esencial**

- ABM de alumnos (alta, baja y modificación).
- Registro de nombre y apellido, curso/división, turno escolar y contacto.
- Registro de fecha de inicio y finalización de la beca (finalización opcional).
- Activación o desactivación de alumnos.
- Búsqueda y filtrado de alumnos.
- Importación inicial de datos desde Excel.

### Módulo 2. Asignación de comidas

**Prioridad: esencial**

- Matriz semanal editable, similar a la utilizada actualmente en Excel.
- Asignación de comidas por alumno y día, de lunes a viernes.
- Cuatro servicios disponibles: desayuno, almuerzo, merienda y cena.
- Edición directa en la grilla, sin abrir formularios individuales.
- Guardado de las asignaciones.
- Posibilidad de copiar asignaciones entre alumnos o días (*mejora*).

**Ejemplo ilustrativo de la grilla de un alumno:**

| Día | Desayuno | Almuerzo | Merienda | Cena |
|---|:---:|:---:|:---:|:---:|
| Lunes | ✓ | ✓ | ✓ | |
| Martes | | ✓ | ✓ | |
| Miércoles | ✓ | ✓ | ✓ | ✓ |
| Jueves | | | | |
| Viernes | | | | |

### Módulo 3. Calendario y períodos

**Prioridad: esencial**

- ABM de feriados y días sin actividad.
- Registro de vacaciones y recesos escolares.
- Definición de fechas excluidas de la generación.
- Selección del período: mes actual, mes siguiente o rango personalizado.
- Consideración de las fechas de vigencia individuales de cada beca.

### Módulo 4. Generación de vouchers

**Prioridad: esencial**

- Selección de alumnos: todos, ninguno o selección individual.
- Procesamiento de las asignaciones semanales.
- Generación de un voucher por cada alumno, fecha y comida asignada.
- Exclusión de sábados, domingos y días registrados como no hábiles.
- Aplicación de las fechas de inicio y finalización de cada beca.
- Cálculo de la cantidad total de vouchers generados.
- Validaciones antes del procesamiento.
- Prevención de duplicaciones accidentales al volver a generar (*mejora*).

### Módulo 5. Vouchers e impresión

**Prioridad: esencial**

- Plantilla de voucher con nombre del alumno, fecha, tipo de comida y leyenda de validez.
- Distribución de múltiples vouchers por hoja para aprovechar el papel.
- Vista previa del documento.
- Generación de un archivo PDF listo para imprimir.
- Impresión por lote.
- Posibilidad de regenerar o reimprimir vouchers (*mejora*).

**Ejemplo ilustrativo de voucher:**

> **Voucher Cantina "La 35"**  
> **Nombre:** Alumno de ejemplo  
> **Fecha:** 20/11/2026  
> **Comida:** Almuerzo  
> *No transferible · Sujeto a actividad escolar*

### Módulo 6. Reportes y consultas

**Prioridad: esencial**

- Cantidad total de alumnos beneficiarios.
- Totales de comidas por día de la semana.
- Totales semanales por servicio.
- Totales agrupados de desayuno/merienda y almuerzo/cena.
- Consulta de vouchers generados por alumno y período.
- Exportación de reportes a Excel o PDF (*mejora*).

---

## Priorización para una primera versión

| Prioridad | Funcionalidad |
|---|---|
| **P1** | ABM de alumnos y grilla semanal de comidas |
| **P1** | Administración de feriados y fechas |
| **P1** | Generación automática de vouchers |
| **P1** | Exportación a PDF e impresión |
| **P2** | Reportes y estadísticas |
| **P2** | Importación masiva y exportación de datos |
| **P3** | Historial de generaciones y reimpresiones |
| **P3** | Personalización del diseño del voucher |

## Consideraciones de implementación

- El sistema se utilizará **localmente, en un solo equipo y por una sola persona**, sin necesidad inicial de hosting.
- No se requiere, en la primera versión, un módulo de autenticación, permisos, notificaciones o funcionalidades multiusuario.
- Se debe priorizar una **experiencia de carga similar a Excel** para editar numerosos registros de forma ágil.
- El sistema puede utilizar una base de datos local, manteniendo una interfaz simple para la persona que lo opera.
- Conviene diferenciar el **turno escolar** del alumno (mañana, tarde o noche) del **servicio alimentario** (desayuno, almuerzo, merienda o cena).

## Alcance mínimo viable (MVP)

El MVP consiste en **cargar alumnos, asignarles comidas mediante una grilla, definir fechas y feriados, presionar «Generar vouchers» y obtener un PDF listo para imprimir**.

Los reportes avanzados, el historial de reimpresiones y la personalización del formato pueden incorporarse en etapas posteriores.
