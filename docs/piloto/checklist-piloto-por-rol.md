# Checklist de piloto — SGMI Acobamba (Fase 1)

**Versión:** 1.0  
**Fecha:** 2026-06-26  
**Objetivo:** Validar el sistema de punta a punta con **3 unidades piloto** y roles representativos antes del despliegue institucional.

---

## 1. Unidades piloto sugeridas

| Unidad | Código org | Rol en el piloto |
|--------|------------|------------------|
| Unidad de Trámite Documentario y Archivo | ORG-048 | Flujo documentario principal (registro, archivo) |
| Unidad de Presupuesto | ORG-052 | Operación transversal + consulta SIAF |
| Unidad de Tecnología de la Información (UTIS) | ORG-061 | Patrimonio TI, integraciones, soporte |

**Gerencia de referencia:** ORG-021 (Planeamiento y Presupuesto) — agrupa Presupuesto y UTIS.

---

## 2. Preparación del entorno

### Infraestructura

- [ ] MySQL activo en XAMPP (puerto 3306)
- [ ] Base de datos `sgmi_mpa` creada
- [ ] `.env` configurado (`DB_*`, `INTEGRATION_*_DRIVER=simulator`)
- [ ] `php artisan key:generate` (si aplica)
- [ ] `php artisan migrate`
- [ ] `php artisan db:seed` (incluye usuarios piloto vía `PilotoSeeder`)
- [ ] O solo piloto: `php artisan db:seed --class=PilotoSeeder`
- [ ] `npm install` y `npm run build` (o `npm run dev` en desarrollo)
- [ ] `php artisan serve` → http://127.0.0.1:8000
- [ ] Suite de tests en verde: `php artisan test` (46 tests)

### Usuario administrador (ya existe tras seed)

| Campo | Valor |
|-------|-------|
| Usuario | `admin.utis` |
| Contraseña | `Admin@123` |
| Rol | ADMIN_SISTEMA |
| Unidad | ORG-061 UTIS |

### Usuarios piloto (`PilotoSeeder`)

Se crean automáticamente con `php artisan db:seed` o `php artisan db:seed --class=PilotoSeeder`.
Contraseña temporal: **`Piloto@2026`** (cambiar en producción).

| Usuario | Rol | Unidad activa | Uso en piloto |
|---------|-----|---------------|---------------|
| `operador.presupuesto` | OPERADOR | ORG-052 Presupuesto | Tramitación + incidencias TI |
| `supervisor.presupuesto` | SUPERVISOR_UNIDAD | ORG-052 | Bandeja + supervisión |
| `operador.tramite` | OPERADOR | ORG-048 Trámite Doc. | Registro y derivación |
| `gerente.planeamiento` | GERENTE | ORG-021 Gerencia P&P | Dashboard gerencial |
| `finanzas.siaf` | FINANZAS_SIAF | ORG-052 Presupuesto | Bloque presupuestal |
| `patrimonio.unidad` | PATRIMONIO | ORG-059 Patrimonio | Inventario + sync SIGA |
| `auditor.oci` | AUDITOR_OCI | ORG-015 OCI | Auditoría + Calidad SGC |
| `vista.ejecutiva` | VISTA_EJECUTIVA | ORG-003 Gerencia Municipal | Dashboard estratégico |

- [ ] 8 usuarios piloto creados y probados (login exitoso)
- [ ] Roles asignados correctamente (ver menú acorde a permisos)

### Datos iniciales recomendados

- [ ] Ejecutar sync SIGA: `php artisan sgmi:sync-siga all` (o desde UI Integraciones)
- [ ] Ejecutar sync SIAF: `php artisan sgmi:sync-siaf`
- [ ] Verificar inventario con equipos importados (≥20 ítems informáticos)
- [ ] Verificar dashboard con badge **“Datos simulados”**

---

## 3. Flujo transversal (todos los roles)

Escenario único que atraviesa **documentación + TI + dashboard**. Anotar código de expediente generado: `_______________`

| Paso | Actor | Acción | Resultado esperado | ✓ |
|------|-------|--------|-------------------|---|
| 1 | `operador.tramite` | Login → Registro expediente (tipo MEM o INF) | Código generado, estado `registrado` | |
| 2 | `operador.tramite` | Firmar documento principal | Estado documento `firmado`, sello aplicado | |
| 3 | `operador.tramite` | Derivar a ORG-052 Presupuesto | Movimiento `derivacion`, constancia digital | |
| 4 | `operador.presupuesto` | Bandeja → Recepcionar expediente | Estado `en_tramite` | |
| 5 | `operador.presupuesto` | Devolver a ORG-048 con proveído | Estado `devuelto`, historial visible | |
| 6 | `supervisor.presupuesto` | Trazabilidad del expediente | Timeline completo, permanencia por oficina | |
| 7 | `gerente.planeamiento` | Dashboard operativo | KPIs incluyen expediente del flujo | |
| 8 | `operador.presupuesto` | Soporte TI → reportar incidencia en equipo de su unidad | Incidencia `abierta` | |
| 9 | `admin.utis` | Incidencias → asignar y cerrar | Incidencia `cerrada`, equipo actualizado si aplica | |
| 10 | `vista.ejecutiva` | Dashboard estratégico | Semáforo TI, alertas, tramitación por gerencia | |

