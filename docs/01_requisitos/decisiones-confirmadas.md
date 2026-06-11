# Registro de decisiones de negocio — Requisitos v1.1

**Fecha:** 2026-06-10  
**Fuente:** Respuestas stakeholders a PA-01 … PA-22  
**Uso:** Entrada obligatoria para fase de diseño (D-01, D-02)

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

numeracion:
  administrador: secretaria_general
  codigo_global_unico: false
  criterio: por_tipo_documental_y_anio

firma:
  firma_digital: obligatoria_todos_documentos
  sello: obligatorio_por_documento

devolucion:
  permitida: true
  condicion: con_observaciones
  destino: unidad_anterior

sla_tramites:
  existe: false
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
