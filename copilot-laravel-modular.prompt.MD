Actúa como un desarrollador senior experto en Laravel y arquitectura modular.

OBJETIVO GENERAL
Generar migraciones y modelos en Laravel usando una arquitectura modular basada en dominio.
Las tablas serán proporcionadas posteriormente.

REGLAS GENERALES
- NO crear una migración por tabla.
- Crear UNA migración por MÓDULO.
- Cada archivo de migración debe contener múltiples Schema::create().
- Usar convenciones estándar de Laravel (snake_case, unsignedBigInteger, foreignId).
- Todas las tablas deben incluir timestamps().
- Usar softDeletes() donde aplique (suscripciones, clientes, empresas, etc.).
- Todas las claves foráneas deben tener índices.
- Mantener consistencia en nombres y tipos de datos.
- No inventar tablas ni campos que no estén en las tablas proporcionadas.

ARQUITECTURA POR MÓDULOS
Los módulos del sistema son:

1. Autenticación y Usuarios
   - Tabla: users
   - Modelo: User (Laravel default)

2. Personas y Empresas
   - Tablas:
     - clientes
     - empresas
     - rubros_empresas
   - Modelos:
     - Cliente
     - Empresa
     - RubroEmpresa

3. Catálogo de Membresías
   - Tablas:
     - tipos_membresias
     - membresias
     - paquetes_membresias
     - intervalos_membresias
   - Modelos:
     - TipoMembresia
     - Membresia
     - PaqueteMembresia
     - IntervaloMembresia

4. Precios y Beneficios
   - Tablas:
     - precios_paquetes_membresias
     - beneficios_membresias
     - areas_disponibles
   - Modelos:
     - PrecioPaqueteMembresia
     - BeneficioMembresia
     - AreaDisponible

5. Suscripciones
   - Tablas:
     - suscripciones
     - detalles_suscripcion
     - suscripcion_has_clientes
   - Modelos:
     - Suscripcion
     - DetalleSuscripcion
     - SuscripcionCliente

6. Uso, Visitas y Descuentos
   - Tablas:
     - visitas_suscripcion
     - descuentos_visitas
   - Modelos:
     - VisitaSuscripcion
     - DescuentoVisita

ESTRUCTURA DE CARPETAS PARA MODELOS
- NO colocar modelos en app/Models directamente.
- Usar subcarpetas por módulo.

Ejemplo:
app/Models/
 ├── Personas/
 ├── Membresias/
 ├── Suscripciones/
 ├── Uso/
 └── Catalogos/

Cada modelo debe usar su namespace correcto según el módulo.

MIGRACIONES
- Nombre del archivo: create_[nombre_modulo]_tables.php
- Cada migración debe crear TODAS las tablas de su módulo.
- Definir:
  - primary keys
  - foreign keys
  - índices
  - timestamps()
  - softDeletes() donde aplique
- No crear migraciones individuales por tabla.

MODELOS (REQUISITOS OBLIGATORIOS)
Para CADA modelo definir explícitamente:
- protected $table
- protected $fillable (solo campos editables)
- protected $casts (fechas, decimales, booleanos)
- Relaciones Eloquent completas y correctas
- Uso de SoftDeletes si la tabla lo incluye

RELACIONES ELOQUENT ESPERADAS
- Cliente belongsTo User
- Empresa belongsTo RubroEmpresa
- Membresia belongsTo TipoMembresia
- PaqueteMembresia belongsTo Membresia
- PaqueteMembresia hasMany BeneficioMembresia
- Membresia hasMany IntervaloMembresia
- Suscripcion belongsTo PaqueteMembresia
- Suscripcion hasMany DetalleSuscripcion
- Suscripcion belongsToMany Cliente (tabla pivote suscripcion_has_clientes)
- Suscripcion morphTo (cliente o empresa)
- VisitaSuscripcion belongsTo Suscripcion
- DescuentoVisita belongsTo AreaDisponible

POLIMORFISMO
- Usar model_id y model_type
- NO crear claves foráneas físicas para model_type
- Validar el polimorfismo solo a nivel de Eloquent

BUENAS PRÁCTICAS
- Usar nombres de métodos en singular/plural correctamente
- Usar casts para decimal y date/datetime
- Agregar índices a todas las claves foráneas
- Código limpio, legible y mantenible
- Pensar en escalabilidad y mantenimiento

