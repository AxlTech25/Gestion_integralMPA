-- SGMI - Schema inicial Fase 1
-- Motor: MySQL 8.0+ (MariaDB 10.6+ compatible)
-- Charset: utf8mb4
-- Versión: 1.1 (migrado desde PostgreSQL)
-- Generado desde: docs/02_diseno/modelo-datos.md

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
    tipo                ENUM('gerencia', 'unidad', 'comite') NOT NULL,
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
-- DOCUMENTARIA
-- ============================================================================

CREATE TABLE tipos_documentales (
    id                           BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo                       VARCHAR(20) NOT NULL,
    nombre                       VARCHAR(150) NOT NULL,
    prefijo_numeracion           VARCHAR(20) NOT NULL,
    formato_display              VARCHAR(50) NOT NULL DEFAULT '{prefijo}-{anio}-{secuencial}',
    requiere_firma_antes_derivar TINYINT(1) NOT NULL DEFAULT 1,
    activo                       TINYINT(1) NOT NULL DEFAULT 1,
    created_at                   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_tipos_codigo (codigo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE numeraciones_expediente (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo_documental_id  BIGINT UNSIGNED NOT NULL,
    anio                SMALLINT NOT NULL,
    ultimo_secuencial   INT NOT NULL DEFAULT 0,
    UNIQUE KEY uk_numeracion_tipo_anio (tipo_documental_id, anio),
    CONSTRAINT fk_numeracion_tipo FOREIGN KEY (tipo_documental_id) REFERENCES tipos_documentales(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
    estado              ENUM('registrado', 'en_tramite', 'archivado') NOT NULL DEFAULT 'registrado',
    registrado_por      BIGINT UNSIGNED NOT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_expediente_tipo_anio_sec (tipo_documental_id, anio, secuencial),
    KEY idx_expedientes_codigo (codigo),
    KEY idx_expedientes_unidad_actual (unidad_actual_id, estado),
    FULLTEXT KEY ft_expedientes_asunto (asunto),
    CONSTRAINT fk_exp_tipo FOREIGN KEY (tipo_documental_id) REFERENCES tipos_documentales(id),
    CONSTRAINT fk_exp_origen FOREIGN KEY (unidad_origen_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_exp_actual FOREIGN KEY (unidad_actual_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_exp_registrador FOREIGN KEY (registrado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documentos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id   BIGINT UNSIGNED NOT NULL,
    version         SMALLINT NOT NULL DEFAULT 1,
    titulo          VARCHAR(300) NOT NULL,
    archivo_path    VARCHAR(500) NULL,
    hash_contenido  VARCHAR(64) NULL,
    estado          ENUM('borrador', 'pendiente_firma', 'firmado') NOT NULL DEFAULT 'borrador',
    creado_por      BIGINT UNSIGNED NOT NULL,
    created_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_documentos_expediente (expediente_id),
    CONSTRAINT fk_doc_expediente FOREIGN KEY (expediente_id) REFERENCES expedientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_doc_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documento_firmas (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_id    BIGINT UNSIGNED NOT NULL,
    usuario_id      BIGINT UNSIGNED NOT NULL,
    firma_hash      VARCHAR(128) NOT NULL,
    firma_metadata  JSON NULL,
    firmado_at      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_firma_documento (documento_id),
    CONSTRAINT fk_firma_documento FOREIGN KEY (documento_id) REFERENCES documentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_firma_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documento_sellos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    documento_id        BIGINT UNSIGNED NOT NULL,
    sello_imagen_path   VARCHAR(500) NOT NULL,
    sello_metadata      JSON NULL,
    aplicado_at         TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_sello_documento (documento_id),
    CONSTRAINT fk_sello_documento FOREIGN KEY (documento_id) REFERENCES documentos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expediente_movimientos (
    id                  BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id       BIGINT UNSIGNED NOT NULL,
    tipo_movimiento     ENUM('registro', 'derivacion', 'devolucion') NOT NULL,
    unidad_origen_id    BIGINT UNSIGNED NULL,
    unidad_destino_id   BIGINT UNSIGNED NULL,
    usuario_id          BIGINT UNSIGNED NOT NULL,
    observacion         TEXT NULL,
    proveido            TEXT NULL,
    created_at          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_movimientos_expediente (expediente_id, created_at),
    CONSTRAINT fk_mov_expediente FOREIGN KEY (expediente_id) REFERENCES expedientes(id) ON DELETE CASCADE,
    CONSTRAINT fk_mov_origen FOREIGN KEY (unidad_origen_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_mov_destino FOREIGN KEY (unidad_destino_id) REFERENCES unidades_organizacionales(id),
    CONSTRAINT fk_mov_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    CONSTRAINT chk_devolucion_observacion CHECK (
        tipo_movimiento <> 'devolucion' OR observacion IS NOT NULL AND CHAR_LENGTH(TRIM(observacion)) > 0
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE expediente_adjuntos (
    id              BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    expediente_id   BIGINT UNSIGNED NOT NULL,
    nombre_archivo  VARCHAR(255) NOT NULL,
    path            VARCHAR(500) NOT NULL,
    mime_type       VARCHAR(100) NOT NULL,
    tamano_bytes    BIGINT NOT NULL,
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

SET FOREIGN_KEY_CHECKS = 1;
