<?php

/**
 * Catálogo de mensajes de notificación del sistema.
 *
 * Organizado por módulo. Cada operación tiene versiones para:
 * - success
 * - error
 * - warning
 * - info
 *
 * Uso: config("messages.estudiantes.store.success")
 */

return [

    /* ───────────────────────────────────────────────
       MÓDULO: ESTUDIANTES
       ─────────────────────────────────────────────── */
    'estudiantes' => [
        'store' => [
            'success' => 'Estudiante registrado exitosamente.',
            'error' => 'No se pudo registrar el estudiante. Verifique que el código y DNI no estén repetidos.',
            'warning' => 'El estudiante fue registrado, pero faltan datos opcionales como dirección o teléfono.',
        ],
        'update' => [
            'success' => 'Datos del estudiante actualizados correctamente.',
            'error' => 'No se pudieron actualizar los datos. Intente nuevamente.',
            'warning' => 'Algunos cambios no pudieron guardarse porque el registro fue modificado por otro usuario.',
        ],
        'destroy' => [
            'success' => 'Estudiante eliminado del sistema.',
            'error' => 'No se puede eliminar el estudiante porque tiene entregas o devoluciones registradas. Elimine primero esos registros.',
            'warning' => 'El estudiante tiene observaciones activas. Considere revisarlas antes de eliminar.',
        ],
        'force_destroy' => [
            'success' => 'Estudiante y todos sus registros vinculados fueron eliminados permanentemente.',
            'error' => 'No se pudo completar la eliminación en cascada. Contacte al administrador.',
            'warning' => 'Esta acción eliminará entregas, devoluciones y observaciones del estudiante.',
        ],
        'not_found' => [
            'error' => 'Estudiante no encontrado. Verifique que el registro no haya sido eliminado.',
        ],
        'status' => [
            'success' => 'Estado del estudiante actualizado.',
            'info' => 'El estudiante cambió a estado :estado.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: LIBROS
       ─────────────────────────────────────────────── */
    'libros' => [
        'store' => [
            'success' => 'Libro "":titulo"" registrado en el catálogo.',
            'error' => 'No se pudo registrar el libro. El código o ISBN podría estar duplicado.',
            'warning' => 'El libro se registró con stock en cero. Recuerde actualizar el inventario.',
        ],
        'update' => [
            'success' => 'Información del libro actualizada correctamente.',
            'error' => 'No se pudieron guardar los cambios del libro.',
            'warning' => 'La cantidad disponible es menor que la cantidad entregada. Revise el inventario.',
        ],
        'destroy' => [
            'success' => 'Libro eliminado del catálogo.',
            'error' => 'No se puede eliminar el libro porque tiene entregas registradas.',
            'warning' => 'El libro tiene ejemplares en circulación. Asegúrese de que todos hayan sido devueltos.',
        ],
        'not_found' => [
            'error' => 'Libro no encontrado en el catálogo.',
        ],
        'stock' => [
            'success' => 'Stock del libro actualizado. Cantidad disponible: :cantidad.',
            'error' => 'No se pudo actualizar el stock. La operación generaría cantidades negativas.',
            'warning' => 'Stock bajo. Quedan :cantidad unidades disponibles.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: INVENTARIO / EJEMPLARES
       ─────────────────────────────────────────────── */
    'inventario' => [
        'store' => [
            'success' => 'Ejemplar registrado en inventario. Stock actualizado.',
            'error' => 'No se pudo registrar el ejemplar. Verifique el código de barras.',
        ],
        'update' => [
            'success' => 'Datos del ejemplar actualizados.',
            'error' => 'Error al actualizar el ejemplar.',
        ],
        'destroy' => [
            'success' => 'Ejemplar dado de baja del inventario.',
            'error' => 'No se puede dar de baja el ejemplar porque está asignado a un estudiante.',
            'warning' => 'El ejemplar tiene observaciones de deterioro. Revise antes de dar de baja.',
        ],
        'movimiento' => [
            'success' => 'Movimiento de inventario registrado: :tipo.',
            'error' => 'No se pudo registrar el movimiento. Intente nuevamente.',
            'info' => 'Stock anterior: :anterior → Stock nuevo: :nuevo.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: ENTREGAS
       ─────────────────────────────────────────────── */
    'entregas' => [
        'store' => [
            'success' => 'Entrega :codigo registrada. :total libro(s) asignado(s) al estudiante.',
            'error' => 'No se pudo registrar la entrega. Verifique que el estudiante no tenga entregas activas.',
            'warning' => 'La entrega se registró, pero algunos libros tienen stock bajo.',
        ],
        'update' => [
            'success' => 'Entrega :codigo actualizada correctamente.',
            'error' => 'No se pudo actualizar la entrega.',
            'warning' => 'Al cambiar el estado a "anulada", los libros volverán al inventario.',
        ],
        'destroy' => [
            'success' => 'Entrega eliminada y stock de libros restaurado.',
            'error' => 'No se puede eliminar la entrega porque ya tiene una devolución registrada.',
            'warning' => 'Eliminar esta entrega revertirá el inventario. Esta acción no se puede deshacer.',
        ],
        'not_found' => [
            'error' => 'Entrega no encontrada.',
        ],
        'constancia' => [
            'success' => 'Constancia de entrega generada.',
            'error' => 'Error al generar la constancia. Verifique los datos de la entrega.',
            'info' => 'La constancia está lista para descargar o imprimir.',
        ],
        'validacion' => [
            'warning' => 'El estudiante ya tiene una entrega activa para el año escolar actual.',
            'error' => 'No hay libros disponibles para el grado y nivel seleccionados.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: DEVOLUCIONES
       ─────────────────────────────────────────────── */
    'devoluciones' => [
        'store' => [
            'success' => 'Devolución :codigo registrada. :devueltos libro(s) devuelto(s), :deficientes deficiente(s), :perdidos perdido(s).',
            'error' => 'No se pudo registrar la devolución. Verifique que la entrega no haya sido devuelta anteriormente.',
            'warning' => 'Algunos libros fueron marcados como perdidos o deficientes. Se aplicarán las sanciones correspondientes.',
        ],
        'update' => [
            'success' => 'Devolución :codigo actualizada correctamente.',
            'error' => 'No se pudo actualizar la devolución.',
        ],
        'destroy' => [
            'success' => 'Devolución eliminada. El stock de los libros ha sido revertido.',
            'error' => 'No se puede eliminar la devolución.',
            'warning' => 'Eliminar esta devolución revertirá el estado de los libros en inventario.',
        ],
        'not_found' => [
            'error' => 'Devolución no encontrada.',
        ],
        'constancia' => [
            'success' => 'Constancia de devolución generada.',
            'error' => 'Error al generar la constancia.',
            'info' => 'La constancia está lista para descargar o imprimir.',
        ],
        'validacion' => [
            'warning' => 'Esta entrega ya tiene una devolución registrada.',
            'error' => 'La entrega seleccionada no existe o no está completada.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: USUARIOS
       ─────────────────────────────────────────────── */
    'usuarios' => [
        'store' => [
            'success' => 'Usuario :nombre registrado exitosamente.',
            'error' => 'No se pudo registrar el usuario. El correo o DNI podría estar duplicado.',
            'warning' => 'El usuario fue creado sin asignar roles. Configúrelo desde el panel de permisos.',
        ],
        'update' => [
            'success' => 'Datos del usuario actualizados correctamente.',
            'error' => 'No se pudieron actualizar los datos del usuario.',
        ],
        'destroy' => [
            'success' => 'Usuario eliminado del sistema.',
            'error' => 'No se puede eliminar el usuario porque tiene registros asociados (entregas, devoluciones, etc.).',
            'warning' => 'Está eliminando su propia cuenta. Esta acción cerrará su sesión inmediatamente.',
        ],
        'password' => [
            'success' => 'Contraseña actualizada correctamente.',
            'error' => 'La contraseña actual es incorrecta.',
            'warning' => 'La nueva contraseña es similar a una anterior. Por seguridad, use una diferente.',
        ],
        'login' => [
            'success' => 'Bienvenido, :nombre.',
            'error' => 'Credenciales incorrectas. Verifique su usuario y contraseña.',
            'warning' => 'Su sesión está por expirar. Guarde su trabajo.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: REPORTES
       ─────────────────────────────────────────────── */
    'reportes' => [
        'export' => [
            'success' => 'Reporte generado exitosamente. Descargando archivo...',
            'error' => 'Error al generar el reporte. Verifique los filtros seleccionados.',
            'warning' => 'El reporte contiene muchos registros y puede tardar en generarse.',
            'info' => 'El reporte se está procesando. Recibirá una notificación cuando esté listo.',
        ],
        'print' => [
            'success' => 'Documento enviado a la impresora.',
            'error' => 'Error al preparar la impresión.',
            'info' => 'Use la vista previa para verificar antes de imprimir.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MÓDULO: CONFIGURACIÓN
       ─────────────────────────────────────────────── */
    'configuracion' => [
        'store' => [
            'success' => 'Configuración guardada correctamente.',
            'error' => 'No se pudo guardar la configuración. Verifique los permisos de escritura.',
        ],
        'backup' => [
            'success' => 'Copia de seguridad creada. Archivo: :archivo.',
            'error' => 'Error al crear la copia de seguridad. Verifique el espacio en disco.',
            'warning' => 'La última copia de seguridad tiene más de 7 días. Considere crear una nueva.',
            'info' => 'La copia de seguridad se está generando. Este proceso puede tardar varios minutos.',
        ],
        'restore' => [
            'success' => 'Base de datos restaurada correctamente.',
            'error' => 'Error al restaurar la base de datos. El archivo podría estar corrupto.',
            'warning' => 'Esta acción reemplazará todos los datos actuales. Asegúrese de tener una copia de seguridad reciente.',
        ],
    ],

    /* ───────────────────────────────────────────────
       MENSAJES GENÉRICOS DEL SISTEMA
       ─────────────────────────────────────────────── */
    'sistema' => [
        'generic_success' => 'Operación completada exitosamente.',
        'generic_error' => 'Ocurrió un error inesperado. Intente nuevamente o contacte al administrador.',
        'generic_warning' => 'La operación se completó, pero requiere su atención.',
        'generic_info' => 'Información actualizada.',

        'session_expired' => [
            'error' => 'Su sesión ha expirado. Inicie sesión nuevamente para continuar.',
        ],
        'unauthorized' => [
            'error' => 'No tiene permisos para realizar esta acción. Contacte al administrador.',
        ],
        'not_found' => [
            'error' => 'El recurso solicitado no existe o ha sido eliminado.',
        ],
        'validation' => [
            'error' => 'Verifique los campos marcados en rojo y corrija los errores.',
        ],
        'connection' => [
            'error' => 'Error de conexión. Verifique su red e intente nuevamente.',
        ],
    ],

];