RESULTADO ESPERADO
- Migraciones limpias y agrupadas por módulo
- Modelos organizados por dominio
- Relaciones Eloquent correctamente definidas
- Código listo para producción

Las tablas serán adjuntadas a continuación.



/*
  Tabla users para el modelo de users 
  de Laravel
*/
Table users {
  id int 
  name nvarchar 
  email nvarchar 
  password nvarchar

}

Table areas_disponibles {
  id int 
  nombre_area nvarchar 
  capacidad_maxima int 
}

Table tipos_membresias {
  id int 
  nombre_tipo nvarchar 
}


Table membresias {
  id int
  nombre_membresia nvarchar 
  tipo_menbresia_id nvarchar 

  fecha_inicio date
  fecha_final date 
}

Table paquetes_membresias {
  id int
  nombre_paquete nvarchar 
  membresia_id int 
  cant_per_max int 
}

Table precios_paquetes_membresias {
  id int 
  paquete_membresia_id int 
  intervalo_inicio_id int 
  intervalo_final_id int 
}

Table intervalos_membresias {
  id int 
  membresia_id int 
  fecha date
}

Table beneficios_membresias {
  id int
  area_id int 
  paquete_membresia_id int 
}

/*
  Relacion polimorfica
  permite suscribir a una empresa
  o a un cliente 
*/

Table suscripciones {
  id int 

  fecha_suscripcion date 
  precio decimal(5,2)
  paquete_membresia_id int 
  intervalo_inicio_id int 
  intervalo_final_id int 
  model_id  int 
  model_type nvarchar
}

Table detalles_suscripcion {
  id int 
  suscripcion_id int 
  intervalo_id int 
  precio decimal(5,2)
  
  descuento decimal(5,2)
  descuento_porcentaje decimal(5,2)
  

}

/*
SI ES DE TIPO EMPRESA ENTONCES TENDRA 
CLIENTES ASOCIADOS 
*/
Table suscripcion_has_clientes {
  id int 
  suscripcion_id int 
  cliente_id int 
}


Table rubros_empresas {
  id int 
  nombre_rubro nvarchar 
}

Table empresas {
  id int 
  nombre nvarchar
  rtn nvarchar 
  direccion nvarchar 
  rubro_id int 
  telefono nvarchar 
  email nvarchar 
  representante_id nvarchar 
}


Table clientes {
  id int
  user_id int 
  nombres nvarchar
  apellidos nvarchar 
  codigo_afiliado nvarchar 
  telefono nvarchar 
  email nvarchar 

}


Table visitas_suscripcion {
  id int 
  suscripcion_id int 
}

Table descuentos_visitas {
  id int 
  visita_suscripcion_id  int 
  area_id int 
}



Ref: "tipos_membresias"."id" < "membresias"."tipo_menbresia_id"
  
Ref: "areas_disponibles"."id" < "beneficios_membresias"."area_id"
Ref: "clientes"."id" < "suscripciones"."model_id"

Ref: "suscripciones"."id" < "visitas_suscripcion"."suscripcion_id"

Ref: "visitas_suscripcion"."id" < "descuentos_visitas"."visita_suscripcion_id"

Ref: "areas_disponibles"."id" < "descuentos_visitas"."area_id"

Ref: "rubros_empresas"."id" < "empresas"."rubro_id"

Ref: "membresias"."id" < "intervalos_membresias"."membresia_id"

Ref: "empresas"."id" < "suscripciones"."model_type"

Ref: "membresias"."id" < "paquetes_membresias"."membresia_id"

Ref: "paquetes_membresias"."id" < "beneficios_membresias"."paquete_membresia_id"

Ref: "paquetes_membresias"."id" < "precios_paquetes_membresias"."paquete_membresia_id"

Ref: "intervalos_membresias"."id" < "precios_paquetes_membresias"."intervalo_inicio_id"

Ref: "intervalos_membresias"."id" < "precios_paquetes_membresias"."intervalo_final_id"

Ref: "intervalos_membresias"."id" < "suscripciones"."intervalo_inicio_id"

Ref: "intervalos_membresias"."id" < "suscripciones"."intervalo_final_id"

Ref: "paquetes_membresias"."id" < "suscripciones"."paquete_membresia_id"

Ref: "suscripciones"."id" < "suscripcion_has_clientes"."suscripcion_id"

Ref: "clientes"."id" < "suscripcion_has_clientes"."cliente_id"

Ref: "suscripciones"."id" < "detalles_suscripcion"."suscripcion_id"