---

## 4. Checklist por rol

### 4.1 OPERADOR (`operador.presupuesto` / `operador.tramite`)

**Menú esperado:** Panel de control, Gestión documental, Soporte TI  
**No debe ver:** Núcleo, Integraciones, Dashboard estratégico, Calidad (salvo permiso)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| O-01 | Login | Portal → Login | Acceso al panel | |
| O-02 | Bandeja | Gestión documental → Bandeja | Solo expedientes de su unidad/alcance | |
| O-03 | Registro | Nuevo documento → tipo MEM | Expediente creado con numeración | |
| O-04 | Firma | Firmar documento borrador | Documento firmado + sello | |
| O-05 | Derivación | Derivar a unidad con `permite_derivacion` | Éxito; rechazo si unidad comité/inactiva | |
| O-06 | Recepción | Recepcionar expediente derivado a su unidad | Estado `en_tramite` | |
| O-07 | Devolución | Devolver con observación | Estado `devuelto` | |
| O-08 | Trazabilidad | Buscar por código | Historial y movimientos | |
| O-09 | Adjunto | Subir PDF en expediente | Adjunto guardado; rechazo si .exe | |
| O-10 | Incidencia TI | Soporte TI → reportar falla en equipo de su unidad | Incidencia creada | |
| O-11 | Incidencia ajena | Intentar equipo de otra unidad | Bloqueado (403 o no listado) | |
| O-12 | Dashboard | Panel de control | KPIs básicos; **sin** bloque SIAF | |

---

### 4.2 SUPERVISOR DE UNIDAD (`supervisor.presupuesto`)

**Menú extra vs operador:** Panel con tramitación (dash.tramitacion.ver)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| S-01 | Bandeja ampliada | Bandeja con filtro antigüedad | Filtros funcionan | |
| S-02 | Supervisión | Ver expedientes pendientes de su unidad | Lista coherente post-traslado de operadores | |
| S-03 | Dashboard operativo | Panel de control | KPIs y tramitación por unidad (alcance gerencia) | |
| S-04 | Calidad | Calidad SGC → reportar NC | NC registrada (F-ISO-01) | |
| S-05 | Sin estratégico | Intentar `/admin/dashboard-estrategico` | Redirige o sin acceso | |

---

### 4.3 GERENTE (`gerente.planeamiento`)

**Menú esperado:** Panel, Gestión documental (consulta/derivar), Dashboard estratégico, Calidad (consultar/reportar)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| G-01 | Alcance gerencia | Dashboard operativo | Solo expedientes de su gerencia (ORG-021) | |
| G-02 | Dashboard estratégico | Abrir dashboard estratégico | KPIs consolidados de gerencia | |
| G-03 | Tramitación por gerencia | Revisar gráficos/tablas | Datos por unidad dependiente | |
| G-04 | Derivar | Derivar expediente dentro de gerencia | Éxito | |
| G-05 | Sin SIAF | Panel operativo | Bloque SIAF oculto (sin permiso dash.siaf.ver) | |
| G-06 | NC | Reportar no conformidad | NC visible en listado | |

---

### 4.4 UTIS / ADMIN (`admin.utis`)

**Menú esperado:** Casi completo (Núcleo, Patrimonio, Integraciones, Calidad gestión, Dashboards)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| U-01 | Usuarios | Núcleo → crear usuario operador | Usuario activo con rol y unidad | |
| U-02 | Traslado | Trasladar operador a otra unidad piloto | Historial en traslados; bandeja coherente | |
| U-03 | Organigrama | Núcleo → Unidades → desactivar unidad | Derivación a esa unidad rechazada | |
| U-04 | Auditoría | Núcleo → Auditoría | Logs de login, derivaciones, sync | |
| U-05 | Inventario | Patrimonio → Inventario | Lista equipos (manual + SIGA) | |
| U-06 | Ficha técnica | Detalle equipo → ficha + mantenimiento | Datos guardados | |
| U-07 | ML | Semáforo ML / `php artisan sgmi:ml-predict` | Predicciones y niveles de riesgo | |
| U-08 | Incidencias | Gestionar incidencias abiertas | Asignar, cerrar, auditar | |
| U-09 | Integraciones SIGA | Integraciones → Sync patrimonio | ~22 equipos OK, 3 omitidos, log `ok` | |
| U-10 | Integraciones SIGA | Sync organigrama + personal | `codigo_siga` en unidades; referencias personal | |
| U-11 | Integraciones SIAF | Sync SIAF | Snapshot nuevo; dashboard muestra simulación | |
| U-12 | Dashboard SIAF | Panel / estratégico | Bloque PIM y % ejecución visible | |
| U-13 | Calidad | Gestionar NC → crear AC → cerrar NC | Flujo ISO 10.2 completo | |
| U-14 | Tipos documentales | Gestión documental → Tipos (si permiso) | Catálogo institucional editable | |

