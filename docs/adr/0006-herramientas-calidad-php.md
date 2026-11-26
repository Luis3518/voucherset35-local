# 0006. Herramientas de calidad PHP

Estado: vigente.

## Contexto
El proyecto necesita comprobaciones reproducibles de estilo y análisis estático para su código PHP.

## Decisión
Se incorporan Pint y Larastan como dependencias de desarrollo. Pint utiliza el estilo predeterminado de Laravel; Larastan analiza `app` en el nivel inicial 0. Composer ofrece los comandos `format`, `lint` y `analyse`.

## Alternativas
Mantener revisiones manuales de estilo y tipos no proporciona comprobaciones automatizadas reproducibles.

## Consecuencias
Las herramientas se ejecutan en el entorno PHP del proyecto. El nivel inicial de análisis permite aumentar el rigor gradualmente.
