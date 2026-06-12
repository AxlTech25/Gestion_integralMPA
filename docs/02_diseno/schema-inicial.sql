-- SGMI - Schema integral Fase 1
-- Motor: MySQL 8.0+ (MariaDB 10.6+ compatible)
-- Charset: utf8mb4
-- Versión: 2.0
-- Documentación: docs/02_diseno/base-datos-sgmi.md

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================================
-- NÚCLEO
-- ============================================================================

CREATE TABLE unidades_organizacionales (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_org          VARCHAR(20) NOT NULL,
    codigo_siga         VARCHAR(50) NULL,
    nombre              VARCHAR(200) NOT NULL,
    tipo                ENUM('politico', 'ejecutivo', 'gerencia', 'unidad', 'comite') NOT NULL,
    permite_derivacion  TINYINT(1) NOT NULL DEFAULT 0,
    gerencia_id         BIGINT UNSIGNED NULL,
    padre_id            BIGINT UNSIGNED NULL,
    activa              TINYINT(1) NOT NULL DEFAULT 1,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_codigo_org (codigo_org),
    KEY idx_unidades_gerencia (gerencia_id),
    KEY idx_unidades_padre (padre_id),
    KEY idx_unidades_derivacion (permite_derivacion, activa),
    CONSTRAINT fk_unidades_gerencia FOREIGN KEY (gerencia_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_unidades_padre FOREIGN KEY (padre_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT chk_comite_no_derivacion CHECK (tipo <> 'comite' OR permite_derivacion = 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username            VARCHAR(50) NOT NULL,
    email               VARCHAR(100) NULL,
    password            VARCHAR(255) NOT NULL,
    nombre_completo     VARCHAR(200) NOT NULL,
    unidad_activa_id    BIGINT UNSIGNED NOT NULL,
    activo              TINYINT(1) NOT NULL DEFAULT 1,
    intentos_fallidos   SMALLINT NOT NULL DEFAULT 0,
    bloqueado_hasta     TIMESTAMP NULL,
    ultimo_login        TIMESTAMP NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_username (username),
    KEY idx_usuarios_unidad (unidad_activa_id),
    CONSTRAINT fk_usuarios_unidad FOREIGN KEY (unidad_activa_id) REFERENCES unidades_organizacionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo      VARCHAR(50) NOT NULL,
    nombre      VARCHAR(100) NOT NULL,
    descripcion TEXT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_roles_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permisos (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo      VARCHAR(100) NOT NULL,
    modulo      VARCHAR(50) NOT NULL,
    descripcion TEXT NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_permisos_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permiso (
    role_id     BIGINT UNSIGNED NOT NULL,
    permiso_id  BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, permiso_id),
    CONSTRAINT fk_rp_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    CONSTRAINT fk_rp_permiso FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuario_role (
    usuario_id  BIGINT UNSIGNED NOT NULL,
    role_id     BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (usuario_id, role_id),
    CONSTRAINT fk_ur_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    CONSTRAINT fk_ur_role FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuario_traslados (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      BIGINT UNSIGNED NOT NULL,
    unidad_id       BIGINT UNSIGNED NOT NULL,
    fecha_inicio    DATE NOT NULL,
    fecha_fin       DATE NULL,
    motivo          TEXT NULL,
    registrado_por  BIGINT UNSIGNED NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_traslados_usuario (usuario_id, fecha_inicio),
    CONSTRAINT fk_traslados_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_traslados_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_traslados_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE auditoria_logs (
    id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  BIGINT UNSIGNED NULL,
    modulo      VARCHAR(50) NOT NULL,
    accion      VARCHAR(50) NOT NULL,
    entidad     VARCHAR(50) NOT NULL,
    entidad_id  BIGINT UNSIGNED NULL,
    ip_address  VARCHAR(45) NULL,
    metadata    JSON NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_auditoria_modulo (modulo, created_at),
    KEY idx_auditoria_usuario (usuario_id, created_at),
    KEY idx_auditoria_entidad (entidad, entidad_id),
    CONSTRAINT fk_auditoria_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DOCUMENTARIA — catálogo
-- ============================================================================

CREATE TABLE tipos_documentales (
    id                           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo                       VARCHAR(20) NOT NULL,
    nombre                       VARCHAR(150) NOT NULL,
    prefijo_numeracion           VARCHAR(20) NOT NULL,
    formato_display              VARCHAR(50) NOT NULL DEFAULT '{prefijo}-{anio}-{secuencial}',
    clase_norma                  ENUM('acuerdo','decreto','ordenanza','resolucion','directiva','gestion_interna','otro') NOT NULL DEFAULT 'otro',
    ambito_emision               ENUM('concejo','alcaldia','gerencia_municipal','gerencia','sub_gerencia','unidad') NOT NULL DEFAULT 'unidad',
    unidad_emisora_id            BIGINT UNSIGNED NULL,
    registro_por_secretaria      TINYINT(1) NOT NULL DEFAULT 0,
    requiere_firma_antes_derivar TINYINT(1) NOT NULL DEFAULT 1,
    requiere_recepcion           TINYINT(1) NOT NULL DEFAULT 1,
    activo                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tipos_codigo (codigo),
    KEY idx_tipos_emisora (unidad_emisora_id, activo),
    CONSTRAINT fk_tipos_unidad_emisora FOREIGN KEY (unidad_emisora_id) REFERENCES unidades_organizacionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tipo_documental_unidades_registro (
    tipo_documental_id  BIGINT UNSIGNED NOT NULL,
    unidad_id           BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (tipo_documental_id, unidad_id),
    CONSTRAINT fk_tdur_tipo FOREIGN KEY (tipo_documental_id) REFERENCES tipos_documentales(id) ON DELETE CASCADE,
    CONSTRAINT fk_tdur_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE numeraciones_expediente (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_documental_id  BIGINT UNSIGNED NOT NULL,
    anio                SMALLINT NOT NULL,
    ultimo_secuencial   INT NOT NULL DEFAULT 0,
    UNIQUE KEY uk_numeracion_tipo_anio (tipo_documental_id, anio),
    CONSTRAINT fk_numeracion_tipo FOREIGN KEY (tipo_documental_id) REFERENCES tipos_documentales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sellos_institucionales (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    unidad_id       BIGINT UNSIGNED NULL,
    nombre          VARCHAR(150) NOT NULL,
    imagen_path     VARCHAR(500) NOT NULL,
    activo          TINYINT(1) NOT NULL DEFAULT 1,
    vigente_desde   DATE NULL,
    vigente_hasta   DATE NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_sellos_unidad (unidad_id, activo),
    CONSTRAINT fk_sellos_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- DOCUMENTARIA — expediente y trámite
-- ============================================================================

CREATE TABLE expedientes (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_documental_id  BIGINT UNSIGNED NOT NULL,
    anio                SMALLINT NOT NULL,
    secuencial          INT NOT NULL,
    codigo              VARCHAR(50) NOT NULL,
    asunto              VARCHAR(500) NOT NULL,
    prioridad           ENUM('baja', 'media', 'alta', 'urgente') NOT NULL DEFAULT 'media',
    unidad_origen_id    BIGINT UNSIGNED NOT NULL,
    unidad_actual_id    BIGINT UNSIGNED NOT NULL,
    estado              ENUM('registrado', 'por_recepcionar', 'en_tramite', 'devuelto', 'archivado') NOT NULL DEFAULT 'registrado',
    documento_principal_id BIGINT UNSIGNED NULL,
    registrado_por      BIGINT UNSIGNED NOT NULL,
    archivado_por       BIGINT UNSIGNED NULL,
    archivado_at        TIMESTAMP NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_expediente_tipo_anio_sec (tipo_documental_id, anio, secuencial),
    UNIQUE KEY uk_expedientes_codigo (codigo),
    KEY idx_expedientes_unidad_actual (unidad_actual_id, estado, prioridad),
    KEY idx_expedientes_origen (unidad_origen_id),
    FULLTEXT KEY ft_expedientes_asunto (asunto),
    CONSTRAINT fk_exp_tipo FOREIGN KEY (tipo_documental_id) REFERENCES tipos_documentales(id),
    CONSTRAINT fk_exp_origen FOREIGN KEY (unidad_origen_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_exp_actual FOREIGN KEY (unidad_actual_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_exp_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id),
    CONSTRAINT fk_exp_archivador FOREIGN KEY (archivado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documentos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id       BIGINT UNSIGNED NOT NULL,
    version             SMALLINT NOT NULL DEFAULT 1,
    titulo              VARCHAR(300) NOT NULL,
    es_principal        TINYINT(1) NOT NULL DEFAULT 0,
    documento_anterior_id BIGINT UNSIGNED NULL,
    archivo_path        VARCHAR(500) NULL,
    hash_contenido      VARCHAR(64) NULL,
    estado              ENUM('borrador', 'pendiente_firma', 'firmado') NOT NULL DEFAULT 'borrador',
    creado_por          BIGINT UNSIGNED NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_documentos_expediente (expediente_id, es_principal),
    CONSTRAINT fk_doc_expediente FOREIGN KEY (expediente_id) REFERENCES expedientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_anterior FOREIGN KEY (documento_anterior_id) REFERENCES documentos(id),
    CONSTRAINT fk_doc_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE expedientes
    ADD CONSTRAINT fk_exp_documento_principal FOREIGN KEY (documento_principal_id) REFERENCES documentos(id);

CREATE TABLE documento_firmas (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_id    BIGINT UNSIGNED NOT NULL,
    usuario_id      BIGINT UNSIGNED NOT NULL,
    unidad_id       BIGINT UNSIGNED NOT NULL,
    firma_hash      VARCHAR(128) NOT NULL,
    firma_metadata  JSON NULL,
    firmado_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_firma_documento (documento_id),
    CONSTRAINT fk_firma_documento FOREIGN KEY (documento_id) REFERENCES documentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_firma_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_firma_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documento_sellos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_id        BIGINT UNSIGNED NOT NULL,
    sello_institucional_id BIGINT UNSIGNED NULL,
    sello_imagen_path   VARCHAR(500) NOT NULL,
    sello_metadata      JSON NULL,
    aplicado_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_sello_documento (documento_id),
    CONSTRAINT fk_sello_documento FOREIGN KEY (documento_id) REFERENCES documentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_sello_institucional FOREIGN KEY (sello_institucional_id) REFERENCES sellos_institucionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expediente_movimientos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id       BIGINT UNSIGNED NOT NULL,
    tipo_movimiento     ENUM('registro', 'recepcion', 'derivacion', 'devolucion') NOT NULL,
    unidad_origen_id    BIGINT UNSIGNED NULL,
    unidad_destino_id   BIGINT UNSIGNED NULL,
    unidad_actuante_id  BIGINT UNSIGNED NOT NULL,
    usuario_id          BIGINT UNSIGNED NOT NULL,
    observacion         TEXT NULL,
    proveido            TEXT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_movimientos_expediente (expediente_id, created_at),
    KEY idx_movimientos_destino (unidad_destino_id, created_at),
    CONSTRAINT fk_mov_expediente FOREIGN KEY (expediente_id) REFERENCES expedientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_mov_origen FOREIGN KEY (unidad_origen_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_mov_destino FOREIGN KEY (unidad_destino_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_mov_actuante FOREIGN KEY (unidad_actuante_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_mov_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT chk_devolucion_observacion CHECK (
        tipo_movimiento <> 'devolucion' OR observacion IS NOT NULL AND CHAR_LENGTH(TRIM(observacion)) > 0
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tramite_constancias (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_movimiento_id BIGINT UNSIGNED NOT NULL,
    documento_id            BIGINT UNSIGNED NULL,
    usuario_id              BIGINT UNSIGNED NOT NULL,
    unidad_id               BIGINT UNSIGNED NOT NULL,
    tipo_acto               ENUM('recepcion', 'proveido_salida', 'devolucion', 'firma_documento') NOT NULL,
    firma_hash              VARCHAR(128) NOT NULL,
    firma_metadata          JSON NULL,
    sello_institucional_id  BIGINT UNSIGNED NULL,
    sello_imagen_path       VARCHAR(500) NULL,
    sello_texto             VARCHAR(500) NULL,
    pdf_resultante_path     VARCHAR(500) NULL,
    sello_metadata          JSON NULL,
    created_at              TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_constancia_movimiento (expediente_movimiento_id),
    KEY idx_constancias_unidad (unidad_id, created_at),
    CONSTRAINT fk_const_movimiento FOREIGN KEY (expediente_movimiento_id) REFERENCES expediente_movimientos(id) ON DELETE CASCADE,
    CONSTRAINT fk_const_documento FOREIGN KEY (documento_id) REFERENCES documentos(id),
    CONSTRAINT fk_const_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT fk_const_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_const_sello FOREIGN KEY (sello_institucional_id) REFERENCES sellos_institucionales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expediente_adjuntos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id   BIGINT UNSIGNED NOT NULL,
    nombre_archivo  VARCHAR(255) NOT NULL,
    path            VARCHAR(500) NOT NULL,
    mime_type       VARCHAR(100) NOT NULL,
    tamano_bytes    BIGINT NOT NULL,
    hash_archivo    VARCHAR(64) NULL,
    subido_por      BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_adjuntos_expediente (expediente_id),
    CONSTRAINT fk_adj_expediente FOREIGN KEY (expediente_id) REFERENCES expedientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_adj_usuario FOREIGN KEY (subido_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- PATRIMONIO / TI
-- ============================================================================

CREATE TABLE equipos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_patrimonial  VARCHAR(50) NULL,
    codigo_siga         VARCHAR(50) NULL,
    tipo_equipo         ENUM('pc', 'servidor', 'impresora', 'red', 'otro') NOT NULL,
    marca               VARCHAR(100) NOT NULL,
    modelo              VARCHAR(100) NOT NULL,
    numero_serie        VARCHAR(100) NULL,
    estado_operativo    ENUM('operativo', 'reparacion', 'baja', 'almacen') NOT NULL DEFAULT 'operativo',
    unidad_id           BIGINT UNSIGNED NOT NULL,
    custodio_nombre     VARCHAR(200) NULL,
    custodio_cargo      VARCHAR(150) NULL,
    valor_patrimonial   DECIMAL(12, 2) NULL,
    fecha_adquisicion   DATE NULL,
    registrado_por      BIGINT UNSIGNED NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_equipo_patrimonial (codigo_patrimonial),
    KEY idx_equipos_unidad (unidad_id),
    KEY idx_equipos_siga (codigo_siga),
    CONSTRAINT fk_equipos_unidad FOREIGN KEY (unidad_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_equipos_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fichas_tecnicas (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id           BIGINT UNSIGNED NOT NULL,
    cpu                 VARCHAR(100) NULL,
    ram_gb              SMALLINT NULL,
    almacenamiento_gb   INT NULL,
    sistema_operativo   VARCHAR(100) NULL,
    red                 VARCHAR(100) NULL,
    antiguedad_anios    DECIMAL(4, 1) NULL,
    componentes_json    JSON NULL,
    registrado_por      BIGINT UNSIGNED NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ficha_equipo (equipo_id),
    CONSTRAINT fk_ficha_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    CONSTRAINT fk_ficha_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE fichas_mantenimiento (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id       BIGINT UNSIGNED NOT NULL,
    tipo            ENUM('preventivo', 'correctivo') NOT NULL,
    fecha           DATE NOT NULL,
    descripcion     TEXT NOT NULL,
    resultado       TEXT NULL,
    tecnico         VARCHAR(150) NULL,
    registrado_por  BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_mantenimiento_equipo (equipo_id, fecha),
    CONSTRAINT fk_mant_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    CONSTRAINT fk_mant_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE incidencias (
    id               BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id        BIGINT UNSIGNED NOT NULL,
    reportado_por    BIGINT UNSIGNED NOT NULL,
    tipo             ENUM('falla', 'averia', 'requerimiento') NOT NULL,
    descripcion      TEXT NOT NULL,
    estado           ENUM('abierta', 'en_atencion', 'cerrada') NOT NULL DEFAULT 'abierta',
    solucion         TEXT NULL,
    asignado_utis_id BIGINT UNSIGNED NULL,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cerrada_at       TIMESTAMP NULL,
    KEY idx_incidencias_estado (estado, created_at),
    KEY idx_incidencias_equipo (equipo_id, created_at),
    CONSTRAINT fk_incid_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_incid_reportador FOREIGN KEY (reportado_por) REFERENCES usuarios(id),
    CONSTRAINT fk_incid_utis FOREIGN KEY (asignado_utis_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ml_modelos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    version         VARCHAR(20) NOT NULL,
    algoritmo       VARCHAR(50) NOT NULL DEFAULT 'random_forest',
    parametros_json JSON NULL,
    metricas_json   JSON NULL,
    modelo_path     VARCHAR(500) NULL,
    entrenado_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_ml_version (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ml_predicciones (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    equipo_id           BIGINT UNSIGNED NOT NULL,
    ml_modelo_id        BIGINT UNSIGNED NOT NULL,
    probabilidad_falla  DECIMAL(5, 4) NOT NULL,
    nivel_riesgo        ENUM('verde', 'amarillo', 'rojo') NOT NULL,
    factores_json       JSON NULL,
    calculado_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_predicciones_equipo (equipo_id, calculado_at),
    CONSTRAINT fk_pred_equipo FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    CONSTRAINT fk_pred_modelo FOREIGN KEY (ml_modelo_id) REFERENCES ml_modelos(id),
    CONSTRAINT chk_probabilidad CHECK (probabilidad_falla >= 0 AND probabilidad_falla <= 1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- INTEGRACIONES
-- ============================================================================

CREATE TABLE sync_logs (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sistema         ENUM('siga', 'siaf') NOT NULL,
    tipo_sync       VARCHAR(50) NOT NULL,
    modo            ENUM('automatico', 'manual') NOT NULL,
    estado          ENUM('ok', 'parcial', 'error') NOT NULL,
    registros_ok    INT NOT NULL DEFAULT 0,
    registros_error INT NOT NULL DEFAULT 0,
    mensaje         TEXT NULL,
    ejecutado_por   BIGINT UNSIGNED NULL,
    ejecutado_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_sync_sistema (sistema, ejecutado_at),
    CONSTRAINT fk_sync_usuario FOREIGN KEY (ejecutado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sync_log_detalles (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sync_log_id     BIGINT UNSIGNED NOT NULL,
    entidad_externa VARCHAR(50) NOT NULL,
    referencia      VARCHAR(100) NULL,
    entidad_local   VARCHAR(50) NULL,
    entidad_local_id BIGINT UNSIGNED NULL,
    estado          ENUM('ok', 'error', 'omitido') NOT NULL,
    mensaje         TEXT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_sync_detalle_log (sync_log_id),
    CONSTRAINT fk_sync_detalle_log FOREIGN KEY (sync_log_id) REFERENCES sync_logs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE siaf_ejecucion_snapshots (
    id                      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    periodo                 VARCHAR(20) NOT NULL,
    pim                     DECIMAL(14, 2) NOT NULL,
    ejecucion_total         DECIMAL(14, 2) NOT NULL,
    porcentaje_ejecucion    DECIMAL(5, 2) NOT NULL,
    detalle_resumido_json   JSON NULL,
    es_simulacion           TINYINT(1) NOT NULL DEFAULT 0,
    sincronizado_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_siaf_periodo (periodo, sincronizado_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- VISTAS — dashboard y bandeja
-- ============================================================================

CREATE OR REPLACE VIEW v_bandeja_pendientes AS
SELECT
    e.id,
    e.codigo,
    e.asunto,
    e.prioridad,
    e.estado,
    e.unidad_actual_id,
    e.unidad_origen_id,
    e.tipo_documental_id,
    td.codigo AS tipo_codigo,
    td.nombre AS tipo_nombre,
    ua.nombre AS unidad_actual_nombre,
    e.created_at,
    e.updated_at
FROM expedientes e
INNER JOIN tipos_documentales td ON td.id = e.tipo_documental_id
INNER JOIN unidades_organizacionales ua ON ua.id = e.unidad_actual_id
WHERE e.estado IN ('por_recepcionar', 'en_tramite', 'devuelto', 'registrado');

CREATE OR REPLACE VIEW v_expediente_timeline AS
SELECT
    m.id AS movimiento_id,
    m.expediente_id,
    m.tipo_movimiento,
    m.observacion,
    m.proveido,
    m.created_at AS movimiento_at,
    uo.nombre AS unidad_origen_nombre,
    ud.nombre AS unidad_destino_nombre,
    ua.nombre AS unidad_actuante_nombre,
    us.nombre_completo AS usuario_nombre,
    tc.tipo_acto,
    tc.firma_hash,
    tc.sello_texto,
    tc.created_at AS constancia_at
FROM expediente_movimientos m
INNER JOIN unidades_organizacionales ua ON ua.id = m.unidad_actuante_id
INNER JOIN usuarios us ON us.id = m.usuario_id
LEFT JOIN unidades_organizacionales uo ON uo.id = m.unidad_origen_id
LEFT JOIN unidades_organizacionales ud ON ud.id = m.unidad_destino_id
LEFT JOIN tramite_constancias tc ON tc.expediente_movimiento_id = m.id;

CREATE OR REPLACE VIEW v_dashboard_tramitacion AS
SELECT
    e.unidad_actual_id,
    u.nombre AS unidad_nombre,
    e.estado,
    COUNT(*) AS total_expedientes
FROM expedientes e
INNER JOIN unidades_organizacionales u ON u.id = e.unidad_actual_id
WHERE e.estado <> 'archivado'
GROUP BY e.unidad_actual_id, u.nombre, e.estado;

CREATE OR REPLACE VIEW v_equipos_riesgo AS
SELECT
    eq.id AS equipo_id,
    eq.codigo_patrimonial,
    eq.marca,
    eq.modelo,
    eq.estado_operativo,
    eq.unidad_id,
    p.probabilidad_falla,
    p.nivel_riesgo,
    p.calculado_at AS prediccion_at
FROM equipos eq
LEFT JOIN ml_predicciones p ON p.id = (
    SELECT p2.id
    FROM ml_predicciones p2
    WHERE p2.equipo_id = eq.id
    ORDER BY p2.calculado_at DESC
    LIMIT 1
);

CREATE OR REPLACE VIEW v_equipos_utis AS
SELECT
    id,
    codigo_patrimonial,
    tipo_equipo,
    marca,
    modelo,
    numero_serie,
    estado_operativo,
    unidad_id,
    custodio_nombre,
    custodio_cargo,
    fecha_adquisicion,
    created_at,
    updated_at
FROM equipos
WHERE estado_operativo <> 'baja';

-- ============================================================================
-- SEED: Roles base
-- ============================================================================

INSERT INTO roles (codigo, nombre) VALUES
    ('ADMIN_SISTEMA', 'Administrador del sistema (UTIS)'),
    ('VISTA_EJECUTIVA', 'Vista ejecutiva (Alcaldía / Gerencia Municipal)'),
    ('GERENTE', 'Gerente de línea'),
    ('PATRIMONIO', 'Unidad de Patrimonio'),
    ('UTIS_SOPORTE', 'Soporte TI (UTIS)'),
    ('FINANZAS_SIAF', 'Acceso dashboard SIAF'),
    ('SECRETARIA_GENERAL', 'Secretaría General'),
    ('SUPERVISOR_UNIDAD', 'Supervisor de unidad'),
    ('OPERADOR', 'Operador'),
    ('AUDITOR_OCI', 'Auditor OCI');

INSERT INTO permisos (codigo, modulo, descripcion) VALUES
    ('core.usuarios.gestionar', 'NUCLEO', 'Gestionar usuarios y roles'),
    ('core.auditoria.consultar', 'NUCLEO', 'Consultar auditoría'),
    ('doc.expediente.registrar', 'MOD-DOC', 'Registrar expedientes'),
    ('doc.expediente.consultar', 'MOD-DOC', 'Consultar expedientes'),
    ('doc.expediente.derivar', 'MOD-DOC', 'Derivar expedientes'),
    ('doc.expediente.devolver', 'MOD-DOC', 'Devolver expedientes'),
    ('doc.expediente.recepcionar', 'MOD-DOC', 'Recepcionar y acusar expedientes'),
    ('doc.expediente.archivar', 'MOD-DOC', 'Archivar expedientes'),
    ('doc.documento.firmar', 'MOD-DOC', 'Firmar y sellar documentos'),
    ('doc.tipos.gestionar', 'MOD-DOC', 'Administrar tipos documentales'),
    ('pat.equipo.registrar', 'MOD-PAT-TI', 'Registrar equipos'),
    ('pat.equipo.consultar', 'MOD-PAT-TI', 'Consultar equipos'),
    ('pat.ficha.gestionar', 'MOD-PAT-TI', 'Gestionar fichas técnicas y mantenimiento'),
    ('pat.incidencia.gestionar', 'MOD-PAT-TI', 'Gestionar incidencias TI'),
    ('dash.tramitacion.ver', 'MOD-DASH', 'Ver dashboard tramitación'),
    ('dash.estrategico.ver', 'MOD-DASH', 'Ver dashboard estratégico'),
    ('dash.siaf.ver', 'MOD-DASH', 'Ver datos SIAF'),
    ('int.sync.ejecutar', 'INT', 'Ejecutar sincronizaciones');

SET FOREIGN_KEY_CHECKS = 1;
