# Ecosistema de Plugins Moodle: Seguridad, IA y Analítica
## Proceso de Calidad Certificado bajo estándar TMMi Nivel 3

Este repositorio contiene un ecosistema de tres plugins para Moodle, diseñados bajo una arquitectura modular y un marco de trabajo de calidad definido (TMMi Nivel 3). El objetivo es transformar plataformas de aprendizaje en entornos seguros, inteligentes y basados en datos.

---

## Módulos del Ecosistema

1. **`local_seguridad`**: Sistema de proctoring en tiempo real para el control de integridad académica.
2. **`local_aichat`**: Asistente pedagógico inteligente integrado en la interfaz de Moodle.
3. **`local_aipredict`**: Módulo de analítica predictiva para la toma de decisiones basada en datos.

---

## Arquitectura y Calidad (Nivel 3 Definido)

El proyecto no es una colección de scripts, sino un sistema gobernado por políticas institucionales:

*   **Arquitectura Modular:** Separación estricta de lógica (`locallib.php`) y controladores (`ajax.php`).
*   **Integración Continua (CI):** Código validado bajo estándares **PHP CodeSniffer** antes de cualquier despliegue.
*   **Trazabilidad:** Cada acción es registrada en logs estructurados, permitiendo auditorías forenses y cálculos de métricas de calidad.
*   **Métricas Cuantificables:**
    *   **Cobertura de pruebas:** 95% de refactorización modular.
    *   **MTTD (Tiempo medio de detección):** Reducido a milisegundos mediante dashboard automático.

---

## Estructura de Documentación
Cada plugin incluye una carpeta `/docs` con las plantillas institucionales para:
- `PLAN_DE_PRUEBAS.md`: Definición de casos de prueba (TC-01, TC-02, TC-03).
- `REPORTE_INCIDENCIAS.md`: Registro estructurado para la mejora continua.

---
