# Registro de decisiones de negocio — Requisitos v1.3

**Fecha:** 2026-06-10 (PA-01 … PA-22); **2026-06-11** (PA-23 … PA-29)  
**Fuente:** Respuestas stakeholders + sesión de planificación módulos y flujo documental  
**Uso:** Entrada obligatoria para fase de diseño (D-01, D-02) e implementación MOD-DOC

---

## Seguridad y acceso

```yaml
autenticacion:
  tipo: local
  ldap_fase_1: false

politica_contraseña:
  longitud_minima: 8
  requiere_especiales: true
  bloqueo:
    intentos: 5
    duracion_minutos: 5

usuario_unidad:
  unidades_simultaneas: 1
  permite_traslado: true
  historial_traslados: true
```

## Organigrama y roles

```yaml
flujo_documentario:
  incluye_comites: false
  incluye_mesas: false
  incluye: [unidades_operativas, gerencias]
  derivacion_por: gerencia_real

alcaldia_gerencia_municipal:
  acceso: vista_ejecutiva
  permite: [paneles_generales, consultas]
  no_permite: [operacion_documentaria_completa, bandejas_operativas]
```

## Gestión documentaria

```yaml
tipos_documentales:
  fuente: listado_institucional_municipal
  administrador: secretaria_general
  catalogo_normas_legales: catalogo-tipos-normas-documentales.md
  gestion_por_area: true
  unidad_emisora_por_tipo: true
  registro_filtrado_por_unidad_activa: true
  secretaria_registra_alcaldia_concejo: true

numeracion:
  administrador: secretaria_general
  codigo_global_unico: false
  criterio: por_tipo_documental_y_anio

firma:
  firma_digital: obligatoria_todos_documentos
  sello: obligatorio_por_documento

devolucion:
  permitida: true
  condicion: con_observaciones_obligatorias
  destino: automatico_unidad_remitente_inmediata
  operador_elige_destino: false

rechazo:
  usado_en_entidad: false
  implementar_fase_1: false

rutas_documentales:
  tipo: libre
  plantillas_por_tipo_documental: false
  operador_elige_destino_derivacion: true

flujo_multietapa:
  un_expediente_circula_entre_unidades: true
  historial_obligatorio: true
  puede_retornar_a_unidad_origen: true
  ejemplo_cadenas:
    - [presupuesto, almacen, administracion, logistica, tesoreria]
    - [patrimonio, logistica, almacen]

seguimiento_digital:
  cargo_fisico_como_seguimiento_principal: false
  sustituto: expediente_electronico_con_historial_movimientos
  firma_sello_por_unidad_y_movimiento: true
  recepcion_con_firma_sello_antes_derivar: true
  pdf_constancia_desde_historial: opcional_fase_2
  documentacion: digitalizacion-tramite-documentario.md

sla_tramites:
  existe: false
```

## Interfaz y módulos (PA-23, PA-24)

```yaml
arquitectura_ui:
  modelo: una_aplicacion_sgmi
  ventanas_separadas_por_area: false
  portal_servidor: hub_institucional_separado
  menu: dinamico_por_rol_y_permisos
  bandeja_filtrada_por: unidad_actual_expediente

modulos_misma_shell:
  - NUCLEO
  - MOD-DOC
  - MOD-PAT-TI
  - MOD-DASH
  - INT
```

## Patrimonio e TI

```yaml
equipos:
  ambito: solo_municipalidad
  registro: unidad_patrimonio
  consulta_utis: parcial_datos_relevantes

responsable_equipo:
  rol: jefe_area_o_unidad
  proposito: documentacion_custodia
  no_define: permisos_consulta

dueño_dato:
  patrimonio: dueño_registro
  utis: fichas_tecnicas_y_mantenimiento

ml_predictivo:
  algoritmo: random_forest
  entradas:
    - ficha_tecnica
    - ficha_mantenimiento
    - historial_equipo
    - estado_equipo
```

## Integraciones

```yaml
siga:
  metodo: api
  sincronizacion: [diaria, manual_usuario]
  desarrollo: simulador

siaf:
  metodo: lectura
  desarrollo: simulador
  areas_acceso: [presupuesto, tesoreria, contabilidad]
  detalle: limitado
```

## Infraestructura y UX

```yaml
red: solo_municipal
idioma_ui: es
capacidad:
  usuarios_registrados: 200
  usuarios_concurrentes: 100
```
