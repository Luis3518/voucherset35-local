---
name: lint-vulnerabilidades
description: Ejecutar lint y comprobaciones de vulnerabilidades apropiadas para los ecosistemas afectados por cambios de código o dependencias.
---

# Lint y vulnerabilidades

Identificar los ecosistemas afectados y usar primero las herramientas y comandos configurados por el proyecto o su CI. Ejecutar el lint disponible y la auditoría de dependencias correspondiente (por ejemplo, `composer audit` para Composer y `npm audit` para npm), respetando los lockfiles y el entorno del proyecto. Si no hay lint configurado, ejecutar una comprobación disponible apropiada e informar la limitación; no afirmar que se ejecutó un linter inexistente. Corregir problemas relacionados dentro del alcance sin actualizar dependencias a ciegas. Informar resultados, vulnerabilidades pendientes y controles que no pudieron ejecutarse, con su motivo.
