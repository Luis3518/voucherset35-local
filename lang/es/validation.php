<?php

return [
    'after_or_equal' => 'El campo :attribute debe ser igual o posterior a :date.',
    'array' => 'El campo :attribute debe ser una lista válida.',
    'between' => ['numeric' => 'El campo :attribute debe estar entre :min y :max.'],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'date_format' => 'El campo :attribute debe tener el formato :format.',
    'distinct' => 'El campo :attribute tiene valores repetidos.',
    'in' => 'El valor de :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'list' => 'El campo :attribute debe ser una lista.',
    'max' => ['string' => 'El campo :attribute admite hasta :max caracteres.'],
    'min' => ['numeric' => 'El campo :attribute debe ser como mínimo :min.'],
    'required' => 'Completá el campo :attribute.',
    'string' => 'El campo :attribute debe ser texto.',
    'attributes' => [
        'first_name' => 'nombre', 'last_name' => 'apellido', 'classroom' => 'curso/división',
        'school_shift' => 'turno escolar', 'contact' => 'contacto', 'active' => 'estado',
        'starts_on' => 'fecha de inicio', 'ends_on' => 'fecha de fin', 'kind' => 'tipo',
        'description' => 'motivo', 'student_ids' => 'alumnos', 'student_ids.*' => 'alumno',
        'changes' => 'asignaciones', 'changes.*.student_id' => 'alumno', 'changes.*.weekday' => 'día',
        'changes.*.meal' => 'comida', 'changes.*.assigned' => 'asignación',
    ],
];
