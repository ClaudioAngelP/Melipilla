--
-- PostgreSQL database dump
--

-- Started on 2010-06-24 15:30:05 CLT

SET client_encoding = 'LATIN1';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1988 (class 1259 OID 33980)
-- Dependencies: 2421 6
-- Name: func_permiso; Type: TABLE; Schema: public; Owner: sistema; Tablespace: 
--

CREATE TABLE func_permiso (
    permiso_id integer DEFAULT nextval('func_permiso_permiso_id_seq'::regclass) NOT NULL,
    permiso_nombre character varying(60),
    permiso_tipo smallint,
    permgrupo_id smallint,
    permiso_orden smallint
);


ALTER TABLE public.func_permiso OWNER TO sistema;

--
-- TOC entry 2424 (class 0 OID 33980)
-- Dependencies: 1988
-- Data for Name: func_permiso; Type: TABLE DATA; Schema: public; Owner: sistema
--

INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (24, 'Ajuste de Stock', 3, 1, 4);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (3, 'Movimiento de Artículos', 5, 1, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (21, 'Modificar/Eliminar Recetas Ingresadas', 0, 2, 5);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (10, 'Entrega de Medicamentos', 7, 2, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (11, 'Ficha Básica', 0, 2, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (1, 'Ingreso/Edición de Artículos', 0, 1, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (2, 'Recepción de Artículos', 1, 1, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (41, 'Administración de Anaquéles', 0, 5, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (70, 'Hoja de Cargo por Pacientes', 5, 1, 20);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (4, 'Bincard de Productos', 5, 1, 6);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (5, 'Definir Stock Crítico/Pedido', 5, 1, 7);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (6, 'Valorización de Artículos', 5, 1, 8);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (7, 'Libro de Controlados', 6, 1, 9);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (8, 'Lista de Pedido', 5, 1, 10);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (9, 'Recepción de Pedidos', 5, 1, 11);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (26, 'Recepción de Gastos Externos', 0, 1, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (29, 'Autorizar Salida de Pedidos', 5, 1, 16);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (31, 'Órdenes de Compra', 0, 1, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (27, 'Listado de Reposición', 3, 1, 14);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (28, 'Informe General de Gastos', 0, 1, 15);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (19, 'Búsqueda de Recetas', 7, 2, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (22, 'Historial de Pedidos', 5, 1, 11);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (23, 'Historial de Recepciones', 1, 1, 4);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (42, 'Crear Solicitud de Fichas', 0, 5, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (47, 'Priorización y Autorización de Solicitudes', 0, 5, 7);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (46, 'Estado de Solicitudes de Fichas a Archivo', 0, 5, 6);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (43, 'Salida de Fichas de Archivo', 0, 5, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (44, 'Recepción de Fichas en Archivo', 0, 5, 4);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (45, 'Búsqueda de Fichas en Sistema', 0, 5, 5);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (33, 'Informes Generales', 0, 21, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (32, 'Informes Farmacia', 0, 21, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (30, 'Informes Abastecimiento', 0, 21, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (12, 'Ubicaciones', 0, 20, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (13, 'Items Presupuestarios', 0, 20, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (14, 'Departamentos', 0, 20, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (15, 'Centros de Responsabilidad', 0, 20, 4);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (16, 'Proveedores', 0, 20, 5);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (18, 'Convenios', 0, 20, 6);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (25, 'Distribución de Gastos', 0, 20, 7);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (20, 'Talonarios de Recetas', 0, 20, 8);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (17, 'Funcionarios', 0, 20, 9);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (34, 'Ingreso de Interconsultas', 0, 4, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (35, 'Revisión de Solicitudes de Interconsulta', 0, 4, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (36, 'Ver Estado de Solicitudes de Interconsulta', 0, 4, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (37, 'Patologías AUGE', 0, 20, 9);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (40, 'Atencion de Pacientes', 0, 3, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (39, 'Administrar Listas de Espera', 0, 3, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (110, 'Técnicos', 0, 20, 13);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (102, 'Listado de Equipos Médicos Asignados', 0, 101, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (103, 'Asignación de Equipos a Técnicos', 0, 101, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (104, 'Ingreso de Equipos Médicos Nuevos', 0, 101, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (100, 'Listado de Equipos Médicos', 5, 100, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (101, 'Solicitar Mantención Correctiva', 5, 100, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (105, 'Inventario de Equipos Médicos', 0, 101, 4);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (200, 'Consulta por Paciente', 0, 6, 0);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (201, 'Planilla de Prestaciones Diarias', 0, 6, 1);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (202, 'Nóminas del CAE', 0, 6, 2);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (203, 'Registro de FAP', 0, 6, 3);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (204, 'Ingreso de Pacientes Extra', 0, 5, 8);
INSERT INTO func_permiso (permiso_id, permiso_nombre, permiso_tipo, permgrupo_id, permiso_orden) VALUES (50, 'Monitoreo GES', 8, 6, 4);


--
-- TOC entry 2423 (class 2606 OID 41655)
-- Dependencies: 1988 1988
-- Name: pedido_id; Type: CONSTRAINT; Schema: public; Owner: sistema; Tablespace: 
--

ALTER TABLE ONLY func_permiso
    ADD CONSTRAINT pedido_id PRIMARY KEY (permiso_id);


-- Completed on 2010-06-24 15:30:06 CLT

--
-- PostgreSQL database dump complete
--