---

### 4.5 PATRIMONIO (`patrimonio.unidad`)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| P-01 | Inventario | Patrimonio → Inventario | Consulta equipos | |
| P-02 | Alta manual | Registrar equipo nuevo | Equipo en unidad asignada | |
| P-03 | Sync SIGA | Integraciones → patrimonio | Upsert sin duplicar códigos | |
| P-04 | Sin núcleo | Intentar gestionar usuarios | Sin acceso | |

---

### 4.6 FINANZAS SIAF (`finanzas.siaf`)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| F-01 | Bloque SIAF | Panel de control | PIM, ejecución, % y detalle resumido | |
| F-02 | Badge simulación | Revisar texto bajo indicadores | “Datos de simulación SIAF” | |
| F-03 | Sync manual | Integraciones → SIAF | Nuevo snapshot en historial | |
| F-04 | Sin patrimonio | Menú Patrimonio | No visible o sin permiso registrar | |

---

### 4.7 AUDITOR OCI (`auditor.oci`)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| A-01 | Auditoría | Núcleo → Auditoría | Consulta logs; export si disponible | |
| A-02 | Calidad NC | Listar y abrir NC | Lectura y gestión AC | |
| A-03 | Sin tramitar | Intentar registrar expediente | Sin permiso doc.expediente.registrar | |
| A-04 | Trazabilidad | Consultar expediente del flujo piloto | Solo lectura vía API/consulta si aplica | |

---

### 4.8 VISTA EJECUTIVA (`vista.ejecutiva`)

| # | Prueba | Pasos | Esperado | ✓ |
|---|--------|-------|----------|---|
| E-01 | Dashboard estratégico | Acceso directo | Vista institucional completa | |
| E-02 | Semáforo TI | Revisar alertas | Equipos críticos listados | |
| E-03 | Sin operación | Bandeja / registro | Sin menú gestión documental operativa | |
| E-04 | Consulta expediente | Trazabilidad por código (si expuesto) | Solo consulta, sin derivar | |

---

## 5. Criterios de aceptación del piloto

Marcar al cierre de la ronda de pruebas (mínimo **1 semana** con las 3 unidades):

| Criterio | Meta | Cumple |
|----------|------|--------|
| Unidades piloto activas | 3 unidades con ≥1 operador cada una | ☐ |
| Expedientes tramitados digitalmente | ≥10 expedientes con derivación real entre unidades piloto | ☐ |
| Inventario TI alineado SIGA (simulador) | ≥90% equipos informáticos con código patrimonial | ☐ |
| Incidencias TI | ≥3 reportadas y ≥2 cerradas por UTIS | ☐ |
| Dashboard gerencial | Gerente y vista ejecutiva validan KPIs | ☐ |
| Integraciones | ≥1 sync manual SIGA + SIAF con log `ok` | ☐ |
| Calidad SGC | ≥1 NC con AC y cierre documentado | ☐ |
| Seguridad básica | Operador no accede a módulos ajenos (403) | ☐ |
| Tests CI | `php artisan test` — 46/46 PASS | ☐ |

---

## 6. Registro de incidencias del piloto

| ID | Fecha | Rol | Módulo | Descripción | Severidad | Estado |
|----|-------|-----|--------|-------------|-----------|--------|
| PIL-001 | | | | | | |
| PIL-002 | | | | | | |
| PIL-003 | | | | | | |

**Severidad:** Crítica / Alta / Media / Baja

---

## 7. Comandos útiles durante el piloto

```bash
# Entorno
php artisan serve
npm run dev

# Datos
php artisan migrate
php artisan db:seed
php artisan db:seed --class=PilotoSeeder
php artisan db:seed --class=RolePermisoSeeder

# Integraciones (simulador)
php artisan sgmi:sync-siga all
php artisan sgmi:sync-siaf

# ML
php artisan sgmi:ml-predict

# Calidad
php artisan test --filter=IntegracionTest
php artisan test
```

---

## 8. Acta de cierre (firmas)

| Rol | Nombre | Fecha | Conforme |
|-----|--------|-------|----------|
| Responsable UTIS | | | ☐ |
| Gerencia Planeamiento | | | ☐ |
| Representante Trámite Documentario | | | ☐ |
| OCI / Calidad | | | ☐ |

**Observaciones generales:**

_______________________________________________________________________________

_______________________________________________________________________________

---

*Documento vivo — actualizar tras cada ronda de piloto.*
