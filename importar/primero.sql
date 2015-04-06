--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'LATIN1';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: lista_dinamica_bandejas; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE lista_dinamica_bandejas (
    codigo_bandeja text NOT NULL,
    nombre_bandeja text,
    lista_plazo_alerta_amarilla smallint DEFAULT 5,
    lista_plazo_alerta_roja smallint DEFAULT 10,
    lista_campos_tabla text,
    lista_mostrar_fecha_evento boolean DEFAULT false,
    lista_mostrar_cual boolean DEFAULT false,
    lista_remonitorear boolean DEFAULT false
);


ALTER TABLE public.lista_dinamica_bandejas OWNER TO postgres;

--
-- Name: monitoreo_ges; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE monitoreo_ges (
    mon_id bigint NOT NULL,
    mon_fecha date,
    mon_func_id1 bigint,
    mon_func_id2 bigint,
    mon_fecha1 timestamp without time zone,
    mon_fecha2 timestamp without time zone,
    mon_estado boolean,
    mon_pst_id bigint,
    mon_rut text,
    mon_nombre text,
    mon_condicion smallint,
    mon_fecha_inicio date,
    mon_fecha_limite date,
    mon_patologia text,
    mon_garantia text,
    mon_fecha_monitoreo timestamp without time zone,
    mon_fecha_ingreso timestamp without time zone DEFAULT now(),
    mon_cod_especialidad text,
    mon_rama text
);


ALTER TABLE public.monitoreo_ges OWNER TO postgres;

--
-- Name: monitoreo_ges_mon_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE monitoreo_ges_mon_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.monitoreo_ges_mon_id_seq OWNER TO postgres;

--
-- Name: monitoreo_ges_mon_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE monitoreo_ges_mon_id_seq OWNED BY monitoreo_ges.mon_id;


--
-- Name: monitoreo_ges_mon_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('monitoreo_ges_mon_id_seq', 105048, true);


--
-- Name: monitoreo_ges_registro; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE monitoreo_ges_registro (
    monr_id bigint NOT NULL,
    mon_id bigint NOT NULL,
    monr_func_id bigint,
    monr_fecha timestamp without time zone,
    monr_clase text,
    monr_subclase text,
    monr_observaciones text,
    monr_fecha_proxmon date,
    monr_descripcion text,
    monr_subcondicion text,
    monr_fecha_evento date,
    monr_estado smallint DEFAULT 0
);


ALTER TABLE public.monitoreo_ges_registro OWNER TO postgres;

--
-- Name: monitoreo_ges_registro_monr_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE monitoreo_ges_registro_monr_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.monitoreo_ges_registro_monr_id_seq OWNER TO postgres;

--
-- Name: monitoreo_ges_registro_monr_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE monitoreo_ges_registro_monr_id_seq OWNED BY monitoreo_ges_registro.monr_id;


--
-- Name: monitoreo_ges_registro_monr_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('monitoreo_ges_registro_monr_id_seq', 62002, true);


--
-- Name: mon_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY monitoreo_ges ALTER COLUMN mon_id SET DEFAULT nextval('monitoreo_ges_mon_id_seq'::regclass);


--
-- Name: monr_id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY monitoreo_ges_registro ALTER COLUMN monr_id SET DEFAULT nextval('monitoreo_ges_registro_monr_id_seq'::regclass);


--
-- Data for Name: lista_dinamica_bandejas; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY lista_dinamica_bandejas (codigo_bandeja, nombre_bandeja, lista_plazo_alerta_amarilla, lista_plazo_alerta_roja, lista_campos_tabla, lista_mostrar_fecha_evento, lista_mostrar_cual, lista_remonitorear) FROM stdin;
AA	Paciente Reclama Excepcion	5	10	\N	f	f	f
C	Bandeja GES llamado	5	10	\N	f	f	f
F	Bandeja Jefa Estadística	5	10	\N	f	f	f
H	Bandeja Imagenologia	5	10	\N	f	f	f
I	Bandeja Excepción y Correo	5	10	\N	f	f	f
J	Bandeja UGAC	5	10	\N	f	f	f
K	Bandeja Cierre de Caso	5	10	\N	f	f	f
P	Bandeja Validación Compra	5	10	\N	f	f	f
R	Bandeja Call Center Llamada 1	5	10	\N	f	f	f
S	Bandeja Call Center Llamada 2	5	10	\N	f	f	f
T	Call Center UGAC	5	10	\N	f	f	f
U	Bandeja Call Center GES	5	10	\N	f	f	f
W	Bandeja Call Center Imagenologia	5	10	\N	f	f	f
D	Bandeja Monitor Compras	5	10	Fecha Envío Documentos>>>2|Fecha Citación>>>2|Fecha Atención>>>2	f	f	f
AB	Bandeja UGAC Tabla	5	10	\N	f	f	f
A	Bandeja UGAA	5	10	\N	t	f	f
B	Bandeja UGAA Fuera de Plazo	5	10	\N	t	f	f
M	Bandeja Archivo	5	10	\N	f	f	f
N	Bandeja Abastecimiento	5	10	Fecha OC>>>2|Nro. OC>>>3	f	f	f
G	Bandeja Monitor GES por Patología	5	10	\N	f	f	t
O	Bandeja Coordinador GES	5	10	\N	f	f	t
Q	Bandeja Monitor RED	5	10	\N	f	f	t
E	Bandeja SDA	5	10	Tipo de Compra>>>5>>>Normal//Directa|Proveedor>>>5>>>//ISV//Hospital Clínico VDM//IST//Hospital Naval//Clínica Reñaca//(Cotizar...)	f	f	f
X	Bandeja Monitor SIGGES	5	10	\N	f	f	t
V	Bandeja UGAA Macro Red	5	10	\N	f	f	f
L	Bandeja SDM	5	10	\N	f	f	f
\.


--
-- Data for Name: monitoreo_ges; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY monitoreo_ges (mon_id, mon_fecha, mon_func_id1, mon_func_id2, mon_fecha1, mon_fecha2, mon_estado, mon_pst_id, mon_rut, mon_nombre, mon_condicion, mon_fecha_inicio, mon_fecha_limite, mon_patologia, mon_garantia, mon_fecha_monitoreo, mon_fecha_ingreso, mon_cod_especialidad, mon_rama) FROM stdin;
102832	2012-08-17	7	7	\N	\N	f	1088	10778948-0	BUSTOS OLAVE, FRIDA CATARINE	0	2012-07-26	2012-08-06	Linfoma en Adultos .{decreto nº 228}	Tratamiento - Quimioterapia	2012-08-29 00:00:00	2012-08-30 09:40:21.042139		Quimioterapia
102833	2012-07-05	7	7	\N	\N	f	1086	7646574-6	CARVAJAL ARREDONDO, BALTAZAR DEL ROSARIO	0	2012-07-03	2012-08-07	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.0475		No Especifica
102834	2012-07-12	7	7	\N	\N	f	1086	6034749-2	ALTAMIRANO ZÁRATE, MARÍA PATRICIA	0	2012-07-11	2012-08-16	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.050829		No Especifica
102835	2012-07-06	7	7	\N	\N	f	848	4350016-3	OLAVARRÍA DEJEAS, JUAN EDUARDO	0	2012-06-18	2012-08-17	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:21.053769		No Especifica
102836	2012-07-26	7	7	\N	\N	f	821	4487340-0	LAGOS VARO, DOMITILA DEL CARMEN	0	2012-07-20	2012-08-20	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.056916		Derecha
102837	2012-06-26	7	7	\N	\N	f	848	4265339-K	HERRERA SILVA, MANUEL DE LA CRUZ	0	2012-06-22	2012-08-21	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:21.05993		No Especifica
102838	2012-08-17	7	7	\N	\N	f	793	18998171-6	ALBORNOZ VIDAL, DANIEL ALEJANDRO	0	2012-08-09	2012-08-29	Asma Bronquial 15 Años y Más . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.063105		No Especifica
102839	2012-08-09	7	7	\N	\N	f	978	23917902-9	BRIONES JAMETT, NICOLÁS AMARO	0	2012-07-30	2012-08-29	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.066189		No Especifica
102840	2012-08-01	7	7	\N	\N	f	821	10520715-8	HERRERA ZAMORANO, ROSA DEL CARMEN	0	2012-07-30	2012-08-29	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.069275		Izquierda
102841	2012-08-06	7	7	\N	\N	f	821	10216467-9	VEGA PLAZA, ANA ROSA	0	2012-07-30	2012-08-29	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.072278		Izquierda
102842	2012-08-01	7	7	\N	\N	f	1153	5843684-4	ALVARADO FIERRO, RUTH ELENA	0	2012-07-30	2012-08-29	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.075255		No Especifica
102843	2012-08-03	7	7	\N	\N	f	905	5652942-K	COFRÉ ADASME, NORMA ANGELINA	0	2012-07-30	2012-08-29	Cáncer Gástrico . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.078162		No Especifica
102844	2012-06-07	7	7	\N	\N	f	927	5168357-9	SEGUEL ORTEGA, MARÍA	0	2012-05-31	2012-08-29	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:21.081925		Bilateral Igual o Inferior a 0,1
102845	2012-08-28	7	7	\N	\N	f	961	5553108-0	AVELLO CANDIA, MARÍA ISABEL	0	2012-08-24	2012-08-29	Desprendimiento de Retina . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.08501		No Especifica
102846	2012-08-29	7	7	\N	\N	f	794	6473486-5	MAUREIRA BALLADARES, HERNÁN JOSÉ	0	2012-08-28	2012-08-29	Asma Bronquial 15 Años y Más . {decreto n° 1/2010}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.088336		No Especifica
102847	2012-08-17	7	7	\N	\N	f	800	5654893-9	ARAVENA IBACETA, CELIA ELENA DEL CARM	0	2012-08-10	2012-08-30	Cáncer Cervicouterino Invasor {decreto nº 228}	Diagnóstico - Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:21.091524		Invasor
102848	2012-07-23	7	7	\N	\N	f	1053	23969704-6	SÁNCHEZ ZÁRATE, RAFAEL LUIGI	0	2012-05-31	2012-08-30	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.094449		No Especifica
102849	2012-07-26	7	7	\N	\N	f	775	21850173-7	ARMAND MOTTET, CHARLES	0	2012-07-21	2012-08-30	Artrosis de Caderas Izquierda {decreto nº 228}	Control Seguimiento Traumatólogo Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.097437		Izquierda
102850	2012-08-06	7	7	\N	\N	f	821	20174743-0	PEÑA PACHECO, JESLYNE ALEJANDRA	0	2012-07-31	2012-08-30	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.100541		Izquierda
102851	2012-08-03	7	7	\N	\N	f	807	12972701-2	INZUNZA CABRALES, PATRICIA ANDREA	0	2012-07-31	2012-08-30	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-29 00:00:00	2012-08-30 09:40:21.103873		Pre-Invasor
102852	2012-06-05	7	7	\N	\N	f	1046	10348489-8	MORALES REYES, CLAUDIO FERNANDO	0	2012-06-01	2012-08-30	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.107161		No Especifica
102853	2012-08-07	7	7	\N	\N	f	1153	5629093-1	OLGUÍN RAMÍREZ, SONIA DEL CARMEN	0	2012-07-31	2012-08-30	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.110517		No Especifica
102854	2012-08-07	7	7	\N	\N	f	1153	3955602-2	ACUÑA FUENTES, ELENA	0	2012-07-31	2012-08-30	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.113444		No Especifica
102855	2012-08-07	7	7	\N	\N	f	1153	3858561-4	GONZÁLEZ LLANCA, ROSA DEL CARMEN	0	2012-07-31	2012-08-30	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.116308		No Especifica
102856	2012-08-07	7	7	\N	\N	f	1153	3162440-1	OYANADEL BENÍTEZ, SILVIA ELIANA	0	2012-07-31	2012-08-30	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.119332		No Especifica
102857	2012-07-23	7	7	\N	\N	f	1053	23968538-2	AGUILERA OSSANDÓM, MANUELANTONIO	0	2012-06-01	2012-08-31	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.122257		No Especifica
102858	2012-07-23	7	7	\N	\N	f	1053	23968522-6	AGUILERA OSSANDÓN, IGNACIO	0	2012-06-01	2012-08-31	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.125045		No Especifica
102859	2012-08-03	7	7	\N	\N	f	789	20484277-9	ARDILES BENAVENTE, MARÍA FERNANDA	0	2012-08-01	2012-08-31	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.127874		No Especifica
102860	2012-08-07	7	7	\N	\N	f	1153	5937395-1	CHAMORRO ÁLVAREZ, CRISTINA DEL CARMEN	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.130859		No Especifica
102861	2012-08-07	7	7	\N	\N	f	1153	5505256-5	ROJAS BELTRÁN, HILDA NELLY	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.13374		No Especifica
102862	2012-08-07	7	7	\N	\N	f	1153	4621926-0	SAAVEDRA HERMOSILLA, GEORGINA DEL CARMEN	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.136526		No Especifica
102863	2012-08-07	7	7	\N	\N	f	1153	4297471-4	PALMA YÁÑEZ, MIGUEL HERNÁN	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.139492		No Especifica
102864	2012-08-07	7	7	\N	\N	f	1153	3827783-9	ORELLANA SÁNCHEZ, LASTENIA PROSPERINA	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.142643		No Especifica
102865	2012-08-07	7	7	\N	\N	f	1153	3386476-0	SANZ RUIZ, FRANCISCO	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.145552		No Especifica
102866	2012-08-07	7	7	\N	\N	f	1153	2479725-2	LIZANA GUTIÉRREZ, LUIS CECILIO	0	2012-08-01	2012-08-31	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.148395		No Especifica
102867	2012-08-07	7	7	\N	\N	f	978	23920750-2	ACHA PUGA, ELIZABETH JAVIERA	0	2012-08-03	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.151367		No Especifica
102868	2012-08-07	7	7	\N	\N	f	978	23875050-4	ARAYA VARGAS, VALENTINA DEBBIE AMO	0	2012-08-03	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.154197		No Especifica
102869	2012-08-13	7	7	\N	\N	f	807	15097516-6	ALEGRÍA GALLARDO, MAKARENA ANDREA	0	2012-08-03	2012-09-03	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-13 00:00:00	2012-08-30 09:40:21.157081		Pre-Invasor
102870	2012-06-07	7	7	\N	\N	f	968	15082202-5	DÍAZ GONZÁLEZ, MARCELA BEATRIZ	0	2012-06-04	2012-09-03	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.160545		No Especifica
102871	2012-08-08	7	7	\N	\N	f	955	13192082-2	PUÑO TORDECILLA, RUTH DEL CARMEN	0	2012-06-04	2012-09-03	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-29 00:00:00	2012-08-30 09:40:21.163906		No Especifica
102872	2012-08-06	7	7	\N	\N	f	821	12137661-K	ESPINOZA SAAVEDRA, SANDRA MARÍA	0	2012-08-03	2012-09-03	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.167101		Derecha
102873	2012-07-19	7	7	\N	\N	f	829	9592174-4	LEYTON PEREIRA, ELIZABETH SUSANA	0	2012-07-18	2012-09-03	Cáncer de Mama Derecha {decreto nº 228}	Diagnóstico-Etapificación Mama Derecha.	2012-08-22 00:00:00	2012-08-30 09:40:21.170353		Derecha
102874	2012-03-07	7	7	\N	\N	f	925	9148357-2	AURORA DEL CARMEN DÍAZ FERNÁNDEZ	0	2012-03-05	2012-09-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:21.173522		No Especifica
102875	2012-08-03	7	7	\N	\N	f	821	7705802-8	CÁRDENAS VÁSQUEZ, MARCIA JEANNETTE	0	2012-08-02	2012-09-03	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.176469		Izquierda
102876	2012-08-08	7	7	\N	\N	f	1097	6274200-3	CORTÉS CASTRO, JOSÉ URBANO	0	2012-08-02	2012-09-03	Marcapaso . {decreto nº 228}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.17938		No Especifica
102877	2012-08-08	7	7	\N	\N	f	1153	5733351-0	CORDOVA ESPINOZA, MERCEDES DEL ROSARIO	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.18236		No Especifica
102878	2012-03-23	7	7	\N	\N	f	925	5619919-5	JOVITA DEL PILAR MENA ESCOBAR	0	2012-03-07	2012-09-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.185193		No Especifica
102879	2012-08-07	7	7	\N	\N	f	1101	5518640-5	MATURANA VEGA, ROSA ESTER	0	2012-08-03	2012-09-03	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:21.188316		No Especifica
102880	2012-03-08	7	7	\N	\N	f	925	5363505-9	MARÍA DEL CARMEN TRONCOSO OLMEDO	0	2012-03-06	2012-09-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.191171		No Especifica
102881	2012-08-08	7	7	\N	\N	f	1153	5013287-0	CANEO SALINAS, HUGO	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.193993		No Especifica
102882	2012-08-08	7	7	\N	\N	f	1153	4960828-4	LÓPEZ FLORES, ALEJANDRINA DE LAS M	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.196814		No Especifica
102883	2012-08-08	7	7	\N	\N	f	1153	4931522-8	TIRADO CARVAJAL, ANA MERCEDES	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.199653		No Especifica
102884	2012-08-08	7	7	\N	\N	f	1153	4921444-8	RAMOS GARAY, ROSA DE LAS MERCEDES	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.202999		No Especifica
102885	2012-08-07	7	7	\N	\N	f	1153	4918907-9	RAMOS RIQUELME, MACLOVIA DEL CARMEN	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.205772		No Especifica
102886	2012-08-08	7	7	\N	\N	f	1153	4843375-8	CONTRERAS SOTO, LUIS ENRIQUE	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.208623		No Especifica
102887	2012-08-08	7	7	\N	\N	f	1153	4740043-0	RAMÍREZ SANHUEZA, GLADYS DEL CARMEN	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.211455		No Especifica
102888	2012-08-06	7	7	\N	\N	f	1097	4735772-1	GAETE BERRÍOS, SARA DEL CARMEN	0	2012-08-03	2012-09-03	Marcapaso . {decreto nº 228}	Diagnóstico	2012-08-16 00:00:00	2012-08-30 09:40:21.214523		No Especifica
102889	2012-08-06	7	7	\N	\N	f	907	4722893-K	CABRERA ARANCIBIA, PEDRO	0	2012-08-02	2012-09-03	Cáncer Gástrico . {decreto nº 228}	Intervención Quirúrgica	2012-08-27 00:00:00	2012-08-30 09:40:21.217419		No Especifica
102890	2012-08-07	7	7	\N	\N	f	978	23926056-K	ARANCIBIA SAAVEDRA, ISABELLA IGNACIA	0	2012-08-03	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.220424		No Especifica
102891	2012-08-07	7	7	\N	\N	f	978	23922377-K	AESCHLIMANN VERA, PALOMA PASCALL	0	2012-08-03	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.223211		No Especifica
102892	2012-03-06	7	7	\N	\N	f	1043	4331227-8	YUSEF JADUE JADUE	0	2012-03-05	2012-09-03	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:21.226229		Retención Urinaria Aguda Repetida
102893	2012-08-08	7	7	\N	\N	f	1153	4328196-8	CABEZAS MESEN, MANUEL FERNANDO	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.229051		No Especifica
102894	2012-08-08	7	7	\N	\N	f	1153	3906830-3	MENESES NEGRETE, LLILITA MARÍA	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.231944		No Especifica
102895	2012-06-27	7	7	\N	\N	f	775	3633312-K	ZSCHOCHE MARYNECK, TERESA	0	2012-07-23	2012-09-03	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.234885		Derecha
102896	2012-07-19	7	7	\N	\N	f	829	3575247-1	PIMENTEL PIZARRO, ELSA DE JESÚS	0	2012-07-18	2012-09-03	Cáncer de Mama Izquierda {decreto nº 228}	Diagnóstico-Etapificación Mama Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:21.237892		Izquierda
102897	2012-03-09	7	7	\N	\N	f	925	3370953-6	SEGUNDO PIZARRO SUÁREZ	0	2012-03-07	2012-09-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.240876		No Especifica
102898	2012-08-06	7	7	\N	\N	f	1097	3009736-K	TAPIA TAPIA, OLGA	0	2012-08-02	2012-09-03	Marcapaso . {decreto nº 228}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.243664		No Especifica
102899	2012-08-08	7	7	\N	\N	f	1153	2809476-0	BILBAO GUERRA, TERESA JENOVEVA	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.246501		No Especifica
102900	2012-08-08	7	7	\N	\N	f	1153	2736639-2	OLIVARES FERNÁNDEZ, JULIO	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.249436		No Especifica
102901	2012-08-08	7	7	\N	\N	f	1153	2613665-2	MUÑOZ ELGUETA, GILBERTO	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.252432		No Especifica
102902	2012-08-08	7	7	\N	\N	f	1153	2431797-8	VÉLIZ LEIVA, CLOTILDE VIRGINIA	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.25521		No Especifica
102903	2012-06-09	7	7	\N	\N	f	848	1863683-2	VÁSQUEZ ESTAY, ARTURO VÍCTOR	0	2012-07-04	2012-09-03	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:21.258194		No Especifica
102904	2012-08-20	7	7	\N	\N	f	1153	3425089-8	VASQUEZ VEGA, JULIO MANUEL	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.261395		No Especifica
102905	2012-08-08	7	7	\N	\N	f	1153	4613721-3	HIDALGO TAPIA, MARÍA LUZ	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.264351		No Especifica
102906	2012-08-08	7	7	\N	\N	f	1153	4490872-7	PAVEZ PAVEZ, OLIVIA MAFALDA	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.267326		No Especifica
102907	2012-08-08	7	7	\N	\N	f	1153	4378868-K	AGUILERA , MALVINA DE MERCEDES	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.270422		No Especifica
102908	2012-08-07	7	7	\N	\N	f	1101	4350369-3	ALEGRÍA VARGAS, BETTY AILEN	0	2012-08-02	2012-09-03	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.274064		No Especifica
102909	2012-08-08	7	7	\N	\N	f	1153	4342111-5	OSORIO OSORIO, MARÍA EUGENIA	0	2012-08-02	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.277151		No Especifica
102910	2012-08-24	7	7	\N	\N	f	925	2883806-9	CISTERNAS PEDRAZA, LUIS REINALDO	0	2012-03-05	2012-09-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.28053		No Especifica
102911	2012-08-24	7	7	\N	\N	f	1099	4188719-2	TEJEDO VELÁSQUEZ, SONIA BEATRIZ	0	2012-08-17	2012-09-03	Marcapaso . {decreto nº 228}	Seguimiento	2012-08-24 00:00:00	2012-08-30 09:40:21.283597		No Especifica
102912	2012-08-24	7	7	\N	\N	f	1099	8495787-9	LÓPEZ CORTÉS, MARIO ALBERTO	0	2012-08-17	2012-09-03	Marcapaso . {decreto nº 228}	Seguimiento	2012-08-24 00:00:00	2012-08-30 09:40:21.286562		No Especifica
102913	2012-06-27	7	7	\N	\N	f	1053	23979614-1	MONTIEL ASTUDILLO, MATIAS ALONSO	0	2012-06-02	2012-09-03	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.28983		No Especifica
102914	2012-08-09	7	7	\N	\N	f	978	23933159-9	ZAMORA SANTIBÁÑEZ, SOPHIA DENISSE	0	2012-08-04	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.293105		No Especifica
102915	2012-08-09	7	7	\N	\N	f	978	23926398-4	ÁLVAREZ LETELIER, AGATHA ANDREA	0	2012-08-04	2012-09-03	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.296717		No Especifica
102916	2012-08-20	7	7	\N	\N	f	1153	4784034-1	ROJAS MENA, HILDA ROSA	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.299952		No Especifica
102917	2012-08-20	7	7	\N	\N	f	1153	5091106-3	ORREGO ACEVEDO, RIGOBERTA DEL CARMEN	0	2012-08-03	2012-09-03	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:21.302999		No Especifica
102918	2012-08-22	7	7	\N	\N	f	1099	7249661-2	PÉREZ RAGGIO, JIMENA RAMONA	0	2012-08-17	2012-09-03	Marcapaso . {decreto nº 228}	Seguimiento	2012-08-22 00:00:00	2012-08-30 09:40:21.306035		No Especifica
102919	2012-08-27	7	7	\N	\N	f	1088	9067992-9	ARQUEROS CERDA, ESTELA AURORA	0	2012-08-23	2012-09-03	Linfoma en Adultos .{decreto nº 228}	Tratamiento - Quimioterapia	2012-08-27 00:00:00	2012-08-30 09:40:21.30926		Quimioterapia
102920	2012-03-09	7	7	\N	\N	f	925	5109046-2	OLGA PURÍSIMA BUSTOS ESCOBAR	0	2012-03-08	2012-09-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.312545		No Especifica
102921	2012-03-23	7	7	\N	\N	f	925	4958575-6	MARÍA EUGENIA GAZMURI VALENZUELA	0	2012-03-08	2012-09-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.315716		No Especifica
102922	2012-03-09	7	7	\N	\N	f	925	4295712-7	ORFELINA MENA	0	2012-03-08	2012-09-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.318906		No Especifica
102923	2012-03-13	7	7	\N	\N	f	927	3679370-8	ADRIANA DE LAS MERCE FERNÁNDEZ ALBORNOZ	0	2012-03-08	2012-09-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-17 00:00:00	2012-08-30 09:40:21.322863		Bilateral
102924	2012-03-13	7	7	\N	\N	f	925	3546804-8	MARTA ELENA CARDEMIL VALENCIA	0	2012-03-08	2012-09-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.325921		No Especifica
102925	2012-03-13	7	7	\N	\N	f	925	3398877-K	JUANA MARÍA MAGDALEN JENERAL ESPINOZA	0	2012-03-08	2012-09-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.329027		No Especifica
102926	2012-08-21	7	7	\N	\N	f	909	17399524-5	LUCERO BERNAL , RN	0	2012-08-14	2012-09-04	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-29 00:00:00	2012-08-30 09:40:21.332623		Post - Natal 8 Días y menor de 2 Años
102927	2012-08-22	7	7	\N	\N	f	1122	10891277-4	COLÍN DÍAZ, GRACE CLAUDIA	0	2012-08-21	2012-09-04	Prematurez Prevención parto prematuro {decreto nº 228}	Diagnóstico. Factores de Riesgo	2012-08-29 00:00:00	2012-08-30 09:40:21.33593		Factor de Riesgo
102928	2012-08-22	7	7	\N	\N	f	1122	13634412-9	LUCERO JAURE, MICHEL SUSAN	0	2012-08-21	2012-09-04	Prematurez Prevención parto prematuro {decreto nº 228}	Diagnóstico. Factores de Riesgo	2012-08-28 00:00:00	2012-08-30 09:40:21.339504		Factor de Riesgo
102929	2012-08-09	7	7	\N	\N	f	978	23938716-0	ORDÓÑEZ MANCILLA, RICHAR ANTONIO SEGUN	0	2012-08-06	2012-09-05	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.342554		No Especifica
102930	2012-08-09	7	7	\N	\N	f	978	23923347-3	UGALDE GONZÁLEZ, EYELÉN DOMINIQUE	0	2012-08-06	2012-09-05	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.345535		No Especifica
102931	2012-08-09	7	7	\N	\N	f	978	23898510-2	LUENGO FRAILE, MATÍAS FELIPE	0	2012-08-06	2012-09-05	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.348521		No Especifica
102932	2012-08-09	7	7	\N	\N	f	789	22471215-4	BARAHONA BARROS, PABLO ANTONIO	0	2012-08-06	2012-09-05	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.351429		No Especifica
102933	2012-08-08	7	7	\N	\N	f	1006	22192048-1	ALBORNOZ CELEDÓN, LORENA DEL CARMEN	0	2012-08-06	2012-09-05	Estrabismo . {decreto nº 228}	Tratamiento Médico	2012-08-16 00:00:00	2012-08-30 09:40:21.354652		No Especifica
102934	2012-06-13	7	7	\N	\N	f	1072	17994620-3	PAVIE SALINAS, FERNANDO ANDRÉS	0	2012-06-07	2012-09-05	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en personas de 15 años y más	2012-08-29 00:00:00	2012-08-30 09:40:21.35813		Hemodiálisis 15 Años y más
102935	2012-08-14	7	7	\N	\N	f	956	10131458-8	VILLANUEVA RIVERA, ANA MARÍA	0	2012-08-06	2012-09-05	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:21.361685		Severa
102936	2012-08-13	7	7	\N	\N	f	1153	5672341-2	CASTILLO MOREL, OLGA DEL CARMEN	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.364918		No Especifica
102937	2012-08-13	7	7	\N	\N	f	1153	4976245-3	MENA PIZARRO, GABRIELA ERMINDA	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-28 00:00:00	2012-08-30 09:40:21.368127		No Especifica
102938	2012-06-11	7	7	\N	\N	f	927	4566201-2	MELLA VALENZUELA, SONIA BLANDINA DEL C	0	2012-06-07	2012-09-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:21.372112		Bilateral Igual o Inferior a 0,1
102939	2012-08-09	7	7	\N	\N	f	1136	4180892-6	ORELLANA , JUSTINA DEL CARMEN	0	2012-08-06	2012-09-05	Prevención Secundaria IRCT . {decreto n° 1/2010}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.375002		No Especifica
102940	2012-08-13	7	7	\N	\N	f	1153	3707155-2	GONZÁLEZ MEJÍAS, MARÍA INÉS DEL CARME	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.377871		No Especifica
102941	2012-08-10	7	7	\N	\N	f	1153	3322484-2	BUSTAMANTE HENRÍQUEZ, GERALDO	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.380707		No Especifica
102942	2012-08-10	7	7	\N	\N	f	1153	3271684-9	LIBUY ROJAS, OSCAR ARTURO	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.383528		No Especifica
102943	2012-08-13	7	7	\N	\N	f	1153	3251526-6	TRIGO , BERTA DEL ROSARIO	0	2012-08-06	2012-09-05	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.386319		No Especifica
102944	2012-03-23	7	7	\N	\N	f	925	3033423-K	JUAN BAUTISTA ROJAS TORRES	0	2012-03-09	2012-09-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.389229		No Especifica
102945	2012-03-13	7	7	\N	\N	f	927	2605387-0	ERNESTO WILLY RACHOW MIRANDA	0	2012-03-09	2012-09-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:21.392434		Bilateral
102946	2012-08-21	7	7	\N	\N	f	996	19612751-8	BALLESTERO ORELLANA, BERNARDO JONATHAN	0	2012-08-16	2012-09-05	Esquizofrenia . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.395349		No Especifica
102947	2012-08-27	7	7	\N	\N	f	1099	421327-0	FARÍAS CORNEJO, JUSTO SALVADOR SEGUN	0	2012-08-21	2012-09-05	Marcapaso . {decreto nº 228}	Seguimiento	2012-08-27 00:00:00	2012-08-30 09:40:21.398269		No Especifica
102948	2012-08-24	7	7	\N	\N	f	980	23923831-9	OLIVARES AGURTO, SOFÍA IGNACIA	0	2012-08-22	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.401596		No Especifica
102949	2012-08-13	7	7	\N	\N	f	978	23940256-9	AEDO MARTÍNEZ, IGNACIO ANDRÉS	0	2012-08-07	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.404455		No Especifica
102950	2012-08-16	7	7	\N	\N	f	978	23931918-1	SANDOVAL TELLO, JEANPIERRE ALEJANDRO	0	2012-08-07	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:21.407259		No Especifica
102951	2012-08-08	7	7	\N	\N	f	978	23923000-8	PONCE COLIPI, LEANDRO	0	2012-08-07	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.410127		No Especifica
102952	2012-08-13	7	7	\N	\N	f	978	23912897-1	DONOSO SASSO, PASKAL IGNACIA	0	2012-08-07	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.412933		No Especifica
102953	2012-08-10	7	7	\N	\N	f	978	23891157-5	DESIDEL PALMA, CRISTÓBAL IGNACIO	0	2012-08-07	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.415671		No Especifica
102954	2012-08-01	7	7	\N	\N	f	888	19328554-6	AHUMADA AVENDAÑO, BELÉN PAZ	0	2012-07-31	2012-09-06	Cáncer en Menores Linfoma y/o Tumor Sólido {decreto nº 228}	Diagnóstico Linfoma y Tumores Sólidos	2012-08-29 00:00:00	2012-08-30 09:40:21.418869		Linfoma y/o Tumores Sólidos
102955	2012-08-10	7	7	\N	\N	f	821	10960366-K	ASTUDILLO CORTÉS, MARÍA DEL PILAR	0	2012-08-07	2012-09-06	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.422241		Izquierda
102956	2012-06-12	7	7	\N	\N	f	968	8545978-3	ALCAYA RIQUELME, ELIZABETH VIRGINIA	0	2012-06-08	2012-09-06	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.425198		No Especifica
102957	2012-08-13	7	7	\N	\N	f	1153	5881475-K	GALLARDO OSSANDÓN, MARÍA DEL CARMEN	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.428088		No Especifica
102958	2012-08-13	7	7	\N	\N	f	1153	5808763-7	ARANDA ASTORGA, FELICITA DE LAS MERC	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.430943		No Especifica
102959	2012-08-13	7	7	\N	\N	f	1153	5652252-2	GONZÁLEZ ELGUETA, CAMILO ENRIQUE	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.43374		No Especifica
102960	2012-08-13	7	7	\N	\N	f	1153	5515232-2	GODOY URRUTIA, MARTA	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.436539		No Especifica
102961	2012-08-10	7	7	\N	\N	f	1153	5428645-7	MARTÍNEZ JIMÉNEZ, IRENE EDELMIRA	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.439343		No Especifica
102962	2012-08-13	7	7	\N	\N	f	1153	5030507-4	MUÑOZ ALFARO, ANTOLINA ALICIA DE L	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.442657		No Especifica
102963	2012-08-10	7	7	\N	\N	f	1153	4844127-0	PEÑA DÍAZ, JUAN FRANCISCO	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.445491		No Especifica
102964	2012-08-13	7	7	\N	\N	f	1153	4827226-6	RAMOS PARDO, FRANCISCO ARMANDO	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.448381		No Especifica
102965	2012-08-13	7	7	\N	\N	f	1153	4795393-6	GALLEGUILLOS ARMIJO, MARÍA	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.451225		No Especifica
102966	2012-06-11	7	7	\N	\N	f	822	4541231-8	LUCARES ROBLEDO, GRACIELA ARLETTE	0	2012-06-08	2012-09-06	Cáncer de Mama Izquierda {decreto nº 228}	Control Seguimiento Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.454197		Izquierda
102967	2012-08-13	7	7	\N	\N	f	1153	4307523-3	FERRU ALVARADO, SALVADOR RAÚL	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.457032		No Especifica
102968	2012-08-13	7	7	\N	\N	f	1153	4039521-0	DELGADO HUAIQUIMILLA, MARÍA MAGDALENA	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.459916		No Especifica
102969	2012-08-13	7	7	\N	\N	f	1153	3973573-3	VALDÉS ROJAS, FLORENTINO MARÍA	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.462991		No Especifica
102970	2012-08-13	7	7	\N	\N	f	1153	3821866-2	PIZARRO , PEDRO MISAEL	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.465816		No Especifica
102971	2012-08-01	7	7	\N	\N	f	775	3684788-3	DAWSON ESPINOZA, EDUARDO ENRIQUE	0	2012-07-28	2012-09-06	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-01 00:00:00	2012-08-30 09:40:21.468862		Derecha
102972	2012-08-13	7	7	\N	\N	f	1153	3552388-K	SEPÚLVEDA QUINTEROS, SILVIA DE LAS MERCED	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.471695		No Especifica
102973	2012-08-13	7	7	\N	\N	f	1153	3458367-6	FIGUEROA ARÉVALO, CARLOS RENÉ	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.474525		No Especifica
102974	2012-08-13	7	7	\N	\N	f	1153	2832646-7	ARAYA OSSANDÓN, MIGUEL TOMÁS	0	2012-08-07	2012-09-06	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.477321		No Especifica
102975	2012-08-20	7	7	\N	\N	f	956	15751255-2	SAAVEDRA PERALTA, GISELLE LORETO	0	2012-08-07	2012-09-06	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:21.480381		Severa
102976	2012-08-28	7	7	\N	\N	f	1099	6454346-6	PIZARRO SILVA, SERGIO ENRIQUE	0	2012-08-22	2012-09-06	Marcapaso . {decreto nº 228}	Seguimiento	2012-08-29 00:00:00	2012-08-30 09:40:21.483208		No Especifica
102977	2012-08-29	7	7	\N	\N	f	980	23936014-9	ESTAY ESTAY, SEBASTIÁN ALONSO	0	2012-08-22	2012-09-06	Displasia Luxante de Caderas . {decreto n° 1/2010}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.486088		No Especifica
102978	2012-08-17	7	7	\N	\N	f	1153	7531323-3	CÉSPEDES JAMETT, EMA GLADIS	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.489003		No Especifica
102979	2012-08-17	7	7	\N	\N	f	1153	6773274-K	MUÑOZ JORQUERA, CELIA DE LAS MERCEDE	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.492254		No Especifica
102980	2012-08-17	7	7	\N	\N	f	1153	6153244-7	GUAJARDO VERA, TERESA INÉS	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-21 00:00:00	2012-08-30 09:40:21.496349		No Especifica
102981	2012-08-17	7	7	\N	\N	f	1153	5753168-1	TORREALBA ESTAY, ANA LUISA	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.500241		No Especifica
102982	2012-08-17	7	7	\N	\N	f	1153	5257934-1	SEPÚLVEDA MONTES, CARLOS MIGUEL	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.503347		No Especifica
102983	2012-08-17	7	7	\N	\N	f	1153	5230646-9	ÁLVAREZ ÁLVAREZ, EDUARDO AMÉRICO	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.507122		No Especifica
102984	2012-08-17	7	7	\N	\N	f	1153	4843435-5	TAPIA CONTRERAS, RAMIRO ALFONSO	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.510523		No Especifica
102985	2012-08-17	7	7	\N	\N	f	1153	4604214-K	TORO BUSTAMANTE, ADRIANA DE LAS MERCE	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.513782		No Especifica
102986	2012-08-17	7	7	\N	\N	f	1153	4234933-K	JORQUERA JAQUE, ANA DE LAS MERCEDES	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.517459		No Especifica
102987	2012-08-17	7	7	\N	\N	f	1153	2951162-4	FUENTES RONDÓN, JULIO ERNESTO	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.520674		No Especifica
102988	2012-08-17	7	7	\N	\N	f	1153	2881195-0	CABELLO GONZÁLEZ, MARÍA EUDOCIA	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.52351		No Especifica
102989	2012-08-17	7	7	\N	\N	f	1153	2537968-3	POZO GUZMÁN, JOSÉ	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-17 00:00:00	2012-08-30 09:40:21.52638		No Especifica
102990	2012-08-10	7	7	\N	\N	f	978	23936583-3	CORTEZ GALDAMES , VALENTINA	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.529549		No Especifica
102991	2012-08-13	7	7	\N	\N	f	978	23929906-7	CALDERON ORTIZ, KIARA ANTONELLA	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.532728		No Especifica
102992	2012-08-13	7	7	\N	\N	f	978	23929575-4	TABOLARI RAMÍREZ, MONSERRATT ELIZABETH	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.535989		No Especifica
102993	2012-08-13	7	7	\N	\N	f	978	23925715-1	MENA SOTO, ASHLEY ANTONELLA	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.539234		No Especifica
102994	2012-08-10	7	7	\N	\N	f	978	23925157-9	GONZÁLEZ LEIVA, LUCAS AGUSTÍN	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.543128		No Especifica
102995	2012-08-10	7	7	\N	\N	f	978	23920409-0	FISCHER NAVEA, SOFÍA EMILIA	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.546299		No Especifica
102996	2012-08-10	7	7	\N	\N	f	978	23912519-0	MONDACA MONDACA, BENJAMIN IGNACIO	0	2012-08-08	2012-09-07	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.549586		No Especifica
102997	2012-08-10	7	7	\N	\N	f	789	21006666-7	PEÑA HIDALGO, ALISON DARINCA	0	2012-08-08	2012-09-07	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.552966		No Especifica
102998	2012-07-10	7	7	\N	\N	f	954	9969177-8	SALINAS MALDONADO, ORFELINA DEL CARMEN	0	2012-07-09	2012-09-07	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:21.556248		No Especifica
102999	2012-08-10	7	7	\N	\N	f	906	6119095-3	MANZO CISTERNA, NELLY DE LAS MERCEDE	0	2012-08-08	2012-09-07	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.55918		No Especifica
103000	2012-08-06	7	7	\N	\N	f	1086	4763592-6	DERPICH REYES, EMMA ANA	0	2012-08-03	2012-09-07	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.56235		No Especifica
103001	2012-08-10	7	7	\N	\N	f	1153	4638925-5	RECABARREN BRITO, OLGA UBERLINDA	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.565308		No Especifica
103002	2012-08-14	7	7	\N	\N	f	1101	3365035-3	ACUÑA , MARÍA SERGIA	0	2012-08-08	2012-09-07	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.568399		No Especifica
103003	2012-08-10	7	7	\N	\N	f	1153	2968585-1	MUÑOZ RIVEROS, HERNÁN	0	2012-08-08	2012-09-07	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-10 00:00:00	2012-08-30 09:40:21.571365		No Especifica
103004	2012-08-14	7	7	\N	\N	f	1101	2772842-1	PINILLA VIDAL, NORA DEL CARMEN	0	2012-08-08	2012-09-07	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.574215		No Especifica
103005	2012-08-24	7	7	\N	\N	f	1136	4128316-5	ARAYA , MARÍA DEL ROSARIO	0	2012-08-09	2012-09-10	Prevención Secundaria IRCT . {decreto n° 1/2010}	Atención Especialista	2012-08-24 00:00:00	2012-08-30 09:40:21.577254		No Especifica
103006	2012-08-14	7	7	\N	\N	f	978	23941563-6	ROBLES MOLINA, MIA PASCAL	0	2012-08-10	2012-09-10	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:21.580203		No Especifica
103007	2012-08-13	7	7	\N	\N	f	978	23924212-K	TORRES ORTEGA, MARTIN HERNAN	0	2012-08-09	2012-09-10	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.583035		No Especifica
103008	2012-08-13	7	7	\N	\N	f	978	23917941-K	TORRES RODRÍGUEZ, MARÍA JOSÉ	0	2012-08-09	2012-09-10	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.585765		No Especifica
103009	2012-08-10	7	7	\N	\N	f	978	23916683-0	SAAVEDRA GÓMEZ, FLORENCIA ANTONELA	0	2012-08-09	2012-09-10	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:21.588891		No Especifica
103010	2012-08-13	7	7	\N	\N	f	978	23913415-7	ORELLANA BERRÍOS, KAMYLA AHYLÍN	0	2012-08-09	2012-09-10	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:21.591776		No Especifica
103011	2012-08-14	7	7	\N	\N	f	789	23228060-3	MENA GIL, MARTINA IGNACIA	0	2012-08-10	2012-09-10	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-14 00:00:00	2012-08-30 09:40:21.594548		No Especifica
103012	2012-08-13	7	7	\N	\N	f	789	22366773-2	JORQUERA HERNANDEZ, HILLEIN ESPERANZA	0	2012-08-09	2012-09-10	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-13 00:00:00	2012-08-30 09:40:21.597292		No Especifica
103013	2012-08-13	7	7	\N	\N	f	789	20184479-7	MUÑOZ BAEZ, SCARLETT JAVIERA	0	2012-08-09	2012-09-10	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-13 00:00:00	2012-08-30 09:40:21.600111		No Especifica
103014	2012-08-06	7	7	\N	\N	f	799	20083944-7	BURGOS PÉREZ, MELISA TANIA	0	2012-08-01	2012-09-10	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:21.603311		No Especifica
103015	2012-07-06	7	7	\N	\N	f	955	12312625-4	ACEVEDO PÉREZ, JESSICA ALEJANDRA	0	2012-06-11	2012-09-10	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-06 00:00:00	2012-08-30 09:40:21.606216		No Especifica
103016	2012-07-17	7	7	\N	\N	f	954	10541777-2	ASTUDILLO ESPEJO, ERICA ELIZABETH	0	2012-07-12	2012-09-10	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.60914		No Especifica
103017	2012-07-12	7	7	\N	\N	f	954	10419805-8	PÉREZ ROSEVEAR, IRMA DE LAS MERCEDES	0	2012-07-11	2012-09-10	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.612069		No Especifica
103018	2012-08-03	7	7	\N	\N	f	829	10078994-9	TORRES VERGARA, NANCY DEL ROSARIO	0	2012-07-25	2012-09-10	Cáncer de Mama Derecha {decreto nº 228}	Diagnóstico-Etapificación Mama Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:21.615193		Derecha
103019	2012-07-13	7	7	\N	\N	f	954	9748402-3	PUENTES FUENTES, MARCELA DE LOS ANGEL	0	2012-07-11	2012-09-10	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.618061		No Especifica
103020	2012-06-15	7	7	\N	\N	f	968	9386723-8	PONCE DONOSO, LEONARDO EMILIO	0	2012-06-12	2012-09-10	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.621095		No Especifica
103021	2012-07-26	7	7	\N	\N	f	829	8614350-K	SILVA CONTRERAS, ISABEL AGUSTINA	0	2012-07-25	2012-09-10	Cáncer de Mama Derecha {decreto nº 228}	Diagnóstico-Etapificación Mama Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:21.623973		Derecha
103022	2012-08-10	7	7	\N	\N	f	956	8070248-5	DONOSO VÉLIZ, MARÍA EUGENIA	0	2012-08-09	2012-09-10	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:21.627063		Severa
103023	2012-08-16	7	7	\N	\N	f	956	6973061-2	PARRA SOTO, PATRICIA DEL CARMEN	0	2012-08-10	2012-09-10	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:21.630045		Severa
103024	2012-03-14	7	7	\N	\N	f	1043	6452572-7	RICARDO ALFREDO BARRIGA GAETE	0	2012-03-12	2012-09-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:21.633357		Retención Urinaria Aguda Repetida
103025	2012-08-13	7	7	\N	\N	f	821	6424472-8	TOBAR MORALES, NELSON JESÚS	0	2012-08-10	2012-09-10	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:21.636394		Izquierda
103026	2012-06-19	7	7	\N	\N	f	1140	6353646-6	MENDOZA CONTRERAS, MARÍA VICTORIA	0	2012-06-11	2012-09-10	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.639422		No Especifica
103027	2012-08-03	7	7	\N	\N	f	775	5589194-K	TELLO CISTERNAS, ALICIA DEL CARMEN	0	2012-07-30	2012-09-10	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.643464		Derecha
103028	2012-03-13	7	7	\N	\N	f	925	5301880-7	SILVIA RODRÍGUEZ URRUTIA	0	2012-03-12	2012-09-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.646558		No Especifica
103029	2012-08-14	7	7	\N	\N	f	1153	5143311-4	MUJICA LAGOS, MARÍA IRENE DEL CARM	0	2012-08-09	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-14 00:00:00	2012-08-30 09:40:21.649476		No Especifica
103030	2012-08-10	7	7	\N	\N	f	775	5052609-7	AGUILAR LEÓN, ELIANA	0	2012-08-01	2012-09-10	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-10 00:00:00	2012-08-30 09:40:21.652816		Derecha
103031	2012-08-14	7	7	\N	\N	f	1153	4978763-4	MANQUEMILLA MOYA, YOLANDA ERNESTINA	0	2012-08-09	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-14 00:00:00	2012-08-30 09:40:21.65618		No Especifica
103032	2012-08-14	7	7	\N	\N	f	1153	4679566-0	BRAVO ARAYA, JAIME SEGUNDO	0	2012-08-09	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-14 00:00:00	2012-08-30 09:40:21.659184		No Especifica
103033	2012-08-14	7	7	\N	\N	f	1153	4517121-3	DÍAZ DÍAZ, MARÍA ADELAIDA	0	2012-08-09	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-14 00:00:00	2012-08-30 09:40:21.662033		No Especifica
103034	2012-08-01	7	7	\N	\N	f	829	4023730-5	MOYANO VELÁSQUEZ, MARÍA SUSANA	0	2012-07-26	2012-09-10	Cáncer de Mama Izquierda {decreto nº 228}	Diagnóstico-Etapificación Mama Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:21.664889		Izquierda
103035	2012-08-03	7	7	\N	\N	f	775	3859201-7	VALDÉS MENESES, ROBERTO VICENTE	0	2012-07-30	2012-09-10	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-03 00:00:00	2012-08-30 09:40:21.667795		Derecha
103036	2012-03-14	7	7	\N	\N	f	925	3852460-7	ELISA DEL TRÁNSITO VALDÉS SEPÚLVEDA	0	2012-03-12	2012-09-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.670845		No Especifica
103037	2012-03-15	7	7	\N	\N	f	925	3846108-7	RINA ATRIZ AHUMADA TELLO	0	2012-03-14	2012-09-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.673648		No Especifica
103038	2012-06-15	7	7	\N	\N	f	1140	3549599-1	SALVO LARA, ROSA ESTER	0	2012-06-11	2012-09-10	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.676409		No Especifica
103039	2012-03-13	7	7	\N	\N	f	1043	3187237-5	ABSALON EVARISTO ALIAGA ARAVENA	0	2012-03-12	2012-09-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:21.679336		Retención Urinaria Aguda Repetida
103040	2012-03-14	7	7	\N	\N	f	1043	2738121-9	FLAVIO AUGUSTO PÉNDOLA SOLÍS	0	2012-03-12	2012-09-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-24 00:00:00	2012-08-30 09:40:21.682278		Retención Urinaria Aguda Repetida
103041	2012-08-20	7	7	\N	\N	f	1153	3112451-4	VERA BARROILHET, MARÍA ISABEL	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.685154		No Especifica
103042	2012-08-20	7	7	\N	\N	f	1153	3846110-9	VALDIVIA VEGA, GRIMANEZA DEL ROSARI	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.688119		No Especifica
103043	2012-08-20	7	7	\N	\N	f	1153	4206929-9	MALDONADO BRAVO, HERNÁN	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.690981		No Especifica
103044	2012-08-20	7	7	\N	\N	f	1153	4675957-5	PALACIOS BUGUEÑO, MARÍA ENRIQUETA	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.693891		No Especifica
103045	2012-08-20	7	7	\N	\N	f	1153	4976179-1	OLIVARES COFRÉ, ORLANDO DEL CARMEN	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.696703		No Especifica
103046	2012-08-20	7	7	\N	\N	f	1153	5112762-5	BERNAL GRONDONA, JUAN SEGUNDO	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.699527		No Especifica
103047	2012-08-20	7	7	\N	\N	f	1153	5335826-8	MARCHANT ABELLO, ROSA MORELIA	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.702335		No Especifica
103048	2012-08-20	7	7	\N	\N	f	1153	6709111-6	PINO MARTÍNEZ, PATRICIA ANGÉLICA	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.705162		No Especifica
103049	2012-08-20	7	7	\N	\N	f	1153	7068565-5	FAÚNDEZ PAVEZ, MARÍA ANDREA	0	2012-08-10	2012-09-10	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.708011		No Especifica
103050	2012-08-21	7	7	\N	\N	f	821	13588982-2	COMULAY ANTIFILO, ANA MARÍA	0	2012-08-10	2012-09-10	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-27 00:00:00	2012-08-30 09:40:21.71103		Izquierda
103051	2012-08-21	7	7	\N	\N	f	851	4009663-9	IBACACHE LÓPEZ, JULIO ALBERTO	0	2012-08-10	2012-09-10	Cáncer de Testículo en Adultos Caso en Sospecha y Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.714404		No Especifica
103052	2012-08-23	7	7	\N	\N	f	986	5933739-4	ANATIBIA ITURRIETA, JAIME GUILLERMO	0	2012-08-20	2012-09-10	Enfermedad de Parkinson . {decreto n° 1/2010}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:21.717393		No Especifica
103053	2012-08-27	7	7	\N	\N	f	789	23025832-5	JARA MENESES, MONSERRAT ELENA	0	2012-08-09	2012-09-10	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.720581		No Especifica
103054	2012-08-08	7	7	\N	\N	f	799	16231690-7	VICENCIO ALLENDES, ROSA EMILIA	0	2012-08-02	2012-09-11	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.723653		No Especifica
103055	2012-08-06	7	7	\N	\N	f	799	15081381-6	GONZÁLEZ ORTIZ, MARCHORIE LORENA	0	2012-08-02	2012-09-11	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.727186		No Especifica
103056	2012-06-15	7	7	\N	\N	f	1140	10179561-6	OLGUÍN CARREÑO, MARIO ANTONIO	0	2012-06-13	2012-09-11	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:21.730044		No Especifica
103057	2012-03-19	7	7	\N	\N	f	925	5232043-7	MARÍA ESTER SILVA SEGUEL	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.732894		No Especifica
103058	2012-03-23	7	7	\N	\N	f	925	5070660-5	ANA DEL CARMEN PÉREZ FUENZALIDA	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.735676		No Especifica
103059	2012-03-19	7	7	\N	\N	f	925	4741692-2	MARÍA DEL CARMEN ARAYA HERNÁNDEZ	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.738512		No Especifica
103060	2012-03-21	7	7	\N	\N	f	925	4290683-2	GLORIA CONCHA EBELING	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.741421		No Especifica
103061	2012-03-19	7	7	\N	\N	f	925	4204045-2	ISMAELA DEL CARMEN MOLINA ZAMORA	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.744224		No Especifica
103062	2012-03-23	7	7	\N	\N	f	925	3985047-8	MARÍA ANGÉLICA PETRO NÚÑEZ NÚÑEZ	0	2012-03-15	2012-09-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.747055		No Especifica
103063	2012-06-20	7	7	\N	\N	f	927	3959421-8	BARRIENTOS SOTO, LEONOR	0	2012-06-13	2012-09-11	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:21.750747		Bilateral Igual o Inferior a 0,1
103064	2012-08-17	7	7	\N	\N	f	807	12031396-7	TAMAYO BARRIENTOS, MARÍA VIVIANA	0	2012-08-13	2012-09-12	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-17 00:00:00	2012-08-30 09:40:21.754098		Pre-Invasor
103065	2012-06-18	7	7	\N	\N	f	982	23801614-2	FUENTES IBACACHE, CONSUELO ANTONIA	0	2012-06-14	2012-09-12	Disrrafias Espinales Disrrafia Cerrada {decreto nº 228}	Consulta con Neurocirujano Disrafia Cerrada	2012-08-29 00:00:00	2012-08-30 09:40:21.757585		Cerrada
103066	2012-08-16	7	7	\N	\N	f	1006	22544727-6	ANGEL OLMOS, CATALINA ANTONIA	0	2012-08-13	2012-09-12	Estrabismo . {decreto nº 228}	Tratamiento Médico	2012-08-14 00:00:00	2012-08-30 09:40:21.760971		No Especifica
103067	2012-08-16	7	7	\N	\N	f	1006	22448983-8	CASANOVA POBLETE, VALENTINA ANTONIA	0	2012-08-13	2012-09-12	Estrabismo . {decreto nº 228}	Tratamiento Médico	2012-08-14 00:00:00	2012-08-30 09:40:21.76449		No Especifica
103068	2012-08-14	7	7	\N	\N	f	789	22098202-5	GONZÁLEZ RODRÍGUEZ, MATÍAS EDUARDO	0	2012-08-13	2012-09-12	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.767382		No Especifica
103069	2012-08-06	7	7	\N	\N	f	799	17792691-4	ORREGO OLIVARES, STEPHANIE BEATRIZ	0	2012-08-03	2012-09-12	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.770325		No Especifica
103070	2012-08-06	7	7	\N	\N	f	799	17477818-3	MOYA AHUMADA, MARCELA CONSUELO	0	2012-08-03	2012-09-12	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:21.773226		No Especifica
103071	2012-06-15	7	7	\N	\N	f	1140	13938035-5	BANDA PINEDA, HANS BANALLER	0	2012-06-14	2012-09-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.776029		No Especifica
103072	2012-06-15	7	7	\N	\N	f	1140	9369297-7	LÓPEZ LUNA, ERIKA GIOCONDA	0	2012-06-14	2012-09-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.779067		No Especifica
103073	2012-06-20	7	7	\N	\N	f	968	9080998-9	LAGOS CALFUMÁN, SANTA MATILDE	0	2012-06-14	2012-09-12	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.782353		No Especifica
103074	2012-08-16	7	7	\N	\N	f	956	8304871-9	SOTO IBACACHE, LAURA DEL CARMEN	0	2012-08-13	2012-09-12	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:21.78573		Severa
103075	2012-06-18	7	7	\N	\N	f	968	7764122-K	ALBARRÁN PINEDA, LUIS ARMANDO	0	2012-06-14	2012-09-12	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.78886		No Especifica
103076	2012-06-18	7	7	\N	\N	f	927	7409380-9	OLIVARES PELUCCHI, ELBA DEL CARMEN	0	2012-06-14	2012-09-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:21.792802		Derecha Igual o Inferior a 0,1
103077	2012-06-15	7	7	\N	\N	f	822	7247115-6	GAETE NAVARRO, CELIA MARCELA	0	2012-06-14	2012-09-12	Cáncer de Mama Izquierda {decreto nº 228}	Control Seguimiento Mama Izquierda	2012-07-12 00:00:00	2012-08-30 09:40:21.796143		Izquierda
103078	2012-06-15	7	7	\N	\N	f	1140	5525207-6	CUEVAS APABLAZA, HILDA DE MERCEDES	0	2012-06-14	2012-09-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.799257		No Especifica
103079	2012-06-18	7	7	\N	\N	f	927	5230523-3	VARELA ÁLVAREZ, PLÁCIDA ADRIANA	0	2012-06-14	2012-09-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:21.802647		Derecha Igual o Inferior a 0,1
103080	2012-01-18	7	7	\N	\N	f	776	4895295-K	HILDA DEL CARMEN VALENCIA BERNAL	0	2012-01-16	2012-09-12	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.805934		Derecha
103081	2012-04-10	7	7	\N	\N	f	925	4741938-7	ALICIA   DEL   CARMEN ROJAS ROJAS	0	2012-03-16	2012-09-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.80914		No Especifica
103082	2012-03-27	7	7	\N	\N	f	925	4258334-0	BERTA BARAHONA NÚÑEZ	0	2012-03-16	2012-09-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.812005		No Especifica
103083	2012-03-20	7	7	\N	\N	f	925	3301994-7	LAURA GENOVEVA ARAYA	0	2012-03-16	2012-09-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.81479		No Especifica
103084	2012-06-18	7	7	\N	\N	f	1072	3127557-1	MENA DÍAZ, FRESIA DEL CARMEN	0	2012-06-14	2012-09-12	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en personas de 15 años y más	2012-08-13 00:00:00	2012-08-30 09:40:21.817811		Hemodiálisis 15 Años y más
103085	2012-03-19	7	7	\N	\N	f	925	3024289-0	JOSÉ MIGUEL ALARCÓN ARCIEGO	0	2012-03-16	2012-09-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.820733		No Especifica
103086	2012-06-18	7	7	\N	\N	f	927	2822537-7	SOTOMAYOR RIVERA, CELIA MARÍA	0	2012-06-14	2012-09-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:21.823843		Derecha Igual o Inferior a 0,1
103087	2012-06-18	7	7	\N	\N	f	927	2752841-4	CRUZ ASTUDILLO, JOSÉ MIGUEL	0	2012-06-14	2012-09-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:21.826928		Bilateral Igual o Inferior a 0,1
103088	2012-03-19	7	7	\N	\N	f	925	1971850-6	JOSE DOMINGO BARRAZA	0	2012-03-16	2012-09-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:21.82984		No Especifica
103089	2012-08-20	7	7	\N	\N	f	1153	4120667-5	BALCAZAR TAPIA, MIRNA DEL CARMEN	0	2012-08-13	2012-09-12	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.832838		No Especifica
103090	2012-08-20	7	7	\N	\N	f	1153	4932187-2	NÚÑEZ GONZÁLEZ, GABRIELA DEL CARMEN	0	2012-08-13	2012-09-12	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.836094		No Especifica
103091	2012-08-20	7	7	\N	\N	f	1153	4966381-1	VALDIVIESO ABRAHAM, LAUTARO LIENICK	0	2012-08-13	2012-09-12	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.839254		No Especifica
103092	2012-08-21	7	7	\N	\N	f	978	23933187-4	OSORIO CONTRERAS, CARLOS IGNACIO	0	2012-08-13	2012-09-12	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:21.842299		No Especifica
103093	2012-08-21	7	7	\N	\N	f	1136	3941148-2	GUTIÉRREZ , NATIVIDAD DEL CARMEN	0	2012-08-13	2012-09-12	Prevención Secundaria IRCT . {decreto n° 1/2010}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.84566		No Especifica
103094	2012-08-23	7	7	\N	\N	f	978	23927520-6	QUIÑONES FUENTES, SAKYNA ANDREA	0	2012-08-13	2012-09-12	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:21.848684		No Especifica
103095	2012-08-16	7	7	\N	\N	f	978	23946641-9	JARA ERICES, LÍA PAZ	0	2012-08-14	2012-09-13	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:21.851574		No Especifica
103096	2012-08-16	7	7	\N	\N	f	978	23936108-0	CÁCERES TORO, FELIPE BLADIMIR	0	2012-08-14	2012-09-13	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.854718		No Especifica
103097	2012-08-16	7	7	\N	\N	f	978	23932119-4	TAPIA MORALES, VICENTE JESÚS	0	2012-08-14	2012-09-13	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.857803		No Especifica
103098	2012-08-10	7	7	\N	\N	f	1086	17160559-8	CABRERA ARAVENA, HERNÁN ALONSO	0	2012-08-09	2012-09-13	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.860823		No Especifica
103099	2012-07-18	7	7	\N	\N	f	954	12231300-K	LUCERO ACEVEDO, ALEX MICHAEL	0	2012-07-15	2012-09-13	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.863888		No Especifica
103100	2012-08-16	7	7	\N	\N	f	906	10485332-3	CASTRO CÁCERES, MARCELA CATALINA	0	2012-08-14	2012-09-13	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-22 00:00:00	2012-08-30 09:40:21.866799		No Especifica
103101	2012-06-19	7	7	\N	\N	f	1140	8978901-K	CIFUENTES PARRA, MARÍA DEL CARMEN	0	2012-06-15	2012-09-13	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.869724		No Especifica
103102	2012-06-20	7	7	\N	\N	f	968	5349649-0	MORA GARRIDO, PREVISTO RAÚL	0	2012-06-15	2012-09-13	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:21.872644		No Especifica
103103	2012-08-16	7	7	\N	\N	f	1097	4731306-6	FUENTES RIVERA, LUIS ENRIQUE	0	2012-08-14	2012-09-13	Marcapaso . {decreto nº 228}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:21.875572		No Especifica
103104	2012-06-19	7	7	\N	\N	f	1140	4646540-7	QUILAQUEO , FRESIA REBECA	0	2012-06-15	2012-09-13	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.878457		No Especifica
103105	2012-03-21	7	7	\N	\N	f	925	4126409-8	CECILIA TERESA PEREIRA ROJAS	0	2012-03-17	2012-09-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.881371		No Especifica
103106	2012-08-20	7	7	\N	\N	f	1153	5869100-3	AGUILAR GONZÁLEZ, LEONOR DEL ROSARIO	0	2012-08-14	2012-09-13	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:21.884196		No Especifica
103107	2012-08-21	7	7	\N	\N	f	956	12823027-0	BERRÍOS ACUÑA, JESSICA DEL CARMEN	0	2012-08-14	2012-09-13	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-21 00:00:00	2012-08-30 09:40:21.887602		Severa
103108	2012-08-21	7	7	\N	\N	f	978	23910695-1	GUZMAN GONZALEZ, ALMENDRA	0	2012-08-14	2012-09-13	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:21.890407		No Especifica
103109	2012-08-22	7	7	\N	\N	f	978	23943589-0	GODOY CABALLERO, AGUSTINA PASCAL	0	2012-08-14	2012-09-13	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:21.893256		No Especifica
103110	2012-08-22	7	7	\N	\N	f	1153	4292481-4	CABRERA NÚÑEZ, ISABEL	0	2012-08-14	2012-09-13	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-22 00:00:00	2012-08-30 09:40:21.896075		No Especifica
103111	2012-08-22	7	7	\N	\N	f	1153	4775579-4	TAPIA CARVAJAL, MARÍA LUISA	0	2012-08-14	2012-09-13	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:21.899031		No Especifica
103112	2012-08-22	7	7	\N	\N	f	1153	5223881-1	HOWARD KRAEMER, PEDRO ROY	0	2012-08-14	2012-09-13	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-22 00:00:00	2012-08-30 09:40:21.902243		No Especifica
103113	2012-08-22	7	7	\N	\N	f	1101	2761670-4	TELLO TAPIA, NORMA	0	2012-08-14	2012-09-13	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-22 00:00:00	2012-08-30 09:40:21.905508		No Especifica
103114	2012-08-29	7	7	\N	\N	f	800	8399920-9	MESINA FERNÁNDEZ, CLAUDIA ANDREA	0	2012-08-24	2012-09-13	Cáncer Cervicouterino Invasor {decreto nº 228}	Diagnóstico - Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:21.909087		Invasor
103115	2012-08-10	7	7	\N	\N	f	888	23757917-8	VILLAGRA FIGUEROA, FERNANDO AGUSTÍN	0	2012-08-08	2012-09-14	Cáncer en Menores Linfoma y/o Tumor Sólido {decreto nº 228}	Diagnóstico Linfoma y Tumores Sólidos	2012-08-29 00:00:00	2012-08-30 09:40:21.912579		Linfoma y/o Tumores Sólidos
103116	2012-08-14	7	7	\N	\N	f	1086	8817821-1	KLIEM NACKEN, ÚRSULA MARÍA	0	2012-08-10	2012-09-14	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.915991		No Especifica
103117	2012-06-19	7	7	\N	\N	f	1140	6603448-8	TORO CARREÑO, ROSENDO EXEQUIEL	0	2012-06-16	2012-09-14	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.919225		No Especifica
103118	2012-05-22	7	7	\N	\N	f	773	4995470-0	GAETE VELÁSQUEZ, AARON JOSÉ	0	2012-05-17	2012-09-14	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:21.922496		No Especifica
103119	2012-08-29	7	7	\N	\N	f	909	24007495-8	FARIAS FARÍAS, MONSERRAT ELIZABETH	0	2012-08-24	2012-09-14	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-29 00:00:00	2012-08-30 09:40:21.925894		Post - Natal 8 Días y menor de 2 Años
103120	2012-08-24	7	7	\N	\N	f	789	22258139-7	MIRANDA RÍOS, CONSTANZA VALENTINA	0	2012-08-17	2012-09-20	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.928904		No Especifica
103121	2012-08-24	7	7	\N	\N	f	807	16775834-7	MARÍN CARMONA, CATHERINE VICTORIA	0	2012-08-21	2012-09-20	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-24 00:00:00	2012-08-30 09:40:21.932583		Pre-Invasor
103122	2012-08-24	7	7	\N	\N	f	978	23924319-3	TORREJÓN MOLINA, FLORENCIA IGNACIA	0	2012-08-21	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.935389		No Especifica
103123	2012-08-24	7	7	\N	\N	f	1153	1869817-K	MARAMBIO WILLIAMS, MARIO ORLANDO	0	2012-08-21	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-24 00:00:00	2012-08-30 09:40:21.938255		No Especifica
103124	2012-08-17	7	7	\N	\N	f	978	23886407-0	JEREZ NÚÑEZ, JADE KATALINA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:21.941071		No Especifica
103125	2012-08-17	7	7	\N	\N	f	821	3719693-2	BERRÍOS QUEZADA, MARÍA REBECA	0	2012-08-16	2012-09-20	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.944209		Derecha
103126	2012-08-17	7	7	\N	\N	f	821	2666181-1	VILCHES BECERRA, MERCEDES ELENA	0	2012-08-16	2012-09-20	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:21.947053		Derecha
103127	2012-07-31	7	7	\N	\N	f	1053	23988115-7	GUERRA GRANDÓN, IVAN SANTOS	0	2012-06-16	2012-09-20	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Diagnóstico	2012-08-28 00:00:00	2012-08-30 09:40:21.950089		No Especifica
103128	2012-03-22	7	7	\N	\N	f	925	17478129-K	CLAUDIO ANDRÉS GONZÁLEZ GONZÁLEZ	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:21.953018		No Especifica
103129	2012-08-16	7	7	\N	\N	f	799	17274125-8	GUZMÁN MORENO, PAULINA EVELYN	0	2012-08-08	2012-09-20	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.956035		No Especifica
103130	2012-08-08	7	7	\N	\N	f	799	15560728-9	SAAVEDRA FERNÁNDEZ, SOLEDAD ALEJANDRA	0	2012-08-07	2012-09-20	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:21.958907		No Especifica
103131	2012-03-26	7	7	\N	\N	f	925	14422496-5	JOSÉ REINALDO CORTÉS	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:21.961798		No Especifica
103132	2012-08-10	7	7	\N	\N	f	799	13715516-8	MOLINA LEIVA, GLORIA MARGARITA	0	2012-08-09	2012-09-20	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:21.964889		No Especifica
103133	2012-03-26	7	7	\N	\N	f	997	13455626-9	JUAN ANTONIO LÓPEZ SOTO	0	2012-03-22	2012-09-20	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-06-01 00:00:00	2012-08-30 09:40:21.967896		No Especifica
103134	2012-07-06	7	7	\N	\N	f	955	13226347-7	GALLARDO MONTERO, MACCARENA ISABEL	0	2012-06-20	2012-09-20	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-06 00:00:00	2012-08-30 09:40:21.970857		No Especifica
103135	2012-07-24	7	7	\N	\N	f	954	11826628-5	VÁSQUEZ MIRANDA, ANA KARINA	0	2012-07-20	2012-09-20	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:21.973818		No Especifica
103136	2012-06-22	7	7	\N	\N	f	1140	10162150-2	MUÑOZ SIEVEKING, EDUARDO ALBERTO	0	2012-06-20	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.976703		No Especifica
103137	2012-06-22	7	7	\N	\N	f	968	10162150-2	MUÑOZ SIEVEKING, EDUARDO ALBERTO	0	2012-06-20	2012-09-20	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:21.979671		No Especifica
103138	2012-06-21	7	7	\N	\N	f	1140	9224301-K	ALIAGA LEYTON, LORENZA DE LAS MERCE	0	2012-06-20	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:21.982578		No Especifica
103139	2012-03-23	7	7	\N	\N	f	925	8685474-0	ANGELINA MYRIAM PALMA ARAVENA	0	2012-03-21	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.985479		No Especifica
103140	2012-06-26	7	7	\N	\N	f	1072	8176317-8	CORTEZ CORTEZ, HÉCTOR HUGO	0	2012-06-21	2012-09-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-29 00:00:00	2012-08-30 09:40:21.988657		Hemodiálisis 15 Años y más
103141	2012-03-22	7	7	\N	\N	f	925	8150378-8	RENZO JOSÉ NICOLINI SOLARI	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.991604		No Especifica
103142	2012-06-19	7	7	\N	\N	f	1140	7719468-1	PASTÉN MIRANDA, EUGENIO DEL CARMEN	0	2012-06-18	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:21.994357		No Especifica
103143	2012-03-20	7	7	\N	\N	f	925	7508350-5	ANA ROSA FUENTES GAJARDO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:21.99717		No Especifica
103144	2012-03-23	7	7	\N	\N	f	925	7137692-3	PERLA POLA OSORIO MONTENEGRO	0	2012-03-22	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.000021		No Especifica
103145	2012-03-21	7	7	\N	\N	f	925	6981951-6	JUANA SILVIA HERNÁNDEZ MARILLANCA	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.002851		No Especifica
103146	2012-03-22	7	7	\N	\N	f	925	6790399-4	JUAN EDUARDO MONTENEGRO RAMOS	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.005698		No Especifica
103147	2012-03-21	7	7	\N	\N	f	925	6732305-K	ADA LEONISA URBINA SILVA	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.009015		No Especifica
103148	2012-03-21	7	7	\N	\N	f	925	6719881-6	MARÍA USTOLIA MENA TORRES	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.012305		No Especifica
103149	2012-03-28	7	7	\N	\N	f	1043	6456752-7	PEDRO ANTONIO ALVAREZ PEREZ	0	2012-03-23	2012-09-20	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:22.01528		Retención Urinaria Aguda Repetida
103150	2012-08-07	7	7	\N	\N	f	988	6361260-K	BERRÍOS AVENDAÑO, JUAN MIGUEL	0	2012-08-03	2012-09-20	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.018253		No Especifica
103151	2012-06-22	7	7	\N	\N	f	1140	6155772-5	FIGUEROA ROMERO, ZACARÍAS ALAMIRO	0	2012-06-20	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.021097		No Especifica
103152	2012-06-21	7	7	\N	\N	f	822	5970909-7	PAREDES EYZAGUIRRE, BERTA DEL CARMEN	0	2012-06-20	2012-09-20	Cáncer de Mama Derecha {decreto nº 228}	Control Seguimiento Mama Derecha	2012-07-12 00:00:00	2012-08-30 09:40:22.02403		Derecha
103153	2012-08-16	7	7	\N	\N	f	775	5913576-7	FIGUEROA CANELEO, MARIA MARINA	0	2012-08-08	2012-09-20	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-27 00:00:00	2012-08-30 09:40:22.027034		Derecha
103154	2012-03-20	7	7	\N	\N	f	925	5464096-K	FELICITA DE LAS MERC VERA VERA	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.02994		No Especifica
103155	2012-06-25	7	7	\N	\N	f	927	5425703-1	ARÉVALO CABALLERO, ZUNILDA DEL CARMEN	0	2012-06-21	2012-09-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:22.033504		Bilateral Igual o Inferior a 0,1
103156	2012-03-23	7	7	\N	\N	f	925	5423882-7	ANA ROSA TORRES HERRERA	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.036388		No Especifica
103157	2012-05-24	7	7	\N	\N	f	773	5298225-1	PÉREZ VARGAS, AÍDA LIDIA	0	2012-05-22	2012-09-20	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:22.039299		No Especifica
103158	2011-11-30	7	7	\N	\N	f	1067	5284590-4	ROSA ELENA UGALDE MEZA	0	2011-11-21	2012-09-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-23 00:00:00	2012-08-30 09:40:22.0423		Estudio Pre-Trasplante
103159	2012-06-21	7	7	\N	\N	f	1140	5179409-5	ARÉVALO DAZA, MARÍA INÉS	0	2012-06-18	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.045175		No Especifica
103160	2012-08-10	7	7	\N	\N	f	775	5105039-8	GÓMEZ SILVA, DORA DEL CARMEN	0	2012-08-06	2012-09-20	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-10 00:00:00	2012-08-30 09:40:22.04798		Derecha
103161	2012-06-26	7	7	\N	\N	f	1072	4977083-9	ORTIZ MAURO, ELISA SONIA	0	2012-06-21	2012-09-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-06-27 00:00:00	2012-08-30 09:40:22.05089		Hemodiálisis 15 Años y más
103162	2012-03-23	7	7	\N	\N	f	1043	4963543-5	SERGIO GUILLERMO CRUZ HERRERA	0	2012-03-21	2012-09-20	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-10 00:00:00	2012-08-30 09:40:22.053786		Retención Urinaria Aguda Repetida
103163	2012-06-26	7	7	\N	\N	f	1072	4916201-4	SÁNCHEZ FERNÁNDEZ, HUGO DEL CARMEN	0	2012-06-21	2012-09-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-06-26 00:00:00	2012-08-30 09:40:22.056655		Hemodiálisis 15 Años y más
103164	2012-03-23	7	7	\N	\N	f	1043	4801360-0	JUAN RAMÓN FIERRO HIDALGO	0	2012-03-21	2012-09-20	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-10 00:00:00	2012-08-30 09:40:22.059612		Retención Urinaria Aguda Repetida
103165	2012-03-22	7	7	\N	\N	f	925	4774282-K	MARINA MAGDALENA CHÁVEZ VALDEBENITO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.062508		No Especifica
103166	2012-03-23	7	7	\N	\N	f	925	4702294-0	ANA MARÍA MELA ÁLVAREZ	0	2012-03-22	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.065393		No Especifica
103167	2012-06-21	7	7	\N	\N	f	1140	4405664-K	VALENCIA CONTRERAS, EMPERATRIZ DEL CARME	0	2012-06-19	2012-09-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.068203		No Especifica
103168	2012-07-03	7	7	\N	\N	f	1072	4392716-7	TAPIA TAPIA, ROSAMEL DEL CARMEN	0	2012-06-21	2012-09-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-17 00:00:00	2012-08-30 09:40:22.070989		Hemodiálisis 15 Años y más
103169	2012-03-22	7	7	\N	\N	f	925	4384873-9	SARA ELISABEL ESPINOZA ROMERO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.073872		No Especifica
103170	2012-03-21	7	7	\N	\N	f	925	4252151-5	MARÍA TERESA ROCHA CUEVAS	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.077214		No Especifica
103171	2012-03-23	7	7	\N	\N	f	925	4171794-7	REGINA SILVA GAJARDO	0	2012-03-22	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.080099		No Especifica
103172	2012-03-21	7	7	\N	\N	f	925	4146537-9	EVA GUILLERMINA CARRASCO GONZÁLEZ	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.082914		No Especifica
103173	2012-03-23	7	7	\N	\N	f	925	4127769-6	CARLOS OLIVARES LARENAS	0	2012-03-21	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.08571		No Especifica
103174	2012-03-21	7	7	\N	\N	f	925	4073386-8	ROSA ELENA TRIVIÑO TENORIO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.08887		No Especifica
103175	2012-03-21	7	7	\N	\N	f	925	3948562-1	DELIA DEL CARMEN AEDO MELLADO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.091652		No Especifica
103176	2012-05-30	7	7	\N	\N	f	773	3923000-3	FONSECA SANHUEZA, RAQUEL DEL CARMEN	0	2012-05-23	2012-09-20	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:22.094554		No Especifica
103177	2012-08-06	7	7	\N	\N	f	1052	3763901-K	ACOSTA ROLDÁN, SILVIA DEL CARMEN	0	2012-08-01	2012-09-20	Hipoacusia Bilateral Adulto Uso de Audífono Requerido . {decreto nº 44}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.097595		No Especifica
103178	2012-06-25	7	7	\N	\N	f	927	3680938-8	PINILLA , JORGE ENRIQUE	0	2012-06-18	2012-09-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:22.100867		Izquierda Igual o Inferior a 0,1
103179	2012-06-25	7	7	\N	\N	f	927	3679594-8	TAPIA ALCÁNTARA, PEDRO	0	2012-06-18	2012-09-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:22.104075		Izquierda Igual o Inferior a 0,1
103180	2012-03-23	7	7	\N	\N	f	1043	3676454-6	JUAN ENRIQUE PUYOL MEDINA	0	2012-03-21	2012-09-20	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:22.106941		Retención Urinaria Aguda Repetida
103181	2012-06-26	7	7	\N	\N	f	927	3570224-5	ASTORGA PALMA, RAÚL ENRIQUE	0	2012-06-21	2012-09-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:22.110186		Bilateral Igual o Inferior a 0,1
103182	2012-03-21	7	7	\N	\N	f	925	3542942-5	ENRIQUE ALBERTO GALLARDO	0	2012-03-19	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:22.113063		No Especifica
103183	2012-06-25	7	7	\N	\N	f	927	3482805-9	PEÑA , NORMA NELIA	0	2012-06-21	2012-09-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-01 00:00:00	2012-08-30 09:40:22.116698		Izquierda Igual o Inferior a 0,1
103184	2012-03-21	7	7	\N	\N	f	925	3451589-1	JOSÉ DOMINGO BERNAL GONZÁLEZ	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:22.119576		No Especifica
103185	2012-03-21	7	7	\N	\N	f	925	3398746-3	ADRIANA MARÍA VILLEGAS MÉNDEZ	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.122416		No Especifica
103186	2012-08-06	7	7	\N	\N	f	988	3377927-5	FUENZALIDA MUÑOZ, SILVIA BERTA	0	2012-08-02	2012-09-20	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.125339		No Especifica
103187	2012-06-25	7	7	\N	\N	f	927	3375003-K	OLIVARES , HERNANDO DEL CARMEN	0	2012-06-21	2012-09-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:22.128582		Bilateral Igual o Inferior a 0,1
103188	2012-03-27	7	7	\N	\N	f	925	3089629-7	GRACIELA TRONCHE LÓPEZ	0	2012-03-23	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.131879		No Especifica
103189	2012-03-26	7	7	\N	\N	f	925	2628225-K	LUCILA DEL ROSARIO OSSANDÓN ARAYA	0	2012-03-22	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.13474		No Especifica
103190	2012-08-14	7	7	\N	\N	f	775	2469265-5	CISTERNAS PIZARRO, FLORENCIO ANTONIO	0	2012-08-07	2012-09-20	Artrosis de Caderas Izquierda {decreto nº 228}	Control Seguimiento Traumatólogo Izquierda	2012-08-27 00:00:00	2012-08-30 09:40:22.13771		Izquierda
103191	2012-06-25	7	7	\N	\N	f	927	2442771-4	ESPINOSA PALMA, MARÍA ISABEL	0	2012-06-19	2012-09-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:22.140886		Bilateral Igual o Inferior a 0,1
103192	2012-06-25	7	7	\N	\N	f	927	2370805-1	ASTUDILLO OLIVARES, LUIS ALBERTO	0	2012-06-21	2012-09-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:22.144072		Bilateral Igual o Inferior a 0,1
103193	2012-06-25	7	7	\N	\N	f	927	2306482-0	CONTRERAS VERGARA, MARÍA AMELIA	0	2012-06-19	2012-09-20	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:22.14723		Derecha Igual o Inferior a 0,1
103194	2012-03-21	7	7	\N	\N	f	925	2249949-1	MANUEL ANTONIO GAETE PACHECO	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.150132		No Especifica
103195	2012-06-25	7	7	\N	\N	f	927	2056836-4	MATURANA TAPIA, CELINDA DEL CARMEN	0	2012-06-19	2012-09-20	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:22.153752		Derecha Igual o Inferior a 0,1
103196	2012-03-21	7	7	\N	\N	f	925	1997368-9	MARÍA ISABEL GAETE PACHECO	0	2012-03-20	2012-09-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.156656		No Especifica
103197	2012-06-25	7	7	\N	\N	f	927	1926176-K	MARTÍNEZ , OLGA	0	2012-06-19	2012-09-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:22.159786		Izquierda Igual o Inferior a 0,1
103198	2012-08-20	7	7	\N	\N	f	956	16121928-2	MORALES ROJAS, DIANA VALENTINA	0	2012-08-16	2012-09-20	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-22 00:00:00	2012-08-30 09:40:22.162853		Severa
103199	2012-08-20	7	7	\N	\N	f	978	23839258-6	LEÓN GALLEGUILLOS, MARTÍN ALEJANDRO	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.165746		No Especifica
103200	2012-08-20	7	7	\N	\N	f	978	23897119-5	ROMAN ARAYA , FRANCISCA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.16852		No Especifica
103201	2012-08-20	7	7	\N	\N	f	978	23901949-8	VILLALOBOS ZÁRATE, MAITE ALEXANDRA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:22.171353		No Especifica
103202	2012-08-20	7	7	\N	\N	f	978	23919449-4	OLIVARES SALINAS, ALEXIS ELÍAS	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.174174		No Especifica
103203	2012-08-20	7	7	\N	\N	f	978	23933971-9	TORO ALLENDE, MONSERRATT ANTONELLA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.176955		No Especifica
103204	2012-08-20	7	7	\N	\N	f	978	23935164-6	GONZÁLEZ FERRADA, FERNANDA BELÉN	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.179783		No Especifica
103205	2012-08-20	7	7	\N	\N	f	978	23937327-5	BARRERA CÁCERES, AYLIN SUSANA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.182533		No Especifica
103206	2012-08-20	7	7	\N	\N	f	978	23939174-5	BARRAZA PALACIOS, AMANDA ANTONIA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.185308		No Especifica
103207	2012-08-20	7	7	\N	\N	f	1153	4995972-9	IBACACHE IBACACHE, HERNÁN DE JESÚS	0	2012-08-16	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:22.188373		No Especifica
103208	2012-08-20	7	7	\N	\N	f	1153	3512174-9	LÓPEZ FERNÁNDEZ, JORGE ROBERTO	0	2012-08-16	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-20 00:00:00	2012-08-30 09:40:22.191268		No Especifica
103209	2012-08-21	7	7	\N	\N	f	821	8818048-8	LARENAS FERNÁNDEZ, ELENA DE LAS MERCEDE	0	2012-08-17	2012-09-20	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:22.194276		Derecha
103210	2012-08-21	7	7	\N	\N	f	978	23884263-8	GACITUA SAAVEDRA, ARIANNA	0	2012-08-17	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:22.197075		No Especifica
103211	2012-08-21	7	7	\N	\N	f	978	23928446-9	ANACONA BERGER, ISAÍAS ALEJANDRO	0	2012-08-20	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.200017		No Especifica
103212	2012-08-21	7	7	\N	\N	f	978	23931057-5	ÁLVAREZ CORNEJO, RAFAELA BELÉN	0	2012-08-17	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.202822		No Especifica
103213	2012-08-21	7	7	\N	\N	f	978	23937497-2	LIZANA CABEZAS, AMALIA JAVIERA	0	2012-08-16	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:22.20559		No Especifica
103214	2012-08-21	7	7	\N	\N	f	1097	5873228-1	SOTO LACROIX, JUAN ELEODORO	0	2012-08-16	2012-09-20	Marcapaso . {decreto nº 228}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:22.208568		No Especifica
103215	2012-08-22	7	7	\N	\N	f	905	3020900-1	MENDOZA VÁSQUEZ, RENATO ROLANDO	0	2012-08-16	2012-09-20	Cáncer Gástrico . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:22.211453		No Especifica
103216	2012-08-22	7	7	\N	\N	f	905	5414309-5	ARAYA RAMÍREZ, LUIS ANTONIO	0	2012-08-16	2012-09-20	Cáncer Gástrico . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:22.214559		No Especifica
103217	2012-08-22	7	7	\N	\N	f	906	6399750-1	OLIVA GUTIÉRREZ, MIGUEL ANGEL	0	2012-08-21	2012-09-20	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.217388		No Especifica
103218	2012-08-22	7	7	\N	\N	f	978	23925609-0	HUERTA PARADA, JOSÉ ANDRÉS	0	2012-08-17	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.220268		No Especifica
103219	2012-08-22	7	7	\N	\N	f	978	23938492-7	NOVOA RODRÍGUEZ, MARTÍN IGNACIO	0	2012-08-21	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.223042		No Especifica
103220	2012-08-22	7	7	\N	\N	f	978	23947276-1	JERIA ALEGRÍA, FRANCIA BELÉN	0	2012-08-21	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.225792		No Especifica
103221	2012-08-22	7	7	\N	\N	f	1101	2884267-8	PRADO CASAS, ISABEL	0	2012-08-17	2012-09-20	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-22 00:00:00	2012-08-30 09:40:22.228796		No Especifica
103222	2012-08-22	7	7	\N	\N	f	1101	3928602-5	PIZARRO PONCE, MARÍA ELIANA	0	2012-08-17	2012-09-20	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-22 00:00:00	2012-08-30 09:40:22.232124		No Especifica
103223	2012-08-22	7	7	\N	\N	f	1101	5007980-5	ROMERO NÚÑEZ, JORGE HERNÁN	0	2012-08-17	2012-09-20	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-22 00:00:00	2012-08-30 09:40:22.234995		No Especifica
103224	2012-08-22	7	7	\N	\N	f	1101	6658983-8	PIZARRO BENAVIDES, JUANA ROSA	0	2012-08-17	2012-09-20	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-22 00:00:00	2012-08-30 09:40:22.238042		No Especifica
103225	2012-08-23	7	7	\N	\N	f	789	22764783-3	LUNA LUNA, CRISTOPHER IGNACIO	0	2012-08-20	2012-09-20	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-23 00:00:00	2012-08-30 09:40:22.240955		No Especifica
103226	2012-08-23	7	7	\N	\N	f	978	23939310-1	CALDERÓN NÚÑEZ, JAVIERA IGNACIA	0	2012-08-21	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:22.243735		No Especifica
103227	2012-08-23	7	7	\N	\N	f	978	23946661-3	VILLEGAS GAMBOA, LEAH ANTONIA	0	2012-08-20	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.246483		No Especifica
103228	2012-08-23	7	7	\N	\N	f	1153	1497397-4	BAEZA NÚÑEZ, EUGENIA	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.249357		No Especifica
103229	2012-08-23	7	7	\N	\N	f	1153	2411830-4	ESTAY , JOSE	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.25269		No Especifica
103230	2012-08-23	7	7	\N	\N	f	1153	3040368-1	URBINA GONZÁLEZ, LUIS MARIO SEGUNDO	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.255572		No Especifica
103231	2012-08-23	7	7	\N	\N	f	1153	3231395-7	HERNÁNDEZ OLMOS, PEDRO JUAN	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.258488		No Especifica
103232	2012-08-23	7	7	\N	\N	f	1153	3258940-5	BASAURE MORALES, LUIS EDUARDO	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.261305		No Especifica
103233	2012-08-23	7	7	\N	\N	f	1153	3260038-7	BÓRQUEZ OLIVARES, ARSILA	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-24 00:00:00	2012-08-30 09:40:22.264111		No Especifica
103234	2012-08-23	7	7	\N	\N	f	1153	3573730-8	VIDAL DÍAZ, NORMA RUTH	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.266925		No Especifica
103235	2012-08-23	7	7	\N	\N	f	1153	3589683-K	CARRASCO ROJAS, NORMA DEL CARMEN	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.269803		No Especifica
103236	2012-08-23	7	7	\N	\N	f	1153	3843099-8	PERALTA NÚÑEZ, ROLANDO DEL CARMEN	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.272657		No Especifica
103237	2012-08-23	7	7	\N	\N	f	1153	4294055-0	VALLEJOS VALLEJOS, ALICIA HAYDÉE	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.275446		No Especifica
103238	2012-08-23	7	7	\N	\N	f	1153	4407281-5	OLIVARES BUSTOS, MARÍA ELENA	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.278267		No Especifica
103239	2012-08-23	7	7	\N	\N	f	1153	4471813-8	BASCUÑÁN CERDA, IRENE	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.281154		No Especifica
103240	2012-08-23	7	7	\N	\N	f	1153	4564070-1	UBEDA FERNÁNDEZ, ELCIRA DE LAS MERCED	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.284822		No Especifica
103241	2012-08-23	7	7	\N	\N	f	1153	4589408-8	HUERTA HERRERA, MARÍA INÉS	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.287865		No Especifica
103242	2012-08-23	7	7	\N	\N	f	1153	4726218-6	AGUILERA MOYANO, MARGARITA DE LAS MER	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-24 00:00:00	2012-08-30 09:40:22.290727		No Especifica
103243	2012-08-23	7	7	\N	\N	f	1153	4863502-4	RUIZ-TAGLE CARMONA, OSVALDO LEONELO	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.293553		No Especifica
103244	2012-08-23	7	7	\N	\N	f	1153	5022690-5	DURÁN CORTEZ, REGINA DEL CARMEN	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.296342		No Especifica
103245	2012-08-23	7	7	\N	\N	f	1153	5166548-1	BASCUÑÁN QUILAPE, ISOLINA DEL CARMEN	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.299246		No Especifica
103246	2012-08-23	7	7	\N	\N	f	1153	5238233-5	CÓRDOVA MORA, NAZLY DEL CARMEN	0	2012-08-20	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.30209		No Especifica
103247	2012-08-23	7	7	\N	\N	f	1153	5292571-1	CORTÉS CASTILLO, MIRTA GLADY	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.304888		No Especifica
103248	2012-08-23	7	7	\N	\N	f	1153	5382032-8	COFRÉ COFRÉ, NANCY DE MERCEDES	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-24 00:00:00	2012-08-30 09:40:22.30775		No Especifica
103249	2012-08-23	7	7	\N	\N	f	1153	5506258-7	GONZÁLEZ AGUIRRE, RODRIGO ORLANDO	0	2012-08-17	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.310592		No Especifica
103250	2012-08-23	7	7	\N	\N	f	1153	5566375-0	CHINCHÓN GÁLVEZ, ALEJANDRO NEMESIO	0	2012-08-16	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-23 00:00:00	2012-08-30 09:40:22.31341		No Especifica
103251	2012-08-27	7	7	\N	\N	f	799	14455907-k	CATALÁN SALINAS, PASCALE SOLEDAD	0	2012-08-07	2012-09-20	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:22.316529		No Especifica
103252	2012-08-28	7	7	\N	\N	f	789	21658152-0	ROMERO ROMERO, MATIAS NICOLAS	0	2012-08-20	2012-09-20	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.31949		No Especifica
103253	2012-08-28	7	7	\N	\N	f	793	16775249-7	HERNÁNDEZ MALEBRÁN, INGRID LORENA	0	2012-08-27	2012-09-20	Asma Bronquial 15 Años y Más . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.322522		No Especifica
103254	2012-08-28	7	7	\N	\N	f	826	5053745-5	GUAJARDO SOTO, FRESIA DEL CARMEN	0	2012-08-17	2012-09-20	Cáncer de Mama Izquierda {decreto nº 228}	Tratamiento Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:22.32612		Izquierda
103255	2012-08-28	7	7	\N	\N	f	978	23922632-9	VALENTINO GALLARDO, LUCIANO MATÍAS	0	2012-08-17	2012-09-20	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.329008		No Especifica
103256	2012-08-29	7	7	\N	\N	f	996	9423294-5	FERNÁNDEZ LESCURE, CLAUDIO MARTÍN	0	2012-08-28	2012-09-20	Esquizofrenia . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.33209		No Especifica
103257	2012-08-29	7	7	\N	\N	f	1153	1930645-3	GALDAMES CONCHA, RAÚL ERNESTO	0	2012-08-21	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.33495		No Especifica
103258	2012-08-29	7	7	\N	\N	f	1153	3214053-k	JIMÉNEZ FUENTES, JUANA ROSA DEL CARME	0	2012-08-21	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.338077		No Especifica
103259	2012-08-29	7	7	\N	\N	f	1153	5729280-6	FRE CALDERÓN, SONIA DEL CARMEN	0	2012-08-21	2012-09-20	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.34095		No Especifica
103260	2012-08-24	7	7	\N	\N	f	906	3541565-3	PINCHEIRA OYARZÚN, ALICIA MIREYA	0	2012-08-22	2012-09-21	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.34386		No Especifica
103261	2012-08-24	7	7	\N	\N	f	851	4617064-4	AGUILAR VALDÉS, JAVIER JOSÉ	0	2012-08-22	2012-09-21	Cáncer de Testículo en Adultos Caso en Sospecha y Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.346812		No Especifica
103262	2012-08-24	7	7	\N	\N	f	1006	22234528-6	CATALÁN CARRASCO, FRANCO ANDRÉ	0	2012-08-22	2012-09-21	Estrabismo . {decreto nº 228}	Tratamiento Médico	2012-08-27 00:00:00	2012-08-30 09:40:22.349959		No Especifica
103263	2012-08-14	7	7	\N	\N	f	988	4313391-8	GONZÁLEZ VARGAS, ROLANDO	0	2012-08-07	2012-09-21	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.352932		No Especifica
103264	2012-08-23	7	7	\N	\N	f	821	6944466-0	DELGADO MORAGA, MARÍA ANGÉLICA	0	2012-08-22	2012-09-21	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-24 00:00:00	2012-08-30 09:40:22.355999		Derecha
103265	2012-08-23	7	7	\N	\N	f	821	5983543-2	SILVA JELDRES, CARMEN ROSA	0	2012-08-22	2012-09-21	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-23 00:00:00	2012-08-30 09:40:22.359011		Izquierda
103266	2012-08-23	7	7	\N	\N	f	906	6562517-2	FERNÁNDEZ URBINA, TERESA DE JESÚS	0	2012-08-22	2012-09-21	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-23 00:00:00	2012-08-30 09:40:22.361865		No Especifica
103267	2012-08-28	7	7	\N	\N	f	821	13266253-3	PULGAR PÉREZ, FABIOLA DEL CARMEN	0	2012-08-22	2012-09-21	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:22.364785		Izquierda
103268	2012-08-28	7	7	\N	\N	f	1101	3166859-k	TRONCOSO RIVERA, EDELMIRA MERCEDES	0	2012-08-22	2012-09-21	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.367816		No Especifica
103269	2012-08-28	7	7	\N	\N	f	1101	5957171-0	GUERRA GUAJARDO, SUSANA NANCY	0	2012-08-22	2012-09-21	Marcapaso . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.371223		No Especifica
103270	2012-08-28	7	7	\N	\N	f	1153	3172331-0	VALDÉS PARRA, ELENA	0	2012-08-22	2012-09-21	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.37404		No Especifica
103271	2012-08-28	7	7	\N	\N	f	1153	6071965-9	ARAYA CONCHA, GILDA ODALIA	0	2012-08-22	2012-09-21	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.37687		No Especifica
103272	2012-08-24	7	7	\N	\N	f	775	4388242-2	MARTÍNEZ CONCHA, MAGDALENA DEL CARMEN	0	2012-08-14	2012-09-24	Artrosis de Caderas Izquierda {decreto nº 228}	Control Seguimiento Traumatólogo Izquierda	2012-08-27 00:00:00	2012-08-30 09:40:22.380065		Izquierda
103273	2012-08-24	7	7	\N	\N	f	821	10982586-7	GAJARDO BRICEÑO, KATHERINE CHARLOTTE	0	2012-08-23	2012-09-24	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-24 00:00:00	2012-08-30 09:40:22.382993		Izquierda
103274	2012-08-24	7	7	\N	\N	f	821	6128185-1	YACONI PIEGER, LUIS RICARDO	0	2012-08-23	2012-09-24	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-24 00:00:00	2012-08-30 09:40:22.385955		Izquierda
103275	2012-08-24	7	7	\N	\N	f	956	5226256-9	GONZÁLEZ JARA, HAYDÉE	0	2012-08-23	2012-09-24	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:22.38908		Severa
103276	2011-12-20	7	7	\N	\N	f	1067	18037196-6	JOCELYN CATALINA VALENCIA VALLEJOS	0	2011-11-24	2012-09-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:22.39219		Estudio Pre-Trasplante
103277	2012-07-31	7	7	\N	\N	f	954	14453467-0	ROCCO MARTÍNEZ, ERIKA ROSA	0	2012-07-25	2012-09-24	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:22.39521		No Especifica
103278	2012-06-26	7	7	\N	\N	f	968	13194639-2	PONCE APABLAZA, ROBERT MACK	0	2012-06-25	2012-09-24	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:22.398285		No Especifica
103279	2012-06-28	7	7	\N	\N	f	968	12155426-7	DELGADO SECO, EVELIN CAROLINA	0	2012-06-26	2012-09-24	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:22.401178		No Especifica
103280	2012-08-14	7	7	\N	\N	f	799	12056321-1	NAVARRETE GONZÁLEZ, RAQUEL	0	2012-08-13	2012-09-24	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:22.404171		No Especifica
103281	2012-07-26	7	7	\N	\N	f	954	9316126-2	CÁRDENAS ARAYA, ALINA DEL CARMEN	0	2012-07-24	2012-09-24	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.407539		No Especifica
103282	2012-07-30	7	7	\N	\N	f	1157	8246150-7	CORTÉS VÉLIZ, MAURICIO ROMUALD	0	2012-07-26	2012-09-24	Hepatitis C . {decreto n° 1/2010}	Evaluación Pre-Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:22.410612		No Especifica
103283	2012-06-28	7	7	\N	\N	f	1140	8191203-3	SILVA GARCÍA, MARÍA ROSA	0	2012-06-25	2012-09-24	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:22.413545		No Especifica
103284	2011-11-30	7	7	\N	\N	f	1067	7999874-5	SERGIO ENRIQUE CÁCERES MENDIETT	0	2011-11-23	2012-09-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:22.416358		Estudio Pre-Trasplante
103285	2012-03-29	7	7	\N	\N	f	925	7801871-2	PATRICIA JIMENA VEGA OJEDA	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.419346		No Especifica
103286	2012-07-30	7	7	\N	\N	f	1072	7541598-2	LÓPEZ ACOSTA, ROSA AMELIA	0	2012-06-25	2012-09-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-13 00:00:00	2012-08-30 09:40:22.422338		Hemodiálisis 15 Años y más
103287	2012-05-28	7	7	\N	\N	f	773	7473467-7	PÉREZ VERA, ANA PATRICIA	0	2012-05-25	2012-09-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:22.425337		No Especifica
103288	2012-06-28	7	7	\N	\N	f	968	7355817-4	MALLEA CISTERNAS, CARMEN CECILIA	0	2012-06-25	2012-09-24	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.428253		No Especifica
103289	2012-07-26	7	7	\N	\N	f	1141	6956602-2	NAVARRO PACHECO, JAIME EDUARDO	0	2012-07-25	2012-09-24	Retinopatía Diabética . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.431371		No Especifica
103290	2012-03-29	7	7	\N	\N	f	925	6815179-1	LAURA DEL ROSARIO ARAYA HODGGER	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.434289		No Especifica
103291	2012-07-06	7	7	\N	\N	f	927	6793024-K	MUÑOZ ANABALÓN, MARÍA ELIANA	0	2012-06-25	2012-09-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-07 00:00:00	2012-08-30 09:40:22.437877		Izquierda Igual o Inferior a 0,1
103292	2012-03-30	7	7	\N	\N	f	925	6035221-6	RAQUEL EUGENIA UBEDA FERNÁNDEZ	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.440931		No Especifica
103293	2012-06-28	7	7	\N	\N	f	1072	5952140-3	NAHUELPI SEGOVIA, ENRIQUE	0	2012-06-26	2012-09-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-07-20 00:00:00	2012-08-30 09:40:22.443751		Hemodiálisis 15 Años y más
103294	2012-08-14	7	7	\N	\N	f	799	5765418-K	MANCILLA RIVERA, NILDA HERMINDA	0	2012-08-13	2012-09-24	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.446829		No Especifica
103295	2012-03-29	7	7	\N	\N	f	925	5569013-8	LUIS CARLOS ENRIQUE REINOSO VILLALOBOS	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.449791		No Especifica
103296	2012-03-29	7	7	\N	\N	f	925	4926684-7	AMELIA ROSA GUERRERO VERA	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.452623		No Especifica
103297	2012-03-28	7	7	\N	\N	f	1043	4722856-5	MANUEL ERNESTO ZÚÑIGA ZAPATA	0	2012-03-26	2012-09-24	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:22.455565		Retención Urinaria Aguda Repetida
103298	2012-06-26	7	7	\N	\N	f	1072	4379781-6	BERNAL ARANCIBIA, VÍCTOR ATILIO	0	2012-06-25	2012-09-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-17 00:00:00	2012-08-30 09:40:22.458458		Hemodiálisis 15 Años y más
103299	2012-07-06	7	7	\N	\N	f	1140	4268930-0	MALLEA LÓPEZ, FLORINDA DE LAS MERC	0	2012-06-25	2012-09-24	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.461391		No Especifica
103300	2012-07-06	7	7	\N	\N	f	927	4127738-6	NAVARRO ORTEGA, LUZ ELVIRA	0	2012-06-25	2012-09-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.464906		Bilateral Igual o Inferior a 0,1
103301	2012-04-04	7	7	\N	\N	f	925	3975070-8	MARÍA ISABEL ORELLANA GÓMEZ	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.467901		No Especifica
103302	2012-06-28	7	7	\N	\N	f	1140	3722636-K	VIDAL PÉREZ, PEDRO ENRIQUE	0	2012-06-26	2012-09-24	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.471173		No Especifica
103303	2012-03-29	7	7	\N	\N	f	927	3707053-K	CLARA LUZ ARAYA REYNUABA	0	2012-03-26	2012-09-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:22.47435		Izquierda
103304	2012-05-28	7	7	\N	\N	f	773	3459056-7	MALDONADO BRAVO, MANUEL	0	2012-05-25	2012-09-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:22.477296		No Especifica
103305	2012-03-29	7	7	\N	\N	f	925	3452796-2	MARCOS HUMBERTO SALINAS CUADRA	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.480181		No Especifica
103306	2012-04-09	7	7	\N	\N	f	776	3034482-0	GONZÁLEZ CADIU, ADRIANA DEL CARMEN	0	2012-01-26	2012-09-24	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-22 00:00:00	2012-08-30 09:40:22.483195		Derecha
103307	2012-04-05	7	7	\N	\N	f	925	2685014-2	PARRAGUEZ MORALES, DESIDERIA DEL CARMEN	0	2012-03-28	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.486054		No Especifica
103308	2012-04-04	7	7	\N	\N	f	925	2359521-4	JULIO GILBERTO SOTO ROJAS	0	2012-03-27	2012-09-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.489408		No Especifica
103309	2012-08-20	7	7	\N	\N	f	988	3518614-K	FARFÁN MUÑOZ, CARLOS ENRIQUE	0	2012-08-10	2012-09-24	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.492398		No Especifica
103310	2012-08-27	7	7	\N	\N	f	821	14420796-3	CABRERA CISTERNAS, SUSANA BEATRIZ	0	2012-08-23	2012-09-24	Cáncer de Mama Derecha {decreto nº 228}	Confirmación Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:22.495463		Derecha
103311	2012-08-27	7	7	\N	\N	f	821	6567413-0	ARAYA ARAYA, LUCILA DE MERCEDES	0	2012-08-24	2012-09-24	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	2012-08-27 00:00:00	2012-08-30 09:40:22.498426		Izquierda
103312	2012-08-27	7	7	\N	\N	f	907	4358080-9	VEAS MONTES, BERNARDO RENÉ	0	2012-08-23	2012-09-24	Cáncer Gástrico . {decreto nº 228}	Intervención Quirúrgica	2012-08-27 00:00:00	2012-08-30 09:40:22.501433		No Especifica
103313	2012-08-27	7	7	\N	\N	f	907	7442678-6	CHÁVEZ ÁLVAREZ, EDUARDO ISIDRO	0	2012-08-23	2012-09-24	Cáncer Gástrico . {decreto nº 228}	Intervención Quirúrgica	2012-08-27 00:00:00	2012-08-30 09:40:22.504276		No Especifica
103314	2012-08-27	7	7	\N	\N	f	978	23912297-3	HUERTA FRITZ, MARTÍN MANUEL	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.507111		No Especifica
103315	2012-08-27	7	7	\N	\N	f	978	23931521-6	SALAS RIVERA, GALA MARTINA	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.509933		No Especifica
103316	2012-08-27	7	7	\N	\N	f	978	23931551-8	SALAS RIVERA, JULIETA MÁXIMA	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.512691		No Especifica
103317	2012-08-27	7	7	\N	\N	f	978	23937477-8	VILLALOBOS MOLINA, AMAYA CONSUELO	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.515496		No Especifica
103318	2012-08-27	7	7	\N	\N	f	978	23939356-k	GODOY FLORES, JOSEFA ELEONOR	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.518302		No Especifica
103319	2012-08-28	7	7	\N	\N	f	789	20360021-6	OLIVARES LEIVA, ANDREA ESTEFANI	0	2012-08-24	2012-09-24	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.521329		No Especifica
103320	2012-08-28	7	7	\N	\N	f	826	3272398-5	VALENZUELA SILVA, ISABEL DEL CARMEN	0	2012-08-23	2012-09-24	Cáncer de Mama Derecha {decreto nº 228}	Tratamiento Mama Derecha	2012-08-29 00:00:00	2012-08-30 09:40:22.52438		Derecha
103321	2012-08-28	7	7	\N	\N	f	826	10749751-k	FLORES BUSTOS, SANDRA YANINA	0	2012-08-23	2012-09-24	Cáncer de Mama Izquierda {decreto nº 228}	Tratamiento Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:22.52726		Izquierda
103322	2012-08-28	7	7	\N	\N	f	908	4079781-5	NARVÁEZ ROJAS, HUMBERTO GERMÁN	0	2012-08-24	2012-09-24	Cáncer Gástrico . {decreto nº 228}	Seguimiento	2012-08-29 00:00:00	2012-08-30 09:40:22.530209		No Especifica
103323	2012-08-28	7	7	\N	\N	f	906	5297376-7	ROJAS MOREL, FIDELA DEL CARMEN	0	2012-08-24	2012-09-24	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.533088		No Especifica
103324	2012-08-28	7	7	\N	\N	f	905	5997804-7	BERRÍOS CASTRO, ANA MARÍA	0	2012-08-23	2012-09-24	Cáncer Gástrico . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.535976		No Especifica
103325	2012-08-28	7	7	\N	\N	f	956	10421433-9	VERGARA GOITIANDIA, ANABEL MARISOL	0	2012-08-23	2012-09-24	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:22.539114		Severa
103326	2012-08-28	7	7	\N	\N	f	956	14447666-2	VERDEJO FIGUEROA, PAULINA ELOÍSA	0	2012-08-25	2012-09-24	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:22.54212		Severa
103327	2012-08-28	7	7	\N	\N	f	956	8838778-3	TORRES TORRES, ISOLINA SUSANA	0	2012-08-24	2012-09-24	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:22.54546		Severa
103328	2012-08-28	7	7	\N	\N	f	978	23922973-5	FUENZALIDA FUENZALIDA, ISABEL ANTONELLA ISI	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.548315		No Especifica
103329	2012-08-28	7	7	\N	\N	f	978	23923892-0	TAPIA MARTÍNEZ, ISIDORA BELÉN	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.55143		No Especifica
103330	2012-08-28	7	7	\N	\N	f	978	23934037-7	JOFRÉ GONZÁLEZ, ASTRID PAZ	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.554424		No Especifica
103331	2012-08-28	7	7	\N	\N	f	978	23937770-k	FABIUS SÁEZ, FELIPE ABRAHAM	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.557395		No Especifica
103332	2012-08-28	7	7	\N	\N	f	978	23943890-3	LOBOS TAPIA, JOE SAMMUEL ALEJANDR	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.56062		No Especifica
103333	2012-08-28	7	7	\N	\N	f	978	23944493-8	MARTÍNEZ JORQUERA, BRUNO JAVIER	0	2012-08-25	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.563603		No Especifica
103334	2012-08-28	7	7	\N	\N	f	978	23949620-2	GOMEZ REYES, MADISON	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.566608		No Especifica
103335	2012-08-28	7	7	\N	\N	f	1153	1261100-5	MELA GARABITO, ELENA DEL CARMEN	0	2012-08-23	2012-09-24	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.56995		No Especifica
103336	2012-08-29	7	7	\N	\N	f	789	22133153-2	VALENCIA FERNÁNDEZ, MAXI ENRIQUE	0	2012-08-23	2012-09-24	Asma Bronquial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.573047		No Especifica
103337	2012-08-29	7	7	\N	\N	f	807	10875168-1	GODOY GODOY, JACQUELINE ELIZABETH	0	2012-08-24	2012-09-24	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-29 00:00:00	2012-08-30 09:40:22.576234		Pre-Invasor
103338	2012-08-29	7	7	\N	\N	f	807	15973355-6	HIDALGO BERNAL, NINOSKA CRISTINA	0	2012-08-23	2012-09-24	Cáncer Cervicouterino Pre-Invasor {decreto nº 228}	Tratamiento Cáncer Pre-Invasor	2012-08-29 00:00:00	2012-08-30 09:40:22.579479		Pre-Invasor
103339	2012-08-29	7	7	\N	\N	f	821	5264524-7	RIQUELME DELGADO, CRISTINA DEL CARMEN	0	2012-08-24	2012-09-24	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	\N	2012-08-30 09:40:22.58354		Izquierda
103340	2012-08-29	7	7	\N	\N	f	978	23900539-k	SALINAS ALANIZ, AGUSTINA BELÉN	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.585899		No Especifica
103341	2012-08-29	7	7	\N	\N	f	978	23926113-2	QUINTERO VERGARA, ANTONELLA JAVIERA	0	2012-08-24	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.589308		No Especifica
103342	2012-08-29	7	7	\N	\N	f	978	23959875-7	MARCHANT COVARRUBIAS, FLORENCIA ANABELLA	0	2012-08-23	2012-09-24	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.592473		No Especifica
103343	2012-08-29	7	7	\N	\N	f	1153	3454434-4	HUAIQUIMILLA CALFIL, FRANCISCO SEGUNDO	0	2012-08-24	2012-09-24	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.595532		No Especifica
103344	2012-08-29	7	7	\N	\N	f	1153	3634300-1	YAMAL RIQUELME, ALFREDO	0	2012-08-24	2012-09-24	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.598619		No Especifica
103345	2012-08-29	7	7	\N	\N	f	1153	6017006-1	VALDÉS LARREA, ELIANA	0	2012-08-24	2012-09-24	Vicios de Refracción Otros Vicios de Refracción {decreto nº 228}	Entrega de Lentes Miopía, Astigmatismo o Hipermetropía	2012-08-29 00:00:00	2012-08-30 09:40:22.601773		No Especifica
103346	2012-08-24	7	7	\N	\N	f	775	5147186-5	AGUILERA MACLAO, LUZ MARINA	0	2012-08-16	2012-09-25	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-24 00:00:00	2012-08-30 09:40:22.605104		Derecha
103347	2012-04-04	7	7	\N	\N	f	925	15973021-2	TABITA SINAI BECERRA PARRA	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.608478		No Especifica
103348	2012-07-31	7	7	\N	\N	f	1141	7415869-2	JARA ZAMORA, ROSA VIRGINIA	0	2012-07-27	2012-09-25	Retinopatía Diabética . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.611576		No Especifica
103349	2012-04-04	7	7	\N	\N	f	925	7330103-3	GLADYS ALEJANDRINA RAMÍREZ BUSTAMANTE	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.614506		No Especifica
103350	2012-04-02	7	7	\N	\N	f	925	6210249-7	GAETE GUTIERREZ VICTOR	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.617804		No Especifica
103351	2012-07-04	7	7	\N	\N	f	927	5636925-2	VINNETT SILVA, NILO FERNANDO	0	2012-06-27	2012-09-25	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:22.621795		Izquierda Igual o Inferior a 0,1
103352	2012-04-03	7	7	\N	\N	f	925	5609365-6	BERTA OLIVIA SOTO VEGA	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.624667		No Especifica
103353	2012-06-06	7	7	\N	\N	f	773	5017634-7	DELAIGUE DELAIGUE, BLANCA SONIA	0	2012-05-28	2012-09-25	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:22.627715		No Especifica
103354	2012-05-30	7	7	\N	\N	f	773	4642790-4	ALBORNOZ REYES, HERNÁN	0	2012-05-28	2012-09-25	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-23 00:00:00	2012-08-30 09:40:22.630689		No Especifica
103355	2012-04-02	7	7	\N	\N	f	925	4027237-2	ELSA DEL CARMEN HIDALGO REINOSO	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.633598		No Especifica
103356	2012-04-02	7	7	\N	\N	f	1043	3828685-4	ANDRÉS SEGUNDO TORRIJO GONZÁLEZ	0	2012-03-29	2012-09-25	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:22.636609		Retención Urinaria Aguda Repetida
103357	2012-04-02	7	7	\N	\N	f	927	3165310-K	EUJENIO AURELIO RUSCONI BASILI	0	2012-03-29	2012-09-25	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:22.639793		Bilateral
103358	2012-04-03	7	7	\N	\N	f	925	1281752-5	VIOLETA OVANDO NAVARRO	0	2012-03-29	2012-09-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.642691		No Especifica
103359	2012-08-24	7	7	\N	\N	f	775	4343040-8	RAMOS FIGUEROA, MARÍA DOMITILA	0	2012-08-17	2012-09-26	Artrosis de Caderas Izquierda {decreto nº 228}	Control Seguimiento Traumatólogo Izquierda	2012-08-27 00:00:00	2012-08-30 09:40:22.645504		Izquierda
103360	2012-06-29	7	7	\N	\N	f	968	12000526-K	VERA CRUZ, CAROLINA SOLEDAD	0	2012-06-28	2012-09-26	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.648514		No Especifica
103361	2012-07-03	7	7	\N	\N	f	955	11432029-3	CAICO LINCO, MARITZA VIVIANA	0	2012-06-28	2012-09-26	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-03 00:00:00	2012-08-30 09:40:22.65153		No Especifica
103362	2012-07-04	7	7	\N	\N	f	955	10063748-0	AGUILERA JIMÉNEZ, BLANCA LIDIA	0	2012-06-28	2012-09-26	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-04 00:00:00	2012-08-30 09:40:22.654392		No Especifica
103363	2012-07-31	7	7	\N	\N	f	954	8987126-3	RAMOS RECABARREN, MARCELA ALEJANDRA	0	2012-07-28	2012-09-26	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.657305		No Especifica
103364	2012-07-04	7	7	\N	\N	f	1072	6511405-4	QUINTANILLA MARTÍNEZ, LUIS OMAR	0	2012-06-28	2012-09-26	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-13 00:00:00	2012-08-30 09:40:22.66054		Hemodiálisis 15 Años y más
103365	2012-05-02	7	7	\N	\N	f	1043	6510397-4	MORALES MADRID, ROBERT HERIBERTO	0	2012-03-30	2012-09-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:22.663994		Retención Urinaria Aguda Repetida
103366	2012-04-04	7	7	\N	\N	f	925	4071840-0	MARÍA ANGÉLICA GONZÁLEZ JUSTINIANO	0	2012-03-30	2012-09-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.667631		No Especifica
103367	2012-04-05	7	7	\N	\N	f	925	3883436-3	ZUVIC ROZAS, JORGE LUIS	0	2012-03-30	2012-09-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.670782		No Especifica
103368	2012-04-04	7	7	\N	\N	f	925	3776165-6	JULIO GUSTAVO MUÑOZ ARTEAGA	0	2012-03-30	2012-09-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.673888		No Especifica
103369	2012-04-04	7	7	\N	\N	f	925	3663146-5	ESPERANZA LÓPEZ RODRIGO	0	2012-03-30	2012-09-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-04 00:00:00	2012-08-30 09:40:22.676905		No Especifica
103370	2012-07-04	7	7	\N	\N	f	927	2275823-3	VILLARROEL LILLO, LUIS ALBERTO	0	2012-06-28	2012-09-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:22.680664		Bilateral Igual o Inferior a 0,1
103371	2012-08-21	7	7	\N	\N	f	888	22223687-8	CUEVAS SEPÚLVEDA, CATALINA IGNACIA	0	2012-08-20	2012-09-26	Cáncer en Menores Linfoma y/o Tumor Sólido {decreto nº 228}	Diagnóstico Linfoma y Tumores Sólidos	2012-08-28 00:00:00	2012-08-30 09:40:22.683898		Linfoma y/o Tumores Sólidos
103372	2012-08-28	7	7	\N	\N	f	821	13984316-9	RÍOS HERRERA, MARJORIE ANDREA	0	2012-08-27	2012-09-26	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	\N	2012-08-30 09:40:22.687677		Izquierda
103373	2012-08-28	7	7	\N	\N	f	956	9913844-0	ROMERO SILVA, EDUARDO GABRIEL	0	2012-08-27	2012-09-26	Depresión . {decreto nº 228}	Tratamiento Episodio Depresivo Actual en Trastorno Bipolar y Depresión Refractaria	2012-08-29 00:00:00	2012-08-30 09:40:22.690245		Severa
103374	2012-08-28	7	7	\N	\N	f	978	23918230-5	ORELLANA TAPIA, RENATA BELÉN	0	2012-08-27	2012-09-26	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.693144		No Especifica
103375	2012-08-17	7	7	\N	\N	f	1052	3502503-0	LAZCANO , ELIANA DEL CARMEN	0	2012-08-13	2012-09-27	Hipoacusia Bilateral Adulto Uso de Audífono Requerido . {decreto nº 44}	Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:22.696089		No Especifica
103376	2012-08-07	7	7	\N	\N	f	792	17379398-7	ACEVEDO MORALES, FRANCISCA MARÍA	0	2012-07-29	2012-09-27	Asma Bronquial 15 Años y Más . {decreto n° 1/2010}	Atención con Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.69916		No Especifica
103377	2012-07-06	7	7	\N	\N	f	968	8185296-0	BARRA LARENAS, ROLANDO ALBERTO	0	2012-06-29	2012-09-27	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.702213		No Especifica
103378	2012-07-23	7	7	\N	\N	f	927	3507244-6	FIGUEROA ZAMORA, JUANA MARÍA DE LA CR	0	2012-06-29	2012-09-27	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:22.705486		Izquierda Igual o Inferior a 0,1
103379	2012-08-20	7	7	\N	\N	f	1007	23634879-2	FERNÁNDEZ JEREZ, SEBASTIÁN ANDRÉS	0	2012-06-29	2012-09-27	Estrabismo . {decreto nº 228}	Tratamiento Quirúrgico	2012-08-22 00:00:00	2012-08-30 09:40:22.708385		No Especifica
103380	2012-08-29	7	7	\N	\N	f	821	6115352-7	VÁSQUEZ PINO, AÍDA MARGARITA	0	2012-08-28	2012-09-27	Cáncer de Mama Izquierda {decreto nº 228}	Confirmación Mama Izquierda	\N	2012-08-30 09:40:22.71168		Izquierda
103381	2012-08-29	7	7	\N	\N	f	906	5208664-7	SALAZAR MOYA, HORTENSIA DEL CARMEN	0	2012-08-28	2012-09-27	Cáncer Gástrico . {decreto nº 228}	Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.715114		No Especifica
103382	2012-08-29	7	7	\N	\N	f	978	23955884-4	VELÁSQUEZ VIVANCO, DAIRIZ MARTA DE LOS	0	2012-08-28	2012-09-27	Displasia Luxante de Caderas . {decreto n° 1/2010}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.718101		No Especifica
103383	2012-08-24	7	7	\N	\N	f	775	5052268-7	TAPIA FREDES, LEONOR DEL CARMEN	0	2012-08-20	2012-10-01	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-27 00:00:00	2012-08-30 09:40:22.721217		Derecha
103384	2012-08-14	7	7	\N	\N	f	955	14336636-7	OVALLE GONZÁLEZ, ANGELA ROXANA	0	2012-07-03	2012-10-01	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-14 00:00:00	2012-08-30 09:40:22.724145		No Especifica
103385	2012-07-06	7	7	\N	\N	f	1072	13338516-9	CONTRERAS SANTANDER, GLORIA ANGÉLICA	0	2012-07-03	2012-10-01	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-02 00:00:00	2012-08-30 09:40:22.727245		Hemodiálisis 15 Años y más
103386	2012-04-04	7	7	\N	\N	f	925	12059112-6	SONIA GLADYS NAVARRO ROJAS	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.730305		No Especifica
103387	2012-04-12	7	7	\N	\N	f	925	11356928-K	BARRIENTOS NIETO, SONIA ELENA	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.733268		No Especifica
103388	2012-08-03	7	7	\N	\N	f	954	10421053-8	GONZÁLEZ SILVA, MARCO ANTONIO	0	2012-08-02	2012-10-01	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.736129		No Especifica
103389	2012-01-16	7	7	\N	\N	f	1067	10072318-2	ARNALDO FRANCISCO CATRIL COLLIN	0	2011-11-30	2012-10-01	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-02 00:00:00	2012-08-30 09:40:22.739649		Estudio Pre-Trasplante
103390	2012-08-02	7	7	\N	\N	f	1157	8147261-0	VARAS AROS, MIGUEL ANGEL	0	2012-07-31	2012-10-01	Hepatitis C . {decreto n° 1/2010}	Evaluación Pre-Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:22.743436		No Especifica
103391	2012-06-09	7	7	\N	\N	f	968	6182404-9	ZAMORA JABRE, MARÍA TERESA	0	2012-07-03	2012-10-01	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.747179		No Especifica
103392	2011-12-07	7	7	\N	\N	f	1067	6101030-0	EMILIO ARNALDO BRIONES MATURANA	0	2011-12-01	2012-10-01	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-07-17 00:00:00	2012-08-30 09:40:22.750661		Estudio Pre-Trasplante
103393	2012-07-18	7	7	\N	\N	f	1140	5352960-7	FERNÁNDEZ JORQUERA, GLADYS DEL CARMEN	0	2012-07-03	2012-10-01	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.753804		No Especifica
103394	2012-04-09	7	7	\N	\N	f	925	5119341-5	VILLAGRÁN ROJAS, MARGARITA ROSA	0	2012-04-04	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.756905		No Especifica
103395	2012-04-10	7	7	\N	\N	f	925	4845773-8	YOLANDA  LEMUS LORCA	0	2012-04-04	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.760844		No Especifica
103396	2012-04-04	7	7	\N	\N	f	925	4704424-3	ANA SONIA CONTRERAS BRAVO	0	2012-04-03	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.764394		No Especifica
103397	2012-08-02	7	7	\N	\N	f	1141	4695375-4	VEAS ALVIÑA, ALBERTINA DEL CARMEN	0	2012-08-01	2012-10-01	Retinopatía Diabética . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:22.768116		No Especifica
103398	2012-04-05	7	7	\N	\N	f	925	4652197-8	RODRÍGUEZ TOBAR, ALICIA DEL CARMEN	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.771276		No Especifica
103399	2012-04-04	7	7	\N	\N	f	925	4549892-1	ELSA DEL CARMEN CONTRERAS BRAVO	0	2012-04-03	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.774091		No Especifica
103400	2012-04-04	7	7	\N	\N	f	925	4313187-7	JUANA HERMINIA DEVIA ROZAS	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.777085		No Especifica
103401	2012-04-05	7	7	\N	\N	f	925	4167816-K	DÍAZ POZA, DOMINGA CRISTINA	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.780302		No Especifica
103402	2012-04-04	7	7	\N	\N	f	925	4146240-K	ALDA DEL TRÁNSITO OLIVARES	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.783129		No Especifica
103403	2012-04-05	7	7	\N	\N	f	925	4014364-5	YÁÑEZ GÓMEZ, ESMERALDA	0	2012-04-03	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.78616		No Especifica
103404	2012-04-09	7	7	\N	\N	f	925	3974371-K	ESTAY LAZO, JUVENAL SERAFÍN	0	2012-04-03	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.789254		No Especifica
103405	2012-04-04	7	7	\N	\N	f	1043	3800020-9	LUIS GILBERTO ROMERO CARRASCO	0	2012-04-02	2012-10-01	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-17 00:00:00	2012-08-30 09:40:22.792423		Retención Urinaria Aguda Repetida
103406	2012-06-09	7	7	\N	\N	f	968	3678715-5	ROJAS LORCA, HERIBERTO HUGO	0	2012-07-03	2012-10-01	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:22.795274		No Especifica
103407	2012-04-05	7	7	\N	\N	f	925	3620040-5	SILVA RODRÍGUEZ, BERTINA	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.798278		No Especifica
103408	2012-04-04	7	7	\N	\N	f	925	3430526-9	CAROLINA DEL CARMEN EYZAGUIRRE CALDERÓN	0	2012-04-03	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.801208		No Especifica
103409	2012-04-04	7	7	\N	\N	f	925	1564672-1	ADELA ADRIANA ASTUDILLO CEBRERO	0	2012-04-02	2012-10-01	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.804069		No Especifica
103410	2012-08-27	7	7	\N	\N	f	888	23876885-3	GARCIA CONTRERAS, MAXIMILIANO AQUILES	0	2012-08-24	2012-10-01	Cáncer en Menores Linfoma y/o Tumor Sólido {decreto nº 228}	Diagnóstico Linfoma y Tumores Sólidos	2012-08-29 00:00:00	2012-08-30 09:40:22.807224		Linfoma y/o Tumores Sólidos
103411	2012-08-28	7	7	\N	\N	f	775	5622252-9	HENRÍQUEZ LAZO, NORMA INÉS	0	2012-08-21	2012-10-01	Artrosis de Caderas Derecha {decreto nº 228}	Control Seguimiento Traumatólogo Derecha	2012-08-29 00:00:00	2012-08-30 09:40:22.810289		Derecha
103412	2012-08-28	7	7	\N	\N	f	829	5053745-5	GUAJARDO SOTO, FRESIA DEL CARMEN	0	2012-08-17	2012-10-01	Cáncer de Mama Izquierda {decreto nº 228}	Diagnóstico-Etapificación Mama Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:22.813378		Izquierda
103413	2012-08-29	7	7	\N	\N	f	799	17162751-6	SANTANDER VARGAS, VIVIANA DEL CARMEN	0	2012-08-22	2012-10-01	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.816465		No Especifica
103414	2012-08-29	7	7	\N	\N	f	1086	6474035-0	DÍAZ MALDONADO, NIBALDO DEL CARMEN	0	2012-08-27	2012-10-01	Linfoma en Adultos .{decreto nº 228}	Diagnóstico Consulta Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.821126		No Especifica
103415	2012-07-19	7	7	\N	\N	f	968	11384736-0	ORTIZ BERRÍOS, CAROLITA ANTONIA	0	2012-07-04	2012-10-02	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:22.824195		No Especifica
103416	2012-08-13	7	7	\N	\N	f	848	10344540-K	ORELLANA GÓMEZ, SERGIO ABRAHAM	0	2012-08-03	2012-10-02	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:22.827076		No Especifica
103417	2012-07-05	7	7	\N	\N	f	1140	9566117-3	MORALES VASQUEZ, MARIA TERESA	0	2012-07-04	2012-10-02	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.830128		No Especifica
103418	2012-07-10	7	7	\N	\N	f	968	8702428-8	CHAMORRO CAMACHO, RAÚL ERNESTO	0	2012-07-04	2012-10-02	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:22.832979		No Especifica
103419	2012-07-05	7	7	\N	\N	f	1140	7675120-K	ALVARADO ARAVENA, RICARDO DEMETRIO	0	2012-07-04	2012-10-02	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.835746		No Especifica
103420	2012-06-09	7	7	\N	\N	f	968	6330144-2	BRITO FERNÁNDEZ, JAIME GUSTAVO SALVAD	0	2012-07-04	2012-10-02	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:22.838916		No Especifica
103421	2012-07-05	7	7	\N	\N	f	968	5345195-0	CASTILLO SEGURA, MERCEDES LUISA	0	2012-07-04	2012-10-02	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:22.841946		No Especifica
103422	2012-04-09	7	7	\N	\N	f	1155	4602436-2	BARRA PALMA, GLADYS DEL CARMEN	0	2012-04-05	2012-10-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.845216		No Especifica
103423	2012-06-09	7	7	\N	\N	f	968	4392971-2	RIQUELME RIQUELME, LUCILA	0	2012-07-04	2012-10-02	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-11 00:00:00	2012-08-30 09:40:22.848084		No Especifica
103424	2012-04-10	7	7	\N	\N	f	925	4161779-9	MARIA LUISA ORTIZ ORTIZ	0	2012-04-05	2012-10-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.851063		No Especifica
103425	2012-06-09	7	7	\N	\N	f	927	4075716-3	MADRID VEGAS, MARÍA RITA	0	2012-07-04	2012-10-02	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.854781		Bilateral Igual o Inferior a 0,1
103426	2012-04-09	7	7	\N	\N	f	925	3972950-4	LANAS , MARIA GABY	0	2012-04-05	2012-10-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.857648		No Especifica
103427	2012-06-09	7	7	\N	\N	f	927	3944951-K	ESCÁRATE MOORE, PATRICIA ELIANA	0	2012-07-04	2012-10-02	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:22.860916		Bilateral Igual o Inferior a 0,1
103428	2012-04-10	7	7	\N	\N	f	925	3894338-3	DUILIO ARTEMIO SALDIVIA DE LA FUENTE	0	2012-04-05	2012-10-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.863868		No Especifica
103429	2012-04-10	7	7	\N	\N	f	925	3722574-6	MARIA HLDA FLORIZA URZUA CANCINO	0	2012-04-05	2012-10-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.866669		No Especifica
103430	2012-04-11	7	7	\N	\N	f	925	3670882-4	HERNÁNDEZ GAETE, BENJAMÍN CLEMENTE	0	2012-04-05	2012-10-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.869563		No Especifica
103431	2012-08-13	7	7	\N	\N	f	848	3116435-4	ALFARO GREBE, WALDO DE LA CRUZ	0	2012-08-03	2012-10-02	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:22.872333		No Especifica
103432	2012-08-29	7	7	\N	\N	f	799	8590208-3	FERNÁNDEZ ESCOBAR, JACQUELINE DANIELA	0	2012-08-23	2012-10-02	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.875728		No Especifica
103433	2012-07-10	7	7	\N	\N	f	955	12222373-6	VEGA BARRIGA, BERNARDA ANTONIETA	0	2012-07-05	2012-10-03	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-10 00:00:00	2012-08-30 09:40:22.878778		No Especifica
103434	2012-07-10	7	7	\N	\N	f	968	7855582-3	ZAPATA AGUILERA, HÉCTOR PATRICIO	0	2012-07-05	2012-10-03	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.882217		No Especifica
103435	2012-07-11	7	7	\N	\N	f	927	5706010-7	SEPÚLVEDA GUAJARDO, GLADYS ZUNILDA	0	2012-07-05	2012-10-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.885406		Bilateral Igual o Inferior a 0,1
103436	2012-07-11	7	7	\N	\N	f	927	5679417-4	MAGNA CISTERNAS, ROSA ESTER	0	2012-07-05	2012-10-03	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-07 00:00:00	2012-08-30 09:40:22.888598		Izquierda Igual o Inferior a 0,1
103437	2012-02-08	7	7	\N	\N	f	776	4478079-8	LUISA SILVIA GUZMÁN GUZMÁN	0	2012-02-06	2012-10-03	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-20 00:00:00	2012-08-30 09:40:22.891758		Derecha
103438	2012-07-10	7	7	\N	\N	f	1140	4071161-9	ARNAU AYALA, MARIO MIGUEL	0	2012-07-05	2012-10-03	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.894637		No Especifica
103439	2012-06-13	7	7	\N	\N	f	773	3895189-0	JARAMILLO ACUÑA, NORA DEL CARMEN	0	2012-06-05	2012-10-03	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:22.8976		No Especifica
103440	2012-07-11	7	7	\N	\N	f	927	3669825-K	MERA ROJAS, ALEJANDRO ALBERTO	0	2012-07-05	2012-10-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.900836		Bilateral Igual o Inferior a 0,1
103441	2012-07-13	7	7	\N	\N	f	927	3450643-4	CORDERO VIDAL, JOSÉ ARTURO	0	2012-07-05	2012-10-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.904066		Bilateral Igual o Inferior a 0,1
103442	2012-07-06	7	7	\N	\N	f	968	3449075-9	AHUMADA URETA, RAÚL	0	2012-07-05	2012-10-03	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-08 00:00:00	2012-08-30 09:40:22.906914		No Especifica
103443	2012-06-13	7	7	\N	\N	f	773	3164628-6	BRAVO DUARTE, HEDDA ELLIET REBECA	0	2012-06-05	2012-10-03	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:22.909882		No Especifica
103444	2012-07-11	7	7	\N	\N	f	927	3135424-2	GEISSE TAPIA, FEDERICO SEGUNDO	0	2012-07-05	2012-10-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.913551		Bilateral Igual o Inferior a 0,1
103445	2012-07-06	7	7	\N	\N	f	1140	2846388-K	LÓPEZ LÓPEZ, CARMEN	0	2012-07-05	2012-10-03	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.916369		No Especifica
103446	2012-07-11	7	7	\N	\N	f	927	2810057-4	ASTORGA CARRASCO, ELENA SYLVIA	0	2012-07-05	2012-10-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:22.919539		Bilateral Igual o Inferior a 0,1
103447	2012-08-27	7	7	\N	\N	f	799	8676834-8	ÁVILA AGUILERA, WILDA ISABEL	0	2012-08-24	2012-10-03	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.922764		No Especifica
103448	2012-07-10	7	7	\N	\N	f	1140	12006138-0	TOBAR RODRÍGUEZ, CLAUDIO	0	2012-07-06	2012-10-04	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.925663		No Especifica
103449	2012-07-10	7	7	\N	\N	f	968	7007410-9	CATALÁN AGUILAR, MARÍA ANGÉLICA	0	2012-07-06	2012-10-04	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.928499		No Especifica
103450	2012-07-30	7	7	\N	\N	f	1140	6633727-8	PAVEZ BERNAL, TERESA IRENE	0	2012-07-06	2012-10-04	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.931412		No Especifica
103451	2012-07-17	7	7	\N	\N	f	1140	6506225-9	MEDINA RIVEROS, ROSENDO HERNÁN	0	2012-07-06	2012-10-04	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.93425		No Especifica
103452	2012-07-18	7	7	\N	\N	f	1072	6413821-9	VALENCIA ARANDA, JUAN MANUEL	0	2012-07-06	2012-10-04	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-23 00:00:00	2012-08-30 09:40:22.937371		Hemodiálisis 15 Años y más
103453	2012-08-22	7	7	\N	\N	f	1052	4358751-K	BUSTOS IBARRA, CARLOS HERNÁN	0	2012-08-20	2012-10-04	Hipoacusia Bilateral Adulto Uso de Audífono Requerido . {decreto nº 44}	Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:22.940728		No Especifica
103454	2012-08-08	7	7	\N	\N	f	848	3429729-0	HERNÁNDEZ CASTAÑER, MIGUEL LORENZO FERNA	0	2012-08-06	2012-10-05	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-08 00:00:00	2012-08-30 09:40:22.94401		No Especifica
103455	2012-06-14	7	7	\N	\N	f	850	3089861-3	SCIACCALUGA CAVE, LUIS GUILLERMO	0	2012-06-07	2012-10-05	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-21 00:00:00	2012-08-30 09:40:22.94707		No Especifica
103456	2012-08-13	7	7	\N	\N	f	848	2550958-7	LARENAS GONZÁLEZ, HUGO FERNANDO	0	2012-08-06	2012-10-05	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:22.950144		No Especifica
103457	2012-08-24	7	7	\N	\N	f	1052	4827204-5	MELLA MUÑOZ, JULIA ESTER	0	2012-08-22	2012-10-08	Hipoacusia Bilateral Adulto Uso de Audífono Requerido . {decreto nº 44}	Tratamiento	2012-08-24 00:00:00	2012-08-30 09:40:22.953081		No Especifica
103458	2012-08-24	7	7	\N	\N	f	1035	16502240-8	MARSH ALVARADO, KAREN ANDREA	0	2012-08-23	2012-10-08	Hepatitis B . {decreto n° 1/2010}	Diagnóstico	2012-08-29 00:00:00	2012-08-30 09:40:22.955989		No Especifica
103459	2012-08-10	7	7	\N	\N	f	954	13193518-8	PLAZA BASTÍAS, WENDY LOREN	0	2012-08-08	2012-10-08	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.958953		No Especifica
103460	2012-08-09	7	7	\N	\N	f	954	13191753-8	GONZÁLEZ RUIZ, ALEJANDRA DEL CARMEN	0	2012-08-08	2012-10-08	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:22.961878		No Especifica
103461	2012-07-12	7	7	\N	\N	f	955	10600156-1	POZO ÁLVAREZ, CECILIA IRENE	0	2012-07-10	2012-10-08	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-12 00:00:00	2012-08-30 09:40:22.965112		No Especifica
103462	2011-12-20	7	7	\N	\N	f	1067	8991975-4	RODOLFO ENRIQUE ALISTE ARCOS	0	2011-12-09	2012-10-08	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-20 00:00:00	2012-08-30 09:40:22.968168		Estudio Pre-Trasplante
103463	2012-07-12	7	7	\N	\N	f	1072	8508016-4	ARANCIBIA NÚÑEZ, RUBÉN ENRIQUE	0	2012-07-10	2012-10-08	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-10 00:00:00	2012-08-30 09:40:22.971204		Hemodiálisis 15 Años y más
103464	2012-07-18	7	7	\N	\N	f	1140	7059597-4	ESPINA BAIN, LEONZO SEGUNDO	0	2012-07-10	2012-10-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:22.974018		No Especifica
103465	2012-04-12	7	7	\N	\N	f	1043	6709097-7	VALENCIA OSSANDÓN, JUAN CARLOS	0	2012-04-09	2012-10-08	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-24 00:00:00	2012-08-30 09:40:22.977013		Retención Urinaria Aguda Repetida
103466	2012-07-12	7	7	\N	\N	f	968	6309004-2	SOTO PINO, JUAN RUBÉN	0	2012-07-10	2012-10-08	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:22.98002		No Especifica
103467	2012-04-13	7	7	\N	\N	f	925	5988493-K	SILVA OLIVARES, GRACIELA DOMITILA DE	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.982936		No Especifica
103468	2012-04-17	7	7	\N	\N	f	925	5963291-4	GONZÁLEZ DURÁN, HÉCTOR SEGUNDO	0	2012-04-10	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.985771		No Especifica
103469	2012-04-13	7	7	\N	\N	f	925	5585512-9	URRUTIA CASTILLO, LIONEL ARTURO	0	2012-04-11	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:22.988739		No Especifica
103470	2012-07-11	7	7	\N	\N	f	1140	5561555-1	CORTÉS MARTÍNEZ, PATRICIA ELIANA DEL	0	2012-07-09	2012-10-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:22.991638		No Especifica
103471	2012-04-12	7	7	\N	\N	f	925	5016377-6	ESTAY CERDA, HERNÁN ENRIQUE	0	2012-04-10	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.994497		No Especifica
103472	2012-04-11	7	7	\N	\N	f	925	4980115-7	HERRERA HERRERA, MATÍAS SEGUNDO	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:22.998296		No Especifica
103473	2012-04-13	7	7	\N	\N	f	925	4690913-5	GONZÁLEZ CÁRCAMO, GLORIA EDYTH	0	2012-04-10	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.001207		No Especifica
103474	2012-04-26	7	7	\N	\N	f	925	4079409-3	LEIVA VERA, MARÍA ANGÉLICA	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.004031		No Especifica
103475	2012-04-11	7	7	\N	\N	f	925	3969561-8	BERNDT SUNKEL, INGEBORG	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.006832		No Especifica
103476	2012-08-13	7	7	\N	\N	f	848	3581541-4	PROUST CROVETTO, JORGE PATRICIO	0	2012-08-08	2012-10-08	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:23.009672		No Especifica
103477	2012-07-11	7	7	\N	\N	f	1140	3581255-5	ARÉVALO VERGARA, PEDRO SEGUNDO	0	2012-07-09	2012-10-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.012621		No Especifica
103478	2012-04-16	7	7	\N	\N	f	925	3505811-7	LEGUE MAC LEAN, JOSÉ RENÉ	0	2012-04-11	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.01545		No Especifica
103479	2012-04-11	7	7	\N	\N	f	925	3268986-8	CID CID, MANUEL FERNANDO	0	2012-04-10	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.018298		No Especifica
103480	2012-07-17	7	7	\N	\N	f	1140	3243840-7	ALÉ FRIZ, ESTER	0	2012-07-09	2012-10-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:23.021112		No Especifica
103481	2012-04-16	7	7	\N	\N	f	925	3144995-2	CEPEDA OLIVARES, NICOLÁS ENRIQUE	0	2012-04-11	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.023933		No Especifica
103482	2012-04-16	7	7	\N	\N	f	925	2970292-6	GALDAMES CAMPOS, MARIO ALFONSO	0	2012-04-11	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.02677		No Especifica
103483	2012-04-16	7	7	\N	\N	f	925	2608192-0	CANDON MATURANA, LUIS RENÉ	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.029695		No Especifica
103484	2012-04-10	7	7	\N	\N	f	925	2467501-7	SERGIO  GASTON MARAMBIO MARAMBIO	0	2012-04-09	2012-10-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:23.032954		No Especifica
103485	2012-08-27	7	7	\N	\N	f	988	4261077-1	GÁLVEZ SOLÍS, HUMBERTO RAMÓN	0	2012-08-23	2012-10-08	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-27 00:00:00	2012-08-30 09:40:23.036331		No Especifica
103486	2012-08-28	7	7	\N	\N	f	829	3272398-5	VALENZUELA SILVA, ISABEL DEL CARMEN	0	2012-08-23	2012-10-08	Cáncer de Mama Derecha {decreto nº 228}	Diagnóstico-Etapificación Mama Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:23.039658		Derecha
103487	2012-08-28	7	7	\N	\N	f	829	10749751-k	FLORES BUSTOS, SANDRA YANINA	0	2012-08-23	2012-10-08	Cáncer de Mama Izquierda {decreto nº 228}	Diagnóstico-Etapificación Mama Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:23.0429		Izquierda
103488	2012-08-29	7	7	\N	\N	f	799	16104380-k	OJEDA ACEITUNO, KARLA ANDREA	0	2012-08-27	2012-10-08	Cáncer Cervicouterino Segmento Proceso de Diagnóstico {decreto nº 228}	Diagnóstico - Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.046139		No Especifica
103489	2012-08-29	7	7	\N	\N	f	988	4186910-0	DÍAZ GONZÁLEZ, HUMBERTO SEGUNDO	0	2012-08-23	2012-10-08	Enfermedad Pulmonar Obstructiva Crónica . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.049298		No Especifica
103490	2012-04-18	7	7	\N	\N	f	997	15719599-9	CARLOZA ROSENBAUM, SEBASTIÁN ARTURO	0	2012-04-12	2012-10-09	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-04-18 00:00:00	2012-08-30 09:40:23.052572		No Especifica
103491	2012-07-17	7	7	\N	\N	f	927	9065356-3	ESCOBAR VINETT, ERIKA DEL CARMEN	0	2012-07-11	2012-10-09	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.056383		Derecha Igual o Inferior a 0,1
103492	2012-06-12	7	7	\N	\N	f	773	6339621-4	RIQUELME MENDOZA, DORALISA DEL CARMEN	0	2012-06-11	2012-10-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:23.059742		No Especifica
103493	2012-06-12	7	7	\N	\N	f	773	5727613-4	ZAMORA CONTRERAS, GABRIELA DE LAS MERC	0	2012-06-11	2012-10-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:23.06285		No Especifica
103494	2012-07-17	7	7	\N	\N	f	1140	5724424-0	MISISTRANO ASSAEL, JACK	0	2012-07-11	2012-10-09	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:23.065848		No Especifica
103495	2012-04-16	7	7	\N	\N	f	925	5683880-5	MENA MARZÁN, VIVIANA DEL CARMEN	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.069434		No Especifica
103496	2012-07-12	7	7	\N	\N	f	1140	5581746-4	ARAYA DÍAZ, ROSA ELVIRA	0	2012-07-11	2012-10-09	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.072509		No Especifica
103497	2012-04-17	7	7	\N	\N	f	925	5033427-9	CONTRERAS CORDERO, CARLOS	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.07562		No Especifica
103498	2012-04-23	7	7	\N	\N	f	925	4584273-8	VIRGINIA ARANCIBIA AHUMADA	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.078742		No Especifica
103499	2012-06-14	7	7	\N	\N	f	773	4492204-5	ORELLANA JARA, IRMA	0	2012-06-11	2012-10-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:23.081878		No Especifica
103500	2012-06-18	7	7	\N	\N	f	773	4474198-9	DURÁN RÍOS, JUDITH	0	2012-06-11	2012-10-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:23.084997		No Especifica
103501	2012-04-17	7	7	\N	\N	f	925	4149169-8	OLIVARES MOYA, MARTA ELISA	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.088419		No Especifica
103502	2012-04-25	7	7	\N	\N	f	925	3439681-7	BARRAZA BARRAZA, RAMÓN LUIS DEL CARME	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.091315		No Especifica
103503	2012-04-17	7	7	\N	\N	f	925	3354300-K	ORELLANA GONZÁLEZ, EUGENIA DEL CARMEN	0	2012-04-12	2012-10-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.094206		No Especifica
103504	2012-07-18	7	7	\N	\N	f	955	14238426-4	ZÚÑIGA GALLARDO, PAOLA MARGARITA	0	2012-07-12	2012-10-10	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-18 00:00:00	2012-08-30 09:40:23.09715		No Especifica
103505	2012-07-13	7	7	\N	\N	f	1140	10497807-K	RIQUELME FREZ, DORIS ASCENCIÓN	0	2012-07-12	2012-10-10	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:23.100022		No Especifica
103506	2012-07-17	7	7	\N	\N	f	1046	8266945-0	CORNEJO SARMIENTO, KARINA JETTY DE LAS	0	2012-07-12	2012-10-10	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.103049		No Especifica
103507	2012-04-18	7	7	\N	\N	f	925	7320370-8	PASTÉN CÉSPED, MARÍA ANTONIETA	0	2012-04-13	2012-10-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.105928		No Especifica
103508	2012-07-13	7	7	\N	\N	f	1140	7174513-9	GALLARDO DÍAZ, MARÍA ANTONIA	0	2012-07-12	2012-10-10	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:23.108855		No Especifica
103509	2012-07-17	7	7	\N	\N	f	968	5419760-8	CERDA ESPINOZA, MANUELA DEL CARMEN	0	2012-07-12	2012-10-10	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-07-23 00:00:00	2012-08-30 09:40:23.111814		No Especifica
103510	2012-04-18	7	7	\N	\N	f	925	4957634-K	RAMÍREZ CONTRERAS, CARMEN	0	2012-04-13	2012-10-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.114674		No Especifica
103511	2012-04-17	7	7	\N	\N	f	925	4636313-2	TORRES PONCE, ADRIANA DEL CARMEN	0	2012-04-13	2012-10-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.117518		No Especifica
103512	2012-07-19	7	7	\N	\N	f	968	4617097-0	TIRADO TORRES, TEGUALDA DEL CARMEN	0	2012-07-12	2012-10-10	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.120533		No Especifica
103513	2012-07-13	7	7	\N	\N	f	968	3828990-K	MADERA ROCCO, NORMA EMILIA	0	2012-07-12	2012-10-10	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.123581		No Especifica
103514	2012-04-17	7	7	\N	\N	f	925	3662752-2	MALDONADO DÍAZ, NANCY AÍDA	0	2012-04-13	2012-10-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.126929		No Especifica
103515	2012-06-15	7	7	\N	\N	f	773	3437292-6	VILLALOBOS GERONDA, FLORINDA	0	2012-06-12	2012-10-10	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-10 00:00:00	2012-08-30 09:40:23.129833		No Especifica
103516	2012-07-18	7	7	\N	\N	f	927	3185193-9	CORNEJO CONTRERAS, OSVALDO	0	2012-07-12	2012-10-10	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:23.133337		Bilateral Igual o Inferior a 0,1
103517	2012-04-17	7	7	\N	\N	f	925	3112416-6	PIQUÉ POBLETE, AURELIO ARTURO ARNAL	0	2012-04-13	2012-10-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:23.136666		No Especifica
103518	2012-07-23	7	7	\N	\N	f	1004	23107812-6	MAYA PARRA, FRANCISCO IGNACIO	0	2012-07-13	2012-10-11	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.139604		No Especifica
103519	2012-07-19	7	7	\N	\N	f	1007	22621897-1	CAMPOS VENENCIANO, VALENTINA ALEJANDRA	0	2012-07-13	2012-10-11	Estrabismo . {decreto nº 228}	Tratamiento Quirúrgico	2012-08-23 00:00:00	2012-08-30 09:40:23.142611		No Especifica
103520	2012-07-17	7	7	\N	\N	f	1046	15882018-8	SALAZAR PERALTA, CLAUDIO ANDRÉS	0	2012-07-13	2012-10-11	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-07-17 00:00:00	2012-08-30 09:40:23.145541		No Especifica
103521	2012-07-19	7	7	\N	\N	f	927	5008536-8	CORREA ESPINOZA, JOVINO DEL CARMEN	0	2012-07-13	2012-10-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.148729		Derecha Igual o Inferior a 0,1
103522	2012-07-19	7	7	\N	\N	f	1046	4675203-1	COVARRUBIAS ZAMORA, LUIS SERGIO	0	2012-07-13	2012-10-11	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.151618		No Especifica
103523	2012-08-14	7	7	\N	\N	f	792	6818999-3	VERGARA VEGA, MARÍA ANGÉLICA	0	2012-08-13	2012-10-12	Asma Bronquial 15 Años y Más . {decreto n° 1/2010}	Atención con Especialista	2012-08-28 00:00:00	2012-08-30 09:40:23.154674		No Especifica
103524	2012-02-22	7	7	\N	\N	f	776	6035878-8	MARÍA TERESA TORRES	0	2012-02-15	2012-10-12	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-21 00:00:00	2012-08-30 09:40:23.157783		Derecha
103525	2012-06-20	7	7	\N	\N	f	773	5480832-1	HERRERA VERGARA, MARÍA SILVIA	0	2012-06-14	2012-10-12	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-25 00:00:00	2012-08-30 09:40:23.160754		No Especifica
103526	2012-08-16	7	7	\N	\N	f	848	4355995-8	CASTRO GÓMEZ, JOSÉ ENRIQUE	0	2012-08-13	2012-10-12	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-27 00:00:00	2012-08-30 09:40:23.163653		No Especifica
103527	2012-08-21	7	7	\N	\N	f	954	10337260-7	PALLALEO TIZNADO, SANDRA PATRICIA	0	2012-08-13	2012-10-12	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:23.166596		No Especifica
103528	2012-04-20	7	7	\N	\N	f	925	21318995-6	GONZALEZ VERGARA, GABRIEL ALEJANDRO	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.169567		No Especifica
103529	2012-07-19	7	7	\N	\N	f	955	13193036-4	HUERTA RIQUELME, JOHANNA VIOLETA	0	2012-07-17	2012-10-16	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-19 00:00:00	2012-08-30 09:40:23.1727		No Especifica
103530	2012-08-01	7	7	\N	\N	f	1072	11990925-2	LÓPEZ TAPIA, VERÓNICA DEL CARMEN	0	2012-07-17	2012-10-16	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-02 00:00:00	2012-08-30 09:40:23.175742		Hemodiálisis 15 Años y más
103531	2012-08-14	7	7	\N	\N	f	955	11605480-9	CISTERNAS CISTERNAS, GLORIA MARÍA	0	2012-07-17	2012-10-16	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-14 00:00:00	2012-08-30 09:40:23.178605		No Especifica
103532	2012-04-20	7	7	\N	\N	f	925	10163615-1	NÚÑEZ VERA, PEDRO	0	2012-04-16	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:23.181537		No Especifica
103533	2012-07-23	7	7	\N	\N	f	1140	8697164-K	URZÚA DÍAZ, HUMBERTO ANDRÉS	0	2012-07-18	2012-10-16	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:23.184399		No Especifica
103534	2012-07-19	7	7	\N	\N	f	955	8618986-0	VALENZUELA BERNAL, ISABEL ERNESTINA	0	2012-07-17	2012-10-16	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-19 00:00:00	2012-08-30 09:40:23.187298		No Especifica
103535	2012-07-20	7	7	\N	\N	f	1072	8357834-3	RAMOS JAIMEN, IVONNE DEL CARMEN	0	2012-07-17	2012-10-16	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-13 00:00:00	2012-08-30 09:40:23.190311		Hemodiálisis 15 Años y más
103536	2012-04-23	7	7	\N	\N	f	1043	7539841-7	RAFAEL EMILIO AREVALO MERCADO	0	2012-04-18	2012-10-16	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-24 00:00:00	2012-08-30 09:40:23.200689		Retención Urinaria Aguda Repetida
103537	2012-02-27	7	7	\N	\N	f	776	6211102-K	MARTA GLORIA DEL CAR ADELMANN WALKER	0	2012-02-16	2012-10-16	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-02-28 00:00:00	2012-08-30 09:40:23.203857		Izquierda
103538	2012-04-24	7	7	\N	\N	f	925	5728433-1	FAJARDO PÉREZ, CARLOS ALBERTO	0	2012-04-18	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.206725		No Especifica
103539	2012-07-24	7	7	\N	\N	f	1046	5493392-4	BERRÍOS ELGUETA, HERNÁN DEL TRÁNSITO	0	2012-07-17	2012-10-16	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-07-24 00:00:00	2012-08-30 09:40:23.209668		No Especifica
103540	2012-07-20	7	7	\N	\N	f	1072	5424786-9	RAHN JOFRÉ, CARLOS WILHELM	0	2012-07-17	2012-10-16	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-02 00:00:00	2012-08-30 09:40:23.212953		Hemodiálisis 15 Años y más
103541	2012-04-23	7	7	\N	\N	f	927	5109732-7	Mario Sebastian Cortes Jornet	0	2012-04-18	2012-10-16	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.217042		Bilateral
103542	2012-07-19	7	7	\N	\N	f	1140	4963837-K	BRUNA MILLON, LUCILA SANTOS	0	2012-07-17	2012-10-16	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:23.220214		No Especifica
103543	2012-04-25	7	7	\N	\N	f	925	4949781-4	GARCÍA MANRÍQUEZ, GLORIA ANGÉLICA JUAN	0	2012-04-18	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.223328		No Especifica
103544	2012-04-20	7	7	\N	\N	f	925	4932573-8	VEGA VEGA, SARA	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.226463		No Especifica
103545	2012-08-01	7	7	\N	\N	f	1072	4923494-5	TAPIA BARRA, ALEJANDRO	0	2012-07-18	2012-10-16	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-02 00:00:00	2012-08-30 09:40:23.229639		Hemodiálisis 15 Años y más
103546	2012-04-20	7	7	\N	\N	f	1155	4732473-4	CATRILEO GUZMÁN, EUDORA DEL TRÁNSITO	0	2012-04-16	2012-10-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.232927		No Especifica
103547	2012-04-23	7	7	\N	\N	f	925	4562112-K	MARIA DEL CARMEN EGOZCUE MORENO	0	2012-04-18	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.236072		No Especifica
103548	2012-04-23	7	7	\N	\N	f	1043	3795918-9	FELIX ARAYA ROJAS	0	2012-04-18	2012-10-16	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-24 00:00:00	2012-08-30 09:40:23.239211		Retención Urinaria Aguda Repetida
103549	2012-07-05	7	7	\N	\N	f	773	3690693-6	GALLARDO VIVANCO, MARÍA DELIA DEL CARM	0	2012-06-15	2012-10-16	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-17 00:00:00	2012-08-30 09:40:23.242297		No Especifica
103550	2012-04-23	7	7	\N	\N	f	925	3585238-7	HILDA MERCEDES ARANDA URZUA	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.245666		No Especifica
103551	2012-04-25	7	7	\N	\N	f	925	2968804-4	FAJARDO BURGOS, ALDO	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.249618		No Especifica
103552	2012-04-20	7	7	\N	\N	f	925	2760810-8	ESTAY ARANCIBIA, JULIA ELENA	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.253221		No Especifica
103553	2012-04-30	7	7	\N	\N	f	925	2741815-5	MONTEIRO BUSTOS, JULIO	0	2012-04-18	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.256326		No Especifica
103554	2012-04-25	7	7	\N	\N	f	925	2677762-3	FERNÁNDEZ DELGADO, JAVIER DE LA CRUZ	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.260256		No Especifica
103555	2012-04-20	7	7	\N	\N	f	925	2439422-0	GARCIA , LUIS CONRADO	0	2012-04-19	2012-10-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:23.264173		No Especifica
103556	2012-08-20	7	7	\N	\N	f	1141	7444830-5	ESCOBAR SALINAS, ROSA CRISTINA	0	2012-08-16	2012-10-16	Retinopatía Diabética . {decreto nº 228}	Tratamiento	2012-08-28 00:00:00	2012-08-30 09:40:23.268096		No Especifica
103557	2012-08-21	7	7	\N	\N	f	954	10381453-7	HUERTA SÁEZ, YASMINA DEL CARMEN	0	2012-08-17	2012-10-16	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.271835		No Especifica
103558	2012-08-21	7	7	\N	\N	f	954	14257897-2	RAMÍREZ MALDONADO, ROSA DE LA LUZ	0	2012-08-16	2012-10-16	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:23.275016		No Especifica
103559	2012-08-01	7	7	\N	\N	f	1004	23151227-6	VALENZUELA AVENDAÑO, JUSEPH VERÓNICA PAZ	0	2012-07-19	2012-10-17	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.278504		No Especifica
103560	2012-06-27	7	7	\N	\N	f	1004	21156131-9	CASTRO CASTRO, JORDAN BAIRON	0	2012-07-19	2012-10-17	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:23.281445		No Especifica
103561	2012-08-14	7	7	\N	\N	f	955	13226175-K	LEÓN GONZÁLEZ, ANDREA MARISOL	0	2012-07-19	2012-10-17	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-14 00:00:00	2012-08-30 09:40:23.284299		No Especifica
103562	2012-07-23	7	7	\N	\N	f	955	10463665-9	PÉREZ CUMIAN, MIRIAM DEL CARMEN	0	2012-07-19	2012-10-17	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-07-23 00:00:00	2012-08-30 09:40:23.287176		No Especifica
103563	2012-07-23	7	7	\N	\N	f	927	8618633-0	VERA CÓRDOVA, SUSANA DEL CARMEN	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.290573		Izquierda Igual o Inferior a 0,1
103564	2012-06-28	7	7	\N	\N	f	773	7324540-0	MUÑOZ SÁEZ, ANA MERCEDES	0	2012-06-19	2012-10-17	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-17 00:00:00	2012-08-30 09:40:23.294051		No Especifica
103565	2012-07-23	7	7	\N	\N	f	927	6170263-6	ALARCÓN ASTUDILLO, DICLA	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.297266		Derecha Igual o Inferior a 0,1
103566	2012-06-21	7	7	\N	\N	f	773	6152002-3	PAVEZ BECERRA, MARGARITA DEL ROSARI	0	2012-06-19	2012-10-17	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-17 00:00:00	2012-08-30 09:40:23.300235		No Especifica
103567	2012-07-23	7	7	\N	\N	f	927	5504276-4	PEÑALOZA SÁNCHEZ, YOLANDA ADRIANA	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.30339		Derecha Igual o Inferior a 0,1
103568	2012-07-23	7	7	\N	\N	f	927	5086537-1	SOTO GONZÁLEZ, OSVALDO JORGE	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.306531		Izquierda Igual o Inferior a 0,1
103569	2012-07-23	7	7	\N	\N	f	927	4974436-6	SOLÍS RAMÍREZ, FLORA LUISA	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.309785		Izquierda Igual o Inferior a 0,1
103570	2012-04-25	7	7	\N	\N	f	925	4970942-0	OLIVARES MARILLANCA, EUGENIA	0	2012-04-20	2012-10-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.312732		No Especifica
103571	2012-07-23	7	7	\N	\N	f	927	4867512-3	SALAZAR MUÑOZ, ESTER CLARISA	0	2012-07-19	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:23.316079		Bilateral Igual o Inferior a 0,1
103572	2012-07-23	7	7	\N	\N	f	927	4736223-7	MENA SERRANO, ZUNILDA DEL CARMEN	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.319843		Izquierda Igual o Inferior a 0,1
103573	2012-07-23	7	7	\N	\N	f	927	4633289-K	GUERRA SEGOVIA, ITTA DEL CARMEN	0	2012-07-19	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:23.323082		Bilateral Igual o Inferior a 0,1
103574	2012-07-23	7	7	\N	\N	f	927	4561458-1	YÁÑEZ YÁÑEZ, EDUARDO ENRIQUE	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.326196		Izquierda Igual o Inferior a 0,1
103575	2012-04-25	7	7	\N	\N	f	927	4201348-K	ROJAS FIGUEROA, EUGENIO	0	2012-04-20	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.329493		Bilateral
103576	2012-07-23	7	7	\N	\N	f	1072	3671296-1	OÑATE SALDAÑA, RANDO ALONSO	0	2012-07-19	2012-10-17	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-07-23 00:00:00	2012-08-30 09:40:23.332567		Hemodiálisis 15 Años y más
103577	2012-07-23	7	7	\N	\N	f	927	3305616-8	JOPIA CISTERNAS, MIGUEL ANTONIO	0	2012-07-19	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.33572		Bilateral Igual o Inferior a 0,1
103578	2012-07-23	7	7	\N	\N	f	927	2725041-6	MILLON ESCOBAR, JUAN RAMÓN	0	2012-07-19	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:23.339181		Bilateral Igual o Inferior a 0,1
103579	2012-07-23	7	7	\N	\N	f	927	2573018-6	PINILLA , GRACIELA	0	2012-07-19	2012-10-17	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.342872		Izquierda Igual o Inferior a 0,1
103580	2012-06-22	7	7	\N	\N	f	773	1814119-1	CERDA ZÁRATE, JORGE RAÚL	0	2012-06-19	2012-10-17	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:23.34582		No Especifica
103581	2012-07-23	7	7	\N	\N	f	927	1812087-9	ORTIZ SAAVEDRA, MARÍA ZULEMA	0	2012-07-19	2012-10-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:23.349189		Bilateral Igual o Inferior a 0,1
103582	2012-07-26	7	7	\N	\N	f	1046	6677219-5	HERRERA HERRERA, JUAN HUMBERTO	0	2012-07-20	2012-10-18	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-08 00:00:00	2012-08-30 09:40:23.352155		No Especifica
103583	2012-07-23	7	7	\N	\N	f	968	6672214-7	LAZCANO LEÓN, CECILIA ROSA	0	2012-07-20	2012-10-18	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:23.355088		No Especifica
103584	2012-07-24	7	7	\N	\N	f	1046	3639123-5	STEUER STEHN, ELSE ROSEMARIE	0	2012-07-20	2012-10-18	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.357982		No Especifica
103585	2012-08-01	7	7	\N	\N	f	1072	3432004-7	HIDALGO FUENTES, INÉS DE LAS MERCEDES	0	2012-07-20	2012-10-18	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-22 00:00:00	2012-08-30 09:40:23.360969		Hemodiálisis 15 Años y más
103586	2012-07-23	7	7	\N	\N	f	1140	3160711-6	BERRÍOS ESCOBEDO, CARLOS MAURICIO	0	2012-07-20	2012-10-18	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.363956		No Especifica
103587	2012-08-22	7	7	\N	\N	f	848	5383119-2	VALLE MUÑOZ, MOISÉS	0	2012-08-20	2012-10-19	Cáncer de Próstata . {decreto nº 228}	Etapificación	2012-08-29 00:00:00	2012-08-30 09:40:23.367022		No Especifica
103588	2012-08-24	7	7	\N	\N	f	1157	8601906-K	CARMONA MORALES, MANUEL FERNANDO	0	2012-08-22	2012-10-22	Hepatitis C . {decreto n° 1/2010}	Evaluación Pre-Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:23.370124		No Especifica
103589	2012-06-27	7	7	\N	\N	f	1004	23867345-3	FERNÁNDEZ VALENCIA, MATEO ALONSO	0	2012-07-24	2012-10-22	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:23.373031		No Especifica
103590	2012-06-27	7	7	\N	\N	f	1004	23456464-1	SILVA REBOLLEDO, DANAE BELÉN	0	2012-07-23	2012-10-22	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:23.375871		No Especifica
103591	2012-07-30	7	7	\N	\N	f	1004	21902399-5	SANHUEZA ACEVEDO, SANTIAGO GUILLERMO	0	2012-07-24	2012-10-22	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:23.37876		No Especifica
103592	2012-05-02	7	7	\N	\N	f	997	19338132-4	DÍAZ ASTUDILLO, HÉCTOR ISAAC	0	2012-04-25	2012-10-22	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-05-02 00:00:00	2012-08-30 09:40:23.381915		No Especifica
103593	2012-07-25	7	7	\N	\N	f	1072	14414352-3	MACCHIAVELLO GARCÍA, FRANCESCA PAOLA DE L	0	2012-07-24	2012-10-22	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-07 00:00:00	2012-08-30 09:40:23.385203		Hemodiálisis 15 Años y más
103594	2012-07-31	7	7	\N	\N	f	955	12223283-2	GÁLVEZ RIQUELME, ROXANA NORMA	0	2012-07-23	2012-10-22	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-01 00:00:00	2012-08-30 09:40:23.388359		No Especifica
103595	2012-04-27	7	7	\N	\N	f	1043	11735066-5	NAVIA GARAY, RENÉ ANDRÉS	0	2012-04-25	2012-10-22	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:23.391507		Retención Urinaria Aguda Repetida
103596	2012-07-30	7	7	\N	\N	f	1140	10867027-4	VÁSQUEZ VEAS, BERNARDITA DEL CARME	0	2012-07-24	2012-10-22	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.394434		No Especifica
103597	2012-08-02	7	7	\N	\N	f	955	8936256-3	VALDERAS CARVAJAL, EDUARDO ANTONIO	0	2012-07-24	2012-10-22	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-02 00:00:00	2012-08-30 09:40:23.39732		No Especifica
103598	2012-07-25	7	7	\N	\N	f	822	8237540-6	MÁRQUEZ DEVIA, FELICITA DEL CARMEN	0	2012-07-24	2012-10-22	Cáncer de Mama Derecha {decreto nº 228}	Control Seguimiento Mama Derecha	2012-08-03 00:00:00	2012-08-30 09:40:23.400442		Derecha
103599	2012-07-31	7	7	\N	\N	f	1072	7666920-1	HIDALGO VALENCIA, OSVALDO DE LAS MERCE	0	2012-07-24	2012-10-22	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-07 00:00:00	2012-08-30 09:40:23.40329		Hemodiálisis 15 Años y más
103600	2012-04-27	7	7	\N	\N	f	1043	6488852-8	HORTA ROJAS, PATRICIO ERNESTO ALF	0	2012-04-23	2012-10-22	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:23.406136		Retención Urinaria Aguda Repetida
103601	2012-04-27	7	7	\N	\N	f	925	6322066-3	SANDOVAL HUIRCÁN, MERCEDES	0	2012-04-25	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.410453		No Especifica
103602	2012-04-27	7	7	\N	\N	f	925	5975075-5	BISKUPOVIC MAZZEI, NADEZDA ANTONIETA EL	0	2012-04-25	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.413543		No Especifica
103603	2012-05-07	7	7	\N	\N	f	1043	5467607-7	ARANCIBIA VARGAS, JOSÉ RUPERTO	0	2012-04-25	2012-10-22	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:23.41663		Retención Urinaria Aguda Repetida
103604	2012-04-27	7	7	\N	\N	f	925	5352217-3	MORALES FLORES, ERNESTINA	0	2012-04-25	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.42029		No Especifica
103605	2012-07-26	7	7	\N	\N	f	927	4976394-8	SEPÚLVEDA LAGOS, RAÚL HERNÁN	0	2012-07-24	2012-10-22	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.42378		Derecha Igual o Inferior a 0,1
103606	2012-04-27	7	7	\N	\N	f	1043	4887192-5	SUAZO SALINAS, RAÚL FERNANDO	0	2012-04-23	2012-10-22	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:23.426936		Retención Urinaria Aguda Repetida
103607	2012-02-27	7	7	\N	\N	f	776	4675233-3	LUCY UBERLINDA DEL C SALAZAR TOLOSA	0	2012-02-23	2012-10-22	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-17 00:00:00	2012-08-30 09:40:23.430364		Derecha
103608	2012-02-28	7	7	\N	\N	f	776	4541905-3	LUIS ALBERTO OJEDA BARRÍA	0	2012-02-24	2012-10-22	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-02-28 00:00:00	2012-08-30 09:40:23.433575		Derecha
103609	2012-07-30	7	7	\N	\N	f	927	3673707-7	PIZARRO GONZÁLEZ, AÍDA ISMELDA	0	2012-07-24	2012-10-22	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.437193		Izquierda Igual o Inferior a 0,1
103610	2012-04-27	7	7	\N	\N	f	1043	3672567-2	SÁEZ , RENÉ FÉLIX	0	2012-04-23	2012-10-22	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:23.44035		Retención Urinaria Aguda Repetida
103611	2012-07-30	7	7	\N	\N	f	1072	3530250-6	MONTIEL SILVA, JULIA SYLVIA ELENA	0	2012-07-24	2012-10-22	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-07 00:00:00	2012-08-30 09:40:23.443325		Hemodiálisis 15 Años y más
103612	2012-05-04	7	7	\N	\N	f	925	3482796-6	GONZÁLEZ HENRÍQUEZ, SILVIA DEL CARMEN	0	2012-04-24	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.446371		No Especifica
103613	2012-04-27	7	7	\N	\N	f	925	3223300-7	FLORES ROJAS, IVAN MODESTO	0	2012-04-25	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.449345		No Especifica
103614	2012-04-27	7	7	\N	\N	f	925	2807026-8	VENEGAS TORREJÓN, CARLOS ENRIQUE	0	2012-04-25	2012-10-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.4523		No Especifica
103615	2012-06-27	7	7	\N	\N	f	927	2172275-8	MOREND OLIVARES, LADISLAO GABRIELO	0	2012-07-24	2012-10-22	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.455435		Izquierda Igual o Inferior a 0,1
103616	2012-08-23	7	7	\N	\N	f	954	12467479-4	LEÓN ALLENDES, SUSANA VICTORIA	0	2012-08-21	2012-10-22	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.458323		No Especifica
103617	2012-08-27	7	7	\N	\N	f	848	4450383-2	SAGURIE FERNÁNDEZ, YAMIL	0	2012-08-23	2012-10-22	Cáncer de Próstata . {decreto nº 228}	Etapificación	\N	2012-08-30 09:40:23.462078		No Especifica
103618	2012-08-27	7	7	\N	\N	f	848	5014453-4	MANRÍQUEZ PEÑA, FRANCISCO REINALDO	0	2012-08-22	2012-10-22	Cáncer de Próstata . {decreto nº 228}	Etapificación	\N	2012-08-30 09:40:23.465189		No Especifica
103619	2012-07-31	7	7	\N	\N	f	1004	22121333-5	LARA GONZALEZ, ISABELLA ANTONIA	0	2012-07-25	2012-10-23	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:23.467393		No Especifica
103620	2012-06-27	7	7	\N	\N	f	1046	15728119-4	GALLARDO PEÑA, PABLA ALEJANDRA	0	2012-07-25	2012-10-23	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-07-27 00:00:00	2012-08-30 09:40:23.470445		No Especifica
103621	2012-07-26	7	7	\N	\N	f	1140	12848900-2	NÚÑEZ GONZÁLEZ, RODRIGO MARCELO	0	2012-07-25	2012-10-23	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:23.473302		No Especifica
103622	2012-05-02	7	7	\N	\N	f	927	9128825-7	RAIN PLAZA, MARÍA GLORIA	0	2012-04-26	2012-10-23	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.476539		Bilateral
103623	2012-06-26	7	7	\N	\N	f	773	7142749-8	FLORES ZÚÑIGA, ROSA ISOLINA	0	2012-06-25	2012-10-23	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:23.479572		No Especifica
103624	2012-07-26	7	7	\N	\N	f	1140	6159106-0	UMAÑA PACHECO, MARGARITA ISABEL	0	2012-07-25	2012-10-23	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:23.482588		No Especifica
103625	2012-06-26	7	7	\N	\N	f	773	5625662-8	LEIVA LEIVA, ADELA MARÍA	0	2012-06-25	2012-10-23	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:23.485619		No Especifica
103626	2012-07-30	7	7	\N	\N	f	1072	5605337-9	MUÑOZ CASTRO, RAMONA NORMA	0	2012-07-25	2012-10-23	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-07 00:00:00	2012-08-30 09:40:23.489244		Hemodiálisis 15 Años y más
103627	2012-05-02	7	7	\N	\N	f	927	4947427-K	FARÍAS ACOSTA, LINDORIZA DEL CARMEN	0	2012-04-26	2012-10-23	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.492536		Bilateral
103628	2012-05-04	7	7	\N	\N	f	925	3044445-0	PINO VALENZUELA, ROSA	0	2012-04-26	2012-10-23	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.495442		No Especifica
103629	2012-05-02	7	7	\N	\N	f	927	2681851-6	MUÑOZ MUÑOZ, GRACIELA DEL CARMEN	0	2012-04-26	2012-10-23	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.498722		Bilateral
103630	2012-05-02	7	7	\N	\N	f	927	2641318-4	TOLEDO VERA, ELENA	0	2012-04-26	2012-10-23	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.502603		Bilateral
103631	2012-06-27	7	7	\N	\N	f	1046	2477701-4	CABELLO DÍAZ, REBECA DE LAS MERCED	0	2012-07-25	2012-10-23	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-07-27 00:00:00	2012-08-30 09:40:23.505638		No Especifica
103632	2012-05-02	7	7	\N	\N	f	927	1720992-2	JARA LÓPEZ, JOSÉ ROBINSON	0	2012-04-26	2012-10-23	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.509202		Bilateral
103633	2012-08-27	7	7	\N	\N	f	954	11053768-9	OSSES CARRASCO, MARÍA ROSA	0	2012-08-24	2012-10-23	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:23.512451		No Especifica
103634	2012-08-29	7	7	\N	\N	f	954	21588745-6	MORALES , MARIA ISABEL	0	2012-08-24	2012-10-23	Colecistectomía Preventiva . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.516054		No Especifica
103635	2012-08-10	7	7	\N	\N	f	1004	23995494-4	REBOLLEDO MUÑOZ, AGUSTIN IGNACIO	0	2012-07-26	2012-10-24	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:23.519022		No Especifica
103636	2012-07-31	7	7	\N	\N	f	955	14507995-0	GUERRA VILLALOBOS, ZENAIDA YOLANDA	0	2012-07-26	2012-10-24	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-29 00:00:00	2012-08-30 09:40:23.522164		No Especifica
103637	2012-07-31	7	7	\N	\N	f	955	11400967-9	LABRA TORRES, DORIS ELIZABETH	0	2012-07-26	2012-10-24	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-29 00:00:00	2012-08-30 09:40:23.525275		No Especifica
103638	2012-06-28	7	7	\N	\N	f	773	6539082-5	FUENTEALBA ARREDONDO, OLGA GLADYS	0	2012-06-26	2012-10-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-25 00:00:00	2012-08-30 09:40:23.528389		No Especifica
103639	2012-07-06	7	7	\N	\N	f	773	6337333-8	GONZÁLEZ ARAYA, JUAN CARLOS	0	2012-06-26	2012-10-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-25 00:00:00	2012-08-30 09:40:23.531425		No Especifica
103640	2012-07-30	7	7	\N	\N	f	927	4743536-6	GONZÁLEZ ZAVALA, FRESIA ESTER	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.534582		Bilateral Igual o Inferior a 0,1
103641	2012-07-30	7	7	\N	\N	f	927	4636635-2	VOLTA , MARTA REBECA	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-07-30 00:00:00	2012-08-30 09:40:23.537881		Izquierda Igual o Inferior a 0,1
103642	2012-07-31	7	7	\N	\N	f	927	4568069-K	SALDIVIA GONZÁLEZ, SILVIA ROSA	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-23 00:00:00	2012-08-30 09:40:23.541473		Derecha Igual o Inferior a 0,1
103643	2012-07-31	7	7	\N	\N	f	1072	4530349-7	COFRÉ TORO, ENRIQUE OSVALDO	0	2012-07-26	2012-10-24	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-23 00:00:00	2012-08-30 09:40:23.544641		Hemodiálisis 15 Años y más
103644	2012-07-31	7	7	\N	\N	f	927	4436776-9	SALINAS GAJARDO, PEDRO IGNACIO	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-07-31 00:00:00	2012-08-30 09:40:23.548374		Izquierda Igual o Inferior a 0,1
103645	2012-06-28	7	7	\N	\N	f	773	4128800-0	SOTO PALACIOS, MANUEL ENRIQUE	0	2012-06-26	2012-10-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-25 00:00:00	2012-08-30 09:40:23.551356		No Especifica
103646	2012-07-31	7	7	\N	\N	f	927	3833750-5	MALDONADO REQUENA, MARÍA DEL CARMEN	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.554573		Bilateral Igual o Inferior a 0,1
103647	2012-07-30	7	7	\N	\N	f	927	3593123-6	HENRÍQUEZ MIRANDA, AÍDA MARGARITA	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.557736		Bilateral Igual o Inferior a 0,1
103648	2012-07-30	7	7	\N	\N	f	927	3323921-1	VICENCIO HENRÍQUEZ, LAURA AMELIA	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-23 00:00:00	2012-08-30 09:40:23.561103		Derecha Igual o Inferior a 0,1
103649	2012-07-31	7	7	\N	\N	f	927	3221991-8	PAVEZ NÚÑEZ, JORGE ADRIÁN	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:23.564208		Izquierda Igual o Inferior a 0,1
103650	2012-07-31	7	7	\N	\N	f	927	3057380-3	ESTRADA GONZÁLEZ, ESTANISLAO	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.567359		Bilateral Igual o Inferior a 0,1
103651	2012-07-31	7	7	\N	\N	f	927	2938369-3	ITURRIETA ORDÓÑEZ, LETICIA CONCEPCIÓN	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.571209		Izquierda Igual o Inferior a 0,1
103652	2012-07-31	7	7	\N	\N	f	927	2707182-1	MORALES CARRASCO, SERGIO RAMÓN	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.574398		Bilateral Igual o Inferior a 0,1
103653	2012-07-31	7	7	\N	\N	f	927	2624289-4	CÁCERES QUIROZ, CARMEN ELCIRA	0	2012-07-26	2012-10-24	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.577497		Bilateral Igual o Inferior a 0,1
103654	2012-07-30	7	7	\N	\N	f	927	2511157-5	TEJEDA BASAURE, ESMERALDA JULIA	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.580735		Derecha Igual o Inferior a 0,1
103655	2012-07-30	7	7	\N	\N	f	927	2456166-6	ZAVALA ORTEGA, EDMUNDO	0	2012-07-26	2012-10-24	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.583875		Izquierda Igual o Inferior a 0,1
103656	2012-07-31	7	7	\N	\N	f	927	10442231-4	FERNÁNDEZ JIMÉNEZ, CRISTIAN PATRICIO	0	2012-07-27	2012-10-25	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.587002		Derecha Igual o Inferior a 0,1
103657	2012-07-30	7	7	\N	\N	f	1046	8816261-7	URRA GAETE, SANDRA MARIBEL	0	2012-07-27	2012-10-25	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-07-30 00:00:00	2012-08-30 09:40:23.590987		No Especifica
103658	2012-07-31	7	7	\N	\N	f	968	6510397-4	MORALES MADRID, ROBERT HERIBERTO	0	2012-07-27	2012-10-25	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.594826		No Especifica
103659	2012-08-08	7	7	\N	\N	f	1072	6506604-1	GONZÁLEZ LEÓN, LUIS AMADIEL	0	2012-07-27	2012-10-25	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-10 00:00:00	2012-08-30 09:40:23.598632		Hemodiálisis 15 Años y más
103660	2012-08-07	7	7	\N	\N	f	1046	4012686-4	YÁ@EZ , MARÍA ELSA	0	2012-07-27	2012-10-25	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-08 00:00:00	2012-08-30 09:40:23.601549		No Especifica
103661	2012-08-22	7	7	\N	\N	f	1007	22018330-0	CHACANA LATORRE, KEVIN ESTIVEN	0	2012-07-27	2012-10-25	Estrabismo . {decreto nº 228}	Tratamiento Quirúrgico	2012-08-28 00:00:00	2012-08-30 09:40:23.604443		No Especifica
103662	2012-05-07	7	7	\N	\N	f	925	21981376-7	SOSA MONZON, ISNARDA	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.607938		No Especifica
103663	2012-08-09	7	7	\N	\N	f	955	12025175-9	ROJAS ARANDA, EDITH DEL CARMEN	0	2012-07-30	2012-10-29	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-09 00:00:00	2012-08-30 09:40:23.611697		No Especifica
103664	2012-08-06	7	7	\N	\N	f	968	10258788-K	CASTILLO MARTÍNEZ, MARÍA ALEJANDRA	0	2012-07-31	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-06 00:00:00	2012-08-30 09:40:23.615299		No Especifica
103665	2012-08-03	7	7	\N	\N	f	968	9490634-2	MALDONADO CIFUENTES, IRMA ROSA	0	2012-07-31	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.618683		No Especifica
103666	2012-08-08	7	7	\N	\N	f	1046	9260690-2	MORALES REYES, LUIS ALBERTO	0	2012-07-31	2012-10-29	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-08 00:00:00	2012-08-30 09:40:23.62159		No Especifica
103667	2012-08-14	7	7	\N	\N	f	1046	7911732-3	ROMÁN TRAVIESO, LUIS EDGARDO	0	2012-07-31	2012-10-29	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-14 00:00:00	2012-08-30 09:40:23.624484		No Especifica
103668	2012-07-31	7	7	\N	\N	f	968	7514979-4	LEYTON ACEVEDO, MARÍA TERESA	0	2012-07-30	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-07 00:00:00	2012-08-30 09:40:23.627575		No Especifica
103669	2012-08-01	7	7	\N	\N	f	968	6977576-4	TOLEDO MUÑOZ, SOLEDAD DEL CARMEN	0	2012-07-30	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.630652		No Especifica
103670	2012-05-03	7	7	\N	\N	f	925	6799718-2	ESCOBAR ESCOBAR, GLORIA MATILDE	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.633559		No Especifica
103671	2012-08-07	7	7	\N	\N	f	968	6726829-6	CISTERNAS VEGAS, RUBY DEL CARMEN	0	2012-07-29	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-07 00:00:00	2012-08-30 09:40:23.636461		No Especifica
103672	2012-08-03	7	7	\N	\N	f	968	6435821-9	SUÁREZ JELDES, NORA ESTHER	0	2012-07-30	2012-10-29	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.63959		No Especifica
103673	2012-05-02	7	7	\N	\N	f	925	6057481-2	FRÍAS ESQUIVEL, ROSA ZULEMA	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.642904		No Especifica
103674	2012-08-03	7	7	\N	\N	f	1140	5808801-3	ORTIZ PÉREZ, MARIANA DEL CARMEN	0	2012-07-31	2012-10-29	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:23.645758		No Especifica
103675	2012-08-01	7	7	\N	\N	f	1140	5518008-3	GALLARDO FUENTES, GLADYS GRACIELA	0	2012-07-31	2012-10-29	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.648599		No Especifica
103676	2012-08-02	7	7	\N	\N	f	927	5509728-3	PUENTE BRIONES, ROSA ELVIRA	0	2012-07-31	2012-10-29	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-02 00:00:00	2012-08-30 09:40:23.651899		Bilateral Igual o Inferior a 0,1
103677	2012-08-03	7	7	\N	\N	f	927	5495262-7	OLIVARES MARILLANCA, JULIA ROSA	0	2012-07-31	2012-10-29	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.655159		Derecha Igual o Inferior a 0,1
103678	2012-08-02	7	7	\N	\N	f	927	5252561-6	MARRO ROJO, IRMA SONIA	0	2012-07-31	2012-10-29	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.658888		Izquierda Igual o Inferior a 0,1
103679	2012-05-04	7	7	\N	\N	f	925	5016657-0	BARRIOS MARCHANT, RICARDO	0	2012-05-02	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.661815		No Especifica
103680	2012-05-04	7	7	\N	\N	f	925	4928640-6	CORTEZ MORALES, ERNESTINA	0	2012-05-02	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.664929		No Especifica
103681	2012-05-02	7	7	\N	\N	f	925	4845350-3	LEÓN CASTILLO, ALBERTO	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.66777		No Especifica
103682	2012-05-09	7	7	\N	\N	f	925	4829015-9	ESPINOZA ESPINOZA, SONIA DEL CARMEN	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.670763		No Especifica
103683	2012-08-06	7	7	\N	\N	f	1140	4729051-1	HERRERA MUÑOZ, ROSA AMELIA	0	2012-07-31	2012-10-29	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:23.67358		No Especifica
103684	2012-08-02	7	7	\N	\N	f	927	4389811-6	TRONCOSO MÉNDEZ, SILVIA DEL CARMEN	0	2012-07-31	2012-10-29	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:23.676735		Izquierda Igual o Inferior a 0,1
103685	2012-08-03	7	7	\N	\N	f	927	4255056-6	GONZÁLEZ SALINAS, JOSÉ LEOPOLDO	0	2012-07-31	2012-10-29	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:23.680026		Izquierda Igual o Inferior a 0,1
103686	2012-03-08	7	7	\N	\N	f	776	3909724-9	BERTA ESMERALDA DÍAZ RIVERA	0	2012-03-01	2012-10-29	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-03-08 00:00:00	2012-08-30 09:40:23.683173		Izquierda
103687	2012-05-02	7	7	\N	\N	f	925	3548684-4	VILLAGRA ALVEAR, ALICIA DEL CARMEN	0	2012-04-30	2012-10-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.686099		No Especifica
103688	2012-08-02	7	7	\N	\N	f	927	2208817-3	OYARZO RUIZ, MARÍA FRESIA	0	2012-07-31	2012-10-29	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-23 00:00:00	2012-08-30 09:40:23.689298		Bilateral Igual o Inferior a 0,1
103689	2012-08-03	7	7	\N	\N	f	1004	23899212-5	VÉLIZ OLIVARES, ARACELY PRICILA	0	2012-08-01	2012-10-30	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.6923		No Especifica
103690	2012-05-08	7	7	\N	\N	f	927	6904237-6	GALLARDO PINTO, LUIS FERNANDO	0	2012-05-03	2012-10-30	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.696187		Bilateral
103691	2012-08-03	7	7	\N	\N	f	968	6900309-5	REINOSO VILLAR, JANET ILSE	0	2012-08-01	2012-10-30	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.699223		No Especifica
103692	2012-08-03	7	7	\N	\N	f	968	6687940-2	VEGAS BERNAL, ROSA IRENE	0	2012-08-01	2012-10-30	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:23.702125		No Especifica
103693	2012-05-08	7	7	\N	\N	f	925	5966613-4	FLORES COLLAO, MARÍA NOEMÍ	0	2012-05-03	2012-10-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.704972		No Especifica
103694	2012-05-08	7	7	\N	\N	f	927	5842853-1	ANGELI GÓMEZ, NUBBIA ORIANA	0	2012-05-03	2012-10-30	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:23.708202		Bilateral
103695	2012-08-03	7	7	\N	\N	f	927	5762379-9	ROJAS MILLARES, ERNESTINA DEL CARMEN	0	2012-08-01	2012-10-30	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-03 00:00:00	2012-08-30 09:40:23.711517		Bilateral Igual o Inferior a 0,1
103696	2012-08-03	7	7	\N	\N	f	927	4974980-5	CASABOZA PRADO, FELICITA MERCEDES	0	2012-08-01	2012-10-30	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.715098		Bilateral Igual o Inferior a 0,1
103697	2012-08-03	7	7	\N	\N	f	927	4561923-0	GREY IBARRA, FERNANDO ENRIQUE	0	2012-08-01	2012-10-30	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-03 00:00:00	2012-08-30 09:40:23.718343		Izquierda Igual o Inferior a 0,1
103698	2012-08-07	7	7	\N	\N	f	927	4359222-K	BRIONES ARROYO, LONGINO ARMANDO	0	2012-08-01	2012-10-30	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:23.722072		Izquierda Igual o Inferior a 0,1
103699	2012-08-07	7	7	\N	\N	f	927	4341523-9	VILLALÓN CISTERNAS, MANUEL ALEJANDRO	0	2012-08-01	2012-10-30	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.725178		Derecha Igual o Inferior a 0,1
103700	2012-08-03	7	7	\N	\N	f	927	2833568-7	LE FORT DÍAZ, ANA ROSA ESTER	0	2012-08-01	2012-10-30	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.728356		Derecha Igual o Inferior a 0,1
103701	2012-05-08	7	7	\N	\N	f	925	2254316-4	CORNEJO SILVA, ELENA JOSEFINA	0	2012-05-03	2012-10-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.731341		No Especifica
103702	2012-05-08	7	7	\N	\N	f	925	2048857-3	GONZÁLEZ RODRÍGUEZ, ZOILA DEL CARMEN	0	2012-05-03	2012-10-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.734186		No Especifica
103703	2012-08-21	7	7	\N	\N	f	1072	8878498-7	BUSTOS SEPÚLVEDA, VÍCTOR EDUARDO	0	2012-08-01	2012-10-30	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-29 00:00:00	2012-08-30 09:40:23.737104		Hemodiálisis 15 Años y más
103704	2012-08-08	7	7	\N	\N	f	1072	9376646-6	FONSEA GONZÁLEZ, MARÍA ANGÉLICA	0	2012-08-02	2012-10-31	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-16 00:00:00	2012-08-30 09:40:23.740048		Hemodiálisis 15 Años y más
103705	2012-08-07	7	7	\N	\N	f	927	9028212-3	MARDONES VALENZUELA, MIRIAM DE LOS ANGELE	0	2012-08-02	2012-10-31	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.743317		Bilateral Igual o Inferior a 0,1
103706	2012-08-07	7	7	\N	\N	f	955	8914587-2	TORRES TORRES, SANDRA ANGÉLICA	0	2012-08-02	2012-10-31	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-07 00:00:00	2012-08-30 09:40:23.746247		No Especifica
103707	2012-08-07	7	7	\N	\N	f	927	8150382-6	VÉLIZ MARTÍNEZ, JAIME PATRICIO	0	2012-08-02	2012-10-31	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.749447		Bilateral Igual o Inferior a 0,1
103708	2012-08-07	7	7	\N	\N	f	968	7711244-8	VERA SALAZAR, ROSA MARÍA	0	2012-08-02	2012-10-31	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:23.752336		No Especifica
103709	2012-08-07	7	7	\N	\N	f	927	6796789-5	GUAJARDO NÚÑEZ, LAURA ALEJANDRA DEL	0	2012-08-02	2012-10-31	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.755891		Bilateral Igual o Inferior a 0,1
103710	2012-08-07	7	7	\N	\N	f	927	6553754-0	LODIS GARRIDO, PEDRO ÁLVARO	0	2012-08-02	2012-10-31	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:23.759058		Izquierda Igual o Inferior a 0,1
103711	2012-08-08	7	7	\N	\N	f	968	5881961-1	ROJAS CARVAJAL, LUCRECIA DE LAS MERC	0	2012-08-02	2012-10-31	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.761958		No Especifica
103712	2012-08-07	7	7	\N	\N	f	968	5750170-7	VILCHES RIQUELME, ENRIQUE ERNESTO	0	2012-08-02	2012-10-31	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.764824		No Especifica
103713	2012-05-07	7	7	\N	\N	f	925	5454244-5	SANDOVAL ANTIVILO, GLADYS DEL CARMEN	0	2012-05-04	2012-10-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.768175		No Especifica
103714	2012-05-07	7	7	\N	\N	f	925	4940429-8	ALLENDE DÍAZ, ANGELA INÉS	0	2012-05-04	2012-10-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.771079		No Especifica
103715	2012-05-07	7	7	\N	\N	f	925	4918064-0	COLLAO PACHECO, HILDA DE LAS MERCEDE	0	2012-05-04	2012-10-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.773898		No Especifica
103716	2012-08-07	7	7	\N	\N	f	927	4781578-9	ROMERO PIMENTEL, GUSTAVO ALBERTO	0	2012-08-02	2012-10-31	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:23.777055		Derecha Igual o Inferior a 0,1
103717	2012-08-13	7	7	\N	\N	f	1072	4639637-5	CARVAJAL SANTIBÁÑEZ, LUZ NOHEMI	0	2012-08-02	2012-10-31	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-23 00:00:00	2012-08-30 09:40:23.780186		Hemodiálisis 15 Años y más
103718	2012-08-07	7	7	\N	\N	f	968	3896641-3	BUSTOS NOVA, AGUSTINA DEL CARMEN	0	2012-08-02	2012-10-31	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.783045		No Especifica
103719	2012-08-07	7	7	\N	\N	f	927	3689036-3	IRARRÁZABAL FARÍAS, HÉCTOR RAFAEL DE LOS	0	2012-08-02	2012-10-31	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.786183		Bilateral Igual o Inferior a 0,1
103720	2012-05-08	7	7	\N	\N	f	925	3196502-0	ARISMENDI HERRERA, EDITH ADELA	0	2012-05-04	2012-10-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.789105		No Especifica
103721	2012-08-06	7	7	\N	\N	f	1072	3105278-5	URZÚA REVECO, JULIO ADOLFO	0	2012-08-02	2012-10-31	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-06 00:00:00	2012-08-30 09:40:23.792011		Hemodiálisis 15 Años y más
103722	2012-08-07	7	7	\N	\N	f	927	2863905-8	DE LA FUENTE NIELSEN, ORLANDO	0	2012-08-02	2012-10-31	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.795999		Bilateral Igual o Inferior a 0,1
103723	2012-05-07	7	7	\N	\N	f	925	2579381-1	MOYANO IBACETA, GABRIEL GUILLERMO	0	2012-05-04	2012-10-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:23.799231		No Especifica
103724	2012-08-08	7	7	\N	\N	f	1007	23739683-9	ORTIZ BARRAZA, FRANCISCA SOLEDAD	0	2012-08-03	2012-11-05	Estrabismo . {decreto nº 228}	Tratamiento Quirúrgico	2012-08-29 00:00:00	2012-08-30 09:40:23.802326		No Especifica
103725	2012-08-08	7	7	\N	\N	f	1004	23305723-1	ROA PÉREZ, BELÉN JAZMÍN	0	2012-08-06	2012-11-05	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.805218		No Especifica
103726	2012-08-08	7	7	\N	\N	f	1140	15101969-2	ARREDONDO VALDOVINOS, KATHERINE ANDREA	0	2012-08-07	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:23.808208		No Especifica
103727	2012-08-09	7	7	\N	\N	f	955	13194941-3	MONTENEGRO SOTO, YOLANDA DEL CARMEN	0	2012-08-07	2012-11-05	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-09 00:00:00	2012-08-30 09:40:23.811109		No Especifica
103728	2012-08-16	7	7	\N	\N	f	968	12824095-0	ALDAY VEGA, ANGELO ALBERTO	0	2012-08-06	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:23.813939		No Especifica
103729	2012-08-14	7	7	\N	\N	f	955	10908634-7	RAMÍREZ BELTRÁN, ISABEL MARGARITA	0	2012-08-07	2012-11-05	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-14 00:00:00	2012-08-30 09:40:23.817157		No Especifica
103730	2012-08-08	7	7	\N	\N	f	968	9597537-2	ASTUDILLO ORTIZ, PATRICIA DEL PILAR	0	2012-08-06	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.820463		No Especifica
103731	2012-08-08	7	7	\N	\N	f	1140	8638115-K	OVANDO FIGUEROA, VIVIANA JOSEFINA	0	2012-08-04	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:23.823291		No Especifica
103732	2012-08-10	7	7	\N	\N	f	1046	7919013-6	BENAVIDES FERNÁNDEZ, GUILLERMINA DEL CARM	0	2012-08-07	2012-11-05	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-10 00:00:00	2012-08-30 09:40:23.826271		No Especifica
103733	2012-08-09	7	7	\N	\N	f	927	7439688-7	CHEAUSU OSSES, ROSA AMELIA	0	2012-08-03	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.829794		Derecha Igual o Inferior a 0,1
103734	2012-08-08	7	7	\N	\N	f	968	7423086-5	COFRÉ MONTENEGRO, ALEJANDRO LEONEL	0	2012-08-06	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.832819		No Especifica
103735	2012-08-08	7	7	\N	\N	f	1140	7299983-5	OLIVARES PERALTA, NORBERTO CAUPOLICÁN	0	2012-08-03	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:23.835837		No Especifica
103736	2012-07-10	7	7	\N	\N	f	773	6949238-K	ARAVENA ESPINOSA, MARÍA DEL CARMEN	0	2012-07-06	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-23 00:00:00	2012-08-30 09:40:23.839617		No Especifica
103737	2012-08-08	7	7	\N	\N	f	968	6587022-3	NIETO CORONADO, JUANA ROSA	0	2012-08-06	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-13 00:00:00	2012-08-30 09:40:23.843048		No Especifica
103738	2012-08-10	7	7	\N	\N	f	968	6133893-4	ROJO OLIVARES, RAMÓN LINO	0	2012-08-07	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:23.846331		No Especifica
103739	2012-08-07	7	7	\N	\N	f	968	5762541-4	CISTERNA FERNÁNDEZ, ALFONSO OSVALDO	0	2012-08-06	2012-11-05	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:23.849684		No Especifica
103740	2012-05-15	7	7	\N	\N	f	1043	5641800-8	LAZCANO VALENZUELA, MARIO ENRIQUE	0	2012-05-07	2012-11-05	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-13 00:00:00	2012-08-30 09:40:23.853175		Retención Urinaria Aguda Repetida
103741	2012-05-10	7	7	\N	\N	f	925	5506258-7	GONZÁLEZ AGUIRRE, RODRIGO ORLANDO	0	2012-05-09	2012-11-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:23.856761		No Especifica
103742	2012-08-10	7	7	\N	\N	f	927	5505178-K	ALTAMIRANO ROJAS, TERESA NORMA	0	2012-08-07	2012-11-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-10 00:00:00	2012-08-30 09:40:23.861297		Bilateral Igual o Inferior a 0,1
103743	2012-07-10	7	7	\N	\N	f	773	5172393-7	OLIVARES INOSTROZA, RAFAEL	0	2012-07-06	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:23.864702		No Especifica
103744	2012-08-08	7	7	\N	\N	f	927	4997776-K	GONZÁLEZ SÁNCHEZ, MARÍA VICTORINA	0	2012-08-06	2012-11-05	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:23.868104		Izquierda Igual o Inferior a 0,1
103745	2012-08-08	7	7	\N	\N	f	1140	4957391-K	SANTELICES CÉSPEDES, ADELA NINETTE	0	2012-08-07	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.871007		No Especifica
103746	2012-08-09	7	7	\N	\N	f	1072	4912355-8	CISTERNAS VARGAS, NORA DEL CARMEN	0	2012-08-07	2012-11-05	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-28 00:00:00	2012-08-30 09:40:23.873984		Hemodiálisis 15 Años y más
103747	2012-08-10	7	7	\N	\N	f	927	4637568-8	ZEPEDA PIZARRO, LUISA ALBERTINA	0	2012-08-07	2012-11-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-10 00:00:00	2012-08-30 09:40:23.877144		Bilateral Igual o Inferior a 0,1
103748	2012-07-10	7	7	\N	\N	f	773	4630065-3	SASSO AGUIRRE, PATRICIA DEL CARMEN	0	2012-07-05	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:23.880144		No Especifica
103749	2012-06-09	7	7	\N	\N	f	773	4519090-0	VERGARA VIDAL, CARLOS ENRIQUE	0	2012-07-05	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:23.883028		No Especifica
103750	2012-03-13	7	7	\N	\N	f	776	4478520-K	GLADYS DE LAS MARIAS ARROYO CEBALLOS	0	2012-03-09	2012-11-05	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-16 00:00:00	2012-08-30 09:40:23.886069		Izquierda
103751	2012-08-10	7	7	\N	\N	f	927	4299418-9	NÚÑEZ DE LA CRUZ, GUILLERMO JULIO	0	2012-08-07	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-10 00:00:00	2012-08-30 09:40:23.889464		Derecha Igual o Inferior a 0,1
103752	2012-06-09	7	7	\N	\N	f	773	4261841-1	MUÑOZ CLAVERO, CORINA DELFINA	0	2012-07-06	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:23.892837		No Especifica
103753	2012-05-09	7	7	\N	\N	f	925	4250649-4	MÁRQUEZ MIRANDA, ROSA SONIA	0	2012-05-08	2012-11-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.895713		No Especifica
103754	2012-03-13	7	7	\N	\N	f	776	4160913-3	CARLOS VÍCTOR ENRIQU STEFFENS MÜLLER	0	2012-03-09	2012-11-05	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-03-13 00:00:00	2012-08-30 09:40:23.898737		Izquierda
103755	2012-03-14	7	7	\N	\N	f	776	4074598-K	BELISARIO DAVID QUINTANILLA PALMA	0	2012-03-07	2012-11-05	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-20 00:00:00	2012-08-30 09:40:23.901743		Izquierda
103756	2012-08-08	7	7	\N	\N	f	1140	4066064-K	ESPINOZA GARRIDO, NARCISO EDUARDO	0	2012-08-07	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.904575		No Especifica
103757	2012-03-14	7	7	\N	\N	f	776	3982101-K	MARÍA CATALINA ESPINOZA VILCHES	0	2012-03-07	2012-11-05	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-03-14 00:00:00	2012-08-30 09:40:23.907995		Derecha
103758	2012-05-17	7	7	\N	\N	f	925	3855258-9	MÉNDEZ ÁVILA, ROSA DEL CARMEN	0	2012-05-07	2012-11-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.91094		No Especifica
103759	2012-05-10	7	7	\N	\N	f	925	3673074-9	CABRERA CANTILLANO, MARTINA ALICIA	0	2012-05-07	2012-11-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.913813		No Especifica
103760	2012-08-10	7	7	\N	\N	f	1140	3640194-K	JARA ILABACA, ISAVIRA	0	2012-08-06	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:23.916627		No Especifica
103761	2012-08-08	7	7	\N	\N	f	927	3353928-2	CASTRO CASTRO, SYLVIA	0	2012-08-03	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-24 00:00:00	2012-08-30 09:40:23.919979		Derecha Igual o Inferior a 0,1
103762	2012-06-09	7	7	\N	\N	f	773	3212529-8	RIVADENEIRA GONZÁLEZ, HÉCTOR ERNESTO	0	2012-07-05	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:23.922843		No Especifica
103763	2012-08-06	7	7	\N	\N	f	1140	3192712-9	NÚÑEZ SALFATE, VICENTE DEL CARMEN	0	2012-08-03	2012-11-05	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.925733		No Especifica
103764	2012-05-11	7	7	\N	\N	f	1155	3139025-7	MADARIAGA , RAQUEL DEL CARMEN	0	2012-05-08	2012-11-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.928948		No Especifica
103765	2012-06-19	7	7	\N	\N	f	927	2943141-8	FIGUEROA CAMPOS, ELÍAS SEGUNDO	0	2012-05-08	2012-11-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:23.932213		Bilateral
103766	2012-08-10	7	7	\N	\N	f	927	2932299-6	JAÑA ROMERO, MARÍA JERMANCIA	0	2012-08-07	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-10 00:00:00	2012-08-30 09:40:23.935376		Derecha Igual o Inferior a 0,1
103767	2012-03-15	7	7	\N	\N	f	776	2864464-7	HILDA ROSA DEL CARME SÁNCHEZ	0	2012-03-08	2012-11-05	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-03-15 00:00:00	2012-08-30 09:40:23.938325		Derecha
103768	2012-08-10	7	7	\N	\N	f	1046	2854668-8	ORTIZ VERGARA, GUACOLDA DEL CARMEN	0	2012-08-07	2012-11-05	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-21 00:00:00	2012-08-30 09:40:23.941337		No Especifica
103769	2012-05-11	7	7	\N	\N	f	927	2828420-9	BARAHONA NÚÑEZ, ROSA DEL CARMEN	0	2012-05-09	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-06-13 00:00:00	2012-08-30 09:40:23.945569		Derecha
103770	2012-08-10	7	7	\N	\N	f	927	2825020-7	ABARCA LEIVA, RAQUEL FILOMENA	0	2012-08-07	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-10 00:00:00	2012-08-30 09:40:23.950385		Derecha Igual o Inferior a 0,1
103771	2012-05-11	7	7	\N	\N	f	925	2586876-5	STRAPPA FAUST, SILVIA ADRIANA GRISA	0	2012-05-09	2012-11-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:23.953843		No Especifica
103772	2012-08-10	7	7	\N	\N	f	927	2167929-1	PÉREZ , RITA DEL CARMEN	0	2012-08-07	2012-11-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-10 00:00:00	2012-08-30 09:40:23.957397		Bilateral Igual o Inferior a 0,1
103773	2012-05-14	7	7	\N	\N	f	927	2095371-3	PINOCHET ÁLVAREZ, JORGE ANTONIO	0	2012-05-09	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:23.96175		Derecha
103774	2012-07-11	7	7	\N	\N	f	773	2066977-2	TAPIA , GEORGINA EMILIA	0	2012-07-06	2012-11-05	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:23.965788		No Especifica
103775	2012-08-08	7	7	\N	\N	f	927	1105745-4	CHACÓN PIZARRO, ILDA SOFÍA	0	2012-08-06	2012-11-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:23.970239		Bilateral Igual o Inferior a 0,1
103776	2012-08-20	7	7	\N	\N	f	1072	9810083-0	GONZÁLEZ CASTILLO, MARÍA EUGENIA	0	2012-08-04	2012-11-05	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-28 00:00:00	2012-08-30 09:40:23.973676		Hemodiálisis 15 Años y más
103777	2012-08-23	7	7	\N	\N	f	927	3574395-2	NÚÑEZ ZÚÑIGA, MATILDE	0	2012-08-07	2012-11-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-23 00:00:00	2012-08-30 09:40:23.977252		Derecha Igual o Inferior a 0,1
103778	2012-08-10	7	7	\N	\N	f	968	10472421-3	GALLARDO GALLARDO, CÉSAR MAXIMILIANO	0	2012-08-08	2012-11-06	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:23.980996		No Especifica
103779	2012-08-13	7	7	\N	\N	f	968	7820005-7	RIVEROS DÍAZ, SYLVIA DEL CARMEN	0	2012-08-08	2012-11-06	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:23.984266		No Especifica
103780	2012-08-10	7	7	\N	\N	f	1140	6845197-3	HERRERA ENCINA, MARÍA IGNACIA DE LA	0	2012-08-08	2012-11-06	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:23.988261		No Especifica
103781	2012-08-13	7	7	\N	\N	f	927	5949903-3	CÁRDENAS ESCUDERO, CLICE DE MARÍA	0	2012-08-08	2012-11-06	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-13 00:00:00	2012-08-30 09:40:23.99199		Bilateral Igual o Inferior a 0,1
103782	2012-08-07	7	7	\N	\N	f	850	5353892-4	AVILÉS PÉREZ, CARLOS HUMBERTO	0	2012-07-09	2012-11-06	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-07 00:00:00	2012-08-30 09:40:23.9958		No Especifica
103783	2012-08-13	7	7	\N	\N	f	968	4894665-8	AGUIRRE SAN MARTÍN, HÉCTOR DEL CARMEN	0	2012-08-08	2012-11-06	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:23.999328		No Especifica
103784	2012-07-11	7	7	\N	\N	f	773	4743402-5	VERA LORCA, INÉS OBDULIA	0	2012-07-09	2012-11-06	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.002571		No Especifica
103785	2012-06-07	7	7	\N	\N	f	1155	4733178-1	ROMÁN GONZÁLEZ, JUANA LUISA	0	2012-05-10	2012-11-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.005874		No Especifica
103786	2012-08-10	7	7	\N	\N	f	968	4350543-2	THIZNAU QUIJADA, HUGO ROBERTO	0	2012-08-08	2012-11-06	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:24.009225		No Especifica
103787	2012-08-10	7	7	\N	\N	f	1140	2860953-1	ORTIZ GODOY, NORMA ELIANA	0	2012-08-08	2012-11-06	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:24.012511		No Especifica
103788	2012-08-17	7	7	\N	\N	f	968	4142082-0	ROJAS HENRÍQUEZ, LAUTARO ADOLFO	0	2012-08-09	2012-11-07	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:24.01573		No Especifica
103789	2012-08-13	7	7	\N	\N	f	1004	23922703-1	RUIZ ESCOBAR, ANTAY	0	2012-08-09	2012-11-07	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.019153		No Especifica
103790	2012-08-13	7	7	\N	\N	f	955	11824617-9	BRUNET DÍAZ, PAOLA JEANNETTE	0	2012-08-09	2012-11-07	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-13 00:00:00	2012-08-30 09:40:24.022547		No Especifica
103791	2012-08-13	7	7	\N	\N	f	955	11547417-0	IBACACHE PONCE, CLAUDINA LEONOR	0	2012-08-09	2012-11-07	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-13 00:00:00	2012-08-30 09:40:24.025968		No Especifica
103792	2012-08-16	7	7	\N	\N	f	955	10434240-K	DURÁN OLAVE, EVELYN DEL CARMEN	0	2012-08-09	2012-11-07	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-16 00:00:00	2012-08-30 09:40:24.029367		No Especifica
103793	2012-08-13	7	7	\N	\N	f	955	9376389-0	CONTRERAS CERDA, MAX YERKO	0	2012-08-09	2012-11-07	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-13 00:00:00	2012-08-30 09:40:24.032344		No Especifica
103794	2012-08-13	7	7	\N	\N	f	927	8285496-7	ROJAS SANTANA, HÉCTOR EDUARDO	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:24.035501		Izquierda Igual o Inferior a 0,1
103795	2012-08-13	7	7	\N	\N	f	1072	7064912-8	LASTRA CHACANA, OSCAR PATRICIO	0	2012-08-09	2012-11-07	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-13 00:00:00	2012-08-30 09:40:24.038482		Hemodiálisis 15 Años y más
103796	2012-05-16	7	7	\N	\N	f	925	6553651-K	VALENZUELA BRAVO, JUANA ANGÉLICA	0	2012-05-11	2012-11-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.041592		No Especifica
103797	2012-08-13	7	7	\N	\N	f	927	5460599-4	LOBOS VALDIVIA, GUILLERMINA DEL CARM	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-13 00:00:00	2012-08-30 09:40:24.044767		Derecha Igual o Inferior a 0,1
103798	2012-07-12	7	7	\N	\N	f	773	5180127-K	VERGARA SANTIBÁÑEZ, HÉCTOR MIGUEL	0	2012-07-10	2012-11-07	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.047627		No Especifica
103799	2012-08-13	7	7	\N	\N	f	968	5173097-6	QUIROZ ÁLVAREZ, LUIS SERGIO	0	2012-08-09	2012-11-07	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.051172		No Especifica
103800	2012-08-14	7	7	\N	\N	f	968	4837713-0	DE RODT ARREDONDO, CARLOS SEGUNDO	0	2012-08-09	2012-11-07	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-21 00:00:00	2012-08-30 09:40:24.053999		No Especifica
103801	2012-07-13	7	7	\N	\N	f	850	4596052-8	LÓPEZ ÁLVAREZ, JESÚS EMILIANO DEL C	0	2012-07-10	2012-11-07	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:24.056984		No Especifica
103802	2012-05-15	7	7	\N	\N	f	925	4542079-5	CÓRDOVA QUIROGA, OLGA ESTER	0	2012-05-11	2012-11-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:24.06001		No Especifica
103803	2012-08-13	7	7	\N	\N	f	927	4420620-K	PARGA PAVEZ, RAQUEL	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:24.063304		Izquierda Igual o Inferior a 0,1
103804	2012-08-13	7	7	\N	\N	f	927	4316711-1	GUTIÉRREZ BUSTOS, CATALINA AMELIA	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:24.066441		Izquierda Igual o Inferior a 0,1
103805	2012-05-16	7	7	\N	\N	f	1155	4289424-9	ARDILES APROSIO, NORA LILIANA	0	2012-05-11	2012-11-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.069401		No Especifica
103806	2012-08-14	7	7	\N	\N	f	927	3917040-K	FERNÁNDEZ MORALES, SONIA ROSA	0	2012-08-09	2012-11-07	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:24.072586		Bilateral Igual o Inferior a 0,1
103807	2012-08-13	7	7	\N	\N	f	927	3776437-K	DURÁN , HUGO ANTONIO	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-13 00:00:00	2012-08-30 09:40:24.075701		Derecha Igual o Inferior a 0,1
103808	2012-08-13	7	7	\N	\N	f	927	3612071-1	VERGARA JORQUERA, CARLOS ANDRÉS	0	2012-08-09	2012-11-07	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-13 00:00:00	2012-08-30 09:40:24.078885		Bilateral Igual o Inferior a 0,1
103809	2012-08-13	7	7	\N	\N	f	927	3600231-K	ARREDONDO CORTÉS, JOEL	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:24.082485		Izquierda Igual o Inferior a 0,1
103810	2012-05-15	7	7	\N	\N	f	1043	3589787-9	MOLINA LAZO, GUILLERMO PEDRO	0	2012-05-11	2012-11-07	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-05-15 00:00:00	2012-08-30 09:40:24.085542		Retención Urinaria Aguda Repetida
103811	2012-08-13	7	7	\N	\N	f	968	3467843-K	URRA , FRESIA	0	2012-08-09	2012-11-07	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.08893		No Especifica
103812	2012-08-13	7	7	\N	\N	f	927	3334485-6	CHÁVEZ , JOSÉ JUVENAL	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-13 00:00:00	2012-08-30 09:40:24.092233		Derecha Igual o Inferior a 0,1
103813	2012-08-14	7	7	\N	\N	f	927	2881938-2	RUBIÑO RIST, CARLOS	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:24.09543		Derecha Igual o Inferior a 0,1
103814	2012-08-20	7	7	\N	\N	f	1046	4313415-9	UMAÑA , JOSÉ	0	2012-08-09	2012-11-07	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.098393		No Especifica
103815	2012-08-29	7	7	\N	\N	f	927	3749140-3	NÚÑEZ GONZÁLEZ, SILVIA YOLANDA	0	2012-08-09	2012-11-07	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.101595		Izquierda Igual o Inferior a 0,1
103816	2012-08-14	7	7	\N	\N	f	1007	23300055-8	SALINAS FUENTES, DIEGO ANDRÉS	0	2012-08-10	2012-11-08	Estrabismo . {decreto nº 228}	Tratamiento Quirúrgico	2012-08-28 00:00:00	2012-08-30 09:40:24.1045		No Especifica
103817	2012-08-14	7	7	\N	\N	f	927	12032328-8	MACHUCA SEPÚLVEDA, ELSA CONCEPCIÓN	0	2012-08-10	2012-11-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:24.107712		Bilateral Igual o Inferior a 0,1
103818	2012-08-16	7	7	\N	\N	f	927	11621310-9	TORDECILLA TRONCOSO, JUAN GREGORIO	0	2012-08-10	2012-11-08	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-16 00:00:00	2012-08-30 09:40:24.111521		Izquierda Igual o Inferior a 0,1
103819	2012-08-16	7	7	\N	\N	f	927	9554706-0	GARCÍA HERRERA, LUCÍA MARLENE	0	2012-08-10	2012-11-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-16 00:00:00	2012-08-30 09:40:24.114762		Derecha Igual o Inferior a 0,1
103820	2012-08-13	7	7	\N	\N	f	1140	7115386-K	PÉREZ HERRERA, NELSON NIBALDO	0	2012-08-10	2012-11-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:24.117658		No Especifica
103821	2012-07-20	7	7	\N	\N	f	850	6725804-5	VERA HIDALGO, ALBERTO RENÉ	0	2012-07-11	2012-11-08	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-07-20 00:00:00	2012-08-30 09:40:24.120634		No Especifica
103822	2012-08-14	7	7	\N	\N	f	968	6631301-8	PÁEZ SEPÚLVEDA, FRESIA DEL CARMEN	0	2012-08-10	2012-11-08	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-14 00:00:00	2012-08-30 09:40:24.123597		No Especifica
103823	2012-07-12	7	7	\N	\N	f	773	6090647-5	HERRERA SEGURA, MARÍA CRISTINA	0	2012-07-11	2012-11-08	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.126558		No Especifica
103824	2012-07-17	7	7	\N	\N	f	773	6063195-6	ZÚÑIGA SOLÍS, LAURA ROSA	0	2012-07-11	2012-11-08	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:24.130699		No Especifica
103825	2012-08-14	7	7	\N	\N	f	1140	5696812-1	MAUNA GARRIDO, FRESIA DEL CARMEN	0	2012-08-10	2012-11-08	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.134481		No Especifica
103826	2012-08-16	7	7	\N	\N	f	927	5522556-7	VALENZUELA VALENZUELA, MARÍA DE LA CRUZ	0	2012-08-10	2012-11-08	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-16 00:00:00	2012-08-30 09:40:24.138474		Izquierda Igual o Inferior a 0,1
103827	2012-07-12	7	7	\N	\N	f	773	5366808-9	CALDERÓN MUÑOZ, MARÍA INÉS	0	2012-07-11	2012-11-08	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.141689		No Especifica
103828	2012-08-16	7	7	\N	\N	f	968	4926286-8	FERREIRA VEGA, TIRSA ELIANA	0	2012-08-10	2012-11-08	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.145052		No Especifica
103829	2012-05-16	7	7	\N	\N	f	1155	4541638-0	GUZMÁN MILESI, MARÍA ANGÉLICA	0	2012-05-12	2012-11-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.148803		No Especifica
103830	2012-08-16	7	7	\N	\N	f	927	4287687-9	BALBONTÍN HIERRO, SONIA CLARA DEL PILA	0	2012-08-10	2012-11-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-16 00:00:00	2012-08-30 09:40:24.152948		Derecha Igual o Inferior a 0,1
103831	2012-08-16	7	7	\N	\N	f	927	2819719-5	ROJAS TORRES, ROSA	0	2012-08-10	2012-11-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-16 00:00:00	2012-08-30 09:40:24.157776		Bilateral Igual o Inferior a 0,1
103832	2012-07-23	7	7	\N	\N	f	773	8752726-3	TAPIA FERNÁNDEZ, MARÍA ISABEL	0	2012-07-12	2012-11-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.161225		No Especifica
103833	2012-07-17	7	7	\N	\N	f	773	3377937-2	TAPIA , ANGEL CUSTODIO	0	2012-07-12	2012-11-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.16468		No Especifica
103834	2012-07-13	7	7	\N	\N	f	773	2782710-1	LEIVA , ELSA SUSANA	0	2012-07-12	2012-11-09	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.168181		No Especifica
103835	2012-08-17	7	7	\N	\N	f	1140	9593300-9	MENAY GAMBOA, EUGENIA DE LOURDES	0	2012-08-14	2012-11-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.171522		No Especifica
103836	2012-08-17	7	7	\N	\N	f	1004	23318826-3	RIVERA NÚÑEZ, HANDRUS DEHAN	0	2012-08-13	2012-11-12	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.174895		No Especifica
103837	2012-07-23	7	7	\N	\N	f	773	10298802-7	BÓRQUEZ PONCE, MARÍA ANGÉLICA	0	2012-07-13	2012-11-12	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.17832		No Especifica
103838	2012-08-16	7	7	\N	\N	f	1072	9593626-1	REBUSNANTE PIZARRO, MEDELIS MARGARITA	0	2012-08-12	2012-11-12	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-28 00:00:00	2012-08-30 09:40:24.182121		Hemodiálisis 15 Años y más
103839	2012-08-16	7	7	\N	\N	f	822	9228180-9	GALLARDO GONZÁLEZ, INÉS ANGÉLICA	0	2012-08-14	2012-11-12	Cáncer de Mama Izquierda {decreto nº 228}	Control Seguimiento Mama Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:24.185603		Izquierda
103840	2012-08-14	7	7	\N	\N	f	1140	7541011-5	VALENZUELA SANDOVAL, JOSÉ IGNACIO	0	2012-08-13	2012-11-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:24.18884		No Especifica
103841	2012-05-16	7	7	\N	\N	f	1155	6001561-9	HERRERA MUÑOZ, JUANA ROSA	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.192116		No Especifica
103842	2012-05-16	7	7	\N	\N	f	925	5918993-K	ROMO SILVA, CARLOS ANTONIO	0	2012-05-15	2012-11-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.195501		No Especifica
103843	2012-05-17	7	7	\N	\N	f	1155	5881965-4	OLIVOS VERA, MARTA GUMERCINDA	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.198815		No Especifica
103844	2012-08-03	7	7	\N	\N	f	773	5842833-7	MOREL PIZARRO, JUAN MANUEL	0	2012-07-15	2012-11-12	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:24.202041		No Especifica
103845	2012-07-20	7	7	\N	\N	f	773	5674587-4	ROMERO FREZ, LUIS ALBERTO	0	2012-07-13	2012-11-12	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.20531		No Especifica
103846	2012-07-25	7	7	\N	\N	f	773	5433940-2	GONZÁLEZ COLLAO, ELISEO SEGUNDO	0	2012-07-13	2012-11-12	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-07-30 00:00:00	2012-08-30 09:40:24.208749		No Especifica
103847	2012-05-16	7	7	\N	\N	f	1155	5372376-4	CERPA CAVIERES, ROSA	0	2012-05-15	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.211746		No Especifica
103848	2012-05-15	7	7	\N	\N	f	1043	5195709-1	JORQUERA GONZÁLEZ, RUBÉN JOSÉ	0	2012-05-14	2012-11-12	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-05-15 00:00:00	2012-08-30 09:40:24.215024		Retención Urinaria Aguda Repetida
103849	2012-05-16	7	7	\N	\N	f	1155	5186378-K	MANRÍQUEZ RIVAS, JORGE JOSÉ	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.218004		No Especifica
103850	2012-03-26	7	7	\N	\N	f	776	4879419-K	PATRICIO AUGUSTO GAETE GONZÁLEZ	0	2012-03-15	2012-11-12	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-14 00:00:00	2012-08-30 09:40:24.221207		Derecha
103851	2012-05-31	7	7	\N	\N	f	1043	4848985-0	FUENTES MÁRQUEZ, LUIS HUMBERTO	0	2012-05-16	2012-11-12	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-05-31 00:00:00	2012-08-30 09:40:24.224129		Retención Urinaria Aguda Repetida
103852	2012-05-17	7	7	\N	\N	f	1043	4600831-6	RUZ VILCHES, JOSÉ MIGUEL	0	2012-05-14	2012-11-12	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-05-17 00:00:00	2012-08-30 09:40:24.22702		Retención Urinaria Aguda Repetida
103853	2012-05-18	7	7	\N	\N	f	1155	4451001-4	BUSTAMANTE RAMÍREZ, MARTA OLIVIA	0	2012-05-15	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.229949		No Especifica
103854	2012-03-20	7	7	\N	\N	f	776	4269467-3	OTILIA DEL CARMEN VILLARROEL NÚÑEZ	0	2012-03-16	2012-11-12	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-03-20 00:00:00	2012-08-30 09:40:24.232842		Izquierda
103855	2012-05-25	7	7	\N	\N	f	1155	4261861-6	GÓMEZ GÓMEZ, ELISA DEL ROSARIO	0	2012-05-15	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.235676		No Especifica
103856	2012-05-18	7	7	\N	\N	f	1155	4214032-5	GUERRA MOLINA, LUISA ADRIANA	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.238543		No Especifica
103857	2012-05-22	7	7	\N	\N	f	1155	4202957-2	GARCÉS KRAVETZ, CORINA ROSA	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.241388		No Especifica
103858	2012-05-17	7	7	\N	\N	f	1155	4079976-1	SEPÚLVEDA VALENZUELA, SILVIA OLGA	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.244289		No Especifica
103859	2012-08-16	7	7	\N	\N	f	1140	4079590-1	OLATE ALARCÓN, LAURA GUACOLDA	0	2012-08-14	2012-11-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.247097		No Especifica
103860	2012-05-17	7	7	\N	\N	f	1155	3989607-9	CORNEJO , ISOLINA DEL CARMEN	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.250051		No Especifica
103861	2012-05-23	7	7	\N	\N	f	1155	3756446-K	DONOSO DAVARTZ, ELIANA DEL CARMEN	0	2012-05-15	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.253468		No Especifica
103862	2012-05-18	7	7	\N	\N	f	1155	3730047-0	DÍAZ CONTRERAS, FRANCISCA JAVIERA	0	2012-05-16	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.25628		No Especifica
103863	2012-08-16	7	7	\N	\N	f	822	3723286-6	SOTO GODOY, ADRIANA DEL CARMEN	0	2012-08-14	2012-11-12	Cáncer de Mama Derecha {decreto nº 228}	Control Seguimiento Mama Derecha	2012-08-16 00:00:00	2012-08-30 09:40:24.259223		Derecha
103864	2012-05-22	7	7	\N	\N	f	925	3527123-6	CARROZA LUCERO, JUANA HORTENSIA	0	2012-05-15	2012-11-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.26226		No Especifica
103865	2012-05-18	7	7	\N	\N	f	1155	3183348-5	CUADRA VÁSQUEZ, DOMITILA LASTENIA DE	0	2012-05-15	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.265101		No Especifica
103866	2012-05-15	7	7	\N	\N	f	1155	2771475-7	CARO SALAZAR, HERNÁN	0	2012-05-14	2012-11-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.267898		No Especifica
103867	2012-08-20	7	7	\N	\N	f	927	2641735-K	VERA VERGARA, LUIS ALBERTO	0	2012-08-14	2012-11-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.271556		Bilateral Igual o Inferior a 0,1
103868	2012-08-20	7	7	\N	\N	f	927	3764521-4	VIVANCO , ELENA MARIA GRACIELA	0	2012-08-13	2012-11-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.27479		Bilateral Igual o Inferior a 0,1
103869	2012-08-20	7	7	\N	\N	f	927	6762475-0	MOLINA LABRA, NORMA DEL CARMEN	0	2012-08-14	2012-11-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.277947		Bilateral Igual o Inferior a 0,1
103870	2012-08-20	7	7	\N	\N	f	927	3900108-K	ALALUF PESSA, ELISA	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:24.28134		Derecha Igual o Inferior a 0,1
103871	2012-08-20	7	7	\N	\N	f	927	4004630-5	CARRASCO ROJAS, JUANA ROSA	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:24.284957		Derecha Igual o Inferior a 0,1
103872	2012-08-20	7	7	\N	\N	f	927	4482622-4	DONOSO PIÑA, DIEGO ANTONIO	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:24.288649		Derecha Igual o Inferior a 0,1
103873	2012-08-20	7	7	\N	\N	f	927	3099652-6	VARGAS CATALDO, ANÍBAL DEL CARMEN	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:24.291915		Izquierda Igual o Inferior a 0,1
103874	2012-08-20	7	7	\N	\N	f	927	4053138-6	VILLALOBOS MOLINA, HONORIO DEL CARMEN	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:24.295059		Izquierda Igual o Inferior a 0,1
103875	2012-08-20	7	7	\N	\N	f	927	5616557-6	FREIRE DONOSO, GUILLERMO DEL TRÁNSI	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:24.298196		Izquierda Igual o Inferior a 0,1
103876	2012-08-21	7	7	\N	\N	f	927	2115401-6	LEIVA PIZARRO, NORMA GUILLERMINA	0	2012-08-14	2012-11-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-21 00:00:00	2012-08-30 09:40:24.301456		Bilateral Igual o Inferior a 0,1
103877	2012-08-21	7	7	\N	\N	f	927	3356901-7	RODRÍGUEZ RIVAS, RICARDO	0	2012-08-14	2012-11-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-21 00:00:00	2012-08-30 09:40:24.304666		Derecha Igual o Inferior a 0,1
103878	2012-08-21	7	7	\N	\N	f	968	4864728-6	PERALTA IBÁÑEZ, MARIO TOMÁS	0	2012-08-14	2012-11-12	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.308121		No Especifica
103879	2012-08-21	7	7	\N	\N	f	1140	10377753-4	DUARTE SANHUEZA, HILDA DEL CARMEN	0	2012-08-14	2012-11-12	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.311144		No Especifica
103880	2012-05-23	7	7	\N	\N	f	1155	6338929-3	PIZARRO SALINAS, SILVIA VERÓNICA	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.314104		No Especifica
103881	2012-05-18	7	7	\N	\N	f	1155	5909360-6	PONCE ARANDA, MARIA IRIS	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.316914		No Especifica
103882	2012-07-18	7	7	\N	\N	f	773	5733491-6	ASTUDILLO BRUNA, MARÍA VIOLETA	0	2012-07-16	2012-11-13	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:24.319896		No Especifica
103883	2012-05-23	7	7	\N	\N	f	1155	5290387-4	PUÑO CASTRO, MARIO ENRIQUE	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.322792		No Especifica
103884	2012-05-24	7	7	\N	\N	f	1155	5150180-2	PIZARRO ESTAY, EDELMIRA DEL CARMEN	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.325596		No Especifica
103885	2012-05-22	7	7	\N	\N	f	925	4357959-2	VÉLIZ MARTÍNEZ, DAVID ALFREDO	0	2012-05-17	2012-11-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-05-22 00:00:00	2012-08-30 09:40:24.32855		No Especifica
103886	2012-05-18	7	7	\N	\N	f	1155	4265156-7	CANALES SAAVEDRA, MARIO ALBERTO	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.33153		No Especifica
103887	2012-05-22	7	7	\N	\N	f	925	3570513-9	EYZAGUIRRE SÁNCHEZ, GUILLERMO DANIEL	0	2012-05-17	2012-11-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:24.334425		No Especifica
103888	2012-05-18	7	7	\N	\N	f	1155	2875355-1	FLORES ESPINOZA, MANUEL LORENZO	0	2012-05-17	2012-11-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.337544		No Especifica
103889	2012-08-17	7	7	\N	\N	f	1140	7468319-3	OLMOS JARAMILLO, VIOLETA DE LAS MERCE	0	2012-08-16	2012-11-14	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.34063		No Especifica
103890	2012-08-17	7	7	\N	\N	f	1140	3507652-2	BRAVO VARGAS, ALFREDO SEGUNDO	0	2012-08-16	2012-11-14	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.343456		No Especifica
103891	2012-08-17	7	7	\N	\N	f	1140	3213760-1	CALDERON , NORMA ESTER	0	2012-08-16	2012-11-14	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.346218		No Especifica
103892	2012-05-23	7	7	\N	\N	f	1155	5724420-8	COLICHEO LEMUNAO, EUGENIA	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.349218		No Especifica
103893	2012-05-23	7	7	\N	\N	f	1155	5532111-6	CARLO PIZARRO, ADA VERÓNICA	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.35221		No Especifica
103894	2012-05-29	7	7	\N	\N	f	1155	5068125-4	PÉREZ ALFARO, MARÍA ELENA	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.35498		No Especifica
103895	2012-05-23	7	7	\N	\N	f	1155	4542043-4	GONZÁLEZ PAJARITO, EDUARDO	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.357853		No Especifica
103896	2012-05-23	7	7	\N	\N	f	1155	3859218-1	PACHECO BARRALES, JUAN ELIZARDO	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.360772		No Especifica
103897	2012-05-24	7	7	\N	\N	f	1155	3417949-2	ARAYA , ELCIRA DEL CARMEN	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.363555		No Especifica
103898	2012-05-23	7	7	\N	\N	f	1155	1805754-9	SILVA NÚÑEZ, ADRIANA ELSA	0	2012-05-18	2012-11-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.366367		No Especifica
103899	2012-08-20	7	7	\N	\N	f	927	1847108-6	CÓRDOVA OLIVARES, OLGA ALICIA	0	2012-08-16	2012-11-14	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.36958		Bilateral Igual o Inferior a 0,1
103900	2012-08-20	7	7	\N	\N	f	927	2854668-8	ORTIZ VERGARA, GUACOLDA DEL CARMEN	0	2012-08-16	2012-11-14	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.372742		Bilateral Igual o Inferior a 0,1
103901	2012-08-20	7	7	\N	\N	f	927	4040106-7	MORGADO CORTEZ, MARÍA ELBA	0	2012-08-16	2012-11-14	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.376401		Bilateral Igual o Inferior a 0,1
103902	2012-08-20	7	7	\N	\N	f	927	8593332-9	PINEDA OLIVARES, EDUARDO ULDARICO	0	2012-08-16	2012-11-14	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:24.3796		Bilateral Igual o Inferior a 0,1
103903	2012-08-20	7	7	\N	\N	f	927	5579212-7	GRANIFO CASTRO, GRACIELA DEL CARMEN	0	2012-08-16	2012-11-14	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:24.382769		Derecha Igual o Inferior a 0,1
103904	2012-08-20	7	7	\N	\N	f	927	2099392-8	TAPIA ARAYA, ENRIQUE ESTEBAN	0	2012-08-16	2012-11-14	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:24.385962		Izquierda Igual o Inferior a 0,1
103905	2012-08-20	7	7	\N	\N	f	927	7501740-5	AGUAYO JARA, ROSA ESTER	0	2012-08-16	2012-11-14	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:24.389154		Izquierda Igual o Inferior a 0,1
103906	2012-08-20	7	7	\N	\N	f	1004	22677285-5	ROJAS MONTECINOS, KAROLAYN ALEJANDRA	0	2012-08-16	2012-11-14	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.392133		No Especifica
103907	2012-08-20	7	7	\N	\N	f	968	11093050-K	GARCÍA VÁSQUEZ, EMILIA TRINIDAD DEL	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:24.395078		No Especifica
103908	2012-08-20	7	7	\N	\N	f	968	4175298-K	MAURAN FIGUEROA, JUANA GRACIELA	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:24.397902		No Especifica
103909	2012-08-21	7	7	\N	\N	f	1004	23109462-8	GAMBOA DÍAZ, LUCIANO THOMÁS	0	2012-08-16	2012-11-14	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.400787		No Especifica
103910	2012-08-21	7	7	\N	\N	f	968	10292390-1	PINO LILLO, PAULINA DEL CARMEN	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.404075		No Especifica
103911	2012-08-21	7	7	\N	\N	f	968	4716530-K	URRA CONTRERAS, TEMISTOCLES HUGO	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.406897		No Especifica
103912	2012-08-21	7	7	\N	\N	f	968	9066934-6	CEA MONTECINOS, JUANA ANGÉLICA	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.409718		No Especifica
103913	2012-08-21	7	7	\N	\N	f	968	9647050-9	MUÑOZ SALDÍVAR, GUILLERMO JOSÉ	0	2012-08-16	2012-11-14	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.412559		No Especifica
103914	2012-08-21	7	7	\N	\N	f	1072	12351537-4	CASTELLANO MONTENEGRO, JORGE ENRIQUE	0	2012-08-16	2012-11-14	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-21 00:00:00	2012-08-30 09:40:24.416034		Hemodiálisis 15 Años y más
103915	2012-08-24	7	7	\N	\N	f	1004	23473193-9	BERNAL CONTRERAS, ANTONELLA PASCALE	0	2012-08-17	2012-11-15	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:24.419064		No Especifica
103916	2012-03-30	7	7	\N	\N	f	776	5205129-0	ZUNILDA DEL CARMEN BONILLA HERNÁNDEZ	0	2012-03-20	2012-11-15	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-03-30 00:00:00	2012-08-30 09:40:24.422279		Derecha
103917	2012-05-23	7	7	\N	\N	f	925	4585471-K	ARANCIBIA NAVIA, BERNARDINA EUGENIA	0	2012-05-19	2012-11-15	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.425325		No Especifica
103918	2012-08-20	7	7	\N	\N	f	1004	24025990-7	TAPIA GALLARDO, ISABELLA ANTONIA	0	2012-08-17	2012-11-15	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.428162		No Especifica
103919	2012-08-21	7	7	\N	\N	f	968	7866788-5	GAETE ASTUDILLO, CARLOS SERGIO	0	2012-08-17	2012-11-15	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.431036		No Especifica
103920	2012-08-22	7	7	\N	\N	f	927	7869439-4	DELGADILLO SILVA, GABRIEL ERNESTO	0	2012-08-17	2012-11-15	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-22 00:00:00	2012-08-30 09:40:24.4342		Derecha Igual o Inferior a 0,1
103921	2012-07-31	7	7	\N	\N	f	850	2321006-1	VARGAS ARAYA, FERNANDO HORACIO	0	2012-07-19	2012-11-16	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-28 00:00:00	2012-08-30 09:40:24.437169		No Especifica
103922	2012-08-24	7	7	\N	\N	f	968	9045960-0	CUBILLOS GONZÁLEZ, MARÍA ELIANA	0	2012-08-20	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-24 00:00:00	2012-08-30 09:40:24.440159		No Especifica
103923	2012-05-24	7	7	\N	\N	f	1155	5643502-6	VILLAR BERNAL, GILBERTO FERNANDO	0	2012-05-22	2012-11-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.44332		No Especifica
103924	2012-03-26	7	7	\N	\N	f	776	5334569-7	MARÍA ELENA BARRIOS	0	2012-03-22	2012-11-19	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-03-26 00:00:00	2012-08-30 09:40:24.446214		Derecha
103925	2012-05-25	7	7	\N	\N	f	1155	5104991-8	CHAPARRO NÚÑEZ, EDNA ANGELINA	0	2012-05-23	2012-11-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.449142		No Especifica
103926	2012-05-25	7	7	\N	\N	f	1155	4932521-5	ABARCA VILLAR, GEORGINA DE LAS MERC	0	2012-05-23	2012-11-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.452132		No Especifica
103927	2012-05-23	7	7	\N	\N	f	927	4827686-5	PÉREZ VENEGAS, CARLINA	0	2012-05-22	2012-11-19	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.455729		Bilateral
103928	2012-03-27	7	7	\N	\N	f	776	4824568-4	EDUARDO ENRIQUE MUNIZAGA MENESES	0	2012-03-23	2012-11-19	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-03-27 00:00:00	2012-08-30 09:40:24.45913		Derecha
103929	2012-07-25	7	7	\N	\N	f	773	4401379-7	PAYACÁN TAPIA, MARÍA GRACIELA	0	2012-07-20	2012-11-19	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:24.462153		No Especifica
103930	2012-05-24	7	7	\N	\N	f	1155	4400976-5	ESCOBAR MC DERMITT, ARMANDO GUILLERMO	0	2012-05-22	2012-11-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.465289		No Especifica
103931	2012-08-21	7	7	\N	\N	f	1046	3850123-2	FLORES CHAPARRO, JUAN IGNACIO	0	2012-08-20	2012-11-19	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.468242		No Especifica
103932	2012-08-21	7	7	\N	\N	f	1046	9816321-2	RAMOS CATALÁN, CECILIA ESTRELLA	0	2012-08-20	2012-11-19	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.471538		No Especifica
103933	2012-08-21	7	7	\N	\N	f	968	5332974-8	PAREDES ÁLVAREZ, MARÍA GLORIA	0	2012-08-20	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.474742		No Especifica
103934	2012-08-21	7	7	\N	\N	f	1140	4621926-0	SAAVEDRA HERMOSILLA, GEORGINA DEL CARMEN	0	2012-08-20	2012-11-19	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.47848		No Especifica
103935	2012-08-21	7	7	\N	\N	f	1140	7464101-6	PEÑA RUBINA, ANA MARÍA	0	2012-08-20	2012-11-19	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.482268		No Especifica
103936	2012-08-22	7	7	\N	\N	f	927	5568301-8	ARAYA ESPEJO, AMELIA DE LAS MERCED	0	2012-08-20	2012-11-19	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-22 00:00:00	2012-08-30 09:40:24.485912		Derecha Igual o Inferior a 0,1
103937	2012-08-22	7	7	\N	\N	f	1004	21220936-8	ORELLANA MORALES, PATRICIO ALEXIS	0	2012-08-21	2012-11-19	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.489509		No Especifica
103938	2012-08-22	7	7	\N	\N	f	1004	23557986-3	SILVA PÉREZ, MARCELO ALFONSO	0	2012-08-21	2012-11-19	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:24.492833		No Especifica
103939	2012-08-22	7	7	\N	\N	f	1046	8415668-K	TAPIA TAPIA, IRMA DEL CARMEN	0	2012-08-20	2012-11-19	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.496164		No Especifica
103940	2012-08-22	7	7	\N	\N	f	1054	23810963-9	DONOSO PÉREZ, DIEGO IGNACIO	0	2012-08-20	2012-11-19	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Tratamiento Audífonos	2012-08-27 00:00:00	2012-08-30 09:40:24.499424		No Especifica
103941	2012-08-22	7	7	\N	\N	f	1054	23811002-5	DONOSO PÉREZ, FELIPE IGNACIO	0	2012-08-20	2012-11-19	Hipoacusia bilateral del Prematuro . {decreto n° 1/2010}	Tratamiento Audífonos	2012-08-27 00:00:00	2012-08-30 09:40:24.502383		No Especifica
103942	2012-08-22	7	7	\N	\N	f	968	4226422-9	BLANCO DELGADO, OLGA DEL CARMEN	0	2012-08-21	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-22 00:00:00	2012-08-30 09:40:24.505182		No Especifica
103943	2012-08-22	7	7	\N	\N	f	968	7503096-7	GUERRERO ZAMORANO, HORTENSIA DEL CARMEN	0	2012-08-20	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.508151		No Especifica
103944	2012-08-22	7	7	\N	\N	f	1140	11007087-K	SILVA SANHUEZA, MÓNICA VIVIANA	0	2012-08-21	2012-11-19	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.511059		No Especifica
103945	2012-08-22	7	7	\N	\N	f	1140	4995395-K	GAETE SOZA, SONIA DEL CARMEN	0	2012-08-21	2012-11-19	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.513835		No Especifica
103946	2012-08-22	7	7	\N	\N	f	1140	6859394-8	RISSETTO HERRERA, JUAN ANTONIO	0	2012-08-21	2012-11-19	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.516602		No Especifica
103947	2012-08-23	7	7	\N	\N	f	968	9028830-K	SILVA CONTRERAS, SERGIO RICARDO	0	2012-08-21	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-28 00:00:00	2012-08-30 09:40:24.519458		No Especifica
103948	2012-08-23	7	7	\N	\N	f	927	7821271-3	GONZÁLEZ MONTIEL, MARÍA VERÓNICA	0	2012-08-21	2012-11-19	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-23 00:00:00	2012-08-30 09:40:24.522719		Izquierda Igual o Inferior a 0,1
103949	2012-08-23	7	7	\N	\N	f	1072	12602101-1	SÁNCHEZ MERA, JESSICA MARLENE	0	2012-08-21	2012-11-19	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-28 00:00:00	2012-08-30 09:40:24.525768		Hemodiálisis 15 Años y más
103950	2012-08-23	7	7	\N	\N	f	1072	13428827-2	ARAOS GONZÁLEZ, SILVANA ALEJANDRA	0	2012-08-21	2012-11-19	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-28 00:00:00	2012-08-30 09:40:24.528758		Hemodiálisis 15 Años y más
103951	2012-08-23	7	7	\N	\N	f	1072	8251336-1	GUAJARDO DONOSO, JORGE BENITO	0	2012-08-20	2012-11-19	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Acceso Vascular para Hemodiálisis en Personas de 15 años y más	2012-08-23 00:00:00	2012-08-30 09:40:24.532045		Hemodiálisis 15 Años y más
103952	2012-08-23	7	7	\N	\N	f	1004	23860131-2	NAVARRO VILLALÓN, MICHELLE POLETTE	0	2012-08-20	2012-11-19	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.534865		No Especifica
103953	2012-08-28	7	7	\N	\N	f	968	11011238-6	PIMENTEL CAMPOS, SANDRA DEL CARMEN	0	2012-08-21	2012-11-19	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.537842		No Especifica
103954	2012-08-29	7	7	\N	\N	f	927	3989607-9	CORNEJO , ISOLINA DEL CARMEN	0	2012-08-21	2012-11-19	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.541661		Izquierda Igual o Inferior a 0,1
103955	2012-08-29	7	7	\N	\N	f	955	10992727-9	LOBOS GALAZ, SANDRA JACQUELINE	0	2012-08-20	2012-11-19	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-29 00:00:00	2012-08-30 09:40:24.544589		No Especifica
103956	2012-08-29	7	7	\N	\N	f	955	12453885-8	RAMÍREZ ARAYA, KARINA CARMEN	0	2012-08-21	2012-11-19	Colecistectomía Preventiva . {decreto nº 228}	Intervención Quirúrgica	2012-08-29 00:00:00	2012-08-30 09:40:24.547436		No Especifica
103957	2012-08-24	7	7	\N	\N	f	1004	22482797-0	RÍOS LEIVA, GISSELA ANDREA	0	2012-08-22	2012-11-20	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:24.550468		No Especifica
103958	2012-08-24	7	7	\N	\N	f	1140	4346646-1	OPAZO SEPÚLVEDA, BERTA HERMINIA	0	2012-08-22	2012-11-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:24.553301		No Especifica
103959	2012-06-04	7	7	\N	\N	f	927	6325127-5	MOLINA HIGUERAS, EMILIA DEL CARMEN	0	2012-05-24	2012-11-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.55648		Bilateral
103960	2012-07-17	7	7	\N	\N	f	927	5145303-4	MORALES SILVA, ELENA DE LAS MERCEDE	0	2012-05-24	2012-11-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:24.55998		Bilateral
103961	2012-05-28	7	7	\N	\N	f	1155	4697361-5	PODESTÁ LÓPEZ, ANA MARÍA	0	2012-05-24	2012-11-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.562993		No Especifica
103962	2012-05-29	7	7	\N	\N	f	1155	4227379-1	VERGARA LIZANA, BERNARDA DE LOURDES	0	2012-05-24	2012-11-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.565852		No Especifica
103963	2012-05-25	7	7	\N	\N	f	1155	4041998-5	OSSES ARRIAGADA, RUDEGUER	0	2012-05-24	2012-11-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.568777		No Especifica
103964	2012-05-25	7	7	\N	\N	f	925	3839189-5	ARAVENA MENA, MARÍA LUISA	0	2012-05-24	2012-11-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.571796		No Especifica
103965	2012-05-28	7	7	\N	\N	f	925	3729321-0	BANFI DOREN, VALENTINA ANTONIETA	0	2012-05-24	2012-11-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.57473		No Especifica
103966	2012-06-04	7	7	\N	\N	f	927	3276778-8	CHÁVEZ PALMA, MARGARITA ISABEL	0	2012-05-24	2012-11-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.578079		Bilateral
103967	2012-05-28	7	7	\N	\N	f	925	2342870-9	MUÑOZ RIVERA, ELBA FRESIA DE LAS M	0	2012-05-24	2012-11-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.581067		No Especifica
103968	2012-05-30	7	7	\N	\N	f	1155	1900520-8	ÁVILA ESPINOZA, DIÓGENES DEL CARMEN	0	2012-05-24	2012-11-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.58395		No Especifica
103969	2012-08-23	7	7	\N	\N	f	1004	23723045-0	RUMIE LEIVA, HASNA CELINA	0	2012-08-22	2012-11-20	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.586808		No Especifica
103970	2012-08-23	7	7	\N	\N	f	1140	12041747-9	RODRÍGUEZ ROJAS, CARLOS SEGUNDO	0	2012-08-22	2012-11-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.590141		No Especifica
103971	2012-08-23	7	7	\N	\N	f	1140	6041321-5	VALDIVIA HERMOSILLA, CARLOS MANUEL	0	2012-08-22	2012-11-20	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:24.593003		No Especifica
103972	2012-08-27	7	7	\N	\N	f	927	8640877-5	IBARRA GONZALEZ, BERNARDA DEL CARMEN	0	2012-08-22	2012-11-20	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:24.596793		Bilateral Igual o Inferior a 0,1
103973	2012-08-27	7	7	\N	\N	f	927	4788752-6	ROLDÁN LUNA, ROSA DE LAS MERCEDES	0	2012-08-22	2012-11-20	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-27 00:00:00	2012-08-30 09:40:24.600147		Derecha Igual o Inferior a 0,1
103974	2012-08-27	7	7	\N	\N	f	927	3056693-9	ASTUDILLO COLLAO, MARCOS ARNOLDO	0	2012-08-22	2012-11-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-27 00:00:00	2012-08-30 09:40:24.603321		Izquierda Igual o Inferior a 0,1
103975	2012-08-27	7	7	\N	\N	f	927	4690798-1	TOLEDO PONCE, ADRIANO JESÚS	0	2012-08-22	2012-11-20	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-27 00:00:00	2012-08-30 09:40:24.606441		Izquierda Igual o Inferior a 0,1
103976	2012-08-24	7	7	\N	\N	f	1140	9753885-9	VÉLIZ VIDAL, ANA MARÍA	0	2012-08-23	2012-11-21	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:24.609396		No Especifica
103977	2012-05-29	7	7	\N	\N	f	925	5613332-1	URBINA ZAMORA, SERGIO EDUARDO	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.612544		No Especifica
103978	2012-05-30	7	7	\N	\N	f	1155	5437601-4	LARENAS CHANDÍA, ORIANA MARÍA	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.615363		No Especifica
103979	2012-06-04	7	7	\N	\N	f	1043	5098949-6	VICENCIO FIGUEROA, ALFONSO ANTONIO	0	2012-05-25	2012-11-21	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-04 00:00:00	2012-08-30 09:40:24.618611		Retención Urinaria Aguda Repetida
103980	2012-05-29	7	7	\N	\N	f	927	4926454-2	GUZMÁN GUZMÁN, ISIDORA ANGÉLICA	0	2012-05-25	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.621907		Bilateral
103981	2012-05-30	7	7	\N	\N	f	927	4862646-7	ZÁRATE MARCHANT, TORIBIO NICANOR	0	2012-05-25	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.625505		Bilateral
103982	2012-05-29	7	7	\N	\N	f	925	4809962-9	PONCE TAPIA, GUILLERMINA DEL CARM	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.628425		No Especifica
103983	2012-05-29	7	7	\N	\N	f	927	4431392-8	DEVIA LÓPEZ, MARÍA ELISA	0	2012-05-25	2012-11-21	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:24.631693		Derecha
103984	2012-05-29	7	7	\N	\N	f	927	4392089-8	ZURITA ZAPATA, JOSÉ DEL TRÁNSITO	0	2012-05-25	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.634834		Bilateral
103985	2012-05-28	7	7	\N	\N	f	1155	4284007-6	CORTÉS CERDA, MARCOS ENRIQUE	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.637833		No Especifica
103986	2012-05-28	7	7	\N	\N	f	1155	4130632-7	PINO TAPIA, ADRIANA DEL CARMEN	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.640688		No Especifica
103987	2012-05-31	7	7	\N	\N	f	1155	4129607-0	ARAVENA FERNÁNDEZ, MARGARITA DE LA CRUZ	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.64349		No Especifica
103988	2012-06-04	7	7	\N	\N	f	1043	4069292-4	VÁSQUEZ VERA, REINALDO ADOLFO AURE	0	2012-05-25	2012-11-21	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-13 00:00:00	2012-08-30 09:40:24.646372		Retención Urinaria Aguda Repetida
103989	2012-05-28	7	7	\N	\N	f	925	3956719-9	ROJAS FLORES, NORA DEL CARMEN	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:24.649607		No Especifica
103990	2012-05-28	7	7	\N	\N	f	1155	3246602-8	GUTIÉRREZ ROMERO, JORGE	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.652611		No Especifica
103991	2012-05-31	7	7	\N	\N	f	925	3208345-5	VICENCIO ARANCIBIA, LUCÍA ALEJANDRINA	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.655884		No Especifica
103992	2012-05-28	7	7	\N	\N	f	925	3184167-4	MORALES PALM, LUCÍA KARINE	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.658811		No Especifica
103993	2012-05-29	7	7	\N	\N	f	1155	3006424-0	ORELLANA DELGADO, SILVIA HUMILDE	0	2012-05-25	2012-11-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.661758		No Especifica
103994	2012-05-28	7	7	\N	\N	f	925	2805595-1	TORRES ARAVENA, ALEJANDRO	0	2012-05-25	2012-11-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.664538		No Especifica
103995	2012-08-27	7	7	\N	\N	f	968	6824403-k	VÁSQUEZ ARCOS, MERCEDES GEORGINA	0	2012-08-23	2012-11-21	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-27 00:00:00	2012-08-30 09:40:24.667604		No Especifica
103996	2012-08-29	7	7	\N	\N	f	927	1017343-4	AMARO AYALA, ZOILA ENRIQUETA	0	2012-08-23	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:24.671071		Bilateral Igual o Inferior a 0,1
103997	2012-08-29	7	7	\N	\N	f	927	2172790-3	FERNÁNDEZ CORVALÁN, ADRIANA ISABEL	0	2012-08-23	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:24.674895		Bilateral Igual o Inferior a 0,1
103998	2012-08-29	7	7	\N	\N	f	927	3457813-3	PÉREZ OYARZÚN, CLEMENTINA DEL TRÁNS	0	2012-08-23	2012-11-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:24.678387		Bilateral Igual o Inferior a 0,1
103999	2012-08-29	7	7	\N	\N	f	927	3859405-2	CARVAJAL CORTEZ, PEDRO LUCIANO	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.681614		Izquierda Igual o Inferior a 0,1
104000	2012-08-29	7	7	\N	\N	f	927	4979434-7	VILLARROEL , REBECA DEL CARMEN	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.684755		Izquierda Igual o Inferior a 0,1
104001	2012-08-29	7	7	\N	\N	f	927	5749488-3	ABARCA DONOSO, OLGA MAURICIA	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.687961		Izquierda Igual o Inferior a 0,1
104002	2012-08-29	7	7	\N	\N	f	927	3669922-1	MATURANA , RAUL	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:24.691345		Derecha Igual o Inferior a 0,1
104003	2012-08-29	7	7	\N	\N	f	927	5012655-2	BUSTOS NASI, CECILIA ROSALINA	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:24.694935		Derecha Igual o Inferior a 0,1
104004	2012-08-29	7	7	\N	\N	f	927	7455160-2	GONZÁLEZ ESTAY, VIOLETA HERMINIA	0	2012-08-23	2012-11-21	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:24.698159		Derecha Igual o Inferior a 0,1
104005	2012-01-25	7	7	\N	\N	f	1067	17627369-0	DAVID ALFREDO GALLARDO BARRERA	0	2012-01-23	2012-11-22	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-13 00:00:00	2012-08-30 09:40:24.701389		Estudio Pre-Trasplante
104006	2012-06-18	7	7	\N	\N	f	925	7226195-K	VILLEGAS CANDIA, HILDA ROSA	0	2012-05-26	2012-11-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.704408		No Especifica
104007	2012-06-27	7	7	\N	\N	f	773	5462547-2	FIERRO OLIVARES, JOSÉ ENRIQUE	0	2012-07-25	2012-11-22	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:24.707424		No Especifica
104008	2012-08-28	7	7	\N	\N	f	968	5563307-k	MONTECINOS LIZAMA, ERNESTINA ESTER	0	2012-08-24	2012-11-22	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.710751		No Especifica
104009	2012-08-28	7	7	\N	\N	f	968	5947043-4	RETAMAL DÍAZ, MARÍA INÉS	0	2012-08-24	2012-11-22	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.713974		No Especifica
104010	2012-08-28	7	7	\N	\N	f	1046	8169104-5	BERNAL GALDAMES, ANA MARÍA	0	2012-08-24	2012-11-22	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.716895		No Especifica
104011	2012-08-28	7	7	\N	\N	f	1004	22674113-5	FREDES GALLARDO, BIANCA MASSIEL	0	2012-08-24	2012-11-22	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.719831		No Especifica
104012	2012-08-29	7	7	\N	\N	f	927	2519897-2	VERDEJO SALINAS, JUAN SIMÓN	0	2012-08-24	2012-11-22	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:24.723159		Bilateral Igual o Inferior a 0,1
104013	2012-07-31	7	7	\N	\N	f	773	6207117-6	PONCE GAETE, JUANA DOMINGA	0	2012-07-26	2012-11-23	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:24.725972		No Especifica
104014	2012-05-08	7	7	\N	\N	f	776	4923092-3	PIZARRO PIZARRO, GUSTAVO GONZALO	0	2012-03-28	2012-11-23	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2011-05-14 00:00:00	2012-08-30 09:40:24.729083		Derecha
104015	2012-03-30	7	7	\N	\N	f	776	3907028-6	YOLANDA SEPÚLVEDA FARÍAS	0	2012-03-28	2012-11-23	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-03-30 00:00:00	2012-08-30 09:40:24.732116		Izquierda
104016	2012-08-10	7	7	\N	\N	f	773	3730549-9	OLIVARES ORTIZ, MARÍA MAGDALENA	0	2012-07-26	2012-11-23	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:24.735019		No Especifica
104017	2012-02-02	7	7	\N	\N	f	1067	15949904-9	LENY ANDRÉS SOTO VARGAS	0	2012-01-26	2012-11-26	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:24.737878		Estudio Pre-Trasplante
104018	2012-01-26	7	7	\N	\N	f	1067	9146641-4	ANA DEL CARMEN GARCÍA GONZÁLEZ	0	2012-01-25	2012-11-26	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-28 00:00:00	2012-08-30 09:40:24.741067		Estudio Pre-Trasplante
104019	2012-01-26	7	7	\N	\N	f	1067	7124074-6	NELSON ALBERTO GUAJARDO SANHUEZA	0	2012-01-25	2012-11-26	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:24.744383		Estudio Pre-Trasplante
104020	2012-05-31	7	7	\N	\N	f	1155	6730088-2	CERNA VARELA, NORMA	0	2012-05-28	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.747346		No Especifica
104021	2012-06-05	7	7	\N	\N	f	1155	6630818-9	FIGUEROA MATURANA, GABRIEL EUGENIO	0	2012-05-28	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.750299		No Especifica
104022	2012-06-05	7	7	\N	\N	f	1155	6539143-0	IBACACHE VALENCIA, MARÍA GLADY	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.75327		No Especifica
104023	2012-06-05	7	7	\N	\N	f	1155	6404297-1	LEÓN PRICKEN, MARÍA LUISA	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.756064		No Especifica
104024	2012-05-31	7	7	\N	\N	f	925	6034601-1	CERDA ROJAS, VÍCTOR ABELARDO	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.759118		No Especifica
104025	2012-06-04	7	7	\N	\N	f	925	5970947-K	VEGA ROBLES, LUISA GLADYS	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.762303		No Especifica
104026	2012-06-04	7	7	\N	\N	f	1043	5825087-2	CASTRO ARANEDA, DANIEL ALEJANDRO	0	2012-05-30	2012-11-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-04 00:00:00	2012-08-30 09:40:24.765353		Retención Urinaria Aguda Repetida
104027	2012-05-30	7	7	\N	\N	f	925	5653236-6	SÁNCHEZ TAPIA, GUILLERMINA DE LAS N	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.768298		No Especifica
104028	2012-06-05	7	7	\N	\N	f	1155	5207073-2	VIDELA CABALLERO, MARGARITA INÉS	0	2012-05-28	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.771164		No Especifica
104029	2012-05-30	7	7	\N	\N	f	1155	5173161-1	FUENTES , MARIA ANGELICA	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.773998		No Especifica
104030	2012-05-30	7	7	\N	\N	f	925	5094674-6	YÁÑEZ MIÑOS, ISABEL DEL CARMEN	0	2012-05-28	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.777271		No Especifica
104031	2012-03-30	7	7	\N	\N	f	776	4917529-9	HUGO HUMBERTO RODRÍGUEZ ARAVENA	0	2012-03-29	2012-11-26	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-03-30 00:00:00	2012-08-30 09:40:24.78035		Izquierda
104032	2012-06-06	7	7	\N	\N	f	1155	4869140-4	MUÑOZ LOBO, VÍCTOR RAÚL	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.783242		No Especifica
104033	2012-06-01	7	7	\N	\N	f	925	4729053-8	CISTERNAS FEBRES, RUBELINDA NELLY	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.786074		No Especifica
104034	2012-05-30	7	7	\N	\N	f	925	4642808-0	BRITO VÁSQUEZ, MARÍA OLGA	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.788987		No Especifica
104035	2012-06-04	7	7	\N	\N	f	1043	4597996-2	PARRAGUEZ VALENCIA, MANUEL LIBORIO	0	2012-05-28	2012-11-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-04 00:00:00	2012-08-30 09:40:24.791909		Retención Urinaria Aguda Repetida
104036	2012-05-31	7	7	\N	\N	f	1155	4543258-0	PIRO SAAVEDRA, HELIBERTO	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.79478		No Especifica
104037	2012-06-05	7	7	\N	\N	f	925	4536542-5	GUTIÉRREZ SOTO, JOSÉ CASIMIRO	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.797628		No Especifica
104038	2012-06-01	7	7	\N	\N	f	925	4505710-0	MORALES BENAVIDES, IVÁN ALBERTO	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.800814		No Especifica
104039	2012-06-01	7	7	\N	\N	f	925	4471671-2	RAMÍREZ TORREJÓN, ROSA DEL CARMEN	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.803828		No Especifica
104040	2012-06-08	7	7	\N	\N	f	1155	4413222-2	LÓPEZ PEÑA, HILDA LUZ	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.80686		No Especifica
104041	2012-05-31	7	7	\N	\N	f	925	4388813-7	NÚÑEZ FIGUEROA, MORELIA DEL CARMEN	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.809894		No Especifica
104042	2012-06-04	7	7	\N	\N	f	1043	4297773-K	ZAPATA CRUCES, OSCAR DANIEL	0	2012-05-28	2012-11-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-04 00:00:00	2012-08-30 09:40:24.812737		Retención Urinaria Aguda Repetida
104043	2012-06-05	7	7	\N	\N	f	927	4297377-7	GUZMÁN TORO, SILVIA LUISA	0	2012-05-28	2012-11-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.816726		Bilateral
104044	2012-05-31	7	7	\N	\N	f	1155	4290461-9	CHAPARRO NAVARRETE, CARLOS SERGIO	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.819902		No Especifica
104045	2012-06-05	7	7	\N	\N	f	1155	4258336-7	ALARCÓN SURJAN, CARLOS ERNESTO	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.822997		No Especifica
104046	2012-06-01	7	7	\N	\N	f	925	4217530-7	ESCOBAR FUENTES, LUCILA DE LAS MERCED	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.826093		No Especifica
104047	2012-06-04	7	7	\N	\N	f	1155	4093126-0	MARTÍNEZ SÁEZ, ROSA HUMBERTA	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.829193		No Especifica
104048	2012-06-01	7	7	\N	\N	f	925	3948644-K	PACHECO SUÁREZ, LUIS ALBERTO	0	2012-05-28	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.832109		No Especifica
104049	2012-06-01	7	7	\N	\N	f	925	3859521-0	GONZÁLEZ LOBOS, LUIS	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.834929		No Especifica
104050	2012-06-05	7	7	\N	\N	f	925	3853845-4	CUADRA ALFARO, RAFAEL DEL CARMEN	0	2012-05-28	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.838099		No Especifica
104051	2012-05-31	7	7	\N	\N	f	1155	3779689-1	RAMÍREZ AGUILERA, EDUARDO	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.841052		No Especifica
104052	2012-05-31	7	7	\N	\N	f	1155	3739374-6	CORTÉS DÍAZ, ELENA DEL CARMEN	0	2012-05-28	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:24.843887		No Especifica
104053	2012-05-31	7	7	\N	\N	f	925	3737574-8	LEDEZMA DAVADIE, LIDIA BRUNILDA	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.846825		No Especifica
104054	2012-06-05	7	7	\N	\N	f	925	3698000-1	NIETO GARCÍA, RICARDO DUBERILDO	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.84975		No Especifica
104055	2012-06-05	7	7	\N	\N	f	925	3366729-9	VICENCIO OLIVARES, MARÍA MERCEDES	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.852899		No Especifica
104056	2012-06-04	7	7	\N	\N	f	1043	3324366-9	LEÓN ALFARO, JORGE RAÚL	0	2012-05-30	2012-11-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-04 00:00:00	2012-08-30 09:40:24.855714		Retención Urinaria Aguda Repetida
104057	2012-06-01	7	7	\N	\N	f	925	3227383-1	CALVO NÚÑEZ, RUTH MARTA	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.85873		No Especifica
104058	2012-05-29	7	7	\N	\N	f	925	3166924-3	SEPÚLVEDA LUCERO, TELMA DE LAS MERCEDE	0	2012-05-28	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.861921		No Especifica
104059	2012-06-08	7	7	\N	\N	f	1155	3128064-8	SOTO GARRAO, ADELA BRÍGIDA	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.864934		No Especifica
104060	2012-06-01	7	7	\N	\N	f	1155	3107267-0	VARGAS RIVEROS, ANGEL CUSTODIO	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.867782		No Especifica
104061	2012-06-05	7	7	\N	\N	f	1155	3099668-2	MARTÍNEZ DURÁN, MARÍA ISABEL MÓNICA	0	2012-05-30	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.870715		No Especifica
104062	2012-05-31	7	7	\N	\N	f	925	3677920-9	CABELLO PONCE, GERMÁN DEL TRÁNSITO	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.873578		No Especifica
104063	2012-05-31	7	7	\N	\N	f	925	3408762-8	HERRERA CALDERÓN, OLGA ORIANA	0	2012-05-30	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.876402		No Especifica
104064	2012-05-31	7	7	\N	\N	f	925	3086687-8	CONCHA BAHAMONDES, JAVIER HUMBERTO	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.879239		No Especifica
104065	2012-05-29	7	7	\N	\N	f	925	3076261-4	LÓPEZ RIVERA, ADRIANA DEL CARMEN	0	2012-05-28	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.882125		No Especifica
104066	2012-06-01	7	7	\N	\N	f	925	3034725-0	AROS OYARZO, ROSA IDA	0	2012-05-29	2012-11-26	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.884935		No Especifica
104067	2012-06-01	7	7	\N	\N	f	1155	2886965-7	CANO , JOSEFINA ELENA	0	2012-05-28	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.887782		No Especifica
104068	2012-06-01	7	7	\N	\N	f	1155	2864341-1	SANHUEZA CALDERÓN, TERESA DEL CARMEN	0	2012-05-29	2012-11-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.891181		No Especifica
104069	2012-06-07	7	7	\N	\N	f	927	2672078-8	MENDOZA MENDOZA, ELIANA	0	2012-05-28	2012-11-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-06-07 00:00:00	2012-08-30 09:40:24.894704		Bilateral
104070	2012-08-28	7	7	\N	\N	f	1046	7443550-5	GONZÁLEZ FARFÁN, MANUEL ENRIQUE	0	2012-08-27	2012-11-26	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.897833		No Especifica
104071	2012-08-28	7	7	\N	\N	f	1140	8275299-4	ARAYA GUAJARDO, MÓNICA EVARISTA	0	2012-08-27	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.901327		No Especifica
104072	2012-08-29	7	7	\N	\N	f	927	1796062-8	WUNDERLICH PIDERIT, INGEBORG DEL CARMEN	0	2012-08-27	2012-11-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,1 Unilateral Izquierda.	2012-08-29 00:00:00	2012-08-30 09:40:24.904611		Izquierda Igual o Inferior a 0,1
104073	2012-08-29	7	7	\N	\N	f	1004	23875480-1	BERNALES FERNÁNDEZ, ENZO ANDRÉS	0	2012-08-27	2012-11-26	Estrabismo . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.907555		No Especifica
104074	2012-08-29	7	7	\N	\N	f	1046	3764714-4	ZAMORA VEAS, ELIANA DEL CARMEN	0	2012-08-27	2012-11-26	Hipertensión Arterial . {decreto nº 228}	Atención Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.910432		No Especifica
104075	2012-08-29	7	7	\N	\N	f	968	6909351-5	CASTILLO UBILLO, MINTA DEL CARMEN	0	2012-08-27	2012-11-26	Diabetes Mellitus Tipo 2 . {decreto nº 228}	Atención por Especialista	2012-08-29 00:00:00	2012-08-30 09:40:24.913434		No Especifica
104076	2012-08-29	7	7	\N	\N	f	1140	3097588-k	VÁSQUEZ ESCOBAR, BERNARDO EULOGIO	0	2012-08-28	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.916299		No Especifica
104077	2012-08-29	7	7	\N	\N	f	1140	3126365-4	HUGHES SILVA, EDMUNDO	0	2012-08-28	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.919131		No Especifica
104078	2012-08-29	7	7	\N	\N	f	1140	5628975-5	MANCILLA GATICA, PEDRO	0	2012-08-27	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.921966		No Especifica
104079	2012-08-29	7	7	\N	\N	f	1140	5980533-9	HIDALGO ADASME, GUILLERMO DEL CARMEN	0	2012-08-28	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.92481		No Especifica
104080	2012-08-29	7	7	\N	\N	f	1140	9925800-4	CAMPOS SALAS, MILEN DEL CARMEN	0	2012-08-27	2012-11-26	Retinopatía Diabética . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.927637		No Especifica
104081	2012-06-05	7	7	\N	\N	f	927	9233378-7	HERRERA CRUZAT, XIMENA HAYDÉE	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.930962		Bilateral
104082	2012-06-08	7	7	\N	\N	f	1155	6214223-5	CORTÉS BRAVO, SILVIA	0	2012-05-31	2012-11-27	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.933842		No Especifica
104083	2012-06-01	7	7	\N	\N	f	925	6115456-6	VALDIVIA ASTUDILLO, LUIS VICENTE	0	2012-05-31	2012-11-27	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.936688		No Especifica
104084	2012-06-05	7	7	\N	\N	f	927	5148668-4	SANDOVAL CHEUQUETA, LUCÍA	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.940572		Bilateral
104085	2012-06-07	7	7	\N	\N	f	927	4589062-7	DÍAZ VIVANCO, MIGUEL JAVIER	0	2012-05-31	2012-11-27	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:24.943931		Derecha
104086	2012-06-04	7	7	\N	\N	f	1155	4209847-7	CARMONA GHÍO, OSVALDO ARTURO ANTON	0	2012-05-31	2012-11-27	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.946977		No Especifica
104087	2012-06-06	7	7	\N	\N	f	927	4083119-3	PUEBLA MONTENEGRO, MARÍA OLIVIA	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.950383		Bilateral
104088	2012-06-06	7	7	\N	\N	f	927	3644644-7	ROJAS FLORES, JOSEFINA DEL ROSARIO	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.953571		Bilateral
104089	2012-06-05	7	7	\N	\N	f	927	3638079-9	BORGOÑO ORTEGA, CARLOS EMILIANO	0	2012-05-31	2012-11-27	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:24.95671		Derecha
104090	2012-06-01	7	7	\N	\N	f	925	3629172-9	BELLO ALVARADO, MIGUEL ORLANDO	0	2012-05-31	2012-11-27	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.959651		No Especifica
104091	2012-06-05	7	7	\N	\N	f	925	3322940-2	GUZMÁN CABELLO, LUIS JORGE	0	2012-05-31	2012-11-27	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.962867		No Especifica
104092	2012-06-05	7	7	\N	\N	f	927	3135419-6	URETA VILLEGAS, ENRIQUE	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.966535		Bilateral
104093	2012-06-06	7	7	\N	\N	f	927	2809255-5	SHAW ORTIZ, GUILLERMO SEGUNDO	0	2012-05-31	2012-11-27	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.969763		Bilateral
104094	2012-06-07	7	7	\N	\N	f	925	2674497-0	ROJAS RIQUELME, MARCOS AURELIO	0	2012-05-31	2012-11-27	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.972678		No Especifica
104095	2012-06-14	7	7	\N	\N	f	925	2484591-5	MORALES AYALA, MARÍA INÉS	0	2012-05-31	2012-11-27	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.97551		No Especifica
104096	2012-06-05	7	7	\N	\N	f	927	1617250-2	GALARCE PÉREZ, LUIS FERNANDO	0	2012-05-31	2012-11-27	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:24.978709		Derecha
104097	2012-06-06	7	7	\N	\N	f	925	6365516-3	SANTIS ZÚÑIGA, ADRIANA DEL CARMEN	0	2012-06-01	2012-11-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.981571		No Especifica
104098	2012-06-12	7	7	\N	\N	f	927	6075602-3	HERNÁNDEZ CALDERÓN, ELENA DEL CARMEN	0	2012-06-01	2012-11-28	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:24.984685		Bilateral
104099	2012-06-05	7	7	\N	\N	f	1155	5931714-8	NAVARRO ALLENDES, JUANA DEL CARMEN	0	2012-06-01	2012-11-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.987737		No Especifica
104100	2012-06-13	7	7	\N	\N	f	927	4746672-5	GUZMÁN TORO, SONIA DEL CARMEN	0	2012-06-01	2012-11-28	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-17 00:00:00	2012-08-30 09:40:24.991127		Bilateral
104101	2012-06-04	7	7	\N	\N	f	925	4260702-9	MORENO USIN, JUANA	0	2012-06-01	2012-11-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.993991		No Especifica
104102	2012-06-08	7	7	\N	\N	f	925	3666035-K	VALENZUELA ORELLANA, OLGA ROSA	0	2012-06-01	2012-11-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:24.99681		No Especifica
104103	2012-06-05	7	7	\N	\N	f	1155	3540866-5	GONZÁLEZ MOLINA, FRANCISCO RUBÉN	0	2012-06-01	2012-11-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:24.999693		No Especifica
104104	2012-06-04	7	7	\N	\N	f	925	2567572-K	PEÑA JIMÉNEZ, JULIO ARMANDO	0	2012-06-01	2012-11-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:25.002993		No Especifica
104105	2012-06-07	7	7	\N	\N	f	1155	2403079-2	ORREGO , ERASMO SEGUNDO	0	2012-06-01	2012-11-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.005838		No Especifica
104106	2012-06-14	7	7	\N	\N	f	1155	5836790-7	OLIVARES JUICA, MARTA LUISA AMANDA	0	2012-06-02	2012-11-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.008713		No Especifica
104107	2012-06-08	7	7	\N	\N	f	925	4889022-9	CARTES VALDÉS, MARÍA MARGARITA	0	2012-06-02	2012-11-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:25.01159		No Especifica
104108	2012-08-06	7	7	\N	\N	f	850	2793431-5	GONZÁLEZ GÓMEZ, EMILIO FERNANDO	0	2012-08-01	2012-11-29	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-21 00:00:00	2012-08-30 09:40:25.01447		No Especifica
104109	2012-06-11	7	7	\N	\N	f	925	2508241-9	PULIDO GALDAMES, ADOLFA	0	2012-06-02	2012-11-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:25.017349		No Especifica
104110	2012-08-07	7	7	\N	\N	f	773	6641218-0	GUZMÁN LIZANA, MARÍA INÉS	0	2012-08-02	2012-11-30	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:25.020504		No Especifica
104111	2012-02-08	7	7	\N	\N	f	1067	6301833-3	SYLVIA BERENICE DEL POMFRETT BRIONES	0	2012-02-01	2012-11-30	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:25.023537		Estudio Pre-Trasplante
104112	2012-08-07	7	7	\N	\N	f	773	4541380-2	ORREGO TORO, YOLANDA DEL CARMEN	0	2012-08-02	2012-11-30	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:25.02684		No Especifica
104113	2012-06-20	7	7	\N	\N	f	925	21559000-3	VARGAS MIRANDA, SCARLET ESPERANZA	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.029828		No Especifica
104114	2012-06-12	7	7	\N	\N	f	925	8623449-1	FIGUEROA ASTUDILLO, VICTORIA DEL CARMEN	0	2012-06-05	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.032743		No Especifica
104115	2012-06-08	7	7	\N	\N	f	1043	8259510-4	NAVARRETE CERDA, LUCIANO ARMAN	0	2012-06-06	2012-12-03	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-08 00:00:00	2012-08-30 09:40:25.03583		Retención Urinaria Aguda Repetida
104116	2012-06-08	7	7	\N	\N	f	925	7927889-0	RÍOS PALOMINOS, TERESA MÓNICA	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.038802		No Especifica
104117	2012-06-07	7	7	\N	\N	f	925	6646548-9	PULGAR COSTAGUTA, TERESA DEL CARMEN	0	2012-06-05	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.041705		No Especifica
104118	2012-06-12	7	7	\N	\N	f	927	6452572-7	BARRIGA GAETE, RICARDO ALFREDO	0	2012-06-05	2012-12-03	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-01 00:00:00	2012-08-30 09:40:25.04493		Izquierda
104119	2012-06-07	7	7	\N	\N	f	1155	5972396-0	BAZAES CISTERNAS, MERCEDES CARMEN	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.047875		No Especifica
104120	2012-06-12	7	7	\N	\N	f	927	5851155-2	OJEDA LÓPEZ, VERÓNICA	0	2012-06-06	2012-12-03	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.051122		Bilateral
104121	2012-06-06	7	7	\N	\N	f	1043	5756811-9	LORCA SOTOMAYOR, SERGIO ANDRÉS	0	2012-06-04	2012-12-03	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-06 00:00:00	2012-08-30 09:40:25.053978		Retención Urinaria Aguda Repetida
104122	2012-06-08	7	7	\N	\N	f	1155	5692825-1	AGUILERA BASCUÑÁN, ANA MARÍA	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.057019		No Especifica
104123	2012-06-06	7	7	\N	\N	f	1155	5590434-0	GONZÁLEZ JIMÉNEZ, RITA DEL CARMEN	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.060042		No Especifica
104124	2012-06-07	7	7	\N	\N	f	1155	5517711-2	OLAVE BRICEÑO, SILDA TRINIDAD	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.062937		No Especifica
104125	2012-06-06	7	7	\N	\N	f	1155	5501563-5	FLORES AGUILERA, MIRZA ANTONIA	0	2012-06-05	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.06628		No Especifica
104126	2012-06-07	7	7	\N	\N	f	1155	5283864-9	NÚÑEZ POZA, LUIS NOLBERTO	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.069191		No Especifica
104127	2012-06-11	7	7	\N	\N	f	1155	5269222-9	PÉREZ BARAHONA, MARÍA DEL CARMEN	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.072072		No Especifica
104128	2012-06-08	7	7	\N	\N	f	1155	5253001-6	ÓRDENES CARVAJAL, GLADYS DEL CARMEN	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.074902		No Especifica
104129	2012-06-07	7	7	\N	\N	f	925	5206459-7	LARA SOTO, FILOMENA ROSA	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.077749		No Especifica
104130	2012-06-06	7	7	\N	\N	f	925	5126938-1	ESPÍNOLA MUÑOZ, EDMUNDO ENRIQUE	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.080713		No Especifica
104131	2012-08-07	7	7	\N	\N	f	773	5108443-8	OYANEDEL OYANEDEL, ANA ROSA	0	2012-08-03	2012-12-03	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.083561		No Especifica
104132	2012-06-05	7	7	\N	\N	f	925	5037535-8	MORÁN SEPÚLVEDA, CLORINDA ELIANA	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.086417		No Especifica
104133	2012-06-06	7	7	\N	\N	f	1155	5008059-5	OSSANDÓN CORTÉS, FERMÍN ALBERTO	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.089597		No Especifica
104134	2012-06-08	7	7	\N	\N	f	1155	4953893-6	LETELIER SALINAS, MARÍA JULIETA	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.092479		No Especifica
104135	2012-06-08	7	7	\N	\N	f	1043	4793729-9	OLIVARES VILLARROEL, FRANCISCO ROLANDO	0	2012-06-06	2012-12-03	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-08 00:00:00	2012-08-30 09:40:25.095304		Retención Urinaria Aguda Repetida
104136	2012-06-06	7	7	\N	\N	f	1155	4698346-7	GODOY VINNETT, MARÍA ENRIQUETA	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.098204		No Especifica
104137	2012-06-06	7	7	\N	\N	f	1043	4648757-5	ABURTO ANDRADE, RICARDO VÍCTOR	0	2012-06-04	2012-12-03	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-06 00:00:00	2012-08-30 09:40:25.101034		Retención Urinaria Aguda Repetida
104138	2012-06-05	7	7	\N	\N	f	925	4613602-0	HERREROS CUADRA, JAVIER JAIME	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.10388		No Especifica
104139	2012-06-08	7	7	\N	\N	f	925	4499472-0	BENAVIDES JORQUERA, VÍCTOR MANUEL	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.106703		No Especifica
104140	2012-06-07	7	7	\N	\N	f	925	4379177-K	PEIRANO WALTON, YOLANDA DEL CARMEN	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.109558		No Especifica
104141	2012-08-07	7	7	\N	\N	f	773	4317056-2	TAPIA FERNÁNDEZ, ALFREDO EDUARDO	0	2012-08-03	2012-12-03	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.112404		No Especifica
104142	2012-06-08	7	7	\N	\N	f	925	4267554-7	VALENZUELA CÁCERES, ROSA AMELIA	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.115591		No Especifica
104143	2012-06-05	7	7	\N	\N	f	1155	4254517-1	SALINAS LAZCANO, ORFELIA	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:25.118523		No Especifica
104144	2012-06-06	7	7	\N	\N	f	1155	4254373-K	SILVA , JUANA	0	2012-06-05	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.121387		No Especifica
104145	2012-06-08	7	7	\N	\N	f	1155	4127366-6	LETELIER JEREZ, MARÍA INELIA	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.124204		No Especifica
104146	2012-06-08	7	7	\N	\N	f	925	4026523-6	FLORES CANDIA, JUVENAL	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.127051		No Especifica
104147	2012-04-11	7	7	\N	\N	f	776	3845266-5	MUÑOZ CARVAJAL, NELY DEL TRÁNSITO	0	2012-04-05	2012-12-03	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-04-11 00:00:00	2012-08-30 09:40:25.13027		Derecha
104148	2012-06-12	7	7	\N	\N	f	925	3765603-8	ESTAY COLLAO, SERGIO RODOLFO	0	2012-06-05	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.133169		No Especifica
104149	2012-06-08	7	7	\N	\N	f	1155	3764749-7	NAVIA FERNÁNDEZ, JUAN ALBERTO	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:25.135967		No Especifica
104150	2012-06-14	7	7	\N	\N	f	1155	3722712-9	FIGUEROA HERRERA, ANA REBECA	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.138775		No Especifica
104151	2012-06-07	7	7	\N	\N	f	1155	3626378-4	ALFARO ROJAS, LUIS HOMERO	0	2012-06-05	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:25.141588		No Especifica
104152	2012-06-07	7	7	\N	\N	f	1155	3495913-7	GUTIERREZ LILLO, MANUEL ENRIQUE	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.144882		No Especifica
104153	2012-06-08	7	7	\N	\N	f	1155	3488540-0	PIZARRO PIZARRO, MARIO	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.147727		No Especifica
104154	2012-06-19	7	7	\N	\N	f	1155	3360853-5	CASTRO ESPINOSA, ALFONSO ESTEBAN	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.151012		No Especifica
104155	2012-06-08	7	7	\N	\N	f	925	3280916-2	LÓPEZ CAMPUSANO, CARLOS INFANTE	0	2012-06-06	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.153956		No Especifica
104156	2012-06-06	7	7	\N	\N	f	1155	3255275-7	PEREIRA ARELLANO, DANIEL ARTURO	0	2012-06-04	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.156878		No Especifica
104157	2012-06-08	7	7	\N	\N	f	1155	3002880-5	ORTIZ ROMERO, LUIS ALBERTO	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.159658		No Especifica
104158	2012-06-14	7	7	\N	\N	f	1155	2984323-6	RIQUELME ORELLANA, ELISA ESTER	0	2012-06-06	2012-12-03	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.16246		No Especifica
104159	2012-06-06	7	7	\N	\N	f	925	2427812-3	RETAMAL QUEZADA, AÍDA DEL CARMEN	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.165253		No Especifica
104160	2012-06-08	7	7	\N	\N	f	925	2373953-4	HERRERA HERRERA, MARÍA	0	2012-06-04	2012-12-03	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.168216		No Especifica
104161	2012-06-14	7	7	\N	\N	f	1155	5590856-7	MORA VIDAL, HULDA DEL CARMEN	0	2012-06-07	2012-12-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.171057		No Especifica
104162	2012-08-09	7	7	\N	\N	f	773	4918690-8	JIMÉNEZ ARANCIBIA, JOSÉ MAXIMILIANO	0	2012-08-06	2012-12-04	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-13 00:00:00	2012-08-30 09:40:25.173851		No Especifica
104163	2012-06-11	7	7	\N	\N	f	927	4584682-2	OLIVA CANO, PETRONILA ELENA	0	2012-06-07	2012-12-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-24 00:00:00	2012-08-30 09:40:25.177371		Bilateral
104164	2012-06-20	7	7	\N	\N	f	1155	4473695-0	ABARCA CONTRERAS, LEONARDO ENRIQUE	0	2012-06-07	2012-12-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.180359		No Especifica
104165	2012-06-14	7	7	\N	\N	f	1155	4163379-4	SOTO ROJAS, JUDITH ORIANA	0	2012-06-07	2012-12-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.183211		No Especifica
104166	2012-06-11	7	7	\N	\N	f	927	3978041-0	GONZÁLEZ MORALES, ROSA AMELIA	0	2012-06-07	2012-12-04	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-06-11 00:00:00	2012-08-30 09:40:25.186344		Izquierda
104167	2012-06-11	7	7	\N	\N	f	927	3691824-1	YUCRA YUCRA, FLORA	0	2012-06-07	2012-12-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.18959		Bilateral
104168	2012-06-11	7	7	\N	\N	f	927	3497057-2	AROS BECAR, SONIA DEL CARMEN	0	2012-06-07	2012-12-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.193314		Bilateral
104169	2012-06-08	7	7	\N	\N	f	925	3292103-5	QUINTANA FREZ, LUZ DEL CARMEN	0	2012-06-07	2012-12-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.1962		No Especifica
104170	2012-06-08	7	7	\N	\N	f	1155	3218924-5	VARGAS TORRES, JUAN DE DIOS	0	2012-06-07	2012-12-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.199158		No Especifica
104171	2012-06-11	7	7	\N	\N	f	1155	3094818-1	CHANDÍA ARAUS, TERESA LIDIA	0	2012-06-07	2012-12-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.202057		No Especifica
104172	2012-06-11	7	7	\N	\N	f	925	3033874-K	TORO BUSTAMANTE, DAGOBERTO ENRIQUE	0	2012-06-07	2012-12-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.204972		No Especifica
104173	2012-06-11	7	7	\N	\N	f	927	2660845-7	NÚÑEZ , MARGARITA LUZ	0	2012-06-07	2012-12-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.208209		Bilateral
104174	2012-06-12	7	7	\N	\N	f	1043	7564118-4	PERALTA VÁSQUEZ, HERIBERTO GABRIEL	0	2012-06-08	2012-12-05	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-12 00:00:00	2012-08-30 09:40:25.21129		Retención Urinaria Aguda Repetida
104175	2012-06-21	7	7	\N	\N	f	1043	4844947-6	SIR BARRÍA, TOMÁS WINSTON	0	2012-06-08	2012-12-05	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-21 00:00:00	2012-08-30 09:40:25.214412		Retención Urinaria Aguda Repetida
104176	2012-06-14	7	7	\N	\N	f	1155	4690798-1	TOLEDO PONCE, ADRIANO JESÚS	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.217254		No Especifica
104177	2012-06-14	7	7	\N	\N	f	1155	4585644-5	CURÍN QUIDEL, DOMINGO	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.220198		No Especifica
104178	2012-06-13	7	7	\N	\N	f	1155	4423544-7	PONCE CASTRO, HILDA ELCIRA	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.222994		No Especifica
104179	2012-06-13	7	7	\N	\N	f	1155	4126157-9	BECERRA BARRERA, PASTORIZA DE LAS NIE	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.225863		No Especifica
104180	2012-06-11	7	7	\N	\N	f	925	4030605-6	MANSILLA SEGOVIA, DELMA	0	2012-06-08	2012-12-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.228786		No Especifica
104181	2012-06-11	7	7	\N	\N	f	1155	3032966-K	PINILLA ARANCIBIA, MARÍA LINA	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.23181		No Especifica
104182	2012-06-12	7	7	\N	\N	f	925	2628482-1	BARRÍA MANCILLA, RIGOBERTO OCTAVIO	0	2012-06-08	2012-12-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.234743		No Especifica
104183	2012-06-15	7	7	\N	\N	f	1155	1367847-2	PRADENAS RAMÍREZ, MARÍA	0	2012-06-08	2012-12-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.23765		No Especifica
104184	2012-08-17	7	7	\N	\N	f	773	1863241-1	MIDDLETON ACUÑA, OLGA ELENA	0	2012-08-08	2012-12-06	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.240767		No Especifica
104185	2012-06-14	7	7	\N	\N	f	925	5082546-9	CÉSPEDES RAMÍREZ, LAURA ELISA	0	2012-06-09	2012-12-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.243712		No Especifica
104186	2012-08-10	7	7	\N	\N	f	773	4438346-2	MUÑOZ ABARCA, FIDELINA DEL CARMEN	0	2012-08-08	2012-12-06	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.246594		No Especifica
104187	2012-08-13	7	7	\N	\N	f	773	5970139-8	MENDOZA SOLÍS, ROSA ADRIANA	0	2012-08-09	2012-12-07	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.24944		No Especifica
104188	2012-08-13	7	7	\N	\N	f	773	4453643-9	VALENZUELA JARA, FRESIA	0	2012-08-09	2012-12-07	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.252409		No Especifica
104189	2012-06-14	7	7	\N	\N	f	1155	8606953-9	GARRIDO FUENZALIDA, LUIS ALBERTO	0	2012-06-13	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.255266		No Especifica
104190	2012-02-13	7	7	\N	\N	f	1067	8232436-4	ANA LUISA FRAU OYARZÚN	0	2012-02-10	2012-12-10	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:25.258328		Estudio Pre-Trasplante
104191	2012-06-14	7	7	\N	\N	f	925	7606787-2	QUIÑONES BENAVIDES, DAVID	0	2012-06-13	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.261832		No Especifica
104192	2012-06-14	7	7	\N	\N	f	1043	7249311-7	ROJAS FREZ, JUAN JOSÉ	0	2012-06-11	2012-12-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-14 00:00:00	2012-08-30 09:40:25.264766		Retención Urinaria Aguda Repetida
104193	2012-08-13	7	7	\N	\N	f	773	7089407-6	VERGARA HERRERA, MARIO EUGENIO	0	2012-08-10	2012-12-10	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.267642		No Especifica
104194	2012-06-18	7	7	\N	\N	f	1043	6615417-3	DUMAS DÍAZ, PEDRO GUSTAVO	0	2012-06-13	2012-12-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-18 00:00:00	2012-08-30 09:40:25.271051		Retención Urinaria Aguda Repetida
104195	2012-02-13	7	7	\N	\N	f	1067	6314898-9	DAVID TOMÁS VEGA ARCE	0	2012-02-09	2012-12-10	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-29 00:00:00	2012-08-30 09:40:25.274098		Estudio Pre-Trasplante
104196	2012-06-14	7	7	\N	\N	f	925	6115568-6	ACEVEDO CISTERNAS, HERMINIA	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.277114		No Especifica
104197	2012-06-18	7	7	\N	\N	f	1155	6096793-8	ANDRADE RIVERA, NORMA BEATRIZ	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.280088		No Especifica
104198	2012-06-18	7	7	\N	\N	f	1155	5679216-3	MAERTEN JARA, ALICIA AGUSTINA	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.282929		No Especifica
104199	2012-06-19	7	7	\N	\N	f	1155	5511228-2	FERNÁNDEZ DELGADO, MARÍA DE LAS MERCEDE	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.285747		No Especifica
104200	2012-08-14	7	7	\N	\N	f	773	5510353-4	GONZÁLEZ CASAUBON, SILVIA ELIANA	0	2012-08-10	2012-12-10	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.28867		No Especifica
104201	2012-06-13	7	7	\N	\N	f	1155	5463025-5	SALAS VICENCIO, ROSA CLORINDA DEL CA	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.291562		No Especifica
104202	2012-06-13	7	7	\N	\N	f	1155	5345359-7	PIZARRO VÉLIZ, HILDA MAGDALENA	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.294456		No Especifica
104203	2012-06-14	7	7	\N	\N	f	1155	5311913-1	BERTINI CAZOR, MARGARITA ADRIANA	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.297352		No Especifica
104204	2012-06-13	7	7	\N	\N	f	1155	5253903-K	MERCADO FUENTES, REBECA DE LAS MERCED	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.300225		No Especifica
104205	2012-06-19	7	7	\N	\N	f	1155	5101329-8	OLGUÍN IGOR, NORMA	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.303058		No Especifica
104206	2012-06-14	7	7	\N	\N	f	925	5069170-5	CAMPOS CAMPOS, LUISA ESMERITA	0	2012-06-13	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.305941		No Especifica
104207	2012-06-14	7	7	\N	\N	f	925	5061920-6	ZAMORA LEMUS, MARÍA ELIANA	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.308857		No Especifica
104208	2012-06-19	7	7	\N	\N	f	1155	4922755-8	LIZARDI VERGARA, SILVIA	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.311786		No Especifica
104209	2012-06-12	7	7	\N	\N	f	1155	4858017-3	GERDTZEN VARGAS, HUGO ALFREDO	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.314614		No Especifica
104210	2012-06-14	7	7	\N	\N	f	1155	4800891-7	MADRID LIVESEY, CINTHIA EMLEN KNIGHT	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.317473		No Especifica
104211	2012-06-14	7	7	\N	\N	f	925	4633674-7	CORROTEA ZÚÑIGA, ANA DEL CARMEN	0	2012-06-12	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.320504		No Especifica
104212	2012-06-18	7	7	\N	\N	f	925	4610073-5	NIETO AGUILERA, MARÍA IRMA	0	2012-06-12	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.323435		No Especifica
104213	2012-08-16	7	7	\N	\N	f	850	4600006-4	RECABARREN VALENZUELA, MARIO SERGIO EDUARDO	0	2012-08-10	2012-12-10	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-29 00:00:00	2012-08-30 09:40:25.326441		No Especifica
104214	2012-06-15	7	7	\N	\N	f	1155	4490693-7	TOLEDO JELVES, VALENTÍN ADOLFO	0	2012-06-13	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.329502		No Especifica
104215	2012-06-12	7	7	\N	\N	f	1155	4456303-7	PARRAGUEZ VALENCIA, JOSÉ ALFONSO	0	2012-06-11	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.332516		No Especifica
104216	2012-06-18	7	7	\N	\N	f	1155	4406112-0	FRITZ URRUTIA, MERCEDES NATIVIDAD	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.335352		No Especifica
104217	2012-06-13	7	7	\N	\N	f	925	4217643-5	BONANSEA CORDOVEZ, MIRIAM ORIETA	0	2012-06-12	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.33843		No Especifica
104218	2012-06-18	7	7	\N	\N	f	1043	4154798-7	SALAS CRUCES, LUIS ALBERTO	0	2012-06-13	2012-12-10	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-18 00:00:00	2012-08-30 09:40:25.341054		Retención Urinaria Aguda Repetida
104219	2012-06-14	7	7	\N	\N	f	925	4105521-9	HERRERA CARVACHO, OLGA LORETO MARÍA LU	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.343758		No Especifica
104220	2012-06-15	7	7	\N	\N	f	1155	4027242-9	SOTO ARAYA, BERTA DORILA	0	2012-06-13	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.346273		No Especifica
104221	2012-06-13	7	7	\N	\N	f	1155	4025963-5	UBILLA DE LOS SANTOS, EDUARDO ANTONIO	0	2012-06-12	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.349083		No Especifica
104222	2012-04-16	7	7	\N	\N	f	776	3790303-5	NÚÑEZ BASTÍAS, YOLANDA	0	2012-04-12	2012-12-10	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-04-16 00:00:00	2012-08-30 09:40:25.352153		Derecha
104223	2012-06-14	7	7	\N	\N	f	925	3789423-0	AGUILAR AGUILAR, DOMÍNICA RAQUEL	0	2012-06-13	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.354757		No Especifica
104224	2012-06-14	7	7	\N	\N	f	925	3756246-7	GÓMEZ VERA, JORGE LUIS	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.357716		No Especifica
104225	2012-06-19	7	7	\N	\N	f	927	3712146-0	ARAVENA CANABES, LUCÍA VICTORIA	0	2012-06-13	2012-12-10	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:25.361021		Derecha
104226	2012-08-13	7	7	\N	\N	f	773	3581213-K	MALDONADO DÍAZ, LAURA ROSA	0	2012-08-10	2012-12-10	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.363627		No Especifica
104227	2012-08-13	7	7	\N	\N	f	773	3499645-8	DE LA VEGA , SARA DEL CARMEN	0	2012-08-10	2012-12-10	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-21 00:00:00	2012-08-30 09:40:25.366111		No Especifica
104228	2012-06-21	7	7	\N	\N	f	1155	3328988-K	VEGA RIVERA, JOSÉ SEGUNDO	0	2012-06-13	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.368673		No Especifica
104229	2012-06-13	7	7	\N	\N	f	925	3126952-0	BAHAMONDES ARANDA, ADRIANA GUILLERMINA	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.371292		No Especifica
104230	2012-06-14	7	7	\N	\N	f	925	3056690-4	ASTUDILLO NAVEA, BRIGADIER	0	2012-06-12	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.373928		No Especifica
104231	2012-06-19	7	7	\N	\N	f	1155	3013200-9	MARTÍNEZ AYALA, OSCAR LUIS	0	2012-06-13	2012-12-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.376698		No Especifica
104232	2012-06-14	7	7	\N	\N	f	925	3000764-6	RIVERA , MARÍA	0	2012-06-12	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.379381		No Especifica
104233	2012-06-18	7	7	\N	\N	f	925	1840613-6	OLAVARRÍA SEPÚLVEDA, LUIS ENRIQUE	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.382064		No Especifica
104234	2012-06-13	7	7	\N	\N	f	925	1132955-1	REYES ALLENDE, FRESIA DE LAS MERCED	0	2012-06-11	2012-12-10	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:25.384622		No Especifica
104235	2012-06-18	7	7	\N	\N	f	1155	5554165-5	IBÁÑEZ CHAPARRO, PATRICIA GUADALUPE	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.387157		No Especifica
104236	2012-06-18	7	7	\N	\N	f	925	5186090-K	VARGAS CARDOZO, JUAN ORLANDO	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.389735		No Especifica
104237	2012-06-19	7	7	\N	\N	f	925	4911457-5	VERDEJO ARANCIBIA, MARGARITA GLADYS	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.392384		No Especifica
104238	2012-06-15	7	7	\N	\N	f	1155	4602190-8	PANES , MARIA AGUSTINA	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.394967		No Especifica
104239	2012-08-14	7	7	\N	\N	f	773	4378831-0	AGUILERA CISTERNAS, TERESA DEL CARMEN	0	2012-08-13	2012-12-11	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.39753		No Especifica
104240	2012-06-21	7	7	\N	\N	f	925	4241322-4	PIMENTEL AGUIRRE, MARÍA TERESA	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.400211		No Especifica
104241	2012-06-19	7	7	\N	\N	f	1155	3999389-9	BALBONTÍN BALBONTÍN, CARMEN	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.402791		No Especifica
104242	2012-06-19	7	7	\N	\N	f	1155	3933481-K	CERÓN GONZÁLEZ, JOSÉ ORLANDO	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.405345		No Especifica
104243	2012-06-19	7	7	\N	\N	f	925	3661610-5	VERGARA MORALES, DORIS MARIANA	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.407939		No Especifica
104244	2012-06-19	7	7	\N	\N	f	1155	3388208-4	GAETE BERNAL, LIDIA ROSA	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.410673		No Especifica
104245	2012-06-18	7	7	\N	\N	f	927	3125868-5	VILCHES SARAVIA, MARIO EDUARDO	0	2012-06-14	2012-12-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:25.414044		Derecha
104246	2012-06-19	7	7	\N	\N	f	925	3058162-8	LOBOS ECHEVERRÍA, PALMIRA DE LAS MERCE	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.416652		No Especifica
104247	2012-06-18	7	7	\N	\N	f	925	2816377-0	GONZÁLEZ SOTO, REGULO DE LA CRUZ	0	2012-06-14	2012-12-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.419316		No Especifica
104248	2012-06-19	7	7	\N	\N	f	1155	2246262-8	MORONI LUCERO, JORGE	0	2012-06-14	2012-12-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:25.422144		No Especifica
104249	2012-08-24	7	7	\N	\N	f	850	3671113-2	LINEROS VILCHES, JAIME	0	2012-08-14	2012-12-12	Cáncer de Próstata . {decreto nº 228}	Tratamiento	2012-08-27 00:00:00	2012-08-30 09:40:25.425297		No Especifica
104250	2012-06-20	7	7	\N	\N	f	1155	5820722-5	CISTERNAS CONTRERAS, MARGARITA ISMENIA	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-20 00:00:00	2012-08-30 09:40:25.42835		No Especifica
104251	2012-06-18	7	7	\N	\N	f	925	5820722-5	CISTERNAS CONTRERAS, MARGARITA ISMENIA	0	2012-06-15	2012-12-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.431164		No Especifica
104252	2012-06-26	7	7	\N	\N	f	1155	5115214-K	ENDARA GALASSI, BLANCA NIEVES	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.433789		No Especifica
104253	2012-06-18	7	7	\N	\N	f	1155	4880309-1	ESCOBAR DINAMARCA, GLADYS DE LAS MERCED	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-18 00:00:00	2012-08-30 09:40:25.436407		No Especifica
104254	2012-06-19	7	7	\N	\N	f	1043	4869139-0	FIGUEROA ARAYA, MARIO DEL TRÁNSITO	0	2012-06-15	2012-12-12	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-19 00:00:00	2012-08-30 09:40:25.439332		Retención Urinaria Aguda Repetida
104255	2012-06-18	7	7	\N	\N	f	1155	4584927-9	ARAYA ASPE, MILITINA DEL CARMEN	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-18 00:00:00	2012-08-30 09:40:25.442611		No Especifica
104256	2012-06-19	7	7	\N	\N	f	1155	4317974-8	TELLO DÍAZ, CARLOS ENRIQUE	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-19 00:00:00	2012-08-30 09:40:25.445767		No Especifica
104257	2012-06-26	7	7	\N	\N	f	1155	3975509-2	SEPÚLVEDA MOLINA, ROSA BENITA	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.448999		No Especifica
104258	2012-06-19	7	7	\N	\N	f	925	3624677-4	JIMÉNEZ DE LA BARRA, GRACIELA DEL CARMEN	0	2012-06-15	2012-12-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.451992		No Especifica
104259	2012-06-26	7	7	\N	\N	f	1155	3584590-9	AGUILAR ARREDONDO, BRITANIA TEODOLINDA	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.454783		No Especifica
104260	2012-06-19	7	7	\N	\N	f	1043	2708026-K	BUSTOS NÚÑEZ, HUGO EUGENIO	0	2012-06-15	2012-12-12	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-19 00:00:00	2012-08-30 09:40:25.457591		Retención Urinaria Aguda Repetida
104261	2012-06-18	7	7	\N	\N	f	1155	1851937-2	REYES OÑATE, OTEALDO	0	2012-06-15	2012-12-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-18 00:00:00	2012-08-30 09:40:25.460562		No Especifica
104262	2012-06-19	7	7	\N	\N	f	925	3663543-6	SALINAS HIDALGO, FLORENCIO DEL ROSARI	0	2012-06-16	2012-12-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.464032		No Especifica
104263	2012-04-20	7	7	\N	\N	f	776	4159251-6	MUÑOZ TRONCOSO, ZULEMA DEL CARMEN	0	2012-04-18	2012-12-14	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-04-20 00:00:00	2012-08-30 09:40:25.467604		Derecha
104264	2012-08-20	7	7	\N	\N	f	773	5105603-5	SAAVEDRA CORTÉS, VIOLETA	0	2012-08-16	2012-12-14	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.47105		No Especifica
104265	2012-06-21	7	7	\N	\N	f	997	19336587-6	CATALÁN SÁNCHEZ, GUSTAVO ERNESTO	0	2012-06-18	2012-12-17	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:25.473982		No Especifica
104266	2012-06-25	7	7	\N	\N	f	997	18899230-7	LABRA GALLARDO, EDUARDO ANDRÉS	0	2012-06-20	2012-12-17	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-03 00:00:00	2012-08-30 09:40:25.476799		No Especifica
104267	2012-06-22	7	7	\N	\N	f	997	14524043-3	LÓPEZ GONZÁLEZ, ARIEL ALAMIRO	0	2012-06-18	2012-12-17	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.479604		No Especifica
104268	2012-06-20	7	7	\N	\N	f	1155	12035914-2	BASCUÑÁN ROSAS, AMALIA DEL CARMEN	0	2012-06-19	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-20 00:00:00	2012-08-30 09:40:25.482527		No Especifica
104269	2012-06-26	7	7	\N	\N	f	925	11330378-6	ACCURSO TORO, JUAN CRISTIAN	0	2012-06-18	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.485337		No Especifica
104270	2012-06-22	7	7	\N	\N	f	997	10703172-3	DELPINO LLANOS, MARIO HUMBERTO	0	2012-06-18	2012-12-17	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.48816		No Especifica
104271	2012-06-25	7	7	\N	\N	f	927	6269045-3	ACEVEDO VERA, BRUNO LUIS	0	2012-06-18	2012-12-17	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:25.492036		Derecha
104272	2012-06-22	7	7	\N	\N	f	1155	6085556-0	SAU RIFFO, ANA MARÍA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.49506		No Especifica
104273	2012-06-26	7	7	\N	\N	f	1155	5849789-4	VILLALÓN VALLEJOS, NANCY IRIS	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.497716		No Especifica
104274	2012-06-22	7	7	\N	\N	f	1155	5841501-4	CANCINO ARANDA, ADRIANA DE LAS MERCE	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.50059		No Especifica
104275	2012-06-25	7	7	\N	\N	f	1155	5746509-3	DELGADO LEIVA, ISOLINA ERIKA DEL CA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:25.503401		No Especifica
104276	2012-06-21	7	7	\N	\N	f	1155	5732705-7	FERNÁNDEZ RIVERA, ELSA YOLANDA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-21 00:00:00	2012-08-30 09:40:25.506218		No Especifica
104277	2012-06-26	7	7	\N	\N	f	1155	5424453-3	CONTRERAS MORALES, ROSA ZOILA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.509029		No Especifica
104278	2012-06-20	7	7	\N	\N	f	925	5414679-5	ORTIZ MELO, MARIO JORGE ANTONIO	0	2012-06-18	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.511747		No Especifica
104279	2012-06-21	7	7	\N	\N	f	1155	5330987-9	GONZÁLEZ ESCOBAR, IRMA DOMINGA	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-21 00:00:00	2012-08-30 09:40:25.514367		No Especifica
104280	2012-06-25	7	7	\N	\N	f	927	5004860-8	OSORIO CÁCERES, SARA DEL CARMEN	0	2012-06-19	2012-12-17	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:25.51731		Derecha
104281	2012-06-20	7	7	\N	\N	f	1155	4932543-6	SANTANDER HERRERA, VILMA BEATRIZ	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-20 00:00:00	2012-08-30 09:40:25.52007		No Especifica
104282	2012-06-20	7	7	\N	\N	f	1155	4643859-0	GILIBERTO GILIBERTO, GENINA GLENDA	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-20 00:00:00	2012-08-30 09:40:25.522802		No Especifica
104283	2012-06-26	7	7	\N	\N	f	925	4562120-0	DELGADO MUÑOZ, MARGARITA VICTORIA	0	2012-06-20	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.525442		No Especifica
104284	2012-06-26	7	7	\N	\N	f	925	4493218-0	CAMPOS CARRASCO, GAVINA DEL CARMEN	0	2012-06-18	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.528207		No Especifica
104285	2012-06-21	7	7	\N	\N	f	925	4490691-0	MELLADO BUSTOS, MARIO EUGENIO	0	2012-06-19	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.531032		No Especifica
104286	2012-06-20	7	7	\N	\N	f	925	4263786-6	PÉREZ MUÑOZ, LIDIA DE MERCEDES	0	2012-06-18	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.533862		No Especifica
104287	2012-06-22	7	7	\N	\N	f	1043	4205195-0	ROJAS FERMONVOI, ANGEL SEGUNDO	0	2012-06-18	2012-12-17	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-22 00:00:00	2012-08-30 09:40:25.536858		Retención Urinaria Aguda Repetida
104288	2012-06-20	7	7	\N	\N	f	1155	4176481-3	ÁVILA BOUVIY, HELIETTE DEL ROSARIO	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-20 00:00:00	2012-08-30 09:40:25.539762		No Especifica
104289	2012-06-21	7	7	\N	\N	f	925	3988957-9	NAVARRETE DELGADO, MARCOS HÉCTOR	0	2012-06-20	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.54242		No Especifica
104290	2012-06-22	7	7	\N	\N	f	1155	3979365-2	CONEJEROS FRÍAS, FILOMENA DEL CARMEN	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.545032		No Especifica
104291	2012-06-26	7	7	\N	\N	f	1155	3978899-3	OTEY MORALES, MANUEL	0	2012-06-19	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.547646		No Especifica
104292	2012-06-22	7	7	\N	\N	f	1155	3941127-K	GONZÁLEZ , NELLY VIOLETA	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-22 00:00:00	2012-08-30 09:40:25.550392		No Especifica
104293	2012-06-26	7	7	\N	\N	f	1155	3940560-1	MUNIZAGA NÚÑEZ, OLGA ERNESTINA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.553218		No Especifica
104294	2012-06-21	7	7	\N	\N	f	925	3749557-3	FREDES DÍAZ, LUIS ISAÍAS	0	2012-06-18	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.556022		No Especifica
104295	2012-06-21	7	7	\N	\N	f	1155	3520616-7	ROJAS FERNÁNDEZ, UBERLINDA ROSALBA	0	2012-06-20	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-21 00:00:00	2012-08-30 09:40:25.558878		No Especifica
104296	2012-06-26	7	7	\N	\N	f	1155	3347614-0	NEIRA GUTIÉRREZ, NICOLÁS	0	2012-06-19	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.561976		No Especifica
104297	2012-06-21	7	7	\N	\N	f	1155	3224170-0	ESPINOZA FIGUEROA, EDUARDO EMILIO	0	2012-06-18	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-21 00:00:00	2012-08-30 09:40:25.564615		No Especifica
104298	2012-06-21	7	7	\N	\N	f	1155	3222155-6	ÁLVAREZ , FRECIA DEL CARMEN	0	2012-06-19	2012-12-17	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-21 00:00:00	2012-08-30 09:40:25.567221		No Especifica
104299	2012-06-25	7	7	\N	\N	f	927	3013343-9	VERGARA URBINA, ADRIANA DEL CARMEN	0	2012-06-19	2012-12-17	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.570295		Bilateral
104300	2012-07-03	7	7	\N	\N	f	925	2641632-9	VELASCO SEGURA, ANGEL EDUARDO	0	2012-06-20	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.573105		No Especifica
104301	2012-06-21	7	7	\N	\N	f	925	2234256-8	CASTRO NAVARRETE, FÉLIX ROBERTO	0	2012-06-19	2012-12-17	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.575871		No Especifica
104302	2012-06-25	7	7	\N	\N	f	927	1918360-2	QUINTANA VERA, JUAN FRANCISCO	0	2012-06-19	2012-12-17	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-01 00:00:00	2012-08-30 09:40:25.578996		Derecha
104303	2012-06-26	7	7	\N	\N	f	925	6179441-7	ARANCIBIA FIGUEROA, ELISA DEL CARMEN	0	2012-06-21	2012-12-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.58177		No Especifica
104304	2012-06-25	7	7	\N	\N	f	927	5969119-8	CORTEZ MARTÍNEZ, PEDRO PABLO	0	2012-06-21	2012-12-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-01 00:00:00	2012-08-30 09:40:25.584688		Bilateral
104305	2012-06-25	7	7	\N	\N	f	927	5448884-K	NÚÑEZ MUÑOZ, BEATRIZ DEL CARMEN	0	2012-06-21	2012-12-18	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:25.587959		Derecha
104306	2012-06-26	7	7	\N	\N	f	927	5094675-4	ROJAS URBINA, MARÍA ELENA DEL CARM	0	2012-06-21	2012-12-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:25.591724		Bilateral
104307	2012-07-03	7	7	\N	\N	f	925	4731745-2	GALLARDO LAZO, MARÍA FELICIA	0	2012-06-21	2012-12-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.59462		No Especifica
104308	2012-07-03	7	7	\N	\N	f	925	4173578-3	VALENZUELA MARTINEZ, JOSE	0	2012-06-21	2012-12-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.597442		No Especifica
104309	2012-06-29	7	7	\N	\N	f	1155	4171843-9	LAZO PINTO, VICTORIA ELENA	0	2012-06-21	2012-12-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.60053		No Especifica
104310	2012-06-22	7	7	\N	\N	f	925	4134747-3	BERMÚDEZ LEIVA, DOLORES DE LAS MERCE	0	2012-06-21	2012-12-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.603434		No Especifica
104311	2012-06-26	7	7	\N	\N	f	1155	4036424-2	SQUADRITO LOMBARDO, HORACIO	0	2012-06-21	2012-12-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.606246		No Especifica
104312	2012-06-25	7	7	\N	\N	f	927	3467176-1	DÍAZ LEPPEZ, JUAN WALTER	0	2012-06-21	2012-12-18	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:25.609451		Derecha
104313	2012-06-25	7	7	\N	\N	f	927	2206161-5	BOBADILLA GUTIÉRREZ, LUIS ANÍBAL	0	2012-06-21	2012-12-18	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:25.613115		Derecha
104314	2012-06-26	7	7	\N	\N	f	927	798366-2	CAPELLI CIFUENTES, REINALDO SEGUNDO	0	2012-06-21	2012-12-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-06-26 00:00:00	2012-08-30 09:40:25.616367		Bilateral
104315	2012-08-24	7	7	\N	\N	f	773	4708311-7	ESPARZA BURGOS, MARÍA GERTRUDIS	0	2012-08-21	2012-12-19	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-24 00:00:00	2012-08-30 09:40:25.61952		No Especifica
104316	2012-07-18	7	7	\N	\N	f	925	4748895-8	GUTIÉRREZ MORALES, MARGARITA	0	2012-06-22	2012-12-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.622436		No Especifica
104317	2012-07-03	7	7	\N	\N	f	1155	4613631-4	VALENCIA ESPINOZA, ELSA	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.625242		No Especifica
104318	2012-06-25	7	7	\N	\N	f	1155	4343162-5	AGUILERA AGUILERA, EMILIANO DEL CARMEN	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:25.628051		No Especifica
104319	2012-06-26	7	7	\N	\N	f	1155	4318432-6	FRÍAS MONTIEL, OSCAR GUSTAVO	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.630994		No Especifica
104320	2012-06-26	7	7	\N	\N	f	1043	4073184-9	VALDÉS HERNÁNDEZ, RICARDO ALEJANDRO	0	2012-06-22	2012-12-19	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-26 00:00:00	2012-08-30 09:40:25.633973		Retención Urinaria Aguda Repetida
104321	2012-06-25	7	7	\N	\N	f	1155	3937159-6	VILLARROEL HENRÍQUEZ, JUANA EVARISTA	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:25.636851		No Especifica
104322	2012-06-25	7	7	\N	\N	f	1155	3723769-8	RIVERA AVELLO, GLADYS INÉS	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-25 00:00:00	2012-08-30 09:40:25.63968		No Especifica
104323	2012-06-29	7	7	\N	\N	f	1155	3258937-5	GAJARDO MUÑOZ, MANUEL JESÚS	0	2012-06-22	2012-12-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.642486		No Especifica
104324	2012-06-26	7	7	\N	\N	f	925	3134823-4	RIVERA GATICA, HUMBERTO LUIS	0	2012-06-22	2012-12-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.6457		No Especifica
104325	2012-06-26	7	7	\N	\N	f	925	2547923-8	VALENCIA VICENCIO, LUZ ESTER	0	2012-06-22	2012-12-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-28 00:00:00	2012-08-30 09:40:25.648525		No Especifica
104326	2012-08-22	7	7	\N	\N	f	773	3240825-7	PÉREZ MUÑOZ, HUMBERTO SEGUNDO	0	2012-08-21	2012-12-19	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.65131		No Especifica
104327	2012-08-22	7	7	\N	\N	f	773	5504243-8	TILLERÍA RUIZ, SILVIA	0	2012-08-21	2012-12-19	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.654086		No Especifica
104328	2012-08-22	7	7	\N	\N	f	773	7914717-6	ORTIZ OSSANDÓN, MARÍA SOLEDAD	0	2012-08-21	2012-12-19	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-28 00:00:00	2012-08-30 09:40:25.656889		No Especifica
104329	2012-08-24	7	7	\N	\N	f	773	5291583-K	TORRES MUÑOZ, LUCY AMALIA	0	2012-08-22	2012-12-20	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-24 00:00:00	2012-08-30 09:40:25.659749		No Especifica
104330	2012-08-24	7	7	\N	\N	f	773	4801362-7	CÉSPEDES BERMUDES, MARÍA EUGENIA	0	2012-08-22	2012-12-20	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-24 00:00:00	2012-08-30 09:40:25.66269		No Especifica
104331	2012-06-26	7	7	\N	\N	f	1155	4725996-7	LORCA POZO, BERTA FLORENCIA	0	2012-06-23	2012-12-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.665564		No Especifica
104332	2012-08-24	7	7	\N	\N	f	773	3008015-7	SILVA CONTRERAS, AÍDA DE LAS MERCEDES	0	2012-08-23	2012-12-21	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-24 00:00:00	2012-08-30 09:40:25.66843		No Especifica
104333	2012-05-08	7	7	\N	\N	f	776	2431335-2	CANCINO VERA, LEONICIA EDELMIRA	0	2012-04-25	2012-12-21	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2011-05-14 00:00:00	2012-08-30 09:40:25.671508		Derecha
104334	2012-08-27	7	7	\N	\N	f	773	2071682-7	ILABACA ILABACA, MANUELA	0	2012-08-23	2012-12-21	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Rodilla Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Rodilla Leve o Moderada	2012-08-27 00:00:00	2012-08-30 09:40:25.674416		No Especifica
104335	2012-06-26	7	7	\N	\N	f	925	9132410-5	PULGAR ESCOBAR, VERÓNICA ELVIRA	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.677307		No Especifica
104336	2012-06-28	7	7	\N	\N	f	925	8686538-6	MERIÑO ESCALONA, MARÍA INÉS	0	2012-06-27	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-28 00:00:00	2012-08-30 09:40:25.680315		No Especifica
104337	2012-07-12	7	7	\N	\N	f	925	8372875-2	JACOME SEPÚLVEDA, LILIANA PATRICIA	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:25.683276		No Especifica
104338	2012-06-26	7	7	\N	\N	f	1043	7221483-8	PÉREZ PACHECO, CARLOS PATRICIO	0	2012-06-25	2012-12-24	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-27 00:00:00	2012-08-30 09:40:25.68628		Retención Urinaria Aguda Repetida
104339	2012-06-26	7	7	\N	\N	f	925	6987976-4	PÉREZ ALAMO, ROSA DEL CARMEN	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.689179		No Especifica
104340	2012-06-29	7	7	\N	\N	f	925	6210691-3	DUBO ZEPEDA, HUMBERTO DEL CARMEN	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-29 00:00:00	2012-08-30 09:40:25.692109		No Especifica
104341	2012-06-28	7	7	\N	\N	f	1155	5617063-4	GONZÁLEZ VENEGAS, MARÍA AMANDA	0	2012-06-25	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.694938		No Especifica
104342	2012-06-28	7	7	\N	\N	f	1155	5309353-1	FERNÁNDEZ RIVERA, CARMEN ORIANA	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.697758		No Especifica
104343	2012-07-03	7	7	\N	\N	f	925	5214272-5	VERGARA ÁLVAREZ, MARÍA ELBA	0	2012-06-26	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-03 00:00:00	2012-08-30 09:40:25.700866		No Especifica
104344	2012-06-28	7	7	\N	\N	f	1155	5043460-5	TOLEDO , OLGA	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.703634		No Especifica
104345	2012-05-08	7	7	\N	\N	f	776	4921730-7	PAREDES ÁLVAREZ, ALICIA GERTRUDIS	0	2012-04-27	2012-12-24	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-10 00:00:00	2012-08-30 09:40:25.706494		Derecha
104346	2012-05-04	7	7	\N	\N	f	776	4867545-K	ESCUDERO PERALTA, ALFREDO SEGUNDO	0	2012-04-27	2012-12-24	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-30 00:00:00	2012-08-30 09:40:25.709365		Derecha
104347	2012-06-29	7	7	\N	\N	f	1155	4803519-1	ORREGO GUZMÁN, OLDA DEL ROSARIO	0	2012-06-26	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.712617		No Especifica
104348	2012-06-26	7	7	\N	\N	f	1043	4784033-3	CHÁVEZ VÉLIZ, JOSÉ FRANCISCO	0	2012-06-25	2012-12-24	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-27 00:00:00	2012-08-30 09:40:25.715953		Retención Urinaria Aguda Repetida
104349	2012-06-26	7	7	\N	\N	f	1155	4734014-4	CABRERA CARVAJAL, IVÁN JESÚS	0	2012-06-25	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-26 00:00:00	2012-08-30 09:40:25.71881		No Especifica
104350	2012-06-28	7	7	\N	\N	f	925	4720380-5	ESCOBAR ARANCIBIA, MARÍA TERESA	0	2012-06-26	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-28 00:00:00	2012-08-30 09:40:25.721834		No Especifica
104351	2012-07-03	7	7	\N	\N	f	1155	4574652-6	CASTRO ROBLES, GLORIA DEL CARMEN	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.724658		No Especifica
104352	2012-06-26	7	7	\N	\N	f	925	4506354-2	URETA MARÍN, ROSA ISABEL	0	2012-06-26	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.728		No Especifica
104353	2012-07-18	7	7	\N	\N	f	1155	4242495-1	OLIVARES ZORRICUETA, ADELA NORMA	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:25.730834		No Especifica
104354	2012-06-29	7	7	\N	\N	f	1155	4196478-2	BUSTAMANTE LÓPEZ, EDMUNDO DANTE	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.733708		No Especifica
104355	2012-06-26	7	7	\N	\N	f	1155	4192302-4	ROMÁN GONZÁLEZ, BERTA ALICIA	0	2012-06-25	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.736475		No Especifica
104356	2012-06-29	7	7	\N	\N	f	1043	3942380-4	CAPURRO VENEGAS, JOSÉ ENRIQUE	0	2012-06-27	2012-12-24	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-06-29 00:00:00	2012-08-30 09:40:25.739443		Retención Urinaria Aguda Repetida
104357	2012-05-10	7	7	\N	\N	f	776	3859899-6	RIVEROS AGUIRRE, GIL RUBÉN	0	2012-04-27	2012-12-24	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2011-05-14 00:00:00	2012-08-30 09:40:25.742477		Derecha
104358	2012-06-26	7	7	\N	\N	f	1155	3824547-3	MOYA GAJARDO, ELVIRA DEL CARMEN	0	2012-06-26	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.745304		No Especifica
104359	2012-06-26	7	7	\N	\N	f	925	3489385-3	OYARZÚN GALLARDO, JOSÉ ROBERTO	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-27 00:00:00	2012-08-30 09:40:25.748184		No Especifica
104360	2012-06-29	7	7	\N	\N	f	1155	3458364-1	LEMUS ORELLANA, ROBERTO	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.751134		No Especifica
104361	2012-06-29	7	7	\N	\N	f	925	3450539-K	OLIVARES VÁSQUEZ, ANGEL DEL CARMEN	0	2012-06-25	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-06-29 00:00:00	2012-08-30 09:40:25.753975		No Especifica
104362	2012-07-03	7	7	\N	\N	f	925	3208314-5	RIVERA , MARGARITA DEL CARMEN	0	2012-06-26	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-03 00:00:00	2012-08-30 09:40:25.756823		No Especifica
104363	2012-06-29	7	7	\N	\N	f	1155	2829969-9	HERMOSILLA REYES, MANUEL	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.759673		No Especifica
104364	2012-07-06	7	7	\N	\N	f	927	2639877-0	CABELLO AHUMADA, GUILLERMO	0	2012-06-26	2012-12-24	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:25.763267		Derecha
104365	2012-05-10	7	7	\N	\N	f	776	2572442-9	GUERRERO MARAMBIO, DARÍO CARLOS	0	2012-04-27	2012-12-24	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2011-05-14 00:00:00	2012-08-30 09:40:25.766247		Derecha
104366	2012-07-05	7	7	\N	\N	f	925	1902139-4	DEL CANTO SAA, RENATO AMADO	0	2012-06-27	2012-12-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.769178		No Especifica
104367	2012-07-06	7	7	\N	\N	f	927	1847774-2	CORONA MERY, MARÍA BERNARDITA	0	2012-06-26	2012-12-24	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:25.772994		Derecha
104368	2012-06-29	7	7	\N	\N	f	1155	1722265-1	MUÑOZ HERRERA, CECILIA	0	2012-06-27	2012-12-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.775816		No Especifica
104369	2012-08-28	7	7	\N	\N	f	773	4067145-5	CORVALÁN GALLARDO, ROBERTO	0	2012-08-24	2012-12-24	Artrosis de Cadera y/o Rodilla Leve o Moderada Artrosis de Cadera Leve o Moderada {decreto nº 44}	Atención por Especialista Artrosis de Cadera Leve o Moderada	2012-08-29 00:00:00	2012-08-30 09:40:25.778804		No Especifica
104370	2012-08-02	7	7	\N	\N	f	1155	8416346-5	ORTEGA VALENCIA, GLADYS DE LAS MERCED	0	2012-06-28	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:25.781677		No Especifica
104371	2012-07-03	7	7	\N	\N	f	927	6120364-8	CORTÉS SEPÚLVEDA, CARLOS GUILLERMO	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:25.784813		Derecha
104372	2012-07-03	7	7	\N	\N	f	927	5762260-1	CARMONA VICUÑA, MARÍA XIMENA	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-07-03 00:00:00	2012-08-30 09:40:25.78811		Izquierda
104373	2012-06-09	7	7	\N	\N	f	1155	5463065-4	ROMERO BADILLA, JUAN AGUSTO	0	2012-06-28	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.791128		No Especifica
104374	2012-06-09	7	7	\N	\N	f	1155	4691330-2	ARÉVALO CABALLERO, MARTA DE LAS MERCEDE	0	2012-06-29	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.793999		No Especifica
104375	2012-07-03	7	7	\N	\N	f	927	4638053-3	TOLEDO FERNÁNDEZ, MATILDE INÉS	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:25.79712		Izquierda
104376	2012-07-03	7	7	\N	\N	f	1155	4074456-8	MARTÍNEZ CÁCERES, HERNÁN RODOLFO	0	2012-06-28	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.800022		No Especifica
104377	2012-07-13	7	7	\N	\N	f	927	4065538-7	ZENTENO DÍAZ, NORMA DEL CARMEN	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:25.803162		Derecha
104378	2012-07-13	7	7	\N	\N	f	927	3927874-K	NILO ESPINOZA, JUANA MARINA	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-24 00:00:00	2012-08-30 09:40:25.806717		Izquierda
104379	2012-07-04	7	7	\N	\N	f	927	3633944-6	GATICA VEGA, NIEVES DEL CARMEN	0	2012-06-28	2012-12-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:25.810077		Bilateral
104380	2012-07-03	7	7	\N	\N	f	927	3505326-3	DÍAZ ROMERO, ROLANDO ALFONSO	0	2012-06-28	2012-12-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:25.813267		Bilateral
104381	2012-06-09	7	7	\N	\N	f	1155	3277692-2	RIQUELME , SILVIA ROSA	0	2012-06-29	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.816085		No Especifica
104382	2012-06-09	7	7	\N	\N	f	1155	3085433-0	GODOY CORDERO, PETRONILA	0	2012-06-28	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.818893		No Especifica
104383	2012-07-03	7	7	\N	\N	f	927	3046182-7	DÍAZ CUEVAS, SILVIA DEL CARMEN	0	2012-06-28	2012-12-26	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:25.822022		Bilateral
104384	2012-07-03	7	7	\N	\N	f	1155	2938622-6	OVALLE CIFUENTES, PAULINA ADRIANA	0	2012-06-28	2012-12-26	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.82481		No Especifica
104385	2012-07-04	7	7	\N	\N	f	1043	2824590-4	MELÉNDEZ TAPIA, JORGE	0	2012-06-29	2012-12-26	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-04 00:00:00	2012-08-30 09:40:25.827794		Retención Urinaria Aguda Repetida
104386	2012-07-11	7	7	\N	\N	f	927	2535150-9	OYARZÚN GUERRERO, LIDIA	0	2012-06-29	2012-12-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-07 00:00:00	2012-08-30 09:40:25.831077		Izquierda
104387	2012-07-03	7	7	\N	\N	f	927	1631423-4	GÓMEZ OSORIO, WALDO ENRIQUE	0	2012-06-28	2012-12-26	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:25.834204		Izquierda
104388	2012-07-05	7	7	\N	\N	f	997	18996735-7	CAVIEDES ROMERO, NICOLÁS IGNACIO	0	2012-07-04	2012-12-31	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.837423		No Especifica
104389	2012-07-05	7	7	\N	\N	f	925	8143496-4	VERA RÍOS, ANA CRISTINA	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.841031		No Especifica
104390	2012-06-09	7	7	\N	\N	f	925	8024681-1	RUBIO AHUMADA, JORGE EUGENIO	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.843907		No Especifica
104391	2012-06-09	7	7	\N	\N	f	925	7330980-8	CASTRO VEGAS, PABLA DEL PILAR	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.846771		No Especifica
104392	2012-06-09	7	7	\N	\N	f	925	5881506-3	GONZÁLEZ SANHUEZA, MARÍA ELICENIA	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.850179		No Especifica
104393	2012-07-06	7	7	\N	\N	f	925	5733452-5	DONOSO DOREN, MYRTA ESTER	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.85309		No Especifica
104394	2012-07-06	7	7	\N	\N	f	1155	5673288-8	HERRERA ÁVILA, MARGARITA DEL CARMEN	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.855911		No Especifica
104395	2012-06-09	7	7	\N	\N	f	1155	5662783-9	TABILO TABILO, CECILIA ADRIANA	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.85878		No Especifica
104396	2012-06-09	7	7	\N	\N	f	1155	5622830-6	CONTRERAS GONZÁLEZ, CARMEN GLORIA	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.861662		No Especifica
104397	2012-05-08	7	7	\N	\N	f	776	5608014-7	DE LA O NAVARRO, EDUARDO ENRIQUE	0	2012-05-04	2012-12-31	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-10 00:00:00	2012-08-30 09:40:25.864735		Derecha
104398	2012-07-05	7	7	\N	\N	f	1155	5604597-K	ORREGO PACHECO, HILDA NELLYS	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.867567		No Especifica
104399	2012-07-05	7	7	\N	\N	f	1155	5534287-3	TOLOZA BIZAMA, CONSUELO DEL CARMEN	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.870919		No Especifica
104400	2012-06-09	7	7	\N	\N	f	925	5448879-3	ARANCIBIA , JUANA ROSA	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.874393		No Especifica
104401	2012-06-09	7	7	\N	\N	f	1043	5348311-9	PALOMINO OYARCE, SERGIO HERNÁN	0	2012-07-04	2012-12-31	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-09 00:00:00	2012-08-30 09:40:25.877247		Retención Urinaria Aguda Repetida
104402	2012-06-09	7	7	\N	\N	f	1155	5243584-6	ALLENDE ÁLVAREZ, MARÍA ISABEL	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.880231		No Especifica
104403	2012-06-09	7	7	\N	\N	f	1043	5187668-7	PIMENTEL MOLINA, JORGE HERNÁN	0	2012-07-04	2012-12-31	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-09 00:00:00	2012-08-30 09:40:25.88349		Retención Urinaria Aguda Repetida
104404	2012-07-05	7	7	\N	\N	f	925	5162591-9	ARENAS CANTEROS, EDITH DEL CARMEN	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.887139		No Especifica
104405	2012-07-05	7	7	\N	\N	f	1155	5144726-3	MERLET AGUILERA, JOSÉ LUIS DEL CARMEN	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.890776		No Especifica
104406	2012-07-05	7	7	\N	\N	f	925	5112268-2	CUYUL REMOLCOY, NAVIA ERNESTINA	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.894207		No Especifica
104407	2012-07-06	7	7	\N	\N	f	1155	4993483-1	MARTÍNEZ CISTERNAS, ROSALÍA	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.896999		No Especifica
104408	2012-05-08	7	7	\N	\N	f	776	4799250-8	HENRÍQUEZ JARA, RIGOBERTO	0	2012-05-03	2012-12-31	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-05-10 00:00:00	2012-08-30 09:40:25.900162		Izquierda
104409	2012-06-09	7	7	\N	\N	f	1043	4678969-5	GONZÁLEZ MOLINA, RICARDO EXEQUIEL	0	2012-07-04	2012-12-31	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-09 00:00:00	2012-08-30 09:40:25.903147		Retención Urinaria Aguda Repetida
104410	2012-07-05	7	7	\N	\N	f	1155	4566797-9	GÁLVEZ BASTÍAS, SUSANA	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.906025		No Especifica
104411	2012-06-09	7	7	\N	\N	f	1155	4555834-7	BURGOS RIQUELME, BERTA	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.908912		No Especifica
104412	2012-06-09	7	7	\N	\N	f	925	4479371-7	ARCOS LOYOLA, EDUARDO SEGUNDO	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.911956		No Especifica
104413	2012-05-08	7	7	\N	\N	f	776	4354918-9	IBACETA ROMO, LINDOR SEGUNDO	0	2012-05-04	2012-12-31	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-05-10 00:00:00	2012-08-30 09:40:25.914873		Izquierda
104414	2012-06-09	7	7	\N	\N	f	1155	4144461-4	RAMÍREZ VALDÉS, RINA ISABEL	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.918135		No Especifica
104415	2012-07-05	7	7	\N	\N	f	925	3549072-8	PAREDES PÉREZ, ELBA	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-05 00:00:00	2012-08-30 09:40:25.921512		No Especifica
104416	2012-06-09	7	7	\N	\N	f	925	3547352-1	PÉREZ CABAÑAS, LAURA LUCÍA	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.924366		No Especifica
104417	2012-07-04	7	7	\N	\N	f	1155	3542455-5	POBLETE SEGOVIA, JULIO ROBERTO	0	2012-07-03	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.927187		No Especifica
104418	2012-06-09	7	7	\N	\N	f	925	3529514-3	PIZARRO ROJAS, MARGARITA GLADYS	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.93018		No Especifica
104419	2012-07-06	7	7	\N	\N	f	1155	3461929-8	RAMÍREZ ULLOA, BALDOMERO WENCESLAO	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.932978		No Especifica
104420	2012-06-09	7	7	\N	\N	f	1155	3221336-7	MADARIAGA CANALES, SERGIO GASPAR	0	2012-07-04	2012-12-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.935817		No Especifica
104421	2012-06-09	7	7	\N	\N	f	925	3151567-K	NÚÑEZ SEGURA, JULIA HAYDÉE	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.938728		No Especifica
104422	2012-06-09	7	7	\N	\N	f	925	3026803-2	SÁNCHEZ CÉSPED, ROSA HERMINIA	0	2012-07-04	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.941774		No Especifica
104423	2012-07-06	7	7	\N	\N	f	925	2056512-8	TAPIA ZÚÑIGA, LÁZARO HUMBERTO AMAD	0	2012-07-03	2012-12-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.944618		No Especifica
104424	2012-07-10	7	7	\N	\N	f	927	8651799-K	VALDIVIA MENESES, SERAFÍN DEL CARMEN	0	2012-07-05	2013-01-02	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:25.948151		Derecha
104425	2012-07-10	7	7	\N	\N	f	1043	7235049-9	GÓMEZ TORRES, ARIEL FERNANDO	0	2012-07-06	2013-01-02	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-10 00:00:00	2012-08-30 09:40:25.951074		Retención Urinaria Aguda Repetida
104426	2012-07-06	7	7	\N	\N	f	925	6118584-4	CÓRDOVA RIQUELME, AMELIA ROSA	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.953951		No Especifica
104427	2012-07-10	7	7	\N	\N	f	1155	5883875-6	MANSILLA ANTILEF, ELENA DEL CARMEN	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.956805		No Especifica
104428	2012-06-09	7	7	\N	\N	f	925	5873724-0	SALAZAR SILVA, LUIS GONZALO	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.95983		No Especifica
104429	2012-06-09	7	7	\N	\N	f	925	5444832-5	ÁLVAREZ SARAVIA, NOLFA DEL CARMEN	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.962983		No Especifica
104430	2012-07-06	7	7	\N	\N	f	1155	5288161-7	RETAMAL BARROS, JUANA MARÍA	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.965799		No Especifica
104431	2012-07-12	7	7	\N	\N	f	1155	5187983-K	SEGUEL ESPINOZA, PEDRO FERNANDO	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:25.968762		No Especifica
104432	2012-07-13	7	7	\N	\N	f	927	5104755-9	CISTERNAS FEBRE, BLANCA ESTER	0	2012-07-05	2013-01-02	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:25.9725		Bilateral
104433	2012-07-10	7	7	\N	\N	f	1155	4976660-2	CID GALINDO, EUGENIA GERALDINA	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.975356		No Especifica
104434	2012-07-10	7	7	\N	\N	f	927	4971377-0	CAROCA MORALES, ROSA CELESTINA	0	2012-07-05	2013-01-02	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:25.978978		Bilateral
104435	2012-07-10	7	7	\N	\N	f	1155	4897318-3	TRUJILLO TRUJILLO, MARIANO SEGUNDO	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.982005		No Especifica
104436	2012-07-10	7	7	\N	\N	f	1155	4835001-1	SÁNCHEZ CÁRCAMO, BLANCA ESTER	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.985015		No Especifica
104437	2012-06-09	7	7	\N	\N	f	1155	4824568-4	MUNIZAGA MENESES, EDUARDO ENRIQUE	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:25.988098		No Especifica
104438	2012-07-12	7	7	\N	\N	f	1155	4801131-4	GONZÁLEZ GAETE, SARA ROSA	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:25.991211		No Especifica
104439	2012-07-06	7	7	\N	\N	f	1155	4788756-9	RODRÍGUEZ RODRÍGUEZ, JUANA DEL CARMEN	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:25.994272		No Especifica
104440	2012-07-10	7	7	\N	\N	f	1155	4749691-8	MARTÍNEZ SOTO, ORGANDA	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:25.997253		No Especifica
104441	2012-07-10	7	7	\N	\N	f	1155	4742416-K	ACOSTA JAMES, SABINO ALFONSO	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:26.000462		No Especifica
104442	2012-07-10	7	7	\N	\N	f	1043	4644354-3	NAREDO FUENTES, JOSÉ ERNESTO	0	2012-07-06	2013-01-02	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-10 00:00:00	2012-08-30 09:40:26.003487		Retención Urinaria Aguda Repetida
104443	2012-07-06	7	7	\N	\N	f	925	4639492-5	PINTO GAMBOA, ANÍBAL HERNÁN	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:26.006553		No Especifica
104444	2012-07-17	7	7	\N	\N	f	1155	4539457-3	ESPINOZA ARANCIBIA, MARÍA DAMIANA DE LAS	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.009648		No Especifica
104445	2012-07-06	7	7	\N	\N	f	925	4492486-2	DÍAZ DÍAZ, JAIME ERNESTO	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-06 00:00:00	2012-08-30 09:40:26.012888		No Especifica
104446	2012-07-10	7	7	\N	\N	f	1043	4434625-7	MUÑOZ MUÑOZ, JORGE EDUARDO	0	2012-07-06	2013-01-02	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-10 00:00:00	2012-08-30 09:40:26.015984		Retención Urinaria Aguda Repetida
104447	2012-06-09	7	7	\N	\N	f	1155	4409669-2	MEZA FIGUEROA, PABLO HIGINIO	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-09 00:00:00	2012-08-30 09:40:26.019087		No Especifica
104448	2012-07-12	7	7	\N	\N	f	1155	3764807-8	VALENZUELA RIQUERO, MARGARITA DEL CARMEN	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.021972		No Especifica
104449	2012-07-10	7	7	\N	\N	f	1155	3497874-3	MARTINEZ , GLADYS DEL CARMEN	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-10 00:00:00	2012-08-30 09:40:26.024766		No Especifica
104450	2012-07-11	7	7	\N	\N	f	927	2914468-0	CÉSPED MADRID, REINALDO	0	2012-07-05	2013-01-02	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.028026		Bilateral
104451	2012-07-18	7	7	\N	\N	f	1155	2729334-4	BRICEÑO MUÑOZ, BLANCA LUISA	0	2012-07-05	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.031207		No Especifica
104452	2012-07-17	7	7	\N	\N	f	925	2677921-9	GÓMEZ TABILO, ROSA ELENA	0	2012-07-06	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.03407		No Especifica
104453	2012-07-17	7	7	\N	\N	f	925	3464623-6	VALDEBENITO VALDEBENITO, JUAN LEONIDAS	0	2012-07-05	2013-01-02	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.03694		No Especifica
104454	2012-07-31	7	7	\N	\N	f	1155	3104164-3	CUADRA FREDES, JUANA ROSA	0	2012-07-06	2013-01-02	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.039789		No Especifica
104455	2012-07-12	7	7	\N	\N	f	909	19972580-7	CÁRDENAS ARRIAZA, ADRIÁN NICOLÁS	0	2012-07-08	2013-01-04	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-29 00:00:00	2012-08-30 09:40:26.043619		Post - Natal entre 2 Años y Menor de 15 Años
104456	2012-05-11	7	7	\N	\N	f	776	4659153-4	CASTILLO LEÓN, DIOSELINDA DEL CARME	0	2012-05-09	2013-01-04	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2011-05-14 00:00:00	2012-08-30 09:40:26.046805		Derecha
104457	2012-07-13	7	7	\N	\N	f	997	19884668-6	SANDOVAL CÁCERES, MATÍAS MAXIMILIANO	0	2012-07-11	2013-01-07	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-13 00:00:00	2012-08-30 09:40:26.050178		No Especifica
104458	2012-07-12	7	7	\N	\N	f	1155	6165659-6	RODRÍGUEZ FUENTES, CARMEN ROSA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.053076		No Especifica
104459	2012-07-12	7	7	\N	\N	f	1155	5924061-7	VALLEJOS CASTILLO, ANA ISABEL	0	2012-07-09	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.055887		No Especifica
104460	2012-07-12	7	7	\N	\N	f	1155	5446451-7	PONCE CASTRO, CARMEN LUISA	0	2012-07-10	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.058772		No Especifica
104461	2012-07-11	7	7	\N	\N	f	925	5295750-8	COVARRUBIAS MORENO, NICOLÁS RAÚL	0	2012-07-10	2013-01-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-11 00:00:00	2012-08-30 09:40:26.061697		No Especifica
104462	2012-07-12	7	7	\N	\N	f	1155	4915230-2	TRIGO , TERESA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.064516		No Especifica
104463	2012-07-12	7	7	\N	\N	f	1155	4583652-5	RAMÍREZ JULIO, SARA MARÍA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.06737		No Especifica
104464	2012-07-17	7	7	\N	\N	f	927	4443850-K	FLORES FIGUEROA, NATALIA DEL CARMEN	0	2012-07-10	2013-01-07	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:26.070699		Derecha
104465	2012-07-12	7	7	\N	\N	f	925	4356115-4	MANDIOLA SOISSA, ANA RAQUEL	0	2012-07-10	2013-01-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.073594		No Especifica
104466	2012-07-18	7	7	\N	\N	f	1155	4060618-1	ARAYA RAMÍREZ, LUCILA AMANDA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.076416		No Especifica
104467	2012-07-12	7	7	\N	\N	f	1155	4008155-0	PÉREZ BARONA, MERCEDES DEL CARMEN	0	2012-07-09	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.079317		No Especifica
104468	2012-07-13	7	7	\N	\N	f	1155	4005575-4	PUJOL ROJAS, RAFAEL HUMBERTO	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-13 00:00:00	2012-08-30 09:40:26.082182		No Especifica
104469	2012-07-12	7	7	\N	\N	f	925	3995329-3	ARDILES MIRANDA, ROSARIO DEL CARMEN	0	2012-07-11	2013-01-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-12 00:00:00	2012-08-30 09:40:26.085039		No Especifica
104470	2012-07-24	7	7	\N	\N	f	925	3786118-9	TAPIA FLORES, HILDA DEL CARMEN	0	2012-07-09	2013-01-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.088205		No Especifica
104471	2012-07-30	7	7	\N	\N	f	1155	3673412-4	ÁLVAREZ CEPEDA, ESTER DEL ROSARIO	0	2012-07-10	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.091323		No Especifica
104472	2012-07-18	7	7	\N	\N	f	1155	2998010-1	CANTUARIAS FARÍAS, SARA SILVIA	0	2012-07-09	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.094882		No Especifica
104473	2012-07-17	7	7	\N	\N	f	1155	2972142-4	MALDONADO , SILVIA VIOLETA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.097975		No Especifica
104474	2012-07-13	7	7	\N	\N	f	1155	2583329-5	MARTÍNEZ COROCEO, CARLOS ALFONSO	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-13 00:00:00	2012-08-30 09:40:26.101149		No Especifica
104475	2012-07-18	7	7	\N	\N	f	925	1696000-4	ARAVENA ESPINOZA, LUIS HUMBERTO	0	2012-07-10	2013-01-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.10437		No Especifica
104476	2012-08-20	7	7	\N	\N	f	1155	3984575-K	ASTORGA HERRERA, NORA EUGENIA	0	2012-07-11	2013-01-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:26.107487		No Especifica
104477	2012-07-17	7	7	\N	\N	f	997	9810406-2	ASTORGA DONOSO, PEDRO HUMBERTO	0	2012-07-12	2013-01-08	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.110732		No Especifica
104478	2012-07-17	7	7	\N	\N	f	927	8242041-K	BEIZA ABARCA, AURORA DEL CARMEN	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.114646		Bilateral
104479	2012-07-17	7	7	\N	\N	f	927	7912306-4	PÉREZ ARAYA, ANGELA DEL CARMEN	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.118017		Bilateral
104480	2012-07-17	7	7	\N	\N	f	927	6240192-3	BONNIARD MUÑOZ, MARÍA EUGENIA	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.121408		Bilateral
104481	2012-07-13	7	7	\N	\N	f	925	6032976-1	FERNÁNDEZ FERNÁNDEZ, GUILLERMINA	0	2012-07-12	2013-01-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-13 00:00:00	2012-08-30 09:40:26.124473		No Especifica
104482	2012-07-17	7	7	\N	\N	f	1155	5965954-5	ARAVENA FLORES, MARINA YOLANDA	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.127469		No Especifica
104483	2012-07-17	7	7	\N	\N	f	927	5721268-3	TORRES VEGA, ELEBIO ERASMO	0	2012-07-12	2013-01-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-14 00:00:00	2012-08-30 09:40:26.130843		Derecha
104484	2012-07-17	7	7	\N	\N	f	1155	5511130-8	VALENZUELA ARELLANO, MARÍA LUISA	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.133833		No Especifica
104485	2012-07-17	7	7	\N	\N	f	927	5426799-1	CORREA ORELLANA, BERTA LIDIA	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.137178		Bilateral
104486	2012-07-17	7	7	\N	\N	f	927	5055315-9	LECAROS DONOSO, OLGA DEL CARMEN	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.141305		Bilateral
104487	2012-07-17	7	7	\N	\N	f	927	4996742-K	PÉREZ ÓRDENES, MARÍA TERESA	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:26.144751		Bilateral
104488	2012-07-18	7	7	\N	\N	f	1155	4934902-5	HERNÁNDEZ , ELIANA DEL CARMEN	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.147887		No Especifica
104489	2012-07-19	7	7	\N	\N	f	1155	4614356-6	RUIZ DÍAZ, GUSTAVO ENRIQUE	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.151163		No Especifica
104490	2012-07-17	7	7	\N	\N	f	1155	4533495-3	GALLEGUILLOS MARÍN, SERGIO MARTÍN	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.154391		No Especifica
104491	2012-07-17	7	7	\N	\N	f	927	4076455-0	TAPIA VALDIVIA, ROSA CRISTINA	0	2012-07-12	2013-01-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:26.157979		Derecha
104492	2012-07-17	7	7	\N	\N	f	925	3902267-2	BLANCHARD MEYER, TEODORO GABRIEL	0	2012-07-12	2013-01-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.164831		No Especifica
104493	2012-07-17	7	7	\N	\N	f	925	3781253-6	LÓPEZ RIVERA, SOFÍA	0	2012-07-12	2013-01-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.168154		No Especifica
104494	2012-07-17	7	7	\N	\N	f	927	3508987-K	RÍOS RODRÍGUEZ, RODOLFO HERNÁN	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-14 00:00:00	2012-08-30 09:40:26.171583		Bilateral
104495	2012-07-13	7	7	\N	\N	f	1155	3456151-6	BELTRÁN ORTEGA, SERGIO ENRIQUE	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-13 00:00:00	2012-08-30 09:40:26.174676		No Especifica
104496	2012-07-17	7	7	\N	\N	f	925	3219036-7	SEPÚLVEDA TAPIA, FERNANDO	0	2012-07-12	2013-01-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.177812		No Especifica
104497	2012-07-18	7	7	\N	\N	f	925	2715573-1	ASSIS BERROETA, FRANCISCO RAÚL	0	2012-07-12	2013-01-08	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.181094		No Especifica
104498	2012-07-17	7	7	\N	\N	f	927	2684969-1	CASTRO PINILLA, SILVIA GLADY	0	2012-07-12	2013-01-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:26.185371		Derecha
104499	2012-07-17	7	7	\N	\N	f	927	2646960-0	CÓRDOVA BAEZA, JOAQUÍN	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-07-17 00:00:00	2012-08-30 09:40:26.18951		Bilateral
104500	2012-07-17	7	7	\N	\N	f	1155	2472505-7	SALAZAR CARRASCO, JOSÉ GUILLERMO	0	2012-07-12	2013-01-08	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.192734		No Especifica
104501	2012-07-17	7	7	\N	\N	f	927	2323192-1	PÉREZ FELIÚ, FRANCISCO RAÚL	0	2012-07-12	2013-01-08	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-07-17 00:00:00	2012-08-30 09:40:26.196081		Derecha
104502	2012-07-17	7	7	\N	\N	f	927	2130149-3	SÁNCHEZ MEZA, MANUEL SEGUNDO	0	2012-07-12	2013-01-08	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-07-17 00:00:00	2012-08-30 09:40:26.199591		Bilateral
104503	2012-07-18	7	7	\N	\N	f	1155	6748613-7	RAMOS BARRAZA, ROSENDA DEL CARMEN	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.202863		No Especifica
104504	2012-07-18	7	7	\N	\N	f	1155	5910745-3	CONTRERAS CORVERA, SILVIA DEL CARMEN	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.206009		No Especifica
104505	2012-05-15	7	7	\N	\N	f	776	5554009-8	BARRIENTOS ANDRADE, IRIS DE LOURDES	0	2012-05-14	2013-01-09	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-15 00:00:00	2012-08-30 09:40:26.20948		Derecha
104506	2012-07-18	7	7	\N	\N	f	925	5507755-K	FLORES BUSTOS, ANA INÉS	0	2012-07-13	2013-01-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.213121		No Especifica
104507	2012-05-15	7	7	\N	\N	f	776	5219082-7	ZAMORA JARA, JUAN ANTONIO	0	2012-05-14	2013-01-09	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-05-15 00:00:00	2012-08-30 09:40:26.216419		Izquierda
104508	2012-07-17	7	7	\N	\N	f	1155	5100631-3	HERNÁNDEZ CATALÁN, OSCAR DEL CARMEN	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-17 00:00:00	2012-08-30 09:40:26.219719		No Especifica
104509	2012-07-18	7	7	\N	\N	f	1155	5017702-5	MILLON LAZCANO, JUAN SOLITARIO	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.222921		No Especifica
104510	2012-05-17	7	7	\N	\N	f	776	4817942-8	POIRRIER GARGARI, MARÍA CRISTINA	0	2012-05-14	2013-01-09	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-17 00:00:00	2012-08-30 09:40:26.226102		Derecha
104511	2012-07-18	7	7	\N	\N	f	925	4478735-0	CHÁVEZ SUAZO, JANUARIA DEL CARMEN	0	2012-07-13	2013-01-09	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.229275		No Especifica
104512	2012-07-31	7	7	\N	\N	f	1155	4122455-K	VILLALÓN GUZMÁN, MARÍA SONIA	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.232417		No Especifica
104513	2012-07-19	7	7	\N	\N	f	1155	4069049-2	ZUANICH , IVÁN	0	2012-07-13	2013-01-09	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.235977		No Especifica
104514	2012-05-17	7	7	\N	\N	f	776	4034565-5	VALENZUELA MEJÍAS, ALEJANDRO EDUARDO	0	2012-05-14	2013-01-09	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-17 00:00:00	2012-08-30 09:40:26.239179		Derecha
104515	2012-07-18	7	7	\N	\N	f	1155	5070607-9	MEDINA FIGUEROA, NORA LUISA	0	2012-07-14	2013-01-10	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.242749		No Especifica
104516	2012-07-19	7	7	\N	\N	f	925	8308779-K	VERDUGO MORCOM, OSCAR ARMANDO	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.245721		No Especifica
104517	2012-07-23	7	7	\N	\N	f	1155	5994054-6	FREZ YÁÑEZ, TERESA DEL CARMEN	0	2012-07-18	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.24877		No Especifica
104518	2012-07-20	7	7	\N	\N	f	1155	5928847-4	ANCAPI LINAI, ROSA GENOVEVA	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.25162		No Especifica
104519	2012-07-18	7	7	\N	\N	f	925	5809187-1	VELÁSQUEZ ÁLVAREZ, RENÉ HUMBERTO	0	2012-07-17	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.254546		No Especifica
104520	2012-07-24	7	7	\N	\N	f	1155	5414309-5	ARAYA RAMÍREZ, LUIS ANTONIO	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.257402		No Especifica
104521	2012-05-23	7	7	\N	\N	f	776	4961619-8	CAMPAÑA ALVARADO, MARGARITA DEL CARMEN	0	2012-05-17	2013-01-14	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-23 00:00:00	2012-08-30 09:40:26.260576		Derecha
104522	2012-07-20	7	7	\N	\N	f	925	4808606-3	FLORES GONZÁLEZ, JUAN ALBERTO	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.263535		No Especifica
104523	2012-07-20	7	7	\N	\N	f	1155	4535635-3	HUERTA HUERTA, MANUEL JESÚS	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.266467		No Especifica
104524	2012-07-18	7	7	\N	\N	f	925	4465880-1	PARADA DE ARTEAGABEITI, BLANCA MIREYA ESPERA	0	2012-07-17	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.269532		No Especifica
104525	2012-06-27	7	7	\N	\N	f	1155	4423972-8	BARRERA MILLONES, ANGELA GLADYS	0	2012-07-18	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.272404		No Especifica
104526	2012-05-23	7	7	\N	\N	f	776	4351240-4	CONTRERAS TORREJÓN, JUANA MARÍA	0	2012-05-17	2013-01-14	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-05-23 00:00:00	2012-08-30 09:40:26.275285		Derecha
104527	2012-07-23	7	7	\N	\N	f	1155	4301790-K	YÁÑEZ BASUALTO, ELIANA DEL CARMEN	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.278332		No Especifica
104528	2012-07-20	7	7	\N	\N	f	925	4285684-3	SAAVEDRA OLEA, ALICIA ISABEL	0	2012-07-17	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.281399		No Especifica
104529	2012-07-19	7	7	\N	\N	f	925	4176606-9	LILLO VERGARA, ROBERTO	0	2012-07-17	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.284329		No Especifica
104530	2012-07-20	7	7	\N	\N	f	925	4085434-7	LÓPEZ LOBOS, JOSÉ NIBALDO	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.287228		No Especifica
104531	2012-07-23	7	7	\N	\N	f	1155	3418677-4	MANCILLA VARGAS, ROSARIO DEL CARMEN	0	2012-07-18	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.290261		No Especifica
104532	2012-07-19	7	7	\N	\N	f	1155	3399664-0	BRIONES URIBE, EVA	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.293119		No Especifica
104533	2012-07-19	7	7	\N	\N	f	1155	3346550-5	RIFFO LLANOS, ILDA ELIANA	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.295953		No Especifica
104534	2012-07-18	7	7	\N	\N	f	925	3296569-5	SUEZ ., CARLOS	0	2012-07-16	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.299084		No Especifica
104535	2012-07-19	7	7	\N	\N	f	925	3293280-0	AHUMADA ABALLAY, BENJAMÍN ENRIQUE	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.302029		No Especifica
104536	2012-07-20	7	7	\N	\N	f	1155	3221980-2	CONTRERAS CORTEZ, LUCÍA EGLIANTINA	0	2012-07-18	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.304995		No Especifica
104537	2012-07-19	7	7	\N	\N	f	925	3124048-4	GAJARDO MUÑOZ, MARÍA ANGÉLICA	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-19 00:00:00	2012-08-30 09:40:26.308216		No Especifica
104538	2012-07-20	7	7	\N	\N	f	1155	3058272-1	ESPINA VIVANCO, MARÍA TERESA	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.311117		No Especifica
104539	2012-07-18	7	7	\N	\N	f	1155	3022623-2	CORTÉS CISTERNAS, TOMÁS SEGUNDO	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.313989		No Especifica
104540	2012-07-18	7	7	\N	\N	f	1155	3007330-4	VILLARROEL CLIFT, JORGE MANUEL	0	2012-07-17	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-18 00:00:00	2012-08-30 09:40:26.316811		No Especifica
104541	2012-07-24	7	7	\N	\N	f	925	2911227-4	CARRERA CATALÁN, ISABEL	0	2012-07-18	2013-01-14	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.320452		No Especifica
104542	2012-07-25	7	7	\N	\N	f	1155	2082804-8	HEINRICHS VERA, IRMA DEL CARMEN	0	2012-07-18	2013-01-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.32328		No Especifica
104543	2012-07-20	7	7	\N	\N	f	1155	5611006-2	PÉREZ RÍOS, MARGARITA DEL CARMEN	0	2012-07-19	2013-01-15	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.326166		No Especifica
104544	2012-07-23	7	7	\N	\N	f	927	5136302-7	LAMILLA URRUTIA, YOLANDA IRIS	0	2012-07-19	2013-01-15	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-07-23 00:00:00	2012-08-30 09:40:26.32987		Derecha
104545	2012-07-23	7	7	\N	\N	f	927	4567462-2	BIZAMA HIDALGO, ALICIA DEL CARMEN	0	2012-07-19	2013-01-15	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-07-23 00:00:00	2012-08-30 09:40:26.333322		Izquierda
104546	2012-07-24	7	7	\N	\N	f	1155	4272754-7	ACUÑA BECKLER, MARÍA TERESA	0	2012-07-19	2013-01-15	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.336317		No Especifica
104547	2012-07-20	7	7	\N	\N	f	925	4041396-0	SCHWARZENBERG MERA, TRINIDAD DE LAS MERC	0	2012-07-19	2013-01-15	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.340112		No Especifica
104548	2012-07-20	7	7	\N	\N	f	1155	3431615-5	JIMÉNEZ CARVAJAL, EDMUNDO MANUEL	0	2012-07-19	2013-01-15	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.343021		No Especifica
104549	2012-07-20	7	7	\N	\N	f	1155	2439658-4	BAEZA CARVALLO, GRACIELA DEL CARMEN	0	2012-07-19	2013-01-15	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-20 00:00:00	2012-08-30 09:40:26.345887		No Especifica
104550	2012-07-23	7	7	\N	\N	f	997	18706180-6	DÍAZ RIFFO, GRECK ALEJANDRO	0	2012-07-20	2013-01-16	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.348887		No Especifica
104551	2012-07-24	7	7	\N	\N	f	1155	5399849-6	MORENO FREZ, GABRIELA DOLORES	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.351901		No Especifica
104552	2012-07-24	7	7	\N	\N	f	1155	5233363-6	SILVA ÁVALOS, CARLOS OMAR	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.354864		No Especifica
104553	2012-06-27	7	7	\N	\N	f	1155	5195792-K	COVARRUBIAS BRITO, MARÍA ELENA	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.358146		No Especifica
104554	2012-06-27	7	7	\N	\N	f	925	5195792-K	COVARRUBIAS BRITO, MARÍA ELENA	0	2012-07-20	2013-01-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.361072		No Especifica
104555	2012-07-24	7	7	\N	\N	f	1155	4389742-K	VERGARA ARANCIBIA, MARÍA ELENA	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.364018		No Especifica
104556	2012-07-23	7	7	\N	\N	f	925	4263386-0	VARAS MADRID, ERNESTO ENRIQUE	0	2012-07-20	2013-01-16	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.366968		No Especifica
104557	2012-07-23	7	7	\N	\N	f	1155	3817706-0	CABRERA ORREGO, IRIS DEL TRÁNSITO	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-23 00:00:00	2012-08-30 09:40:26.370211		No Especifica
104558	2012-06-27	7	7	\N	\N	f	1155	3675864-3	CAMPOS ROJAS, VENERANDO DEL ROSARI	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.373021		No Especifica
104559	2012-06-27	7	7	\N	\N	f	927	3540866-5	GONZÁLEZ MOLINA, FRANCISCO RUBÉN	0	2012-07-20	2013-01-16	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-07-27 00:00:00	2012-08-30 09:40:26.376207		Bilateral
104560	2012-07-24	7	7	\N	\N	f	1155	3281531-6	LIBERONA MARTÍNEZ, ENRIQUE	0	2012-07-20	2013-01-16	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.379644		No Especifica
104561	2012-05-25	7	7	\N	\N	f	776	4535039-8	LÓPEZ GUAJARDO, LUZ MARÍA DEL CARMEN	0	2012-05-23	2013-01-18	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-05-25 00:00:00	2012-08-30 09:40:26.382756		Izquierda
104562	2012-08-02	7	7	\N	\N	f	925	12825127-8	HENRÍQUEZ BARRÍA, MARIELA SOLEDAD	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.385762		No Especifica
104563	2012-07-26	7	7	\N	\N	f	997	12821140-3	BERNAL VÉLIZ, FRANCISCO JAVIER	0	2012-07-23	2013-01-21	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.388779		No Especifica
104564	2012-07-31	7	7	\N	\N	f	925	7221499-4	GONZÁLEZ CARREÑO, CECILIA LILIANA	0	2012-07-25	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.391805		No Especifica
104565	2012-07-31	7	7	\N	\N	f	925	6263931-8	GONZÁLEZ VERA, JUAN ABRAHAM	0	2012-07-25	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.394701		No Especifica
104566	2012-07-31	7	7	\N	\N	f	925	6220793-0	AVENDAÑO CORDERO, ALEJANDRINA DEL CARM	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.397574		No Especifica
104567	2012-06-27	7	7	\N	\N	f	927	6172014-6	BRICEÑO LOYOLA, ELIANA SILVIA	0	2012-07-24	2013-01-21	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-07-27 00:00:00	2012-08-30 09:40:26.40104		Derecha
104568	2012-07-30	7	7	\N	\N	f	925	5989289-4	CORTÉS CARMONA, RUDEMIL DEL TRÁNSITO	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.404079		No Especifica
104569	2012-06-27	7	7	\N	\N	f	1043	5937415-K	EYZAGUIRRE CALDERÓN, LUIS ALBERTO	0	2012-07-25	2013-01-21	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-27 00:00:00	2012-08-30 09:40:26.407212		Retención Urinaria Aguda Repetida
104570	2012-07-26	7	7	\N	\N	f	1155	5695707-3	VILLEGAS FIGUEROA, ROBERTO PATRICIO	0	2012-07-25	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.410371		No Especifica
104571	2012-06-27	7	7	\N	\N	f	927	5508458-0	RIQUELME TORRES, MARÍA ORIANA	0	2012-07-24	2013-01-21	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-07-27 00:00:00	2012-08-30 09:40:26.413674		Bilateral
104572	2012-07-24	7	7	\N	\N	f	1155	5352103-7	ATIENZO ZEPEDA, ROSA ESTER	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.416545		No Especifica
104573	2012-07-24	7	7	\N	\N	f	1155	5014933-1	LÓPEZ MUÑOZ, TEODOLINA DEL CARMEN	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.419461		No Especifica
104574	2012-07-30	7	7	\N	\N	f	925	5000173-3	CLAVERÍA ESTAY, HÉCTOR EDUARDO	0	2012-07-25	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.422606		No Especifica
104575	2012-07-24	7	7	\N	\N	f	1155	4927696-6	BUGUEÑO GÓMEZ, CARLOS HUGO	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.4255		No Especifica
104576	2012-06-27	7	7	\N	\N	f	1043	4901475-9	ORTIZ NÚÑEZ, JUAN JOSÉ	0	2012-07-25	2013-01-21	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-27 00:00:00	2012-08-30 09:40:26.428573		Retención Urinaria Aguda Repetida
104577	2012-07-25	7	7	\N	\N	f	925	4809297-7	ÁLVAREZ VILLALOBOS, LORENZA DEL ROSARIO	0	2012-07-23	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.431935		No Especifica
104578	2012-07-26	7	7	\N	\N	f	1155	4788328-8	MONTENEGRO ROJO, SUSANA YOLANDA	0	2012-07-25	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.435752		No Especifica
104579	2012-07-26	7	7	\N	\N	f	1155	4691635-2	MENDIETA FUENTES, DOMINGO ANTONIO	0	2012-07-25	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.440282		No Especifica
104580	2012-06-27	7	7	\N	\N	f	925	4679173-8	QUINTEROS ARAYA, MÓNICA CELIA	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.444329		No Especifica
104581	2012-07-24	7	7	\N	\N	f	1155	4668064-2	RIVERA BAEZA, LAUTARO JOSÉ	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.448155		No Especifica
104582	2012-07-25	7	7	\N	\N	f	1155	4630914-6	CANEO PONCE, ANA ROSA	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.452291		No Especifica
104583	2012-07-26	7	7	\N	\N	f	925	4573820-5	VARGAS , HUGO DEL CARMEN	0	2012-07-23	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.456309		No Especifica
104584	2012-07-25	7	7	\N	\N	f	1155	4498927-1	PIZARRO VARELA, BLANCA ROSALIA	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.460286		No Especifica
104585	2012-07-24	7	7	\N	\N	f	1155	4496715-4	LABARCA VÉLIZ, VIOLETA	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.464292		No Especifica
104586	2012-07-24	7	7	\N	\N	f	1155	4471582-1	GAETE APABLAZA, MARÍA VALENTINA	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.46819		No Especifica
104587	2012-06-27	7	7	\N	\N	f	1155	4261869-1	ROMERO GARCÍA, MARÍA DOLORES	0	2012-07-24	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.471829		No Especifica
104588	2012-07-30	7	7	\N	\N	f	925	4251033-5	AGUILERA ZÁRATE, JUAN RENÉ	0	2012-07-25	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.475682		No Especifica
104589	2012-07-26	7	7	\N	\N	f	1155	4162442-6	VENEGAS CONTRERAS, MARÍA IRIS	0	2012-07-23	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.479542		No Especifica
104590	2012-06-27	7	7	\N	\N	f	925	4131614-4	CABALLERO GUTIÉRREZ, ROSA DEL CARMEN	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.483461		No Especifica
104591	2012-06-27	7	7	\N	\N	f	1043	4066648-6	PEREIRA PEREIRA, ROLANDO	0	2012-07-25	2013-01-21	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-27 00:00:00	2012-08-30 09:40:26.487133		Retención Urinaria Aguda Repetida
104592	2012-07-26	7	7	\N	\N	f	1155	4001525-6	BOLADOS VILLEGAS, OLGA ALICIA	0	2012-07-24	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.49095		No Especifica
104593	2012-07-25	7	7	\N	\N	f	925	3794740-7	CATALDO ROJAS, MARÍA ELIANA	0	2012-07-23	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.495255		No Especifica
104594	2012-07-30	7	7	\N	\N	f	925	3668299-K	BAYARLIA GONZÁLEZ, ALBERTO	0	2012-07-23	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.499246		No Especifica
104595	2012-07-26	7	7	\N	\N	f	925	3534856-5	CUADRA ALFARO, NAZARIA DE LAS MERCE	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-26 00:00:00	2012-08-30 09:40:26.503117		No Especifica
104596	2012-06-27	7	7	\N	\N	f	925	3271566-4	PALMA GAETE, CONSUELO ESPERANZA	0	2012-07-25	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.506987		No Especifica
104597	2012-07-26	7	7	\N	\N	f	927	2922004-2	RIEGEL FONCK, GERMÁN HERBERT	0	2012-07-24	2013-01-21	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-14 00:00:00	2012-08-30 09:40:26.511641		Izquierda
104598	2012-07-25	7	7	\N	\N	f	925	2870448-8	VILLANUEVA ZAMORA, MARIO	0	2012-07-24	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-25 00:00:00	2012-08-30 09:40:26.515472		No Especifica
104599	2012-07-24	7	7	\N	\N	f	925	2055023-6	REBOLLEDO SEPÚLVEDA, JUAN ANTONIO	0	2012-07-23	2013-01-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-24 00:00:00	2012-08-30 09:40:26.519533		No Especifica
104600	2012-07-30	7	7	\N	\N	f	1155	1999552-6	UGARTE CORTÉS, ENRIQUE SEGUNDO	0	2012-07-25	2013-01-21	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.523379		No Especifica
104601	2012-08-20	7	7	\N	\N	f	927	5406212-5	VELASCO SEPÚLVEDA, NOELIA ESTER DEL CAR	0	2012-07-25	2013-01-21	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:26.528547		Izquierda
104602	2012-07-31	7	7	\N	\N	f	925	12127747-6	TORRES MONDACA, JULIO ROBERTO EDUARD	0	2012-07-26	2013-01-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.532609		No Especifica
104603	2012-03-30	7	7	\N	\N	f	1067	11547906-7	LUISA XIMENA MEDINA PÁEZ	0	2012-03-23	2013-01-22	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-04-02 00:00:00	2012-08-30 09:40:26.536724		Estudio Pre-Trasplante
104604	2012-08-08	7	7	\N	\N	f	925	5433459-1	ÁLVAREZ MELGAREJO, OLGA DEL CARMEN	0	2012-07-26	2013-01-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.541203		No Especifica
104605	2012-07-31	7	7	\N	\N	f	927	4739486-4	ORREGO ORREGO, JAIME	0	2012-07-26	2013-01-22	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-07-31 00:00:00	2012-08-30 09:40:26.54558		Izquierda
104606	2012-06-27	7	7	\N	\N	f	925	4625641-7	ARAVENA CAMPOS, ELSA AGUEDA	0	2012-07-26	2013-01-22	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-27 00:00:00	2012-08-30 09:40:26.549648		No Especifica
104607	2012-07-31	7	7	\N	\N	f	1155	3171435-4	MÁRQUEZ GUZMÁN, LUISA GEORGINA	0	2012-07-26	2013-01-22	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.553643		No Especifica
104608	2012-07-31	7	7	\N	\N	f	927	3052780-1	MORALES BENÍTEZ, MANUEL RODOLFO	0	2012-07-26	2013-01-22	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-07-31 00:00:00	2012-08-30 09:40:26.558398		Izquierda
104609	2012-07-30	7	7	\N	\N	f	927	2613338-6	CAMPOS GONZÁLEZ, LUZ ERNESTINA	0	2012-07-26	2013-01-22	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-07-30 00:00:00	2012-08-30 09:40:26.562884		Izquierda
104610	2012-07-30	7	7	\N	\N	f	925	6918433-2	LEMUI ANCAPÁN, IRMA	0	2012-07-27	2013-01-23	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.56681		No Especifica
104611	2012-07-31	7	7	\N	\N	f	1155	5847591-2	COLLAO COLLAO, IDOLIA DEL TRÁNSITO	0	2012-07-27	2013-01-23	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.570854		No Especifica
104612	2012-07-30	7	7	\N	\N	f	1155	5562277-9	LANAS LANAS, JUAN DEL ROSARIO	0	2012-07-27	2013-01-23	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.574633		No Especifica
104613	2012-05-30	7	7	\N	\N	f	776	4439135-K	BUSTOS FIGUEROA, MARÍA ELIANA	0	2012-05-28	2013-01-23	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-11 00:00:00	2012-08-30 09:40:26.578874		Derecha
104614	2012-08-07	7	7	\N	\N	f	925	3764017-4	HARDY BRADSHALL, JUANA HERMINIA	0	2012-07-27	2013-01-23	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.58358		No Especifica
104615	2012-08-01	7	7	\N	\N	f	1155	3233383-4	LAMAS YÁÑEZ, HILDA DEL CARMEN	0	2012-07-27	2013-01-23	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-01 00:00:00	2012-08-30 09:40:26.587541		No Especifica
104616	2012-07-30	7	7	\N	\N	f	925	3006305-8	VENEGAS CONCHA, NATASCHA MARÍA GRACI	0	2012-07-27	2013-01-23	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-30 00:00:00	2012-08-30 09:40:26.590469		No Especifica
104617	2012-07-31	7	7	\N	\N	f	925	2715582-0	CHRISTIANSEN NAVARRO, CARLOS FRANCISCO	0	2012-07-27	2013-01-23	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.593144		No Especifica
104618	2012-07-31	7	7	\N	\N	f	1043	2443928-3	GODOY GONZÁLEZ, FERNANDO FRANCISCO	0	2012-07-27	2013-01-23	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-31 00:00:00	2012-08-30 09:40:26.595954		Retención Urinaria Aguda Repetida
104619	2012-07-31	7	7	\N	\N	f	925	6152267-0	BAZAES , ZUNILDA DEL CARMEN	0	2012-07-28	2013-01-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.59874		No Especifica
104620	2012-08-06	7	7	\N	\N	f	1155	5794562-1	SÁNCHEZ SALAS, TERESA MÓNICA	0	2012-07-28	2013-01-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.601458		No Especifica
104621	2012-08-03	7	7	\N	\N	f	1155	4509620-3	OLGUÍN OLGUÍN, MARÍA ROSA	0	2012-07-28	2013-01-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.604043		No Especifica
104622	2012-07-31	7	7	\N	\N	f	925	4298163-K	ABARCA MENA, ISABEL DEL CARMEN	0	2012-07-28	2013-01-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.60666		No Especifica
104623	2012-07-31	7	7	\N	\N	f	925	4214039-2	MENA GALDAMES, JOSEFINA DE LA CRUZ	0	2012-07-28	2013-01-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.60934		No Especifica
104624	2012-07-31	7	7	\N	\N	f	925	3821210-9	SUBIABRE SERÓN, EDUARDO	0	2012-07-28	2013-01-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.612094		No Especifica
104625	2012-08-06	7	7	\N	\N	f	925	3676669-7	BRICEÑO , ENRIQUE ROLANDO	0	2012-07-28	2013-01-24	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.614733		No Especifica
104626	2012-05-30	7	7	\N	\N	f	776	3127684-5	CASTILLO ROJAS, MARTA AURORA	0	2012-05-29	2013-01-24	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-27 00:00:00	2012-08-30 09:40:26.617455		Izquierda
104627	2012-08-01	7	7	\N	\N	f	1155	2894055-6	GONZÁLEZ PEÑA, JUAN LUIS SEGUNDO	0	2012-07-28	2013-01-24	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-01 00:00:00	2012-08-30 09:40:26.620205		No Especifica
104628	2012-06-04	7	7	\N	\N	f	776	4993099-2	ARAYA CALDERÓN, JUANA DE LAS MERCEDE	0	2012-05-30	2013-01-25	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-04 00:00:00	2012-08-30 09:40:26.622891		Derecha
104629	2012-08-03	7	7	\N	\N	f	1043	8635023-8	RIQUELME GONZÁLEZ, MARCO ANTONIO	0	2012-08-01	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-03 00:00:00	2012-08-30 09:40:26.62557		Retención Urinaria Aguda Repetida
104630	2012-08-02	7	7	\N	\N	f	1043	8509315-0	FIGUEROA ARAVENA, MOISÉS MIGUEL	0	2012-07-31	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-02 00:00:00	2012-08-30 09:40:26.628348		Retención Urinaria Aguda Repetida
104631	2012-03-30	7	7	\N	\N	f	1067	7911740-4	LEANDRO ERNESTO MIRANDA CARRASCO	0	2012-03-27	2013-01-28	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-04-02 00:00:00	2012-08-30 09:40:26.631198		Estudio Pre-Trasplante
104632	2012-08-02	7	7	\N	\N	f	1155	6856141-8	ADOFACCI CUADROS, BERTA JUANA DEL CARM	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.633826		No Especifica
104633	2012-08-06	7	7	\N	\N	f	925	6623573-4	FLÁNDEZ CASTRO, MARÍA RACHEL	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.636515		No Especifica
104634	2012-08-02	7	7	\N	\N	f	1155	6391482-7	GONZÁLEZ VELASCO, FRESIA DEL CARMEN	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.63962		No Especifica
104635	2012-06-04	7	7	\N	\N	f	776	6350087-9	HIDALGO JIMÉNEZ, EDGIDIA JUDITH	0	2012-05-31	2013-01-28	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-04 00:00:00	2012-08-30 09:40:26.642323		Derecha
104636	2012-08-02	7	7	\N	\N	f	1155	6099176-6	DÍAZ CARTER, BENEDICTA DE LOURDES	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.644905		No Especifica
104637	2012-08-08	7	7	\N	\N	f	1155	6079740-4	NINIO CONTRERAS, ÚRSULA DE LAS MERCED	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.647475		No Especifica
104638	2012-08-03	7	7	\N	\N	f	1155	5944062-4	SAGREDO CARVAJAL, ESPERANZA DEL CARMEN	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.650356		No Especifica
104639	2012-08-03	7	7	\N	\N	f	925	5836220-4	GUTIÉRREZ DÍAZ, MARÍA HAYDÉE	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.653484		No Especifica
104640	2012-08-06	7	7	\N	\N	f	1155	5759761-5	CISTERNA ACEVEDO, ELENA DEL CARMEN	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.656125		No Especifica
104641	2012-08-03	7	7	\N	\N	f	925	5692820-0	PARRA ALVEAR, ELSA DEL CARMEN	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.658834		No Especifica
104642	2012-08-06	7	7	\N	\N	f	925	5692152-4	GONZÁLEZ AGUILERA, MILAGRO DEL CARMEN	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.66151		No Especifica
104643	2012-07-31	7	7	\N	\N	f	1155	5660713-7	SALAS MÉNDEZ, MARÍA EUGENIA CATALI	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.664098		No Especifica
104644	2012-08-02	7	7	\N	\N	f	925	5625797-7	CONTRERAS TAPIA, MARGARITA DE LAS MER	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.66851		No Especifica
104645	2012-08-02	7	7	\N	\N	f	1155	5618547-K	PÉREZ AGÜERO, ROSALVA	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.671484		No Especifica
104646	2012-08-06	7	7	\N	\N	f	1155	5477330-7	MENA NIETO, ANA ROSA	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.674285		No Especifica
104647	2012-08-01	7	7	\N	\N	f	1043	5459446-1	GONZÁLEZ GONZÁLEZ, EDUARDO LENIN	0	2012-07-30	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-01 00:00:00	2012-08-30 09:40:26.677117		Retención Urinaria Aguda Repetida
104648	2012-08-03	7	7	\N	\N	f	1155	5407926-5	ROMERO TAPIA, JUAN VICENTE	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.680037		No Especifica
104649	2012-08-03	7	7	\N	\N	f	925	5329249-6	AGUILAR MADRID, NANCY ELENA	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.682895		No Especifica
104650	2012-08-02	7	7	\N	\N	f	925	5243450-5	TAPIA HERRERA, JUANA HILDA	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.685735		No Especifica
104651	2012-08-03	7	7	\N	\N	f	925	5187656-3	ALBORNOZ OLIVARES, JULIA	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.688651		No Especifica
104652	2012-07-31	7	7	\N	\N	f	1043	5054494-K	FERNÁNDEZ LEIVA, HORACIO DEL CARMEN	0	2012-07-30	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-07-31 00:00:00	2012-08-30 09:40:26.691753		Retención Urinaria Aguda Repetida
104653	2012-08-03	7	7	\N	\N	f	1155	5050810-2	FREDES GONZÁLEZ, MARÍA CRISTINA	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.694845		No Especifica
104654	2012-08-03	7	7	\N	\N	f	925	4740052-K	FERNÁNDEZ NOVOA, ROLANDO HÉCTOR	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.697758		No Especifica
104655	2012-07-31	7	7	\N	\N	f	925	4733596-5	GÓMEZ AGUILERA, SARA DE LAS MERCEDES	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.700749		No Especifica
104656	2012-08-02	7	7	\N	\N	f	925	4637834-2	CÁCERES GAETE, LUIS ANTONIO	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.703604		No Especifica
104657	2012-08-03	7	7	\N	\N	f	927	4533247-0	CAMUS REYES, FRESIA DEL CARMEN	0	2012-08-01	2013-01-28	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-03 00:00:00	2012-08-30 09:40:26.707132		Derecha
104658	2012-08-03	7	7	\N	\N	f	1043	4424359-8	OLIVARES AROS, JUAN MIGUEL	0	2012-08-01	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-03 00:00:00	2012-08-30 09:40:26.710105		Retención Urinaria Aguda Repetida
104659	2012-08-06	7	7	\N	\N	f	1155	4358652-1	MOLINA BARRA, EUGENIO SEGUNDO	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.713215		No Especifica
104660	2012-07-31	7	7	\N	\N	f	1155	4357392-6	SANTANDER BUSTOS, EDUARDO SANTIAGO IGN	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.71599		No Especifica
104661	2012-07-31	7	7	\N	\N	f	925	4342222-7	PALACIOS SILVA, DORA DE LAS MERCEDES	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.719276		No Especifica
104662	2012-08-02	7	7	\N	\N	f	1043	4163433-2	RIVERA RAMÍREZ, HÉCTOR ROLANDO	0	2012-07-31	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-02 00:00:00	2012-08-30 09:40:26.722081		Retención Urinaria Aguda Repetida
104663	2012-08-02	7	7	\N	\N	f	1155	3857039-0	TORRES DONOSEI, ANGELA	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.724894		No Especifica
104664	2012-08-03	7	7	\N	\N	f	1155	3783312-6	MUÑOZ RAMÍREZ, LUIS HERNÁN	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.727694		No Especifica
104665	2012-08-03	7	7	\N	\N	f	925	3714945-4	VILLALÓN VILLALÓN, JORGE ALBERTO	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.731028		No Especifica
104666	2012-08-03	7	7	\N	\N	f	925	3706929-9	MARCHESSE CAMPODÓNICO, CECILIA CATERINE	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.734283		No Especifica
104667	2012-08-02	7	7	\N	\N	f	1155	3583202-5	OLIVARES MOLINA, ROMELIO SEGUNDO	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.737431		No Especifica
104668	2012-07-31	7	7	\N	\N	f	1155	3536266-5	CALIZ GIRALDES, MICAELA	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-07-31 00:00:00	2012-08-30 09:40:26.740614		No Especifica
104669	2012-08-02	7	7	\N	\N	f	1155	3506223-8	LAVÍN , MARTINA DEL CARMEN	0	2012-07-31	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.743656		No Especifica
104670	2012-08-03	7	7	\N	\N	f	927	3341510-9	FIGUEROA ALVARADO, ALCIDES JORGE DE LA	0	2012-08-01	2013-01-28	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-03 00:00:00	2012-08-30 09:40:26.747035		Derecha
104671	2012-08-02	7	7	\N	\N	f	925	3297115-6	CISTERNAS FIGUEROA, MARÍA ELSA	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.750394		No Especifica
104672	2012-08-02	7	7	\N	\N	f	927	3287331-6	VALENZUELA RAVANAL, LUCILA CLARA ANTONIA	0	2012-07-31	2013-01-28	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-02 00:00:00	2012-08-30 09:40:26.753708		Derecha
104673	2012-08-06	7	7	\N	\N	f	1155	3268212-K	HIDALGO SALINAS, MANUEL ANTONIO	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.756587		No Especifica
104674	2012-08-02	7	7	\N	\N	f	925	3127724-8	SEGURA PÉREZ, JOSÉ LUIS	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.760314		No Especifica
104675	2012-08-03	7	7	\N	\N	f	925	2977549-4	VALERIO GÁLVEZ, ELENA ABIGAIL	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.763757		No Especifica
104676	2012-08-02	7	7	\N	\N	f	1155	2870016-4	VILLARROEL CONTRERAS, CARLOS OSCAR ROLANDO	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.767253		No Especifica
104677	2012-08-03	7	7	\N	\N	f	1155	2774285-8	ROJAS JELDRES, SYLVIA IRENE DEL CAR	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.770464		No Especifica
104678	2012-08-02	7	7	\N	\N	f	925	2767823-8	BERNAL OSORIO, LUCRECIA DEL CARMEN	0	2012-08-01	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.773642		No Especifica
104679	2012-08-03	7	7	\N	\N	f	1043	2376160-2	MACAYA VILLALÓN, PEDRO ENRIQUE	0	2012-08-01	2013-01-28	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-03 00:00:00	2012-08-30 09:40:26.776699		Retención Urinaria Aguda Repetida
104680	2012-08-03	7	7	\N	\N	f	925	2361770-6	ASTUDILLO , BLANCA ZAIRA	0	2012-07-31	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.780035		No Especifica
104681	2012-08-02	7	7	\N	\N	f	1155	2313446-2	MARTÍNEZ , CARLOS JESÚS	0	2012-07-30	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.783353		No Especifica
104682	2012-08-02	7	7	\N	\N	f	925	2313446-2	MARTÍNEZ , CARLOS JESÚS	0	2012-07-30	2013-01-28	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-02 00:00:00	2012-08-30 09:40:26.787452		No Especifica
104683	2012-08-28	7	7	\N	\N	f	1155	5132941-4	SOTO TAPIA, ADELA JOSEFINA	0	2012-08-01	2013-01-28	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:26.790471		No Especifica
104684	2012-08-07	7	7	\N	\N	f	1155	14709152-4	PESANTEZ CORDOVA DE GUER, LID ESPERANZA	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.793746		No Especifica
104685	2012-08-08	7	7	\N	\N	f	997	9120011-2	GEIS BERGMANN, MARIANNE	0	2012-08-02	2013-01-29	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.796937		No Especifica
104686	2012-08-03	7	7	\N	\N	f	1155	6917556-2	PÉREZ TIRADO, DIOMELINA DEL CARMEN	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.800261		No Especifica
104687	2012-08-07	7	7	\N	\N	f	925	5916070-2	BRAVO CAMUS, JOSÉ ISIDRO	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.803202		No Especifica
104688	2012-08-07	7	7	\N	\N	f	927	5562206-K	SUÁREZ JORQUERA, HORACIO SEGUNDO	0	2012-08-02	2013-01-29	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:26.806505		Derecha
104689	2012-08-08	7	7	\N	\N	f	1155	5515178-4	GARCÍA PÉREZ, BERNARDITA DEL CARME	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.809699		No Especifica
104690	2012-08-03	7	7	\N	\N	f	1155	5346659-1	ÁVILA PONCE, IRMA DE LAS MERCEDES	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.813091		No Especifica
104691	2012-08-07	7	7	\N	\N	f	927	5308541-5	MANCILLA SALDIVIA, ELIECER GUILLERMO	0	2012-08-02	2013-01-29	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-07 00:00:00	2012-08-30 09:40:26.816369		Derecha
104692	2012-08-06	7	7	\N	\N	f	1155	4719221-8	AGUILERA JARA, TERESA ELENA	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.819343		No Especifica
104693	2012-08-08	7	7	\N	\N	f	1155	4650810-6	OLIVARES DOTTE, ELISA DEL CARMEN	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.822283		No Especifica
104694	2012-08-07	7	7	\N	\N	f	927	4636680-8	CARVAJAL SALAS, ANA ELVIRA	0	2012-08-02	2013-01-29	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.82533		Bilateral
104695	2012-08-08	7	7	\N	\N	f	925	4590212-9	ESPINOZA MUÑOZ, RAÚL	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.828362		No Especifica
104696	2012-08-08	7	7	\N	\N	f	1155	4424283-4	AGUAYO SÁNCHEZ, AMELIA	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.831447		No Especifica
104697	2012-08-06	7	7	\N	\N	f	925	3896136-5	MONTECINO NEIRA, PROSPERINO	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.834507		No Especifica
104698	2012-08-07	7	7	\N	\N	f	927	3515089-7	JOFRÉ MORA, YOLANDA GABRIELA	0	2012-08-02	2013-01-29	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-07 00:00:00	2012-08-30 09:40:26.838703		Bilateral
104699	2012-08-06	7	7	\N	\N	f	925	3268510-2	OCAÑA BUSTOS, PURIFICACIÓN	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.842189		No Especifica
104700	2012-08-08	7	7	\N	\N	f	1155	2834595-K	LEÓN VARGAS, ALVARA ROSAURA	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.845421		No Especifica
104701	2012-08-03	7	7	\N	\N	f	925	2832965-2	GALLARDO ROJAS, HUGO FEDERICO	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-03 00:00:00	2012-08-30 09:40:26.848715		No Especifica
104702	2012-08-08	7	7	\N	\N	f	1155	2738434-K	SÁNCHEZ INTURIAS, FÉLIX WALTERIO	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.85189		No Especifica
104703	2012-08-08	7	7	\N	\N	f	925	2289312-2	FERNANDEZ , DANTE IVAN	0	2012-08-02	2013-01-29	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.854847		No Especifica
104704	2012-08-08	7	7	\N	\N	f	1155	2200899-4	FERNÁNDEZ MUGURUZA, NÉLIDA DEL CARMEN	0	2012-08-02	2013-01-29	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.85787		No Especifica
104705	2012-08-08	7	7	\N	\N	f	925	6635705-8	VALDIVIESO GONZÁLEZ, ANA ORFELINA	0	2012-08-03	2013-01-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.861106		No Especifica
104706	2012-08-07	7	7	\N	\N	f	1155	5989518-4	GÓMEZ ELGUETA, EMILIA	0	2012-08-03	2013-01-30	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.864334		No Especifica
104707	2012-08-08	7	7	\N	\N	f	1155	5971528-3	REYES BRAVO, MARÍA ESTER	0	2012-08-03	2013-01-30	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.867293		No Especifica
104708	2012-08-06	7	7	\N	\N	f	925	5808801-3	ORTIZ PÉREZ, MARIANA DEL CARMEN	0	2012-08-03	2013-01-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-06 00:00:00	2012-08-30 09:40:26.870381		No Especifica
104709	2012-08-14	7	7	\N	\N	f	1155	5502757-9	VEGA CORTEZ, JORGE EMILIANO	0	2012-08-03	2013-01-30	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:26.873554		No Especifica
104710	2012-08-07	7	7	\N	\N	f	1043	4862825-7	VARGAS DELGADO, OSVALDO HERNÁN	0	2012-08-03	2013-01-30	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-07 00:00:00	2012-08-30 09:40:26.876752		Retención Urinaria Aguda Repetida
104711	2012-08-08	7	7	\N	\N	f	925	4125495-5	SUÁREZ ARAOS, CARMEN	0	2012-08-03	2013-01-30	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.880225		No Especifica
104712	2012-08-08	7	7	\N	\N	f	1155	4033052-6	GONZÁLEZ SALAZAR, BERNABÉ	0	2012-08-03	2013-01-30	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.88372		No Especifica
104713	2012-08-08	7	7	\N	\N	f	927	3446826-5	GARRIDO LÓPEZ, JOSÉ MERCEDES	0	2012-08-03	2013-01-30	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-08 00:00:00	2012-08-30 09:40:26.887405		Derecha
104714	2012-08-09	7	7	\N	\N	f	1155	3327650-8	PEÑALOZA FUENTES, JULIO ENRIQUE	0	2012-08-03	2013-01-30	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:26.890639		No Especifica
104715	2012-08-07	7	7	\N	\N	f	1043	3325529-2	FALFÁN , AUGUSTO	0	2012-08-03	2013-01-30	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-07 00:00:00	2012-08-30 09:40:26.894192		Retención Urinaria Aguda Repetida
104716	2012-08-08	7	7	\N	\N	f	1155	4953990-8	SANTANDER CONCHA, MIGUEL FERNANDO	0	2012-08-04	2013-01-31	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.897366		No Especifica
104717	2012-08-07	7	7	\N	\N	f	925	2186378-5	REBOLLEDO SANDOVAL, MARÍA TERESA	0	2012-08-04	2013-01-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.90072		No Especifica
104718	2012-08-27	7	7	\N	\N	f	925	5764768-k	VALENZUELA CORDERO, JULIO DEL CARMEN	0	2012-08-04	2013-01-31	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:26.903904		No Especifica
104719	2012-06-12	7	7	\N	\N	f	776	5860862-9	GUEVARA PARRAS, VIRGINIA ISABEL	0	2012-06-06	2013-02-01	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-12 00:00:00	2012-08-30 09:40:26.907886		Izquierda
104720	2012-08-24	7	7	\N	\N	f	1155	4362623-K	BARRAZA CASTELLÓN, CLARA	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:26.911096		No Especifica
104721	2012-08-17	7	7	\N	\N	f	925	7588015-4	LEMUS CABRERA, ROSA MERCEDES	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:26.913983		No Especifica
104722	2012-08-16	7	7	\N	\N	f	927	10048555-9	MOGGIA TASCHERI, CARLA MARIS STELLA	0	2012-08-07	2013-02-04	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-16 00:00:00	2012-08-30 09:40:26.91732		Izquierda
104723	2012-08-14	7	7	\N	\N	f	925	9278428-2	FARÍAS PINTO, SILVIA DEL ROSARIO	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:26.920559		No Especifica
104724	2012-08-09	7	7	\N	\N	f	925	8579230-K	MARÍN ZAPATA, VIOLETA DE LAS MERCE	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:26.923733		No Especifica
104725	2012-08-08	7	7	\N	\N	f	1155	8565886-7	MUÑOZ CABEZAS, LUIS ISAAC	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.926682		No Especifica
104726	2012-08-09	7	7	\N	\N	f	1155	8497160-K	IBARRA VILCHES, CARMEN CAMILA	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:26.929837		No Especifica
104727	2012-08-10	7	7	\N	\N	f	925	6289625-6	QUINTEROS CHÁVEZ, ERNESTO ALEJANDRO	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.9331		No Especifica
104728	2012-08-13	7	7	\N	\N	f	1155	6192990-8	IRIARTE DÍAZ, SARA DEL CARMEN	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:26.936362		No Especifica
104729	2012-08-07	7	7	\N	\N	f	925	6161693-4	OTÁROLA SOLAR, MARÍA INÉS	0	2012-08-06	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.93962		No Especifica
104730	2012-08-10	7	7	\N	\N	f	925	5991912-1	GONZÁLEZ NIÑO, BERTA	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.942821		No Especifica
104731	2012-08-08	7	7	\N	\N	f	925	5971528-3	REYES BRAVO, MARÍA ESTER	0	2012-08-06	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.945908		No Especifica
104732	2012-08-10	7	7	\N	\N	f	925	5888391-3	BRAVO MANCILLA, CARMEN	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.948917		No Especifica
104733	2012-06-13	7	7	\N	\N	f	776	5881965-4	OLIVOS VERA, MARTA GUMERCINDA	0	2012-06-08	2013-02-04	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-13 00:00:00	2012-08-30 09:40:26.952037		Derecha
104734	2012-08-09	7	7	\N	\N	f	925	5833993-8	SAAVEDRA SAAVEDRA, ROSALÍA DE MERCEDES	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:26.955072		No Especifica
104735	2012-08-14	7	7	\N	\N	f	1155	5684745-6	VILLEGAS ROA, HILDA	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:26.958201		No Especifica
104736	2012-08-10	7	7	\N	\N	f	925	5614946-5	SILVA SILVA, ERMELINDA DEL CARMEN	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.961243		No Especifica
104737	2012-08-10	7	7	\N	\N	f	927	5605359-K	VARGAS OLGUÍN, JUANA ROSA	0	2012-08-07	2013-02-04	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-10 00:00:00	2012-08-30 09:40:26.965275		Derecha
104738	2012-08-08	7	7	\N	\N	f	1155	5425684-1	VEGA PAVEZ, MARGARITA EDITH	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.968447		No Especifica
104739	2012-08-10	7	7	\N	\N	f	1155	5344210-2	CISTERNAS MELLA, CELIA PATRICIA	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.971414		No Especifica
104740	2012-06-08	7	7	\N	\N	f	776	5342135-0	ORREGO TAPIA, CLARA LUZ	0	2012-06-07	2013-02-04	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-08 00:00:00	2012-08-30 09:40:26.974316		Izquierda
104741	2012-08-07	7	7	\N	\N	f	1155	5109373-9	GARVISO HIDALGO, ANGELA ANTONIETA	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:26.977184		No Especifica
104742	2012-06-12	7	7	\N	\N	f	776	4963516-8	REYES , MARÍA ELENA	0	2012-06-07	2013-02-04	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-12 00:00:00	2012-08-30 09:40:26.980554		Derecha
104743	2012-08-10	7	7	\N	\N	f	1155	4936139-4	ARAYA PINOCHET, VICTORIA	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:26.984065		No Especifica
104744	2012-08-08	7	7	\N	\N	f	1155	4911748-5	NIEVES PEDOT, ANA MARÍA ANGÉLICA	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.987092		No Especifica
104745	2012-08-09	7	7	\N	\N	f	1155	4891492-6	MEZA GUTIÉRREZ, HERIBERTO SEGUNDO	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:26.990184		No Especifica
104746	2012-08-08	7	7	\N	\N	f	1155	4784067-8	GONZÁLEZ ARDILES, ANA MERCEDES	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:26.993393		No Especifica
104747	2012-08-14	7	7	\N	\N	f	1155	4712660-6	ARAYA ARAYA, GUILLERMO BENEDICTO	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:26.997113		No Especifica
104748	2012-08-08	7	7	\N	\N	f	1043	4644793-K	VERA AGUILERA, TRISTÁN SABINO	0	2012-08-06	2013-02-04	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-08 00:00:00	2012-08-30 09:40:27.000556		Retención Urinaria Aguda Repetida
104749	2012-08-08	7	7	\N	\N	f	925	4644650-K	VIDAL BERNIER, NORMA	0	2012-08-06	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:27.00363		No Especifica
104750	2012-08-09	7	7	\N	\N	f	1155	4633453-1	PENELA SANDOVAL, MARÍA DE LA LUZ	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.006778		No Especifica
104751	2012-08-09	7	7	\N	\N	f	1155	4505750-K	VÁSQUEZ VÁSQUEZ, JUAN LUIS	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.009797		No Especifica
104752	2012-08-09	7	7	\N	\N	f	925	4355908-7	CÁRDENAS , ROSA	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.012732		No Especifica
104753	2012-08-08	7	7	\N	\N	f	1155	4310656-2	ARÉVALO ASTORGA, ERNESTO RENÉ	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:27.015643		No Especifica
104754	2012-08-09	7	7	\N	\N	f	925	4151337-3	UBILLA GONZÁLEZ, RAMÓN LUIS	0	2012-08-06	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.018617		No Especifica
104755	2012-08-10	7	7	\N	\N	f	1155	4128767-5	TORRES , SARA DEL CARMEN	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.021985		No Especifica
104756	2012-08-10	7	7	\N	\N	f	1155	3999159-4	GARRIDO POMAREDA, JUAN	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.024915		No Especifica
104757	2012-08-16	7	7	\N	\N	f	927	3859218-1	PACHECO BARRALES, JUAN ELIZARDO	0	2012-08-07	2013-02-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-16 00:00:00	2012-08-30 09:40:27.028414		Bilateral
104758	2012-08-07	7	7	\N	\N	f	1155	3858945-8	TOBAR AROS, GERMÁN	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-07 00:00:00	2012-08-30 09:40:27.03141		No Especifica
104759	2012-08-13	7	7	\N	\N	f	1155	3850024-4	CONTADOR FLORES, MARGARITA DEL PILAR	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.034336		No Especifica
104760	2012-08-10	7	7	\N	\N	f	1155	3829854-2	MEDINA URIBE, ELBA	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.037339		No Especifica
104761	2012-08-10	7	7	\N	\N	f	927	3704478-4	SOTO INOSTROZA, MARÍA ALICIA	0	2012-08-07	2013-02-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-10 00:00:00	2012-08-30 09:40:27.041274		Bilateral
104762	2012-08-13	7	7	\N	\N	f	925	3544336-3	AGUILAR LÓPEZ, ALAMIRO DEL CARMEN	0	2012-08-08	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.044496		No Especifica
104763	2012-08-08	7	7	\N	\N	f	925	3512027-0	CUEVAS JEREZ, ELENA DE LAS MERCEDE	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:27.047527		No Especifica
104764	2012-08-16	7	7	\N	\N	f	1155	3364879-0	PINOCHET VERDEJO, OSVALDO RAÚL	0	2012-08-07	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.050629		No Especifica
104765	2012-08-10	7	7	\N	\N	f	927	3323758-8	PORMA CHIHUAY, JUAN SILVINO	0	2012-08-08	2013-02-04	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-10 00:00:00	2012-08-30 09:40:27.054147		Derecha
104766	2012-06-12	7	7	\N	\N	f	776	3300077-4	CARRASCO GUTIÉRREZ, GRACIELA DEL CARMEN	0	2012-06-07	2013-02-04	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-12 00:00:00	2012-08-30 09:40:27.057379		Izquierda
104767	2012-08-09	7	7	\N	\N	f	1155	3286274-8	PEREIRA GALLARDO, NERY DEL CARMEN	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.060596		No Especifica
104768	2012-08-09	7	7	\N	\N	f	1155	3242744-8	ÁLVAREZ ARAYA, PEDRO HOMERO	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.063684		No Especifica
104769	2012-08-08	7	7	\N	\N	f	925	3171673-K	ÁLVAREZ VERGARA, JULIO CÉSAR	0	2012-08-06	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:27.066614		No Especifica
104770	2012-08-09	7	7	\N	\N	f	1043	3162890-3	GONZÁLEZ PALMA, PEDRO	0	2012-08-07	2013-02-04	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-09 00:00:00	2012-08-30 09:40:27.069865		Retención Urinaria Aguda Repetida
104771	2012-08-13	7	7	\N	\N	f	927	3023396-4	LATIN TAPIA, NICOLÁS ENRIQUE	0	2012-08-07	2013-02-04	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:27.073575		Izquierda
104772	2012-08-09	7	7	\N	\N	f	1155	3006601-4	CHAMENG GUAJARDO, BLANCA LIDIA	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.076675		No Especifica
104773	2012-08-09	7	7	\N	\N	f	1155	2664887-4	TORRES CANALES, MARÍA ELIANA	0	2012-08-06	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-09 00:00:00	2012-08-30 09:40:27.079834		No Especifica
104774	2012-08-08	7	7	\N	\N	f	925	2431772-2	ECHEVERRÍA SOTO, MATILDE FLORENTINA	0	2012-08-07	2013-02-04	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-08 00:00:00	2012-08-30 09:40:27.083029		No Especifica
104775	2012-06-12	7	7	\N	\N	f	776	2209081-K	AMPUERO GALLARDO, DINA DEL CARMEN	0	2012-06-07	2013-02-04	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-12 00:00:00	2012-08-30 09:40:27.086127		Derecha
104776	2012-08-16	7	7	\N	\N	f	1155	1836003-9	JIMÉNEZ , RAQUEL	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.089683		No Especifica
104777	2012-08-16	7	7	\N	\N	f	927	1393144-5	JORDÁN LAS HERAS, MARIANA	0	2012-08-07	2013-02-04	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-16 00:00:00	2012-08-30 09:40:27.09361		Bilateral
104778	2012-08-20	7	7	\N	\N	f	1155	4877378-8	PONCE REYES, ELENA CELEDONIA	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.096657		No Especifica
104779	2012-08-20	7	7	\N	\N	f	1155	5464767-0	URRUTIA VIDELA, ANA PATRICIA	0	2012-08-08	2013-02-04	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.099659		No Especifica
104780	2012-08-10	7	7	\N	\N	f	909	22908500-K	TORRES LEÓN, VICENTE GABRIEL	0	2012-08-09	2013-02-05	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-20 00:00:00	2012-08-30 09:40:27.103342		Post - Natal entre 2 Años y Menor de 15 Años
104781	2012-08-13	7	7	\N	\N	f	997	16775144-K	JARRETT MORALES, CHRISTOPHER GUILLERM	0	2012-08-09	2013-02-05	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.106907		No Especifica
104782	2012-08-13	7	7	\N	\N	f	927	8166271-1	FARFÁN ZAMORA, SONIA DEL CARMEN	0	2012-08-09	2013-02-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-13 00:00:00	2012-08-30 09:40:27.110333		Derecha
104783	2012-08-16	7	7	\N	\N	f	1155	5561552-7	CRESPO SALINAS, ENRIQUE	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.113248		No Especifica
104784	2012-08-14	7	7	\N	\N	f	1155	5374207-6	CIFUENTES GAETE, PEDRO ANGEL	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.116127		No Especifica
104785	2012-08-10	7	7	\N	\N	f	925	4928548-5	DÍAZ , AMÉRICA	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.119087		No Especifica
104786	2012-08-13	7	7	\N	\N	f	1155	4494432-4	DUPRE HERRERA, GUSTAVO ANDRÉS	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.122048		No Especifica
104787	2012-08-13	7	7	\N	\N	f	927	4418957-7	VEAS CISTERNAS, ERCILIA DEL CARMEN	0	2012-08-09	2013-02-05	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-13 00:00:00	2012-08-30 09:40:27.125391		Derecha
104788	2012-08-10	7	7	\N	\N	f	925	4386762-8	BELLENGER PIZARRO, OLGA DEL CARMEN	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.128386		No Especifica
104789	2012-08-14	7	7	\N	\N	f	925	4296613-4	ELGUETA , MARIA ANJELA	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.131629		No Especifica
104790	2012-08-13	7	7	\N	\N	f	1155	4246688-3	GUZMÁN ROJAS, JULIO ARMANDO	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.134614		No Especifica
104791	2012-08-13	7	7	\N	\N	f	927	4241258-9	QUEZADA VILLABLANCA, OSCAR	0	2012-08-09	2013-02-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-13 00:00:00	2012-08-30 09:40:27.138268		Bilateral
104792	2012-08-13	7	7	\N	\N	f	927	4235462-7	MARTÍNEZ BRUCE, ALFREDO NICOLÁS ANTO	0	2012-08-09	2013-02-05	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-13 00:00:00	2012-08-30 09:40:27.141548		Izquierda
104793	2012-08-10	7	7	\N	\N	f	925	4078427-6	SANDOVAL VERA, AURA ROSA	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.144514		No Especifica
104794	2012-08-10	7	7	\N	\N	f	925	3789407-9	MUÑOZ TÉLLEZ, GLADYS	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.147505		No Especifica
104795	2012-08-10	7	7	\N	\N	f	925	3690184-5	RUIZ CARIAGA, JOSÉ ALEJANDRO	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.151052		No Especifica
104796	2012-08-13	7	7	\N	\N	f	925	3441009-7	MANSILLA SUBIABRE, LUISA DEL CARMEN	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.153948		No Especifica
104797	2012-08-13	7	7	\N	\N	f	927	3399433-8	LÓPEZ NAVARRO, YOLANDA DEL CARMEN	0	2012-08-09	2013-02-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-13 00:00:00	2012-08-30 09:40:27.15721		Bilateral
104798	2012-08-10	7	7	\N	\N	f	1155	3323738-3	YÁÑEZ ORTEGA, AVELINO DEL CARMEN	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.160305		No Especifica
104799	2012-08-13	7	7	\N	\N	f	1155	3141464-4	AGUILERA RIFO, RAÚL GONZALO	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.16332		No Especifica
104800	2012-08-10	7	7	\N	\N	f	1155	2867222-5	FIGUEROA ROMÁN, PRECEPTIVA	0	2012-08-09	2013-02-05	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.166356		No Especifica
104801	2012-08-13	7	7	\N	\N	f	925	2327041-2	MONTANER VEJAR, SADY	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.169429		No Especifica
104802	2012-08-13	7	7	\N	\N	f	927	1842433-9	MORENO MASMAN, JORGE RENÉ	0	2012-08-09	2013-02-05	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-13 00:00:00	2012-08-30 09:40:27.172682		Bilateral
104803	2012-08-10	7	7	\N	\N	f	925	1811766-5	MORCHIO DÍAZ, ANGEL JOSÉ	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-10 00:00:00	2012-08-30 09:40:27.175681		No Especifica
104804	2012-08-21	7	7	\N	\N	f	925	4907831-5	FIGUEROA MEJÍAS, HÉCTOR RAMÓN	0	2012-08-09	2013-02-05	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.178777		No Especifica
104805	2012-08-17	7	7	\N	\N	f	1155	4067347-4	BALCAZA GARAY, NORA DEL CARMEN	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.181754		No Especifica
104806	2012-08-17	7	7	\N	\N	f	925	5119336-9	TAPIA CORTÉS, MARÍA INÉS	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.184698		No Especifica
104807	2012-08-13	7	7	\N	\N	f	925	6635728-7	ORELLANA MARTÍNEZ, MARÍA MARGARITA	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.188085		No Especifica
104808	2012-08-13	7	7	\N	\N	f	1155	5420566-K	AVELLO PIÑONES, ANA LUISA	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.191014		No Especifica
104809	2012-08-16	7	7	\N	\N	f	925	5372747-6	SAAVEDRA OLIVARES, EUGENIA URZULA	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.193983		No Especifica
104810	2012-08-13	7	7	\N	\N	f	1155	4976674-2	MARTÍNEZ URRUTIA, NORMA	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-13 00:00:00	2012-08-30 09:40:27.196814		No Especifica
104811	2012-08-16	7	7	\N	\N	f	1155	4914058-4	INOSTROZA MARTÍNEZ, CLODOMIRA DEL CARMEN	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.199666		No Especifica
104812	2012-08-14	7	7	\N	\N	f	925	3460617-K	SÁNCHEZ ROJAS, ELIANA DEL CARMEN	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.202768		No Especifica
104813	2012-08-14	7	7	\N	\N	f	925	2729025-6	AYALA MORENO, SILVIA LIDIA	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.205848		No Especifica
104814	2012-08-16	7	7	\N	\N	f	1155	1491695-4	LÓPEZ LEIVA, MARÍA FILOMENA	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.208804		No Especifica
104815	2012-08-20	7	7	\N	\N	f	1155	4381524-5	OLMEDO SÁNCHEZ, LUIS ALBERTO	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.211716		No Especifica
104816	2012-08-21	7	7	\N	\N	f	927	4062140-7	GALLARDO MEDINA, ELENA DEL CARMEN	0	2012-08-10	2013-02-06	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-21 00:00:00	2012-08-30 09:40:27.215313		Bilateral
104817	2012-08-29	7	7	\N	\N	f	925	4458151-5	GOYENECHE FIGUEROA, CARLOS EUGENIO	0	2012-08-10	2013-02-06	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.218354		No Especifica
104818	2012-08-29	7	7	\N	\N	f	1155	3074128-5	MARAMBIO DURÁN, VIRGILIO SEGUNDO	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.221287		No Especifica
104819	2012-08-29	7	7	\N	\N	f	1155	5007654-7	ROJO ROJO, JUAN AURELIO	0	2012-08-10	2013-02-06	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.224222		No Especifica
104820	2012-06-14	7	7	\N	\N	f	776	6038127-5	DÍAZ PRIETO, CARMEN	0	2012-06-12	2013-02-07	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-14 00:00:00	2012-08-30 09:40:27.227427		Derecha
104821	2012-08-20	7	7	\N	\N	f	925	3272971-1	MARTINEZ , MARIO DEL CARMEN	0	2012-08-11	2013-02-07	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.230557		No Especifica
104822	2012-08-20	7	7	\N	\N	f	1155	5019815-4	ESPINOZA MORALES, LAURA NATIVIDAD	0	2012-08-11	2013-02-07	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.233506		No Especifica
104823	2012-08-17	7	7	\N	\N	f	1155	3677930-6	BRAVO ZEBALLOS, JUAN LUIS	0	2012-08-14	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.236344		No Especifica
104824	2012-08-17	7	7	\N	\N	f	1155	3456213-K	BRANTES PÉREZ, JORGE GUSTAVO	0	2012-08-14	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.239189		No Especifica
104825	2012-08-16	7	7	\N	\N	f	909	22460359-2	BAEZ MUÑOZ, ELÍAS JOSÉ	0	2012-08-13	2013-02-11	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-29 00:00:00	2012-08-30 09:40:27.242571		Post - Natal entre 2 Años y Menor de 15 Años
104826	2012-08-16	7	7	\N	\N	f	909	19613799-8	BERRÍOS LECAROS, CAMILA PATRICIA	0	2012-08-14	2013-02-11	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-29 00:00:00	2012-08-30 09:40:27.245831		Post - Natal entre 2 Años y Menor de 15 Años
104827	2012-08-16	7	7	\N	\N	f	1043	7054742-2	GÓMEZ LEIVA, ORLANDO IVÁN	0	2012-08-13	2013-02-11	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-16 00:00:00	2012-08-30 09:40:27.249479		Retención Urinaria Aguda Repetida
104828	2012-08-16	7	7	\N	\N	f	1043	6405169-5	PÉREZ SALINAS, HÉCTOR ENRIQUE	0	2012-08-13	2013-02-11	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-16 00:00:00	2012-08-30 09:40:27.252519		Retención Urinaria Aguda Repetida
104829	2012-08-14	7	7	\N	\N	f	1155	5930806-8	PIZARRO COLLAO, CLOTILDE DEL CARMEN	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.255594		No Especifica
104830	2012-08-16	7	7	\N	\N	f	1155	5849757-6	REYES ORELLANA, JULIA LILIANA	0	2012-08-14	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.25863		No Especifica
104831	2012-08-16	7	7	\N	\N	f	925	5612170-6	CORTÉS BAHAMONDES, ESTER MILAGRO	0	2012-08-14	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.261616		No Especifica
104832	2012-08-14	7	7	\N	\N	f	925	5442852-9	ALVARADO GUERRA, GUSTAVO FERMÍN	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.264513		No Especifica
104833	2012-08-16	7	7	\N	\N	f	1155	5292555-K	FERNÁNDEZ MUÑOZ, MARÍA ALICIA	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.26737		No Especifica
104834	2012-08-14	7	7	\N	\N	f	925	5204958-K	NAVARRO HERNÁNDEZ, JAIME DEL CARMEN	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.270304		No Especifica
104835	2012-08-16	7	7	\N	\N	f	1043	4519468-K	CISTERNAS VERA, JUAN DE DIOS	0	2012-08-13	2013-02-11	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-16 00:00:00	2012-08-30 09:40:27.273772		Retención Urinaria Aguda Repetida
104836	2012-06-19	7	7	\N	\N	f	776	4281135-1	BRAVO BRAVO, HUMBERTO	0	2012-06-14	2013-02-11	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-19 00:00:00	2012-08-30 09:40:27.276903		Izquierda
104837	2012-08-16	7	7	\N	\N	f	1043	4265339-K	HERRERA SILVA, MANUEL DE LA CRUZ	0	2012-08-13	2013-02-11	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-16 00:00:00	2012-08-30 09:40:27.279936		Retención Urinaria Aguda Repetida
104838	2012-08-14	7	7	\N	\N	f	925	4224710-3	URIBE ÁGUILA, LEOPOLDO ENRIQUE	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-14 00:00:00	2012-08-30 09:40:27.282879		No Especifica
104839	2012-08-16	7	7	\N	\N	f	925	4079590-1	OLATE ALARCÓN, LAURA GUACOLDA	0	2012-08-14	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.285807		No Especifica
104840	2012-08-16	7	7	\N	\N	f	1043	3853521-8	JARA , MARIO ENRIQUE	0	2012-08-13	2013-02-11	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-16 00:00:00	2012-08-30 09:40:27.288743		Retención Urinaria Aguda Repetida
104841	2012-06-19	7	7	\N	\N	f	776	3851204-8	CAMUS REYES, ROSA INÉS	0	2012-06-14	2013-02-11	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-19 00:00:00	2012-08-30 09:40:27.291913		Izquierda
104842	2012-08-16	7	7	\N	\N	f	925	3708523-5	SÁNCHEZ VEGA, EDUVINA DEL CARMEN	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.294903		No Especifica
104843	2012-08-16	7	7	\N	\N	f	925	2699703-8	FABIO ULLOA, HORTENSIA	0	2012-08-14	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.297824		No Especifica
104844	2012-08-16	7	7	\N	\N	f	925	2633007-6	ESTAY OSORIO, AMARIO HERNÁN	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-16 00:00:00	2012-08-30 09:40:27.300873		No Especifica
104845	2012-08-20	7	7	\N	\N	f	925	5364204-7	LAZCANO HIDALGO, OLGA DEL CARMEN	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.303776		No Especifica
104846	2012-08-20	7	7	\N	\N	f	927	3400055-7	BOBADILLA BOBADILLA, ELBA ROSA	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:27.307406		Derecha
104847	2012-08-20	7	7	\N	\N	f	927	5335251-0	CATALÁN MATURANA, ELISA DEL CARMEN	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:27.311164		Derecha
104848	2012-08-20	7	7	\N	\N	f	927	1132948-9	MÉNDEZ , JULIA ESTER	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:27.314388		Izquierda
104849	2012-08-20	7	7	\N	\N	f	927	3330841-8	GONZÁLEZ CONDELL, SILVIA DEL CARMEN	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:27.317595		Izquierda
104850	2012-08-20	7	7	\N	\N	f	927	3675807-4	ULLOA OLIVARES, AGUSTÍN	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:27.321013		Izquierda
104851	2012-08-20	7	7	\N	\N	f	927	4311411-5	CÁRDENAS BARRIENTOS, YOLANDA	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:27.324293		Izquierda
104852	2012-08-20	7	7	\N	\N	f	927	1722413-1	OLIVARES CASTILLO, ALIDIA	0	2012-08-14	2013-02-11	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:27.327539		Bilateral
104853	2012-08-20	7	7	\N	\N	f	1155	3975095-3	TERÁN VIDAL, BERTA MARGARITA	0	2012-08-14	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.330633		No Especifica
104854	2012-08-20	7	7	\N	\N	f	1155	4645903-2	NAVARRO PORRAS, BERTA LUISA	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.334046		No Especifica
104855	2012-08-20	7	7	\N	\N	f	1155	6105717-K	MORALES MEDINA, ISLANDA	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.337278		No Especifica
104856	2012-08-21	7	7	\N	\N	f	925	4564689-0	JHONSON MONTENEGRO, GRACIELA EVANJELINA	0	2012-08-13	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.340356		No Especifica
104857	2012-08-21	7	7	\N	\N	f	925	6429584-5	DÍAZ PERALTA, OSCAR ALFREDO DEL CA	0	2012-08-14	2013-02-11	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.343365		No Especifica
104858	2012-08-21	7	7	\N	\N	f	927	3047409-0	SAAVEDRA VARGAS, ROSA HERMINIA	0	2012-08-14	2013-02-11	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-21 00:00:00	2012-08-30 09:40:27.34658		Bilateral
104859	2012-08-21	7	7	\N	\N	f	927	1847103-5	JAHR DE NORDENFLYCHT, HAROLD ERNST EGON	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-21 00:00:00	2012-08-30 09:40:27.349879		Derecha
104860	2012-08-21	7	7	\N	\N	f	927	1973684-9	RINGELING POLANCO, OLGA MARÍA BERTA	0	2012-08-14	2013-02-11	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-21 00:00:00	2012-08-30 09:40:27.353119		Derecha
104861	2012-08-21	7	7	\N	\N	f	1155	2429166-9	ACOSTA ACOSTA, ADRIANA DEL CARMEN	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.356034		No Especifica
104862	2012-08-21	7	7	\N	\N	f	1155	5062181-2	SILVA MONZÓN, ENCARNACIÓN DEL CARM	0	2012-08-13	2013-02-11	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.359187		No Especifica
104863	2012-08-29	7	7	\N	\N	f	927	3669922-1	MATURANA , RAUL	0	2012-08-14	2013-02-11	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-29 00:00:00	2012-08-30 09:40:27.362445		Bilateral
104864	2012-08-17	7	7	\N	\N	f	1155	4173259-8	ZAMORANO ROJAS, JORGE ABELARDO	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.365358		No Especifica
104865	2012-08-17	7	7	\N	\N	f	1155	3906711-0	ZAVANDO MORALES, SERGIO DE LA CRUZ	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.368343		No Especifica
104866	2012-08-17	7	7	\N	\N	f	1155	2272244-1	NÚÑEZ POZO, HÉCTOR FRANCISCO	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-17 00:00:00	2012-08-30 09:40:27.371355		No Especifica
104867	2012-08-20	7	7	\N	\N	f	927	4079901-K	PEÑA GARRIDO, ALFREDO ILDEFONSO DE	0	2012-08-16	2013-02-12	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-20 00:00:00	2012-08-30 09:40:27.375248		Derecha
104868	2012-08-20	7	7	\N	\N	f	927	2349090-0	GUAJARDO UBEDA, JUAN LUIS	0	2012-08-16	2013-02-12	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-20 00:00:00	2012-08-30 09:40:27.378578		Izquierda
104869	2012-08-20	7	7	\N	\N	f	927	3131593-K	CAMERON LEIVA, NORMA TERESA	0	2012-08-16	2013-02-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:27.381811		Bilateral
104870	2012-08-20	7	7	\N	\N	f	927	3595767-7	VIDAL , MARTINA	0	2012-08-16	2013-02-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:27.384992		Bilateral
104871	2012-08-20	7	7	\N	\N	f	927	5301723-1	MORALES PARDO, ORIANA DEL CARMEN	0	2012-08-16	2013-02-12	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-20 00:00:00	2012-08-30 09:40:27.388217		Bilateral
104872	2012-08-20	7	7	\N	\N	f	997	14454288-6	OLIVARES VEGA, JUAN FRANCISCO	0	2012-08-16	2013-02-12	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.391266		No Especifica
104873	2012-08-20	7	7	\N	\N	f	1155	3768465-1	LUCERO CORNEJO, LETICIA DEL CARMEN	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.394242		No Especifica
104874	2012-08-21	7	7	\N	\N	f	925	2577963-0	MUÑOZ CORTEZ, MANUEL SEGUNDO	0	2012-08-16	2013-02-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.397281		No Especifica
104875	2012-08-21	7	7	\N	\N	f	925	4381811-2	VARGAS ZULETA, ISABEL DEL CARMEN	0	2012-08-16	2013-02-12	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.400784		No Especifica
104876	2012-08-21	7	7	\N	\N	f	1155	4319362-7	ALIAGA DÍAZ, IRIS BETSI	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.403841		No Especifica
104877	2012-08-23	7	7	\N	\N	f	1155	4978758-8	UGALDE HERRERA, MARÍA TERESA	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.406803		No Especifica
104878	2012-08-28	7	7	\N	\N	f	1155	3940274-2	ZAMORA MEDINA, GUILLERMO ABSALÓN	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.409851		No Especifica
104879	2012-08-28	7	7	\N	\N	f	1155	5557505-3	MORENO HEINRICHS, ANGLEE FILOMENA	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.413344		No Especifica
104880	2012-08-29	7	7	\N	\N	f	1155	6152876-8	VELÁSQUEZ VALDÉS, BLANCA HAYDÉE	0	2012-08-16	2013-02-12	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.416381		No Especifica
104881	2012-06-21	7	7	\N	\N	f	776	5188628-3	TRONCOSO VELÁSQUEZ, JAIME ANTONIO	0	2012-06-18	2013-02-13	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-21 00:00:00	2012-08-30 09:40:27.419655		Izquierda
104882	2012-08-20	7	7	\N	\N	f	1155	3784011-4	CONTRERAS PASTENES, SILVIA DEL CARMEN	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.422714		No Especifica
104883	2012-08-20	7	7	\N	\N	f	1155	3804419-2	DE MARCOS CASTRO, EMMA SILVIA	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.425537		No Especifica
104884	2012-08-20	7	7	\N	\N	f	1155	5490399-5	ORELLANA PONCE, NANCY DEL CARMEN	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-20 00:00:00	2012-08-30 09:40:27.428478		No Especifica
104885	2012-08-21	7	7	\N	\N	f	925	2362079-0	CISTERNAS MANZO, RAQUEL DEL CARMEN	0	2012-08-17	2013-02-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.431608		No Especifica
104886	2012-08-21	7	7	\N	\N	f	1155	3388196-7	ALVARADO PLETIKOSIC, EDITH ROSA	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.434523		No Especifica
104887	2012-08-21	7	7	\N	\N	f	1155	5351709-9	DINAMARCA MANSILLA, PATRICIA DE LAS MERC	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.437397		No Especifica
104888	2012-08-21	7	7	\N	\N	f	1155	5924722-0	BEIZA ZAMORANO, ALICIA EUGENIA	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.440388		No Especifica
104889	2012-08-21	7	7	\N	\N	f	1155	6413911-8	MEDINA RIVERA, SOLEDAD DEL CARMEN	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.443335		No Especifica
104890	2012-08-23	7	7	\N	\N	f	1155	3854587-6	GAETE VERGARA, IRIS EMILIA	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.446427		No Especifica
104891	2012-08-28	7	7	\N	\N	f	1155	3233808-9	MEJÍAS VALENZUELA, MIRELLA HAYDÉE	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.449479		No Especifica
104892	2012-08-28	7	7	\N	\N	f	1155	4805937-6	CHACC PÉREZ, HÉCTOR DOMINGO	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.452428		No Especifica
104893	2012-08-29	7	7	\N	\N	f	925	4518897-3	FLORES FLORES, BENITO DEL CARMEN	0	2012-08-17	2013-02-13	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.455523		No Especifica
104894	2012-08-29	7	7	\N	\N	f	1155	5369533-7	SALDÍVAR CORTÉS, ALONSO DEL CARMEN	0	2012-08-17	2013-02-13	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.458475		No Especifica
104895	2012-06-22	7	7	\N	\N	f	776	4813852-7	TAPIA GLADINIER, ADRIANA DEL CARMEN	0	2012-06-19	2013-02-14	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-22 00:00:00	2012-08-30 09:40:27.461459		Derecha
104896	2012-08-27	7	7	\N	\N	f	1155	5321963-2	RODRÍGUEZ CISTERNA, JAIME ABELARDO	0	2012-08-18	2013-02-14	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.464811		No Especifica
104897	2012-08-24	7	7	\N	\N	f	925	2993965-9	ROJAS HURTADO, GUSTAVO JULIO	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.467776		No Especifica
104898	2012-08-24	7	7	\N	\N	f	925	3439309-5	DUREAUX ARÉVALO, ISABEL ELIANA	0	2012-08-22	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.470823		No Especifica
104899	2012-08-24	7	7	\N	\N	f	925	3647376-2	RODRÍGUEZ VALLADARES, ALMA	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.473806		No Especifica
104900	2012-08-24	7	7	\N	\N	f	925	3997250-6	UGALDE MORALES, TERESA DEL CARMEN	0	2012-08-22	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.476803		No Especifica
104901	2012-08-24	7	7	\N	\N	f	925	5196039-4	ALCOTA FLORES, NELY DEL CARMEN	0	2012-08-22	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.479807		No Especifica
104902	2012-08-24	7	7	\N	\N	f	1155	3173191-7	GUZMÁN PONCE, JOSÉ LAUTARO	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.482687		No Especifica
104903	2012-08-24	7	7	\N	\N	f	1155	3978041-0	GONZÁLEZ MORALES, ROSA AMELIA	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.485545		No Especifica
104904	2012-08-24	7	7	\N	\N	f	1155	4002624-K	RODRÍGUEZ GUERRA, NORA LUISA	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.488482		No Especifica
104905	2012-08-24	7	7	\N	\N	f	1155	4102190-K	VERDUGO ESCOBAR, HERNÁN MIGUEL	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.491417		No Especifica
104906	2012-08-24	7	7	\N	\N	f	1155	4785623-K	JARA CASTRO, LAURA DEL ROSARIO	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.494421		No Especifica
104907	2012-08-24	7	7	\N	\N	f	1155	5292573-8	VÁSQUEZ REYNOSO, RUPERTO CAMILO	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.497379		No Especifica
104908	2012-08-24	7	7	\N	\N	f	1155	5932702-K	FERNÁNDEZ TORRES, SARA DEL CARMEN	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.500734		No Especifica
104909	2012-04-19	7	7	\N	\N	f	1067	17388320-K	ESTAY LÓPEZ, CAROL NICOL	0	2012-04-18	2013-02-18	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-04-19 00:00:00	2012-08-30 09:40:27.503884		Estudio Pre-Trasplante
104910	2012-06-26	7	7	\N	\N	f	776	6908208-4	OYANEDER TAPIA, ROSA DE LAS MERCEDES	0	2012-06-21	2013-02-18	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-06-27 00:00:00	2012-08-30 09:40:27.507011		Izquierda
104911	2012-06-26	7	7	\N	\N	f	776	4235793-6	SAAVEDRA CASTRO, CLEMIRA DEL CARMEN	0	2012-06-21	2013-02-18	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-06-27 00:00:00	2012-08-30 09:40:27.51002		Derecha
104912	2012-08-21	7	7	\N	\N	f	925	2737413-1	ROMÁN DONOSO, DANIEL MARIO	0	2012-08-20	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.512995		No Especifica
104913	2012-08-21	7	7	\N	\N	f	925	6537958-9	FLORES MONTENEGRO, VICTORIA DE LAS MERC	0	2012-08-20	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-21 00:00:00	2012-08-30 09:40:27.515984		No Especifica
104914	2012-08-21	7	7	\N	\N	f	1155	5149724-4	SILVA MENA, HILDA DE LAS MERCEDE	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.518922		No Especifica
104915	2012-08-22	7	7	\N	\N	f	925	3219523-7	BRITO VÁSQUEZ, ELEODORO DEL CARMEN	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.522466		No Especifica
104916	2012-08-22	7	7	\N	\N	f	927	4208639-8	MENA CASTILLO, JUAN SEGUNDO	0	2012-08-20	2013-02-18	Catarátas Rama Unilateral Izquierda{decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Izquierda.	2012-08-22 00:00:00	2012-08-30 09:40:27.526309		Izquierda
104917	2012-08-22	7	7	\N	\N	f	1043	3019433-0	LEMUS LEMUS, EXEQUIEL	0	2012-08-20	2013-02-18	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-22 00:00:00	2012-08-30 09:40:27.529532		Retención Urinaria Aguda Repetida
104918	2012-08-22	7	7	\N	\N	f	1043	4724294-0	CHAVARRÍA MORENO, GUILLERMO ALEJANDRO	0	2012-08-20	2013-02-18	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-22 00:00:00	2012-08-30 09:40:27.532618		Retención Urinaria Aguda Repetida
104919	2012-08-22	7	7	\N	\N	f	1043	6461332-4	CUETO PACHECO, RODOLFO EDUARDO	0	2012-08-20	2013-02-18	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-22 00:00:00	2012-08-30 09:40:27.535607		Retención Urinaria Aguda Repetida
104920	2012-08-22	7	7	\N	\N	f	1155	3132899-3	PÉREZ RODRÍGUEZ, JOSÉ	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.538561		No Especifica
104921	2012-08-22	7	7	\N	\N	f	1155	3723879-1	HERRERA GONZÁLEZ, INÉS	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.541454		No Especifica
104922	2012-08-22	7	7	\N	\N	f	1155	3840356-7	CÁDIZ CORTEZ, ELIANA DEL CARMEN	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.544298		No Especifica
104923	2012-08-22	7	7	\N	\N	f	1155	4230218-K	CHAMORRO CASTRO, HERNÁN GERARDO	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.547225		No Especifica
104924	2012-08-22	7	7	\N	\N	f	1155	4357208-3	FIGUEROA ORELLANA, HÉCTOR ROLANDO	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.550351		No Especifica
104925	2012-08-22	7	7	\N	\N	f	1155	5155911-8	LABBÉ , MARÍA	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.553296		No Especifica
104926	2012-08-22	7	7	\N	\N	f	1155	5298553-6	DONOSO RIVERA, CARLOS	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.556147		No Especifica
104927	2012-08-22	7	7	\N	\N	f	1155	6011380-7	ARDELA DURÁN, ADRIANA LUCRECIA	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-22 00:00:00	2012-08-30 09:40:27.55909		No Especifica
104928	2012-08-23	7	7	\N	\N	f	909	22435570-K	ARENAS CONTRERAS, MARTIN MARCELO	0	2012-08-21	2013-02-18	Cardiopatías Congénitas Operables Proceso de Diagnóstico{decreto nº 228}	Confirmación Diagnóstico Post-Natal entre 8 días y 15 años	2012-08-23 00:00:00	2012-08-30 09:40:27.562506		Post - Natal entre 2 Años y Menor de 15 Años
104929	2012-08-23	7	7	\N	\N	f	925	4423607-9	DÍAZ GODOY, MARÍA EUGENIA	0	2012-08-20	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.565576		No Especifica
104930	2012-08-23	7	7	\N	\N	f	925	5820489-7	DAL POZZO VALDEVENITO, GLORIA	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.568976		No Especifica
104931	2012-08-23	7	7	\N	\N	f	1155	3185538-1	REBOLLEDO LÓPEZ, REGINALDO	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.571853		No Especifica
104932	2012-08-23	7	7	\N	\N	f	1155	3897502-1	GONZÁLEZ MUJICA, JORGE ALEX ARMANDO	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.574782		No Especifica
104933	2012-08-23	7	7	\N	\N	f	1155	4605506-3	POBLETE OVANDO, HAYDÉE DEL CARMEN	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.57785		No Especifica
104934	2012-08-23	7	7	\N	\N	f	1155	5104753-2	ORREGO GONZÁLEZ, CARMEN LUISA	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.580853		No Especifica
104935	2012-08-23	7	7	\N	\N	f	1155	5143111-1	BUSTOS VALENCIA, CARLOS	0	2012-08-20	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.583749		No Especifica
104936	2012-08-23	7	7	\N	\N	f	1155	5741277-1	MORALES RIOFRÍO, GLADYS DEL TRÁNSITO	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-23 00:00:00	2012-08-30 09:40:27.58661		No Especifica
104937	2012-08-27	7	7	\N	\N	f	925	5257390-4	AGUILERA GUERRA, FELICINDA DE MERCEDE	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.58987		No Especifica
104938	2012-08-27	7	7	\N	\N	f	925	5732678-6	RIVERA OLIVARES, MARÍA LUISA	0	2012-08-22	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.592941		No Especifica
104939	2012-08-27	7	7	\N	\N	f	927	1998417-6	VILLANUEVA ESCOBAR, OSIEL ARMANDO	0	2012-08-22	2013-02-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:27.596201		Bilateral
104940	2012-08-27	7	7	\N	\N	f	927	2544338-1	RETAMAL LOZANO, MARÍA DOLORES	0	2012-08-22	2013-02-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:27.599573		Bilateral
104941	2012-08-27	7	7	\N	\N	f	927	3361869-7	AGUILERA LEÓN, ROQUE DEL ROSARIO	0	2012-08-21	2013-02-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:27.602932		Bilateral
104942	2012-08-27	7	7	\N	\N	f	927	4424521-3	PORMA CANIO, ROSA DEL CARMEN	0	2012-08-22	2013-02-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:27.60626		Bilateral
104943	2012-08-27	7	7	\N	\N	f	927	4698530-3	VARGAS BARRERA, BARTOLA ASSEMETT	0	2012-08-21	2013-02-18	Catarátas Rama Bilateral {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Bilateral.	2012-08-27 00:00:00	2012-08-30 09:40:27.609976		Bilateral
104944	2012-08-27	7	7	\N	\N	f	1155	3854091-2	FARÍAS ROJAS, ELENA DEL CARMEN	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.613		No Especifica
104945	2012-08-27	7	7	\N	\N	f	1155	4507698-9	ROMÁN TORO, DAVID GUILLERMO	0	2012-08-22	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.615878		No Especifica
104946	2012-08-27	7	7	\N	\N	f	1155	4705167-3	SAGAS GARCÍA, TRINIDAD DEL PILAR	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.618706		No Especifica
104947	2012-08-28	7	7	\N	\N	f	925	9268713-9	VERDEJO LEÓN, ROLANDO HUMBERTO	0	2012-08-22	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.621792		No Especifica
104948	2012-08-28	7	7	\N	\N	f	1155	4726098-1	GALLEGUILLOS , BLANCA REBECA	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.62469		No Especifica
104949	2012-08-28	7	7	\N	\N	f	1155	5263008-8	SÁNCHEZ MARILLANCA, MARÍA ELENA	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.627617		No Especifica
104950	2012-08-29	7	7	\N	\N	f	925	3752441-7	ARENAS CUBILLOS, ELIANA ROSA	0	2012-08-21	2013-02-18	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.630676		No Especifica
104951	2012-08-29	7	7	\N	\N	f	1155	3942458-4	PÉREZ ÁVILA, MARÍA DEL CARMEN	0	2012-08-21	2013-02-18	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.633572		No Especifica
104952	2012-08-24	7	7	\N	\N	f	925	2869752-K	SEGURA , MARÍA ADRIANA	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.636583		No Especifica
104953	2012-08-24	7	7	\N	\N	f	925	3431264-8	TORRES RAMÍREZ, NIMIA	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.639682		No Especifica
104954	2012-08-24	7	7	\N	\N	f	1155	3944534-4	LÓPEZ ROMERO, EMIRA DE LAS MERCEDE	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.642747		No Especifica
104955	2012-08-24	7	7	\N	\N	f	1155	4032607-3	MARX AZAGRA, GERMÁN ALFONSO	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.646147		No Especifica
104956	2012-08-24	7	7	\N	\N	f	1155	4496147-4	ROSSI VALDIVIA, REINALDO EUGENIO	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.649273		No Especifica
104957	2012-08-24	7	7	\N	\N	f	1155	5680665-2	GUERRA ASTUDILLO, ELCIRA ERNESTINA	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-24 00:00:00	2012-08-30 09:40:27.652254		No Especifica
104958	2012-08-27	7	7	\N	\N	f	925	10319793-7	FARFÁN ESCOBAR, LAURA DEL CARMEN	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.655625		No Especifica
104959	2012-08-27	7	7	\N	\N	f	1155	3442358-k	VIDAL , JUANA ELBA	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.658569		No Especifica
104960	2012-08-28	7	7	\N	\N	f	925	3685492-8	PARGA UNDURRAGA, ORIANA LUISA	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.661472		No Especifica
104961	2012-08-28	7	7	\N	\N	f	925	4399330-5	CÁRCAMO MARTÍNEZ, MARÍA ISABEL	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.664524		No Especifica
104962	2012-08-28	7	7	\N	\N	f	925	4569949-8	OLMOS ROJAS, XIMENA DEL CARMEN	0	2012-08-23	2013-02-19	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.667499		No Especifica
104963	2012-08-28	7	7	\N	\N	f	997	18997378-0	OLAVE HERNÁNDEZ, PRISCILLA ANDREA	0	2012-08-23	2013-02-19	Esquizofrenia . {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.670587		No Especifica
104964	2012-08-28	7	7	\N	\N	f	1155	3675279-3	BERNAL , AUGUSTO	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.673472		No Especifica
104965	2012-08-29	7	7	\N	\N	f	1155	3579496-4	DE LA HOZ RODRÍGUEZ, NORA CRUZ	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.676318		No Especifica
104966	2012-08-29	7	7	\N	\N	f	1155	4725719-0	ALVIAL HENRÍQUEZ, CARMEN ADRIANA	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.679226		No Especifica
104967	2012-08-29	7	7	\N	\N	f	1155	4876414-2	RAMÍREZ PÉREZ, GLADYS MERCEDES	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.682172		No Especifica
104968	2012-08-29	7	7	\N	\N	f	1155	5288044-0	MIRANDA AVENDAÑO, ANGELA DEL CARMEN	0	2012-08-23	2013-02-19	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.68522		No Especifica
104969	2012-08-27	7	7	\N	\N	f	925	5463366-1	DELGADO OSSA, JUAN ROBERTO BERNARD	0	2012-08-24	2013-02-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.688723		No Especifica
104970	2012-08-28	7	7	\N	\N	f	1155	4036470-6	REYES JORQUERA, ELIANA DEL CARMEN	0	2012-08-24	2013-02-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.691863		No Especifica
104971	2012-08-28	7	7	\N	\N	f	1155	4294589-7	ROMERO LAGOS, MARTA DEL CARMEN	0	2012-08-24	2013-02-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.694828		No Especifica
104972	2012-08-29	7	7	\N	\N	f	925	2038925-7	CAMUS SALGADO, ILIANA EUGENIA	0	2012-08-24	2013-02-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.697798		No Especifica
104973	2012-08-29	7	7	\N	\N	f	925	7019624-7	CALDERÓN LUCERO, MARÍA EUGENIA	0	2012-08-24	2013-02-20	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.700838		No Especifica
104974	2012-08-29	7	7	\N	\N	f	1155	2098519-4	GONZÁLEZ ASENJO, MARTA EUFEMIA	0	2012-08-24	2013-02-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.703775		No Especifica
104975	2012-08-29	7	7	\N	\N	f	1155	3361588-4	ESPINOZA DURÁN, NORMA CRUZ	0	2012-08-24	2013-02-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.706608		No Especifica
104976	2012-08-29	7	7	\N	\N	f	1155	4745003-9	CABRERA PINO, SARA TERESA	0	2012-08-24	2013-02-20	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.709517		No Especifica
104977	2012-08-27	7	7	\N	\N	f	925	2326474-9	VICENCIO FERNÁNDEZ, SAMUEL DEL TRÁNSITO	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.71275		No Especifica
104978	2012-08-27	7	7	\N	\N	f	925	2510316-5	CHACÓN SCHMIDT, LYLIA GLADIS	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.715778		No Especifica
104979	2012-08-27	7	7	\N	\N	f	925	4704903-2	AHUMADA MOYANO, ELIZABETH DEL CARMEN	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.718736		No Especifica
104980	2012-08-27	7	7	\N	\N	f	925	5018493-5	LEÓN LEÓN, REBECA DEL CARMEN	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.721744		No Especifica
104981	2012-08-27	7	7	\N	\N	f	925	5418213-9	ESTAY FERNÁNDEZ, EMILIA DEL ROSARIO	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-27 00:00:00	2012-08-30 09:40:27.724775		No Especifica
104982	2012-08-29	7	7	\N	\N	f	925	2576125-1	MUÑOZ , GUILLERMO	0	2012-08-25	2013-02-21	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.727784		No Especifica
104983	2012-07-03	7	7	\N	\N	f	776	2809812-K	ARRIAGADA MUÑOZ, SYLVIA DEL CARMEN	0	2012-06-27	2013-02-22	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-03 00:00:00	2012-08-30 09:40:27.731074		Derecha
104984	2012-07-04	7	7	\N	\N	f	776	5933653-3	GUTIÉRREZ GONZÁLEZ, MARÍA ISABEL	0	2012-06-28	2013-02-25	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-04 00:00:00	2012-08-30 09:40:27.734106		Izquierda
104985	2012-08-28	7	7	\N	\N	f	925	3672537-0	VERGARA GATICA, CARMEN JULIA	0	2012-08-27	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.737062		No Especifica
104986	2012-08-28	7	7	\N	\N	f	927	3131683-9	GARCÍA COVARRUBIAS, AÍDA DEL CARMEN	0	2012-08-27	2013-02-25	Catarátas Rama Unilateral Derecha {decreto nº 228}	Tratamiento AV Igual o Inferior a 0,3 Unilateral Derecha.	2012-08-29 00:00:00	2012-08-30 09:40:27.740637		Derecha
104987	2012-08-29	7	7	\N	\N	f	925	10442697-2	CARVAJAL ROJAS, PATRICIA MANUELA	0	2012-08-28	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.74377		No Especifica
104988	2012-08-29	7	7	\N	\N	f	925	2874120-0	SOLAR ITURRIETA, NELLY ESTELIA	0	2012-08-27	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.746705		No Especifica
104989	2012-08-29	7	7	\N	\N	f	925	3483881-k	MENA ESCOBAR, MARÍA LIDIA	0	2012-08-27	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.750093		No Especifica
104990	2012-08-29	7	7	\N	\N	f	925	3763908-7	CARMONA MARCHANT, FRANCISCO	0	2012-08-28	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.753056		No Especifica
104991	2012-08-29	7	7	\N	\N	f	925	4424359-8	OLIVARES AROS, JUAN MIGUEL	0	2012-08-27	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.756104		No Especifica
104992	2012-08-29	7	7	\N	\N	f	925	4843435-5	TAPIA CONTRERAS, RAMIRO ALFONSO	0	2012-08-28	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.759112		No Especifica
104993	2012-08-29	7	7	\N	\N	f	925	4843914-4	MUNIZAGA MUNIZAGA, GRACIELA VIRJINIA DE	0	2012-08-27	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.762024		No Especifica
104994	2012-08-29	7	7	\N	\N	f	925	8043771-4	JARA RAMÍREZ, MARÍA TERESA	0	2012-08-28	2013-02-25	Catarátas Proceso de Diagnóstico {decreto nº 228}	Confirmación-Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.764934		No Especifica
104995	2012-08-29	7	7	\N	\N	f	1043	4941264-9	NAVARRETE LÓPEZ, PATRICIO DANIEL RAÚL	0	2012-08-27	2013-02-25	Hiperplasia de Próstata . {decreto nº 228}	Tratamiento Retención Urinaria Aguda Repetida.	2012-08-29 00:00:00	2012-08-30 09:40:27.768046		Retención Urinaria Aguda Repetida
104996	2012-08-29	7	7	\N	\N	f	1155	3480340-4	GELDRES VALENCIA, HIRAM	0	2012-08-28	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.771748		No Especifica
104997	2012-08-29	7	7	\N	\N	f	1155	4261545-5	PERALTA JIMÉNEZ, MARÍA LUCÍA	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.774671		No Especifica
104998	2012-08-29	7	7	\N	\N	f	1155	4386267-7	HENRÍQUEZ ARACENA, GUILLERMINA DEL CARM	0	2012-08-28	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.777697		No Especifica
104999	2012-08-29	7	7	\N	\N	f	1155	4612617-3	CONTRERAS SALINAS, BALTAZAR DEL CARMEN	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.780658		No Especifica
105000	2012-08-29	7	7	\N	\N	f	1155	5406376-8	CASTRO CASTRO, ESPERANZA DEL CARMEN	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.78354		No Especifica
105001	2012-08-29	7	7	\N	\N	f	1155	5884547-7	LÓPEZ ROJO, ADELA DE LAS MERCEDE	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.786535		No Especifica
105002	2012-08-29	7	7	\N	\N	f	1155	8437682-5	VILLAGRÁN ROJAS, DEILEMA DEL CARMEN	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.789422		No Especifica
105003	2012-08-29	7	7	\N	\N	f	1155	2564913-3	GÓMEZ MATURANA, MARINA LUISA	0	2012-08-27	2013-02-25	Vicios de Refracción Sospecha Vicios de Refracción {decreto nº 228}	Confirmación Diagnóstica	2012-08-29 00:00:00	2012-08-30 09:40:27.792282		No Especifica
105004	2012-07-10	7	7	\N	\N	f	776	4610862-0	PERALTA ZAMORA, JOSÉ RAMÓN	0	2012-07-03	2013-02-28	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-10 00:00:00	2012-08-30 09:40:27.795191		Derecha
105005	2012-07-06	7	7	\N	\N	f	776	3455175-8	UGARTE SALAS, BLANCA ROSA	0	2012-07-03	2013-02-28	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-06 00:00:00	2012-08-30 09:40:27.798216		Izquierda
105006	2012-07-10	7	7	\N	\N	f	776	5150182-9	MORALES BADILLA, ARMANDO HUGO	0	2012-07-04	2013-03-01	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-10 00:00:00	2012-08-30 09:40:27.801294		Derecha
105007	2012-05-07	7	7	\N	\N	f	1067	9953020-0	CORNEJO GÁLVEZ, ROBERTO ANTONIO	0	2012-05-04	2013-03-04	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-05-07 00:00:00	2012-08-30 09:40:27.804528		Estudio Pre-Trasplante
105008	2012-07-10	7	7	\N	\N	f	776	5626141-9	LÓPEZ FIGUEROA, HILDA DE LOS DOLORES	0	2012-07-05	2013-03-04	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-10 00:00:00	2012-08-30 09:40:27.807618		Izquierda
105009	2012-07-10	7	7	\N	\N	f	776	5233053-K	SEPÚLVEDA ASTUDILLO, JOSÉ LUIS	0	2012-07-05	2013-03-04	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-10 00:00:00	2012-08-30 09:40:27.810596		Derecha
105010	2012-07-10	7	7	\N	\N	f	776	4519058-7	LAZCANO ESTAY, RUPERTO DEL CARMEN	0	2012-07-06	2013-03-04	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-10 00:00:00	2012-08-30 09:40:27.813504		Izquierda
105011	2012-07-10	7	7	\N	\N	f	776	4506560-K	ARCE CAVIEDES, TERESA HAYDÉE	0	2012-07-05	2013-03-04	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-10 00:00:00	2012-08-30 09:40:27.816513		Izquierda
105012	2012-05-15	7	7	\N	\N	f	1067	7799165-4	MIRANDA AYCINENA, HUGO ERNESTO	0	2012-05-09	2013-03-08	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-05-15 00:00:00	2012-08-30 09:40:27.819979		Estudio Pre-Trasplante
105013	2012-07-17	7	7	\N	\N	f	776	5644077-1	MENA BERRÍOS, RAQUEL DE LAS MERCED	0	2012-07-11	2013-03-08	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-17 00:00:00	2012-08-30 09:40:27.823088		Izquierda
105014	2012-07-18	7	7	\N	\N	f	776	5376860-1	ROMO CASTILLO, MARÍA ISABEL	0	2012-07-12	2013-03-11	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-18 00:00:00	2012-08-30 09:40:27.826145		Izquierda
105015	2012-07-17	7	7	\N	\N	f	776	5268000-K	MARÍN SILVA, ELVIRA	0	2012-07-12	2013-03-11	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-17 00:00:00	2012-08-30 09:40:27.829161		Derecha
105016	2012-07-18	7	7	\N	\N	f	776	5153335-6	JIL CABRERA, MANUEL	0	2012-07-12	2013-03-11	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-07-18 00:00:00	2012-08-30 09:40:27.832287		Izquierda
105017	2012-07-18	7	7	\N	\N	f	776	3594150-9	PINEDA REYES, ANA EDITH	0	2012-07-12	2013-03-11	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-18 00:00:00	2012-08-30 09:40:27.835217		Derecha
105018	2012-05-18	7	7	\N	\N	f	1067	16890655-2	NÚÑEZ NÚÑEZ, JAIRO IGNACIO	0	2012-05-15	2013-03-14	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-05-18 00:00:00	2012-08-30 09:40:27.838437		Estudio Pre-Trasplante
105019	2012-07-23	7	7	\N	\N	f	776	4048299-7	VARGAS REBOLLEDO, JOSÉ MIGUEL	0	2012-07-17	2013-03-14	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-23 00:00:00	2012-08-30 09:40:27.841447		Derecha
105020	2012-07-20	7	7	\N	\N	f	776	3223871-8	JOFRÉ VALDÉS, CARLOS RENÉ	0	2012-07-18	2013-03-15	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-20 00:00:00	2012-08-30 09:40:27.844369		Derecha
105021	2012-07-23	7	7	\N	\N	f	776	5768931-5	LARENAS GONZÁLEZ, JULIO ERNESTO	0	2012-07-19	2013-03-18	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-23 00:00:00	2012-08-30 09:40:27.847194		Derecha
105022	2012-07-24	7	7	\N	\N	f	776	4451508-3	ORQUERA GODOY, HUGO ALEJO	0	2012-07-20	2013-03-18	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-25 00:00:00	2012-08-30 09:40:27.850058		Derecha
105023	2012-07-25	7	7	\N	\N	f	776	5103551-8	CUBILLOS OLIVARES, ROSA ISABEL	0	2012-07-23	2013-03-20	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-07-25 00:00:00	2012-08-30 09:40:27.852874		Derecha
105024	2012-08-01	7	7	\N	\N	f	776	5495269-4	PEÑA ARANCIBIA, CAMILO ENRIQUE	0	2012-07-26	2013-03-25	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-01 00:00:00	2012-08-30 09:40:27.8557		Izquierda
105025	2012-08-01	7	7	\N	\N	f	776	2706920-7	REGELMANN MUÑOZ, CHRISTIAN SEGUNDO	0	2012-07-27	2013-03-25	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-01 00:00:00	2012-08-30 09:40:27.858624		Derecha
105026	2012-06-01	7	7	\N	\N	f	1067	8972817-7	AGUILERA LÓPEZ, CARLOS ERASMO	0	2012-05-28	2013-03-27	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-06-01 00:00:00	2012-08-30 09:40:27.861607		Estudio Pre-Trasplante
105027	2012-05-29	7	7	\N	\N	f	1067	6562037-5	ALIAGA BARDELLI, SOLEDAD DEL CARMEN	0	2012-05-28	2013-03-27	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-05-29 00:00:00	2012-08-30 09:40:27.864596		Estudio Pre-Trasplante
105028	2012-08-07	7	7	\N	\N	f	776	5385605-5	MIRANDA MIRANDA, ABEL DEL CARMEN	0	2012-08-03	2013-04-01	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-07 00:00:00	2012-08-30 09:40:27.867644		Derecha
105029	2012-08-09	7	7	\N	\N	f	776	5833992-K	ROJAS LARA, MARÍA EDITH	0	2012-08-06	2013-04-03	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-09 00:00:00	2012-08-30 09:40:27.870698		Izquierda
105030	2012-08-08	7	7	\N	\N	f	776	5241566-7	JAURE MORAGA, JUAN MANUEL	0	2012-08-06	2013-04-03	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-08 00:00:00	2012-08-30 09:40:27.873619		Derecha
105031	2012-08-09	7	7	\N	\N	f	776	3699940-3	ZAMORA PASTÉN, JOSÉ DOMINGO	0	2012-08-06	2013-04-03	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-09 00:00:00	2012-08-30 09:40:27.877088		Izquierda
105032	2012-08-10	7	7	\N	\N	f	776	5951856-9	PEÑA ESPINOZA, XIMENA FLORA	0	2012-08-08	2013-04-05	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-10 00:00:00	2012-08-30 09:40:27.88005		Derecha
105033	2012-08-10	7	7	\N	\N	f	776	4757632-6	VALENCIA CONTRERAS, YOLANDA LUISA	0	2012-08-08	2013-04-05	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-10 00:00:00	2012-08-30 09:40:27.882992		Derecha
105034	2012-08-13	7	7	\N	\N	f	776	2484467-6	BUSTOS , ZULEMA DEL CARMEN	0	2012-08-08	2013-04-05	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-13 00:00:00	2012-08-30 09:40:27.885929		Derecha
105035	2012-08-16	7	7	\N	\N	f	776	4630855-7	TUSET JORRATT, MARÍA ANGÉLICA	0	2012-08-09	2013-04-08	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-16 00:00:00	2012-08-30 09:40:27.889586		Derecha
105036	2012-08-13	7	7	\N	\N	f	776	4576696-9	AZABE NASSER, JUANA MARÍA	0	2012-08-09	2013-04-08	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-13 00:00:00	2012-08-30 09:40:27.892701		Izquierda
105037	2012-08-16	7	7	\N	\N	f	776	4067291-5	ESPINOZA HUERTA, HERMOSINA DE LAS MER	0	2012-08-09	2013-04-08	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-16 00:00:00	2012-08-30 09:40:27.895698		Derecha
105038	2012-08-17	7	7	\N	\N	f	776	4695858-6	RODRÍGUEZ FERRADA, CARMEN NURI	0	2012-08-13	2013-04-10	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-17 00:00:00	2012-08-30 09:40:27.898633		Derecha
105039	2012-08-17	7	7	\N	\N	f	776	4740030-9	MORAGA DURÁN, LILIANA DEL CARMEN	0	2012-08-16	2013-04-15	Artrosis de Caderas Derecha {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Derecha	2012-08-17 00:00:00	2012-08-30 09:40:27.901703		Derecha
105040	2012-08-21	7	7	\N	\N	f	776	2976544-8	FUENZALIDA RÍOS, WALDO	0	2012-08-17	2013-04-15	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-21 00:00:00	2012-08-30 09:40:27.90468		Izquierda
105041	2012-08-23	7	7	\N	\N	f	776	5047777-0	GUTIÉRREZ AGUILERA, LUIS EDUARDO	0	2012-08-21	2013-04-18	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-23 00:00:00	2012-08-30 09:40:27.907818		Izquierda
105042	2012-08-29	7	7	\N	\N	f	776	5017634-7	DELAIGUE DELAIGUE, BLANCA SONIA	0	2012-08-27	2013-04-24	Artrosis de Caderas Izquierda {decreto nº 228}	Tratamiento Quirúrgico Endoprótesis Izquierda	2012-08-29 00:00:00	2012-08-30 09:40:27.910926		Izquierda
105043	2012-06-28	7	7	\N	\N	f	1067	15146065-8	BASTÍAS OLIVO, MARÍA GRACIELA	0	2012-06-26	2013-04-25	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-06-28 00:00:00	2012-08-30 09:40:27.913868		Estudio Pre-Trasplante
105044	2012-08-23	7	7	\N	\N	f	1067	14414352-3	MACCHIAVELLO GARCÍA, FRANCESCA PAOLA DE L	0	2012-08-20	2013-06-19	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-23 00:00:00	2012-08-30 09:40:27.916895		Estudio Pre-Trasplante
105045	2012-08-23	7	7	\N	\N	f	1067	7354245-6	ZELADA DINAMARCA, JUAN LUIS	0	2012-08-20	2013-06-19	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-23 00:00:00	2012-08-30 09:40:27.919922		Estudio Pre-Trasplante
105046	2012-08-23	7	7	\N	\N	f	1067	13428827-2	ARAOS GONZÁLEZ, SILVANA ALEJANDRA	0	2012-08-21	2013-06-20	Insuficiencia Renal Crónica Terminal . {decreto nº 228}	Estudio Pre-Transplante	2012-08-23 00:00:00	2012-08-30 09:40:27.922947		Estudio Pre-Trasplante
105047	2012-07-06	7	7	\N	\N	f	921	23023506-6	VARELA CABRERA, SOFÍA ANTONIA	0	2012-07-05	2013-07-05	Cardiopatías Congénitas Operables Otras Cardiopatías Congénitas Operables {decreto nº 228}	CCO Otras. Control desde alta por Cirugía	2012-07-27 00:00:00	2012-08-30 09:40:27.92614		No Especifica
105048	2012-08-17	7	7	\N	\N	f	1143	13752385-K	ARANDA ROJAS, ANTONELA DEL CARMEN	0	2012-08-16	2013-11-15	Salud Oral Integral de la Embarazada . {decreto n° 1/2010}	Alta integral	2012-08-17 00:00:00	2012-08-30 09:40:27.929342		No Especifica
\.


--
-- Data for Name: monitoreo_ges_registro; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY monitoreo_ges_registro (monr_id, mon_id, monr_func_id, monr_fecha, monr_clase, monr_subclase, monr_observaciones, monr_fecha_proxmon, monr_descripcion, monr_subcondicion, monr_fecha_evento, monr_estado) FROM stdin;
61057	104103	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61059	104105	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61022	104068	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60976	104022	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
59814	102855	7	2012-08-29 00:00:00	11	I	Paciente no contesta 2581036  29/08/2012 // llamado Paciente con entrega de lentes dia 24/08/2012 2 pares	\N			2012-08-30	0
59815	102856	7	2012-08-29 00:00:00	11	I	Paciente no contesta llamado 98386988 29/08/2012 // Paciente con entrega de lentes dia 24/08/2012 2 pares	\N			2012-08-30	0
59819	102860	7	2012-08-29 00:00:00	11	I	Paciente en llamado 2739353 no contesta 29/08/2012 // Paciente en llamado 2739353 27/08/2012 afirma que la óptica le dio fecha de entrega el 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59821	102862	7	2012-08-29 00:00:00	11	I	Paciente no contesta llamado 2110938  29/08/2012 // Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-30	0
59824	102865	7	2012-08-29 00:00:00	11	I	Paciente en llamado al 93794121 no contesta 29/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59825	102866	7	2012-08-29 00:00:00	11	I	Paciente en llamado al 99442386 no contesta 29/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59801	102842	7	2012-08-29 00:00:00	22		Según óptica reflejos el paciente retiró 2 pares de lentes el 28/08/2012 // Se llama a paciente para que vaya a retirar lentes y dice que ira hoy	\N			2012-08-28	0
59802	102843	7	2012-08-29 00:00:00	1		29/08/2012 Paciente citado 30-08-2012 Dr Cavala, se llama a pac para recordar hora y se deja recado con marido el llamado fue a las 15:10 hrs al 89668113	\N			2012-08-30	0
59804	102845	7	2012-08-29 00:00:00	22		29/08/2012 PAC CON IPD DESCARTE	\N			2012-08-29	0
59806	102847	7	2012-08-29 00:00:00	22		Paciente Etapificada el 29/08/2012	\N			2012-08-29	0
59816	102857	7	2012-08-29 00:00:00	45	G	Fonoaudiólogo informa adelanta hora para el 29/08/2012; Falt a Entrega de Documento a Secretaria GES	2012-08-30		IPD	2012-08-29	0
59808	102849	7	2012-08-29 00:00:00	22		29/08/2012 falta parametrizacion en sigges	2012-08-30			2012-08-28	0
59809	102850	7	2012-08-29 00:00:00	22		Paciente descartada el 23/08/2012 con Bp.	\N			2012-08-23	0
59810	102851	7	2012-08-29 00:00:00	22		Paciente se realiza Crioterapia el 21/08/2012	\N			2012-08-21	0
59811	102852	7	2012-08-29 00:00:00	22		admision 30/07/2012: Citado para el  23/08/2012 DR. ACEVEDO	\N			2012-08-28	0
59812	102853	7	2012-08-29 00:00:00	22		Según optica reflejo los lentes fuerón retirados el 27/08/2012  2 pares. 29/08/2012 // Optica Reflejo Lentes Probados 31/07/2012 con fecha de Retiro 27/08/2012 cantidad 2	\N			2012-08-29	0
59817	102858	7	2012-08-29 00:00:00	1		Fonoaudiólogo informa adelanta hora para el 29/08/2012; Falt a Entrega de Documento a Secretaria GES	2012-08-30			2012-08-29	0
59818	102859	7	2012-08-29 00:00:00	1		PED.RESPIRATORIO INF.-HDGF 30/08/12	\N			2012-08-30	0
59820	102861	7	2012-08-29 00:00:00	22		Paciente en llamado 99881801 afirma que retiro lentes el 28/08/2012 2 pares // Paciente en llamado 99881801 que óptica le entregará los lentes el 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59822	102863	7	2012-08-29 00:00:00	22		Paciente en llamado  afirma entrega de lentes 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59823	102864	7	2012-08-29 00:00:00	22		Paciente afirma en llamado 2887644 que retiro lentes el 28/08/2012  2 pares // Pac afirma entrega de lentes 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59826	102867	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59827	102868	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	\N			2012-08-28	0
59799	102840	7	2012-08-29 00:00:00	1	B	Dr valenzuela en consulta del  24/08/2012, solicita nueva Eco, se cita para el 16/11/2012 a control con nuevos examenes	\N		Patologia mamaria	2012-11-16	0
59796	102837	7	2012-08-29 00:00:00	1	B	(29/08/2012)  Paciente NSP 14/08/2012 , se gestiona citacion para el 12/09/2012 a las 12:00 .	2012-09-15		cintigrama oseo	2012-09-12	0
59795	102836	7	2012-08-29 00:00:00	3	A	Se Toma Bp El 16/08/2012, Aun no envian resultado de BET; Se llama 4 veces a Santiago para saber de la fecha de resultado y no contestan, se va a unidad de patologia mamaria y nos indica que resultado llegara en la primera semana de septiembre	\N		Patologia mamaria	\N	0
59800	102841	7	2012-08-29 00:00:00	3	A	Se toma BET el 24/08/2012, Aun no envian de Hospital Barros Luco Informe. Examenes App se demora 18 dias	\N		Patologia mamaria	\N	0
59792	102833	7	2012-08-29 00:00:00	5	G	29/08/2012/ Se consulta a Dra. Arancibia e inf. Que ella tiene tabla de AL y AG lunes por 1/2. El  03/09 tiene AG, por lo que el pac. Esta en tabla del 10/09. Ella se va de vacaciones del 10 al 26/09, así que depende de Dra. Lobos que deje reemplante en p	\N		Biopsia Quirurgica	2012-09-10	0
59791	102832	7	2012-08-29 00:00:00	10	G	29/08/2012/ Se consulta a Dra. Toro e informa que se sol. CLADRIBINA, la que es de uso excepcional (se importa). Farmacia inf. Que se esta recabando información de paciente con Dra. Para compra.	\N		Quimioterapia	\N	0
59798	102839	7	2012-08-29 00:00:00	11	I	29/08/2012 PAC NSP A CONTROL	\N			2012-08-30	0
59805	102846	7	2012-08-29 00:00:00	15	G	29/08/2012- Paciente registra retiro de Prednisona el 21/08/2012, corresponde retiro en el mes de Septiembre	\N			2012-08-29	0
59797	102838	7	2012-08-29 00:00:00	15	G	debe ser citada para examenes de evaluacion antes de serconfirmada	\N			2012-08-28	0
59793	102834	7	2012-08-29 00:00:00	17	M	29/08/2012/ Se llamó para confirmar asistencia del 29/08 en reiteradas oportunidades a los fonos   2783984 / 82564075 y no contestan. Requieró ficha para ver asistencia e indicaciones	\N			2012-08-30	0
59807	102848	7	2012-08-29 00:00:00	1	B	admision 29/08/2012: CITADO EL DIA  11/09/2012	\N			2012-09-11	0
59828	102869	7	2012-08-13 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 03/09/2012	\N		CONIZACION Y/O AMPUTACION DEL CUELLO, DIAGNOSTICA Y/O TERAPEUTICA C/S BIOPSIA	2012-09-03	0
59829	102870	7	2012-08-29 00:00:00	22		admision 22/08/2012: Citado para el  27/08/2012	\N			2012-08-27	0
59830	102871	7	2012-08-29 00:00:00	22		29/08/2012 falta parametrizacion en sigges	\N			2012-08-24	0
59843	102884	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59832	102873	7	2012-08-22 00:00:00	1		Se adelanta hora para el 03/09/2012 con Bp Quirurgica.	\N		Etapificacion	2012-09-03	0
59833	102874	7	2012-08-27 00:00:00	1		Citado para el 03/09/2012 OFT nuevo dr Trincado // DICE QUE NO LA LLAMARON, Y QUE NECESITA DE OTRA ATENCION, SE INCLUYE EN HORAS MONITOREO	\N			2012-09-03	0
59837	102878	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59839	102880	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59841	102882	7	2012-08-29 00:00:00	22		Paciente en llamado 88311562 afirma que retiro lentes el 28/08/2012 2 pares // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59847	102888	7	2012-08-16 00:00:00	1		16/08/2012/ Paciente registra hora en el Gis para el 31/08/2012	2012-08-31			2012-08-31	0
59868	102909	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59849	102890	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	\N			2012-08-28	0
59850	102891	7	2012-08-29 00:00:00	22		29/08/2012 pac con ipd descarte	\N			2012-08-28	0
59854	102895	7	2012-08-29 00:00:00	1		29/08/2012 pac asiste a citacion del 29/08/2012 se debe corroborar con nominas	2012-08-31			2012-08-29	0
59856	102897	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59859	102900	7	2012-08-29 00:00:00	22		Paciente en llamado 2842559 afirma que retiro lentes el 28/08/2012  2 pares //  Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59866	102907	7	2012-08-29 00:00:00	22		Paciente en llamado 2842559 afirma que retiro lentes el 28/08/2012 2 pares // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59867	102908	7	2012-08-29 00:00:00	22		Paciente Operado el 24/08/2012	\N			2012-08-24	0
59869	102910	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  30/08/2012	\N			2012-08-30	0
59870	102911	7	2012-08-24 00:00:00	1		24/08/2012/ Paciente registra citación en el Gis para el 31/08/2012	2012-08-31			2012-08-31	0
59871	102912	7	2012-08-24 00:00:00	1		24/08/2012/ Paciente registra citación en el Gis para el 31/08/2012	2012-08-31			2012-08-31	0
59861	102902	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59860	102901	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59853	102894	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59836	102877	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59846	102887	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59863	102904	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 03/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
59855	102896	7	2012-08-29 00:00:00	29	F	Paciente no requiere Etapificacion; Mañana Enviaran Excepcion de Garantia	\N		Excepcion	2012-08-27	0
59838	102879	7	2012-08-27 00:00:00	29	F	operado el 27/08/2012	\N		FAP	2012-08-27	0
59851	102892	7	2012-08-17 00:00:00	29	F	operado el 23/08/2012	\N		FAP	2012-08-23	0
60982	104028	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60983	104029	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
59834	102875	7	2012-08-29 00:00:00	11	I	Paciente NSP el 27/08/2012	\N		Patologia mamaria	2012-08-30	0
59842	102883	7	2012-08-29 00:00:00	11	I	Paciente en llamado 2615268 no contesta llamado 29/08/2012 // Paciente en llamado 90077230 que óptica le entregará los lentes el 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59844	102885	7	2012-08-29 00:00:00	11	I	Paciente en llamado 2862399 no responde llamado 29/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59845	102886	7	2012-08-29 00:00:00	11	I	Paciente en llamado 28338652 no responde llamado 29/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59852	102893	7	2012-08-29 00:00:00	11	I	Paciente en llamado 88522146 no responde llamado 29/08/2012 //Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59857	102898	7	2012-08-29 00:00:00	11	I	Se revisa GIS e indica que paciente NSP el 24/08/2012	\N		Cardiología	2012-08-30	0
59858	102899	7	2012-08-29 00:00:00	11	I	Paciente no responde 81917757 29/08/2012 // Paciente en llamado 2612681 no contesta llamado 27/08/2012 // Optica Reflejo Lentes Probados 03/08/2012 con fecha de Retiro 24/08/2012 cantidad 2	\N			2012-08-30	0
59864	102905	7	2012-08-29 00:00:00	11	I	Paciente en llamado 92177693 no responde llamado 29/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59865	102906	7	2012-08-29 00:00:00	11	I	Paciente en llamado 94403726 no responde llamado 29/08/2012 //Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59862	102903	7	2012-08-29 00:00:00	17	M	27/08/2012 se pide ficha por 3 vez para tramite de excepcion por estar ya en tto con lupron	\N			2012-08-30	0
59835	102876	7	2012-08-29 00:00:00	45	G	Paciente asiste el 28/08/2012; Aun no entregan Documento  a Secretaria GES	\N		IPD	2012-08-28	0
59892	102933	7	2012-08-16 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 1	\N			2012-08-31	0
59873	102914	7	2012-08-29 00:00:00	22		29/08/2012 Pac con ipd confirmación	2012-08-30			2012-08-28	0
59874	102915	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-30			2012-08-28	0
59876	102917	7	2012-08-29 00:00:00	22		Paciente en llamado 78608797 retiro 2 pares de lentes 28/08/2012 // Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-28	0
59877	102918	7	2012-08-22 00:00:00	1		22/08/2012/ Paciente registra hora en el Gis para el 31/08/2012	2012-08-31			2012-08-31	0
59895	102936	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59879	102920	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59880	102921	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59881	102922	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59883	102924	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59884	102925	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59885	102926	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  27/08/2012	\N			2012-08-27	0
59886	102927	7	2012-08-29 00:00:00	1		22/08/2012/ Se consulta a matrona e informa que tiene hora para el 30/08/2012	2012-08-30			2012-08-30	0
59899	102940	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59888	102929	7	2012-08-29 00:00:00	22		29/08/2012 pac con ipd descarte	2012-08-30			2012-08-28	0
59889	102930	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-30			2012-08-28	0
59890	102931	7	2012-08-29 00:00:00	22		29/08/2012 pac con ipd descarte	2012-08-30			2012-08-28	0
59891	102932	7	2012-08-29 00:00:00	22		admision 14/08/2012: Citado para el  24/08/2012 DRA BANDA	\N			2012-08-24	0
59900	102941	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59894	102935	7	2012-08-29 00:00:00	22		admision 20/08/2012: Citado para el  29/08/2012 dra. Natalia guzman	\N			2012-08-29	0
59896	102937	7	2012-08-28 00:00:00	22		Optica Reflejo Lentes Probados 06/08/2012 con fecha de Retiro 28/08/2012 cantidad 2	\N			2012-08-28	0
59901	102942	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59898	102939	7	2012-08-29 00:00:00	22		admision 20/08/2012: Citado para el  27/08/2012 vega	\N			2012-08-27	0
59903	102944	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59902	102943	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 06/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59905	102946	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  03/09/2012	\N			2012-09-03	0
59906	102947	7	2012-08-27 00:00:00	1		27/08/2012/ Paciente registra hora en el Gis para el 04/09/2012	2012-09-04			2012-09-04	0
59907	102948	7	2012-08-29 00:00:00	22		29/08/2012 falta parametrizacion en sigges	2012-08-30			2012-08-22	0
59908	102949	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 31/08/2012 a especialista .	\N			2012-08-31	0
59909	102950	7	2012-08-23 00:00:00	1		23/08/2012 pac nsp el 22/08/2012 con dra Carcamo, tiene nueva hora para el 04/09/2012 con dra Ferisse llama a pac al fono 2721843 a las 15:10 y no corresponde y el fono 77918658 no contesta	\N			2012-09-04	0
59910	102951	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  31/08/2012 DRA CARCAMO	2012-08-30			2012-08-31	0
59911	102952	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 31/08/2012 a especialista .	\N			2012-08-31	0
59912	102953	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59915	102956	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  03/09/2012	\N		Oftalmología	2012-09-03	0
59971	103012	7	2012-08-13 00:00:00	1		citado para PED.RESPIRATORIO INF.-HDGF	\N			2012-08-31	0
59887	102928	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N			\N	0
59897	102938	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Operado el 09/08/2012 falta segundo ojo	\N		Segundo ojo	2012-08-09	0
59904	102945	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-07-17	0
59921	102962	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59917	102958	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59920	102961	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59918	102959	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59916	102957	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59919	102960	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59882	102923	7	2012-08-17 00:00:00	29	F	hospitalizacion 28/8/2012: Operado el 28/08/2012 segundo ojo	\N		FAP	2012-08-28	0
59914	102955	7	2012-08-29 00:00:00	29	F	Paciente Descartada el 29/08/2012, Mañana enviaran IPD de descarte	\N		IPD	2012-08-29	0
60986	104032	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
61001	104047	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
61005	104051	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
59913	102954	7	2012-08-29 00:00:00	17	M	29/08/2012/ Se requiere ficha para ver que indicación dio Dra. Díaz el 06/08/2012. Informan de archivo que la ficha no esta, la solicitaron para reunión oncológica del 24/08/2012	\N			2012-08-30	0
59925	102966	7	2012-08-29 00:00:00	22		Paciente Asintomatica, Seguimiento 29/08/2012	\N			2012-08-29	0
59930	102971	7	2012-08-01 00:00:00	1		(01/08/2012) Paciente citado para el 03/09/2012 .	\N			2012-09-03	0
59934	102975	7	2012-08-29 00:00:00	22		PSIQUIATRIA DEPRESION-HDGF	\N			2012-08-27	0
59935	102976	7	2012-08-29 00:00:00	1		29/08/2012/ Paciente registra citación en el Gis para el 03/09/2012	2012-09-03			2012-09-03	0
59949	102990	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59950	102991	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 04/09/2012 a especialista .	\N			2012-09-04	0
59951	102992	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 04/09/2012 a especialista .	\N			2012-09-04	0
59952	102993	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 31/08/2012 a especialista .	\N			2012-08-31	0
59953	102994	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59954	102995	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59955	102996	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-29	0
59956	102997	7	2012-08-29 00:00:00	1		CITADO EL 29/08/2012 A PED.RESPIRATORIO INF.-HDGF	\N			2012-08-29	0
59957	102998	7	2012-08-24 00:00:00	1		24/08/2012 pac citada con dr Llewenlyn el 06/09/2012	2012-09-07			2012-09-06	0
59958	102999	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  29/08/2012 endoscopia	2012-08-25		ECO	2012-08-29	0
59931	102972	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 20/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
59939	102980	7	2012-08-21 00:00:00	18	D	Optica Reflejo Lentes Probados 21/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
59965	103006	7	2012-08-14 00:00:00	1		citado para TRAUMA.DISPLASIA INF.-HDGF	2012-08-30			2012-09-04	0
59966	103007	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 04/09/2012 a especialista .	\N			2012-09-04	0
59967	103008	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 31/08/2012 a especialista .	\N			2012-08-31	0
59968	103009	7	2012-08-10 00:00:00	1		CITADO EL 31/08/2012 A TRAUMA.DISPLASIA INF.-HDGF	\N			2012-08-31	0
59969	103010	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente citado el 31/08/2012 a especialista .	\N			2012-08-31	0
59970	103011	7	2012-08-14 00:00:00	1		citado para PED.RESPIRATORIO INF.-HDGF	\N			2012-08-31	0
59938	102979	7	2012-08-17 00:00:00	18	D	Paciente llamado 17/08/2012 afirma que entregó receta el 08/08/2012 con fecha de entrega de 2 pares de lentes el 05/09/2012	\N			2012-09-05	0
59933	102974	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59926	102967	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59924	102965	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59923	102964	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59929	102970	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59928	102969	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59927	102968	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59932	102973	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59922	102963	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 07/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59948	102989	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59947	102988	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59946	102987	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59945	102986	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59944	102985	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59942	102983	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59941	102982	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59940	102981	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  09/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59943	102984	7	2012-08-17 00:00:00	18	D	Optica Reflejo: pac entregó receta de lentes  08/08/2012 con fecha de entrega de lentes 31/08/2012  de 2 pares.	\N			2012-08-31	0
59937	102978	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Retiro 31/08/2012 cantidad 2 //Paciente llamado 17/08/2012 no contesta teléfono 2971735 - 2939230	\N			2012-08-31	0
59962	103003	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 08/08/2012 con fecha de Entrega 31/08/2012 cantidad 1	\N			2012-08-31	0
59960	103001	7	2012-08-10 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59961	103002	7	2012-08-29 00:00:00	29	F	operado el 24/08/2012	\N		FAP	2012-08-24	0
59936	102977	7	2012-08-29 00:00:00	29	F	(29/08/2012) Se llama a mama de paciente e indica que esta usando correas , documento aun sin digitar en sigges .	2012-09-05		Nomina	2012-08-22	0
59972	103013	7	2012-08-13 00:00:00	1		citado para PED.RESPIRATORIO INF.-HDGF	\N			2012-08-31	0
59973	103014	7	2012-08-20 00:00:00	1		20/08/2012/ Paciente citada en Gis para el 31/08/2012	2012-08-31			2012-08-31	0
59990	103031	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 14/08/2012 con fecha de Entrega 07/09/2012 cantidad 1	\N			2012-09-07	0
59975	103016	7	2012-08-29 00:00:00	1		ADMISION 28/08/2012:  citado 24/08/2012  ECO 06/09 LLEWELYN	2012-09-08		eco	2012-09-06	0
60007	103048	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
59979	103020	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N		Oftalmología	2012-08-30	0
59980	103021	7	2012-08-29 00:00:00	1		admision 20/08/2012:  30/08/2012 ZUÑIGA	\N		Patologia mamaria	2012-08-30	0
59981	103022	7	2012-08-29 00:00:00	22		admision 20/08/2012: Citado para el  27/08/2012 p. muñoz	\N			2012-08-27	0
59982	103023	7	2012-08-29 00:00:00	1		admision 20/08/2012: Citado para el  29/08/2012 natalia guzman	\N			2012-08-29	0
60000	103041	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
59984	103025	7	2012-08-29 00:00:00	1		Paciente se toma Bp el 28/08/2012, Citado a Control con Bp el 06/09/2012	\N		Patologia mamaria	2012-09-06	0
59985	103026	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-25			2012-08-23	0
59987	103028	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
59989	103030	7	2012-08-10 00:00:00	1		(10/08/2012) Paciente citado el 05/09/2012 .	2012-09-07			2012-09-05	0
59993	103034	7	2012-08-29 00:00:00	1		admision 20/08/2012:  06/08/2012 ZUÑIGA / 30/08/2012 ZUÑIGA	\N		Patologia mamaria	2012-08-30	0
59994	103035	7	2012-08-03 00:00:00	1		(03/08/2012) Paciente citado el 04/09/2012 consulta especialista .	\N			2012-09-04	0
59995	103036	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
59996	103037	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
59997	103038	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  05/09/2012	2012-08-25			2012-09-05	0
59988	103029	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
59992	103033	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Entrega 31/08/2012 cantidad 2	\N			2012-08-31	0
60009	103050	7	2012-08-27 00:00:00	1		Citada a Mx 24/08/2012, y control con Mx el 31/08/2012	\N		Patologia mamaria	2012-08-31	0
59991	103032	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 09/08/2012 con fecha de Entrega 31/08/2012 cantidad 1	\N			2012-08-31	0
60015	103056	7	2012-08-16 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-09-02			2012-08-30	0
60016	103057	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60017	103058	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60018	103059	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60019	103060	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60020	103061	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60012	103053	7	2012-08-29 00:00:00	7	A	admision 29/08/2012: PENDIENTE	\N			\N	0
59974	103015	7	2012-07-06 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Pendiente CI + Ingreso médico	2012-08-20		colecistectomía por video laparoscopia	\N	0
59983	103024	7	2012-08-29 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 30/08/2012 20/8/2012 T.ESFUERZO	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60001	103042	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60002	103043	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60003	103044	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60004	103045	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60005	103046	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60006	103047	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
60008	103049	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 10/08/2012 con fecha de Retiro 31/08/2012 cantidad 2	\N			2012-08-31	0
59977	103018	7	2012-08-29 00:00:00	29	F	Paciente no requiere Etapificacion;Documento aun no entregado a Secretaria GES	\N		Excepcion	2012-08-28	0
60014	103055	7	2012-08-29 00:00:00	29	F	29/08/2012/ Paciente con prestación otorgada el 28/08/2012. Llega IPD a sec. Ges el 29/08, todavía no digitado en SIGGES	\N		ipd	2012-08-28	0
61013	104059	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
61014	104060	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
61015	104061	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
59976	103017	7	2012-08-29 00:00:00	17	M	29/08/2012 ficha pedida por 4 vez para gestionar documento de dr Garate, prestacion en gis no en sigges	2012-08-10			2012-08-30	0
60010	103051	7	2012-08-29 00:00:00	17	M	Se llama a paciente para corroborar asistencia  a los Nº 66349365 - 81225313 - 73696707 y no contesta. Citado nuevamente para el 26/09/2012; se necesita ficha para corroborar asistencia y diagnostico	\N			2012-08-30	0
60011	103052	7	2012-08-29 00:00:00	17	M	29/08/2012- I.C no registra en modulo auge, (Ficha no llego)	\N			2012-08-30	0
59986	103027	7	2012-08-29 00:00:00	46	F	29/08/2012 pac indica haber asistido a control el dia 27/08/2012 prestacion no registrada en gis ni en sigges en espera nominas para su digitacion en sigges	\N			2012-08-27	0
60013	103054	7	2012-08-29 00:00:00	45	G	Paciente confirmada el 24/08/2012, Aun no entregan Documento a Secretaria GES	\N		IPD	2012-08-24	0
60021	103062	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N			2012-08-30	0
60070	103111	7	2012-08-23 00:00:00	18	D	Optica Reflejos Lentes Probados 23/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60049	103090	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 13/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60027	103068	7	2012-08-29 00:00:00	1		citado para PED.RESPIRATORIO INF.-HDGF	\N			2012-08-29	0
60028	103069	7	2012-08-29 00:00:00	1		29/08/2012/ Paciente citada para el 04/09/12 con bp del 27/08, según informa matrona	2012-09-04			2012-09-04	0
60025	103066	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 13/08/2012 con fecha de Entrega 07/09/2012 cantidad 1	\N			2012-09-07	0
60030	103071	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-08-25			2012-08-30	0
60031	103072	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-25			2012-08-23	0
60032	103073	7	2012-08-29 00:00:00	22		ADMISION 28/08/2012:  Atendido el  06/08/2012 DRA ACEVEDO	\N			2012-08-06	0
60033	103074	7	2012-08-29 00:00:00	1		citado para PSIQUIATRIA DEPRESION-HDGF	\N			2012-08-27	0
60034	103075	7	2012-08-29 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	\N		Oftalmologia	2012-08-30	0
60026	103067	7	2012-08-14 00:00:00	18	D	Optica Reflejo Lentes Probados 13/08/2012 con fecha de Entrega 07/09/2012 cantidad 1	\N			2012-09-07	0
60036	103077	7	2012-07-12 00:00:00	1		Paciente citada a seg para el 10/09/2012	\N			2012-09-10	0
60037	103078	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-08-25			2012-08-30	0
60048	103089	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 13/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60050	103091	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 13/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60040	103081	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60041	103082	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60042	103083	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60071	103112	7	2012-08-22 00:00:00	18	D	Optica Reflejo Lentes Probados 14/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60044	103085	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60065	103106	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 14/08/2012 con fecha de Retiro 07/09/2012 cantidad 1	\N			2012-09-07	0
60069	103110	7	2012-08-22 00:00:00	18	D	Optica Reflejo Lentes Probados 14/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60047	103088	7	2012-06-19 00:00:00	1		admision 19/06/2012  Citado para el  06/09/2012 DRA. VENEZIAN	\N			2012-09-06	0
60051	103092	7	2012-08-21 00:00:00	1		(21/08/2012) Paciente citado el 04/09/2012 a especialista .	2012-09-06			2012-09-04	0
60053	103094	7	2012-08-23 00:00:00	1		TRAUMA.DISPLASIA INF.-HDGF	2012-08-30			2012-09-07	0
60054	103095	7	2012-08-16 00:00:00	1		(16/08/2012) Paciente citado el 04/09/2012	\N			2012-09-04	0
60055	103096	7	2012-08-29 00:00:00	1		admision 20/08/2012: Citado para el  29/08/2012 dra. Mellard	\N			2012-08-29	0
60056	103097	7	2012-08-29 00:00:00	1		admision 20/08/2012: Citado para el  29/08/2012 dra. Mellard	\N			2012-08-29	0
60023	103064	7	2012-08-17 00:00:00	29	F	operado el 27/08/2012	\N		FAP	2012-08-27	0
60059	103100	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  05/09/2012 EDA  DR CHAVEZ	2012-08-30		eda	2012-09-05	0
60060	103101	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-09-01			2012-08-30	0
60061	103102	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  31/08/2012	\N		Oftalmología	2012-08-31	0
60063	103104	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-09-01			2012-08-30	0
60064	103105	7	2012-08-29 00:00:00	1		citado para 23/08/2012 OFT.F O AD-HDGF	\N			2012-08-30	0
60066	103107	7	2012-08-21 00:00:00	1		PSIQUIATRIA DEPRESION-HDGF	\N			2012-08-31	0
60067	103108	7	2012-08-21 00:00:00	1		citado para TRAUMA.DISPLASIA INF.-HDGF	2012-08-25			2012-09-11	0
60068	103109	7	2012-08-22 00:00:00	1		TRAUMA.DISPLASIA INF.-HDGF	2012-08-30			2012-09-06	0
61021	104067	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60029	103070	7	2012-08-23 00:00:00	1	B	23/08/2012/ Citada para control con bp para el 13/09/2012	2012-09-13			2012-09-13	0
60024	103065	7	2012-08-29 00:00:00	7	A	admision 29/08/2012: NO HAY HORAS DISPONIBLE	\N			\N	0
60039	103080	7	2012-08-29 00:00:00	10	J	se habla con jefe UGAC y refiere que paciente esta en control con Dr Dinamarca Garantia no se exceptuara	\N			\N	0
60046	103087	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 16/08/2012 12/09/2012 od	\N		Segundo ojo	2012-09-12	0
60022	103063	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: EN TABLA OI  15-08-2012 OD 26/07/12	\N		Segundo ojo	2012-08-15	0
60045	103086	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60035	103076	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60038	103079	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-07-20	0
60043	103084	7	2012-08-13 00:00:00	14	AB	Hospitalizacion 11/07/2012: En Tabla el 31/08/2012	\N		acceso vascular simple (mediante fav) para hemodiálisis	2012-08-31	0
61080	104126	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60073	103114	7	2012-08-29 00:00:00	46	F	29/08/2012/ Se consulta a matrona e informa que todavía esta pendiente la etapificación, falta resultados de exámenes	\N		Etapificación	2012-08-29	0
60052	103093	7	2012-08-29 00:00:00	30	G	admision 29/08/2012: SE CONTRANSFIERE	\N		Centro de Salud Familiar Reñaca Alto Dr. Jorge Kaplan	\N	0
60062	103103	7	2012-08-29 00:00:00	45	G	29/08/2012/ Se llama a paciente e informa que vino a hora del 29/08, que Dr. Le comunica implante de marcapaso. ALVARO VARGAS CUEVAS	\N		IPD	2012-08-29	0
60074	103115	7	2012-08-29 00:00:00	1		29/08/2012/ Se llama al 2932154, se habla con la mamá e informa que la citaron para el 06/09/12 con Dr. Benavides, para repetir examen de sangre. Hoy 29/08 no se lo pueden realizar	2012-09-06			2012-09-06	0
60075	103116	7	2012-08-29 00:00:00	22		29/08/2012/ Prestación otorgada el 23/08/2012	\N			2012-08-23	0
60076	103117	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-09-01			2012-08-30	0
60077	103118	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-15			2012-08-27	0
60082	103123	7	2012-08-24 00:00:00	18	D	Optica Reflejo Lentes Probados 21/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60079	103120	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  14/09/2012	\N			2012-09-14	0
60081	103122	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  05/09/2012	2012-08-30			2012-09-05	0
60083	103124	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  05/09/2012 DRA CARCAMO	\N			2012-09-05	0
60085	103126	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  31/08/2012	\N		BET	2012-08-31	0
60080	103121	7	2012-08-24 00:00:00	29	F	operado el 27/08/2012	\N		FAP	2012-08-27	0
60087	103128	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60088	103129	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  30/08/2012	\N			2012-08-30	0
60089	103130	7	2012-08-16 00:00:00	1		16/08/2012/ Paciente citada para el 04/09/12  con bp del 16/08	2012-09-04			2012-09-04	0
60090	103131	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60091	103132	7	2012-08-20 00:00:00	1		20/08/2012/ Paciente citada en Gis para el 07/09/2012  con bp	2012-09-07			2012-09-07	0
60099	103140	7	2012-08-29 00:00:00	29	F	23/08/2012- Hospitalizacion informa: En Tabla el 24/8//2012/ aun no envian protocolo a estadistica	\N		fap	2012-08-24	0
60095	103136	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  04/09/2012	2012-08-25			2012-09-04	0
60096	103137	7	2012-08-29 00:00:00	22		23/08/2012- Citado el 24/08 a Endo (Se llama a las 15:00Hr a los numeros 68113284-87748536 y no contesta)	\N			2012-08-24	0
60098	103139	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60100	103141	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60101	103142	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  30/08/2012	2012-09-01			2012-08-30	0
60102	103143	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60103	103144	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60104	103145	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60105	103146	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60106	103147	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60107	103148	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60097	103138	7	2012-08-27 00:00:00	17	M	27/08/2012 sin informacion de asistencia, se pide ficha pac no contesta	2012-08-25			2012-08-30	0
60109	103150	7	2012-08-29 00:00:00	22		28/08/2012- Paciente asiste el 21/08/2012 a neumo	\N			2012-08-21	0
60110	103151	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-25			2012-08-23	0
60111	103152	7	2012-07-12 00:00:00	1		21/06/2012 paciente tiene citacion el 06/09/2012 en cir.mama.endocrino	\N			2012-09-06	0
60112	103153	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente citado el 06/09/2012 a especialista . Dr. Vergara	2012-09-09			2012-09-06	0
60113	103154	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60115	103156	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60116	103157	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 07/09/2012  VERGARA	2012-08-30			2012-09-07	0
60117	103158	7	2012-08-23 00:00:00	1		23/08/2012-Paciente citada el 05/09 para Test de esfuerzo (registra en Gis como NSP 22/08/2012)	\N		TEST ESFUERZO	2012-09-05	0
60118	103159	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-25			2012-08-23	0
60119	103160	7	2012-08-10 00:00:00	1		(10/08/2012) Paciente citado el 11/09/2012 .	2012-09-13			2012-09-11	0
60092	103133	7	2012-06-01 00:00:00	13	G	paciente en estudio de Esquizofrenia	\N			\N	0
60084	103125	7	2012-08-29 00:00:00	5	A	admision 29/08/2012: SE LLAMA A PACIENTE AUN NO CITADO	\N		BET	\N	0
60094	103135	7	2012-08-27 00:00:00	45	G	27/08/2012 se gestionará ipd el 28/08/2012 de dr Harire, el dia lunes el no viene al hgf	2012-08-22		IPD	2012-08-22	0
60124	103165	7	2012-06-25 00:00:00	1		citado para 13/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-13	0
60125	103166	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60126	103167	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  04/09/2012	2012-08-25			2012-09-04	0
60127	103168	7	2012-08-17 00:00:00	1		17/08/2012- NSP el 02/08/2012 y 16/08/2012 . Recitado para el 06/09/2012 a MED.NEFRO.ACC.VASCULAR	\N		Nefro.Acc.Vascular	2012-09-06	0
60086	103127	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	2012-08-09			\N	0
60114	103155	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012 OPERADO PRIMER OJO	\N		Segundo ojo	2012-08-25	0
60108	103149	7	2012-08-17 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 13/09/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-13	0
60120	103161	7	2012-06-27 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA  07/09/2012	\N		  Instalación CatéterTransitorio Para Hemodiálisis	2012-09-07	0
60122	103163	7	2012-06-26 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 07/09/2012	\N		acceso vascular simple (mediante fav) para hemodiálisis	2012-09-07	0
60123	103164	7	2012-08-10 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 06/09/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-06	0
60121	103162	7	2012-08-10 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 06/09/2012 PROCESO DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-06	0
60093	103134	7	2012-07-06 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 31/08/2012	2012-08-20		colecistectomía por video laparoscopia	2012-08-31	0
60128	103169	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60129	103170	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60130	103171	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60131	103172	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60132	103173	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60133	103174	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60134	103175	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60136	103177	7	2012-08-29 00:00:00	22		Audifono entregado a paciente dia 28-08-2012	\N			2012-08-20	0
60166	103207	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 16/08/2012 con fecha de Retiro 07/09/2012 cantidad 2	\N			2012-09-07	0
60167	103208	7	2012-08-20 00:00:00	18	D	Optica Reflejo Lentes Probados 16/08/2012 con fecha de Retiro 07/09/2012 cantidad 1	\N			2012-09-07	0
61081	104127	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61082	104128	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60141	103182	7	2012-06-25 00:00:00	1		admision 25/06/2012: Citado para el  06/09/2012 DR. SCHIAPACASSE	\N			2012-09-06	0
60147	103188	7	2012-08-29 00:00:00	17	M	22/08/2012- Citado para el  23/08/2012 DR. SCHIAPACASSE (Se llama para recordar)	\N			2012-08-30	0
60143	103184	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012	\N			2012-09-11	0
60144	103185	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  04/09/2012	\N			2012-09-04	0
60153	103194	7	2012-08-29 00:00:00	17	M	22/08/2012-Citado para el  23/08/2012 DR. SCHIAPACASSE (Se llama para recordar y no contesta)	\N			2012-08-30	0
60148	103189	7	2012-08-29 00:00:00	1		admision 25/06/2012: Citado para el  30/08/2012 DR. SCHIAPACASSE	\N			2012-08-30	0
60149	103190	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente citado el 06/09/2012 a especialista . Dr. Vergara	2012-09-09			2012-09-06	0
60155	103196	7	2012-08-29 00:00:00	17	M	22/08/2012-Citado para el  23/08/2012 DR. SCHIAPACASSE (Se llama para recordar y no contesta)	\N			2012-08-30	0
60135	103176	7	2012-08-29 00:00:00	46	F	29/08/2012 se habla con pac la que iondica haber asistido a control del 27/08/2012 prestacion aun no digitada en gis ni en sigges , en espera de nominas cooroborar la asistencia y su adfecvuada digitacion en sigges	2012-09-05			2012-08-27	0
60157	103198	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  03/09/2012 DRA MUÑOZ	\N			2012-09-03	0
60158	103199	7	2012-08-22 00:00:00	1		CITADO 06/09/2012	2012-09-05			2012-09-06	0
60159	103200	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  06/09/2012 DR FONTAINE	2012-09-05			2012-09-06	0
60160	103201	7	2012-08-20 00:00:00	1		(20/08/2012) Paciente citada para el 07/09/2012 .	2012-09-10			2012-09-07	0
60161	103202	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  04/09/2012 DRA FERIS	2012-09-05			2012-09-04	0
60162	103203	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  12/09/2012 DRA CARCAMO	2012-09-05			2012-09-12	0
60163	103204	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  04/09/2012 DRA FERIS	2012-09-05			2012-09-04	0
60164	103205	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  05/09/2012 DR VALLEJOS	2012-09-05			2012-09-05	0
60165	103206	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  04/09/2012 DRA FERIS	2012-09-05			2012-09-04	0
60168	103209	7	2012-08-29 00:00:00	1		citada para eco el 30/08/2012, y control el 10/09/2012	\N		Eco mamaria	2012-08-30	0
60169	103210	7	2012-08-21 00:00:00	1		citado para TRAUMA.DISPLASIA INF.-HDGF	2012-08-25			2012-09-11	0
60170	103211	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 05/09/2012 DR FERIS	\N			2012-09-05	0
60171	103212	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 05/09/2012 DR FERIS	2012-09-06			2012-09-05	0
60172	103213	7	2012-08-21 00:00:00	1		(21/08/2012) Paciente citado el 05/09/2012 a especialista .	2012-09-07			2012-09-05	0
60173	103214	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  05/09/2012	\N			2012-09-05	0
60174	103215	7	2012-08-27 00:00:00	1		27/08/2012 pac con eda tomada el 24/07/2012 citado al especialista el 16/08/2012 y con nueva citación el 06/09/2012	2012-09-10		IPD	2012-09-06	0
60175	103216	7	2012-08-22 00:00:00	1		(22/08/2012) Paciente con EDA tomada el 16/08/2012 y citado a especialista el 04/09/2012	2012-09-07			2012-09-04	0
60176	103217	7	2012-08-29 00:00:00	1		ADMISION 28/08/2012:  citado 29/08/2012 / ENDOSC. 13/09 HARIRE	2012-09-15		EDA	2012-08-29	0
60177	103218	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 05/09/2012 DR FERIS	2012-08-30			2012-09-05	0
60178	103219	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 11/09/2012 DR FERIS	2012-08-30			2012-09-11	0
60179	103220	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 11/09/2012 DR FERIS	2012-08-30			2012-09-11	0
60146	103187	7	2012-08-01 00:00:00	21	D	hospitalizacion 22/8/2012: En Tabla el 06/09/2012	\N		Segundo ojo	2012-09-06	0
60140	103181	7	2012-08-01 00:00:00	21	D	hospitalizacion 22/8/2012: En Tabla el 06/09/2012	\N		Segundo ojo	2012-09-06	0
60137	103178	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60156	103197	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60152	103193	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 14/08/2012	\N		ojo derecho	2012-08-14	0
60154	103195	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 14/08/2012	\N		ojo derecho	2012-08-14	0
60138	103179	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60142	103183	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-07-20	0
60180	103221	7	2012-08-22 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 31/08/2012	\N		MPD DDD	2012-08-31	0
60139	103180	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para el 30/08/2012	2012-08-31		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60205	103246	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60187	103228	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60184	103225	7	2012-08-23 00:00:00	1		PED.RESPIRATORIO INF.-HDGF	\N			2012-09-03	0
60185	103226	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 11/09/2012 DR FERIS	2012-08-30			2012-09-11	0
60186	103227	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  11/09/2012	2012-08-30			2012-09-11	0
60210	103251	7	2012-08-27 00:00:00	1		27/08/2012/ Paciente registra citación en GIS con bp para el 11/09/2012	2012-09-11			2012-09-11	0
60188	103229	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60213	103254	7	2012-08-29 00:00:00	22		Paciente operada el 27/08/2012	\N			\N	0
60214	103255	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado a especialista el 11/09/2012 .	2012-09-13			2012-09-11	0
60190	103231	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60219	103260	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  05/09/2012  EDA	2012-09-15		EDA	2012-09-05	0
60220	103261	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  11/09/2012	\N			2012-09-11	0
60222	103263	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  14/09/2012	\N			2012-09-14	0
60224	103265	7	2012-08-23 00:00:00	1		Citada para el 05/09/2012 a Patologia Mamaria	\N		Patologia mamaria	2012-09-05	0
60225	103266	7	2012-08-23 00:00:00	1		(23/08/2012) Paciente citado el 31/08/2012 a eda y especialista el 13/09/2012	2012-09-02			2012-08-31	0
60191	103232	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60192	103233	7	2012-08-24 00:00:00	18	D	Optica Reflejo Lentes Probados 24/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60231	103272	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente  citado el 10/09/2012 a especialista . Dr. Fernandez .	2012-09-12			2012-09-10	0
60215	103256	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60181	103222	7	2012-08-22 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 07/09/2012	\N		MPD	2012-09-07	0
60182	103223	7	2012-08-22 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 05/09/2012	\N		MPD DDDR	2012-09-05	0
60194	103235	7	2012-08-23 00:00:00	18	D	Optica Reflejos Lentes Probados 23/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60199	103240	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60200	103241	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60201	103242	7	2012-08-24 00:00:00	18	D	Optica Reflejo Lentes Probados 24/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60202	103243	7	2012-08-23 00:00:00	18	D	Optica Reflejos Lentes Probados 23/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60203	103244	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60204	103245	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 14/09/2012.	\N			2012-09-14	0
60207	103248	7	2012-08-24 00:00:00	18	D	Optica Reflejo Lentes Probados 24/08/2012 con fecha de Entrega 14/09/2012 cantidad 2	\N			2012-09-14	0
60209	103250	7	2012-08-23 00:00:00	18	D	Paciente en llamado 2862812 afirma que se les entregarán el dia 13 de septiembre los lentes, 2 pares.	\N			2012-09-13	0
60189	103230	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60193	103234	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60206	103247	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60195	103236	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60196	103237	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60197	103238	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60198	103239	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60208	103249	7	2012-08-23 00:00:00	18	D	Según óptica reflejos se entregarán: 2 pares de lentes el 07/09/2012.	\N			2012-09-07	0
60230	103271	7	2012-08-29 00:00:00	18	D	Paciente en llamado al fono 76241259, confirma que entregará la receta de lentes el 03/09/2012.	\N			2012-09-03	0
60229	103270	7	2012-08-29 00:00:00	18	D	Paciente en llamado al fono 2721438 confirma que NO a entregado la receta de lentes, se presentará el 30/08/2012.	\N			2012-08-30	0
60221	103262	7	2012-08-27 00:00:00	29	F	(27/08/2012) Paciente con parches entregados , documento sin digitar .	2012-08-30		fap	2012-08-22	0
60183	103224	7	2012-08-22 00:00:00	29	F	operado el 20/08/2012 . FAP NO ESTA COMPLETO	\N		fap	2012-08-20	0
61087	104133	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61099	104145	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61107	104153	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60216	103257	7	2012-08-29 00:00:00	6	U	Paciente no responde al fono 2495310  del dia 29/08/2012	\N			2012-08-29	0
60217	103258	7	2012-08-29 00:00:00	6	U	Paciente no responde al fono 2775465  del dia 29/08/2012	\N			2012-08-29	0
60223	103264	7	2012-08-24 00:00:00	3	A	Se llama a Anatomia e indican que paciente se toma Bp el  23/08/2012, Que se debe esperar para resultado de Examen	\N		IPD	\N	0
60226	103267	7	2012-08-29 00:00:00	3	A	Paciente se toma 24/08/2012; Falta control con Examenes	\N			\N	0
60212	103253	7	2012-08-29 00:00:00	15	G	29/08/2012- Pacientes se derivan confirmados de APS, y se atienden en el hospital por atencion con especialista.	\N			2012-08-29	0
60218	103259	7	2012-08-29 00:00:00	19	D	Paciente en llamado al fono 2335060 responde la sobrina; no sabe la fecha de entrega de los lentes, solo sabe que son 2 pares.	\N			2012-08-29	0
60232	103273	7	2012-08-24 00:00:00	1		Citada a Control con MX	\N		Patologia mamaria	2012-09-07	0
60248	103289	7	2012-08-29 00:00:00	29	F	29/08/2012 pac con panfoto realizada el 23/08/2012 ,las nominas de estadisticas llegan cada 15 dias para las digitacion de esta prestacion	\N		FAP	2012-08-23	0
60235	103276	7	2012-08-29 00:00:00	1		23/08/2012- Citado para el  22/08/2012 DRA TORREGROSA (Seguimiento en Psiquiatria,,)(Se solicitara informacion a trasplante renal, posterior a la evaluacion con Psiquiatra)	\N		MED.CARDIO.ELECTRO	2012-08-22	0
60237	103278	7	2012-08-21 00:00:00	1		admision 20/08/2012:  04/09/2012 PINOCHET	\N		Oftalmología	2012-09-04	0
60238	103279	7	2012-08-13 00:00:00	1		ADMISION 20/08/2012 Citado para el  06/09/2012	\N		Oftalmología	2012-09-06	0
60239	103280	7	2012-08-14 00:00:00	1		14/08/2012/ Paciente citado para el 04/09 con bp del 14/08	2012-09-04			2012-09-04	0
60240	103281	7	2012-08-29 00:00:00	1		admision 30/07/2012: Citado para el  14/08/2012 ECO  / 30/08/2012 DR. HARIRE, se llama a pac para recordar hora de eco y no contesta	2012-09-02			2012-08-30	0
59813	102854	7	2012-08-29 00:00:00	11	I	Paciente en llamado 90077230 que retirará lentes  el 03/09/2012 // Paciente en llamado 90077230 que óptica le entregará los lentes el 28/08/2012 // Optica Reflejo Lentes Probados 02/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
60242	103283	7	2012-08-13 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-09-15			2012-09-13	0
60243	103284	7	2012-08-29 00:00:00	1		11/07/2012- Paciente citado el 27/08/2012 a MED.TEST ESFUERZO AD	\N		MED.TEST ESFUERZO AD	2012-08-27	0
60244	103285	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60245	103286	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012	\N			2012-09-11	0
60246	103287	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  14/09/2012 DRA AVILA	\N			2012-09-14	0
60247	103288	7	2012-08-29 00:00:00	1		admision 20/08/2012:  30/08/2012 L.CONCHA	\N		Endocrinología	2012-08-30	0
60249	103290	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60258	103299	7	2012-08-29 00:00:00	17	M	29/08/2012 se pide ficha por 3º vez para gestionar documento ipd de dr Pinochet, pac asiste a citacion el 21/08/2012 documento no confeccionado	2012-08-25			2012-08-30	0
60251	103292	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-13	0
60253	103294	7	2012-08-29 00:00:00	1		29/08/2012/ Se llama a paciente e informa que vino a hora del 28/08, le hacen bp y le citan para el 13/09/2012	2012-09-13			2012-09-13	0
60254	103295	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60255	103296	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60233	103274	7	2012-08-24 00:00:00	3	A	Paciente se toma Mx el 24/08/2012, Falta control con Examenes	\N		Patologia mamaria	\N	0
60257	103298	7	2012-08-17 00:00:00	1		17/08/2012- NSP el 02/08 y  16/08/2012. Recitado el 06/09/2012 a MED.NEFRO.ACC.VASCULAR	\N		Nefro.Acc.Vascular	2012-09-06	0
60260	103301	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60261	103302	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-09-05			2012-09-13	0
60283	103324	7	2012-08-29 00:00:00	45	G	(29/08/2012) Paciente asiste a citacion del 23/08/2012 , documento sin recepcionar en ges	2012-09-05		IPD	2012-08-23	0
60263	103304	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  13/09/2012 DR FERNANDEZ	\N			2012-09-13	0
60264	103305	7	2012-07-11 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60266	103307	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60267	103308	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60268	103309	7	2012-08-29 00:00:00	1		MED.NEUMOLOGIA-HDGF	\N			2012-08-30	0
60269	103310	7	2012-08-29 00:00:00	1		Citada para el 29/08/2012 a Mx  y el 31/08/2012 a control	\N		Mx	2012-08-31	0
60270	103311	7	2012-08-27 00:00:00	1		Citada para el 07/09/2012 a control con examenes	\N		Patologia mamaria	2012-09-07	0
60273	103314	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  07/09/2012	2012-09-15			2012-09-07	0
60274	103315	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  07/09/2012	2012-09-15			2012-09-07	0
60275	103316	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  07/09/2012	2012-09-15			2012-09-07	0
60276	103317	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  07/09/2012	2012-09-15			2012-09-07	0
60277	103318	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  06/09/2012	2012-09-15			2012-09-06	0
60282	103323	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado a eda el 05/09/2012 y especialista el 25/09/2012	2012-09-07			2012-09-05	0
60278	103319	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
60281	103322	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60284	103325	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60271	103312	7	2012-08-27 00:00:00	10	J	(27/08/2012) Paciente a la espera de IQ , con orden de hospitalizacion	2012-09-15		GASTRECTOMIA TOTAL	\N	0
60259	103300	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60250	103291	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo izquierdo	2012-09-05	0
60241	103282	7	2012-08-27 00:00:00	21	D	Enfermera informa que enviará muestra de paciente a prestador el día 29-08-12	\N		UNIVERSIDAD DE CHILE	2012-08-29	0
60262	103303	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60252	103293	7	2012-07-20 00:00:00	14	AB	hospitalizacion 01/8/2012: En Tabla el 14/09/2012	\N		se deja con ejercicios	2012-09-14	0
60265	103306	7	2012-08-22 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 30/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-08-30	0
60256	103297	7	2012-08-17 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 30/08/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60294	103335	7	2012-08-29 00:00:00	18	D	Paciente en llamado al fono 2121266, confirma haber entregado la receta de lentes, con entrega de 2 pares de lentes el 25/09/2012	\N			2012-09-25	0
60302	103344	7	2012-08-29 00:00:00	18	D	Paciente en llamado al 2470345 confirma entrega de lentes el 24/09/2012 de 2 pares de lentes.	\N			2012-09-24	0
60306	103348	7	2012-08-29 00:00:00	29	F	29/08/2012 las nominas de estadisticas llegan cada 15 dias para las digitacion de esta prestacion	\N		FAP	2012-08-28	0
60311	103353	7	2012-08-29 00:00:00	29	F	(29/08/2012) Paciente asiste a citacion el   27/08/2012 DR FERNANDEZ	2012-09-10		nomina	2012-08-27	0
60314	103356	7	2012-08-17 00:00:00	29	F	operado el 23/08/2012	\N		FAP	2012-08-23	0
59840	102881	7	2012-08-29 00:00:00	11	I	Paciente en llamado al 96128862 no contesta 29/08/2012 //Paciente en llamado 96128862 27/08/2012 afirma que la óptica le dio fecha de entrega el 28/08/2012 Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
59875	102916	7	2012-08-29 00:00:00	11	I	Paciente en llamado 74157211 no responde llamado 29/08/2012 //Pac afirma entrega de lentes 28/08/2012 //Optica Reflejo Lentes Probados 03/08/2012 con fecha de Entrega 24/08/2012 cantidad 2	\N			2012-08-30	0
60292	103333	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado el 11/09/2012 ma especialista	2012-09-13			2012-09-11	0
60312	103354	7	2012-08-23 00:00:00	17	M	23/08/2012 se pide ficha parta corroborar asistencia de pac a control del dia 20/08/2012 con dr Fernandez	\N			2012-08-30	0
60334	103377	7	2012-08-29 00:00:00	17	M	24/08/2012- Citado para el  23/08/2012  DR. CISTERNAS (Se llama para recordar)   (No registra evento en Programas locales)	\N			2012-08-30	0
60301	103343	7	2012-08-29 00:00:00	6	U	Paciente no responde al fono 2962155 del día 29/08/2012	\N			2012-08-29	0
60303	103345	7	2012-08-29 00:00:00	6	U	Paciente en llamado 88031067 no responde 29/08/2012.	\N			2012-08-29	0
60332	103375	7	2012-08-27 00:00:00	20	D	Correo Enviado de SDA a Abastecimiento para generar OC día 27-08-2012	\N		Audifono Derecho	2012-08-27	0
60304	103346	7	2012-08-24 00:00:00	1		(24/08/2012) Paciente citado a especialista el 24/09/2012	2012-09-27			2012-09-24	0
60305	103347	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  13/09/2012 DR SCHIAPACASE	\N			2012-09-13	0
60307	103349	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  13/09/2012 DR SCHIAPPACASE	\N			2012-09-13	0
60308	103350	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  13/09/2012 DR SCHIAPACASSE	\N			2012-09-13	0
60310	103352	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60313	103355	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60316	103358	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60317	103359	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente citado a especialista el 14/09/2012 , Dr. Pizarro .	2012-09-16			2012-09-14	0
60318	103360	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  30/08/2012	\N		Endocrinología	2012-08-30	0
60324	103366	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60325	103367	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60326	103368	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60327	103369	7	2012-07-04 00:00:00	1		admison  03/07/2012: Citado para el  20/09/2012 DRA VENEZIAN	\N			2012-09-20	0
60333	103376	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  03/09/2012	\N			2012-09-03	0
60286	103327	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
60287	103328	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60288	103329	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60289	103330	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60290	103331	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60291	103332	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60293	103334	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60295	103336	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60298	103340	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60299	103341	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60300	103342	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60329	103371	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N			\N	0
60330	103373	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60331	103374	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60328	103370	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012 OPERADO PRIMER OJO	\N		Segundo ojo	2012-08-25	0
60309	103351	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60335	103378	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60315	103357	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-07-17	0
60322	103364	7	2012-08-13 00:00:00	14	AB	hospitalizacion 11/07/2012: En Tabla el 14/09/2012	\N		acceso vascular simple (mediante fav) para hemodiálisis	2012-09-14	0
60323	103365	7	2012-08-17 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 13/09/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-13	0
60319	103361	7	2012-07-03 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 29/08/2012	2012-09-05		colecistectomía por video laparoscopia	2012-08-29	0
60336	103379	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  03/09/2012 DR TRINCADO	2012-09-15			2012-09-03	0
60337	103381	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado el 04/09/2012 a eda y el 11/09/2012 a especialista .	2012-09-05			2012-09-04	0
60353	103397	7	2012-08-29 00:00:00	29	F	(29/08/2012) Paciente asiste a citacion del 28/08/2012 , se toma panfoto  , sin digitar en gis y sigges .	\N		Nomina	2012-08-28	0
60339	103383	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente  citado el 26/09/2012 a especialista con el Dr. Amestica .	2012-09-30			2012-09-25	0
60378	103422	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60321	103363	7	2012-08-29 00:00:00	11	I	(29/08/2012) Paciente Citado para el  27/08/2012 Dr. Bergh y 09/08/2012 Dr. Harire  , se llama a paciente para consultar e indica que requiere nueva hora pero no pudo asistir por no tener plata .	\N			2012-08-30	0
60342	103386	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60343	103387	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60345	103389	7	2012-08-02 00:00:00	1		02/08/2012- Paciente citado para MED.TEST ESFUERZO el 03/09/2012.	\N		MED.TEST ESFUERZO	2012-09-03	0
60347	103391	7	2012-08-29 00:00:00	1		Citado para el  28/08/2012 DR. PINOCHET	\N		Oftalmología	2012-08-28	0
60348	103392	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  05/09/2012 DRA. TORREGROSA	\N		Psiquiatria	2012-09-05	0
60349	103393	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-09-01			2012-09-13	0
60350	103394	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60351	103395	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60352	103396	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60354	103398	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60355	103399	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60356	103400	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60357	103401	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60358	103402	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60359	103403	7	2012-07-10 00:00:00	1		10/07/2012- Citacion del 20/06/2012 fue para la patologia de retinopatia diabetica, Se debe citar a OFT para catarata (Se solicita hora)// asignan nueva hora para el  13/09/2012	\N			2012-09-13	0
60360	103404	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-13	0
60368	103412	7	2012-08-29 00:00:00	3	A	Paciente operada el 27/08/2012, Falta control con Bp Quirurgica	\N			\N	0
60362	103406	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  06/09/2012 DRA. CONCHA	\N		Endocrinología	2012-09-06	0
60363	103407	7	2012-07-11 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60364	103408	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-20	0
60365	103409	7	2012-07-11 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60367	103411	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado el 30/08/2012 a especialista se llama para rcordar citacion .	2012-09-05			2012-08-30	0
60369	103413	7	2012-08-29 00:00:00	1		29/08/2012/ Citada para el 14/09/12 con bp del 27/08/12	2012-09-14			2012-09-14	0
60344	103388	7	2012-08-29 00:00:00	45	G	(29/08/2012) Se llama a paciente e indica que Dr. Le indico HOSPITALIZACION , esta se encuentra digitada en GIS .	\N		ipd	2012-08-28	0
60371	103415	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  31/08/2012 DRA. CONCHA	\N		Endocrinología	2012-08-31	0
60373	103417	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  06/09/2012	2012-08-20			2012-09-06	0
60374	103418	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  07/09/2012 DRA. CONCHA	\N		Endocrinología	2012-09-07	0
60375	103419	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  06/09/2012	2012-08-20			2012-09-06	0
60376	103420	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  06/09/2012 DRA. CONCHA	\N		Endocrinología	2012-09-06	0
60377	103421	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  03/09/2012 DRA. DIAZ	\N		Endocrinología	2012-09-03	0
60379	103423	7	2012-07-11 00:00:00	1		ADMISION 11/07/2012: Citado para el  31/08/2012 DRA. VENEZIAN	\N		Oftalmología	2012-08-31	0
60380	103424	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60382	103426	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60384	103428	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60385	103429	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60386	103430	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60387	103431	7	2012-08-29 00:00:00	1		Paciente Citado Cintigrama oseo el 05-09-2012 a las  12:00 Hrs	\N		CINTIGRAMA OSEO	2012-09-05	0
60338	103382	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-15			\N	0
60366	103410	7	2012-08-29 00:00:00	7	A	admision 29/08/2012: auditoria	\N			\N	0
60370	103414	7	2012-08-29 00:00:00	7	A	29/08/2012/ Paciente no registra hora en el GIS. IC no registrada en Gis	\N			\N	0
60340	103384	7	2012-08-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Traerá exs. De Hosp. De Quintero. Poli Medico UCAM 04/09/12	2012-09-15		colecistectomía por video laparoscopia	\N	0
60381	103425	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60391	103435	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60346	103390	7	2012-08-27 00:00:00	21	D	Enfermera informa que enviará muestra de paciente a prestador el día 29-08-12	\N		UNIVERSIDAD DE CHILe	2012-08-29	0
60383	103427	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60341	103385	7	2012-08-02 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 21/09/2012	\N		FHD	2012-09-21	0
60361	103405	7	2012-08-17 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 20/09/2012 PROCESO DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-20	0
60403	103447	7	2012-08-29 00:00:00	29	F	29/08/2012/ Paciente exceptuado, porque se da tto. Hormonal antes de bp. Dejan docto. El 29/08 en sec. Auge, aún no digitado en SIGGES	\N		excepción	2012-08-28	0
60372	103416	7	2012-08-29 00:00:00	11	I	29/08/2012 Pac sin numeros de telefonos en programas de hgf se debe enviar correo para saber si necesita realizarse la etapificacion . Pac inubicable	2012-08-30			2012-08-30	0
60394	103438	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente Citado para el  30/08/2012 DR. CISTERNAS , se llama para recordaar citacion .	2012-09-05			2012-08-30	0
60395	103439	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  10/09/2012 DR.RAMIREZ	2012-09-25			2012-09-10	0
60409	103453	7	2012-08-27 00:00:00	20	D	Correo Enviado de SDA a Abastecimiento para generar OC día 27-08-2012	\N		O.Derecho	2012-08-27	0
60412	103456	7	2012-08-29 00:00:00	5	G	Se va a unidad de escanner e informa que esta semana el cupo esta listo, se comprometen a programarlo para el dia 04-09-2012	\N		SCANNER	\N	0
60398	103442	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  06/09/2012	\N		Oftalmología	2012-09-06	0
60399	103443	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  03/09/2012 DR.RAMIREZ	2012-09-25			2012-09-03	0
60401	103445	7	2012-07-11 00:00:00	1		Citado para el  06/09/2012 DR. CISTERNAS	2012-09-01			2012-09-06	0
60404	103448	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente Citado para el  30/08/2012 DR. CISTERNAS , se llama para recordaar citacion .	2012-09-05			2012-08-30	0
60405	103449	7	2012-08-29 00:00:00	1		Citado para el  30/08/2012 DR. CISTERNAS	\N		Oftalmología	2012-08-30	0
60406	103450	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente RECITADO el 30/08/2012 , nsp la citacion del 9/08/2012 dice que se confundio de fecha  , se llama para recordar citacion .	2012-09-05			2012-08-30	0
60407	103451	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-09-20			2012-09-13	0
60410	103454	7	2012-08-08 00:00:00	1		(08/08/2012) Paciente citado para cintigrama el 004/09/2012 se corrobora llamando a med . Nuclear	2012-09-15		cintigrama oseo	2012-09-04	0
60411	103455	7	2012-08-21 00:00:00	1		admision 20/08/2012:  24/09/2012 MAC MILLAN	\N		Urología	2012-09-24	0
60414	103458	7	2012-08-29 00:00:00	1		admision 29/08/2012: CITADO EL DIA  20/09/2012	\N			2012-09-20	0
60415	103459	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente CITADO EL 30/08/2012 A CIR.DIGESTIVA-HDGF	\N			2012-08-30	0
60418	103462	7	2012-08-20 00:00:00	1		20/08/2012- Paciente asiste a control con otorrino y nefro. Esta citado el 04/09 a Tx renal para definir  Examenes pendiente	\N			2012-09-04	0
60420	103464	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  27/09/2012	2012-09-01			2012-09-27	0
60422	103466	7	2012-08-29 00:00:00	1		Citado para el  30/08/2012 DR. CISTERNAS	\N		Oftalmología	2012-08-30	0
60423	103467	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60424	103468	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60425	103469	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60426	103470	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente Citado para el  30/08/2012 .	2012-09-05			2012-08-30	0
60427	103471	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60428	103472	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60429	103473	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60430	103474	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60431	103475	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60432	103476	7	2012-08-29 00:00:00	1		Paciente Citado Cintigrama oseo el 11-09-2012 a las  12:00 Hrs	\N		CINTIGRAMA OSEO	2012-09-11	0
60433	103477	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente Citado para el  30/08/2012 DR. CISTERNAS , se llama para recordaar citacion .	2012-09-05			2012-08-30	0
60434	103478	7	2012-07-11 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60435	103479	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60436	103480	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  20/09/2012	2012-09-20			2012-09-20	0
60437	103481	7	2012-07-11 00:00:00	1		ADMISION 11/07/2012: Citado para el  04/10/2012 DRA, VENEZIAN	\N			2012-10-04	0
60438	103482	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60439	103483	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  04/10/2012 DRA.VENEZIAN	\N			2012-10-04	0
60440	103484	7	2012-07-10 00:00:00	1		citado para 27/09/2012 OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
60498	103542	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-09-15			2012-09-13	0
60393	103437	7	2012-08-20 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  TTO.ITU POR 15 DIAS	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60413	103457	7	2012-08-24 00:00:00	10	E	24/08/2012- Audiometria en poder del monitor	\N		AUDIFONO RETROAURICULAR OIDI IZQUIERDO	\N	0
60400	103444	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60402	103446	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60396	103440	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60397	103441	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60392	103436	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo izquierdo	2012-09-05	0
60419	103463	7	2012-08-10 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 28/09/2012	\N		FHI	2012-09-28	0
60408	103452	7	2012-08-23 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 21/09/2012	\N		cateter tunelizado+ FRI	2012-09-21	0
60417	103461	7	2012-07-12 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 29/08/2012	2012-07-20		colecistectomía por video laparoscopia	2012-08-29	0
60497	103541	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012, OPERADO YA	\N		FAP	2012-08-28	0
60485	103529	7	2012-07-19 00:00:00	29	F	hospitalizacion 28/8/2012: Operado el 27/08/2012	2012-09-15		FAP	2012-08-27	0
60491	103535	7	2012-08-13 00:00:00	29	F	operado el 24/08/2012	\N		FAP	2012-08-24	0
59959	103000	7	2012-08-29 00:00:00	17	M	29/08/2012/ Se habla con matrona UPM e informa que llega IHQ y sale como descarte. Se envía email a Dra. Toro, se requiere ficha para hablar con referente. Se a solicitado ficha en reiteradas oportunidades	\N			2012-08-30	0
60057	103098	7	2012-08-29 00:00:00	17	M	29/08/2012/ Se llama en reiteradas oportunidades para confirmar asistencia del día 28/08 al 87465113 y no contestan. Registra OA en LEC del Gis del 09/08 para bp. Se requiere ficha para ver asistencia e indicaciones.	\N			2012-08-30	0
60448	103492	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  10/09/2012 DR.FERNANDEZ	2012-09-30			2012-09-10	0
60449	103493	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  03/09/2012 DR.RAMIREZ	2012-08-30			2012-09-03	0
60450	103494	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  20/09/2012 Dra Venezian	2012-09-20			2012-09-20	0
60451	103495	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60452	103496	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente Citado para el  30/08/2012 .	2012-09-05			2012-08-30	0
60453	103497	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60454	103498	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60455	103499	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  24/09/2012 DR.RAMIREZ	2012-09-15			2012-09-24	0
60456	103500	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  10/09/2012 DR.FERNANDEZ	2012-09-30			2012-09-10	0
60457	103501	7	2012-07-11 00:00:00	1		citado a OFT.NUEVOS AD-HDGF	\N			2012-10-04	0
60458	103502	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60459	103503	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60145	103186	7	2012-08-29 00:00:00	17	M	22/08/2012- Citado para el  21/08/2012 jadue  (En Programas locales no registra eventos) contactado por call Center. (Telefonos no disponibles se da prioridad a paciente citados para el 23//	\N			2012-08-30	0
60461	103505	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  20/09/2012	2012-09-15			2012-09-20	0
60462	103506	7	2012-08-29 00:00:00	22		24/08/2012- Citado para el  23/08/2012 DR. HARBIN (Se llama para recordar)  (No registra evento en Programas locales)	\N			2012-08-23	0
60463	103507	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-06	0
60464	103508	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  20/09/2012	2012-09-15			2012-09-20	0
60465	103509	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  03/09/2012 DRA. LANZA	\N		Med.Interna	2012-09-03	0
60466	103510	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-27	0
60467	103511	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60468	103512	7	2012-08-29 00:00:00	1		ADMISION 23/07/2012: Citado para el  27/08/2012 DRA. ACEVEDO	\N		Med.Interna	2012-08-27	0
60469	103513	7	2012-08-29 00:00:00	1		admision 20/08/2012: Citado para el  28/08/2012 pinochet	\N		Oftalmología	2012-08-28	0
60470	103514	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60471	103515	7	2012-07-10 00:00:00	1		ADMISION 10/07/2012: Citado para el  03/09/2012 DR.FERNANDEZ	2012-09-25			2012-09-03	0
60446	103490	7	2012-04-18 00:00:00	13	G	18/04/2012- Paciente con controles en Psiquiatria.	\N			\N	0
60473	103517	7	2012-07-11 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-04	0
60474	103518	7	2012-08-29 00:00:00	8	U	(29/08/2012) Paciente NSP , se revisa ficha clinica , se llama y no contesta .	2012-09-10		IPD	2012-08-20	0
60476	103520	7	2012-07-17 00:00:00	1		MED.RES DIAG-HDGF	\N		Med.Interna	2012-09-03	0
60478	103522	7	2012-08-29 00:00:00	1		ADMISION 23/07/2012: Citado para el  27/08/2012 DRA. ACEVEDO	\N		Med.Interna	2012-08-27	0
60481	103525	7	2012-07-25 00:00:00	1		ADMISION 25/07/2012: Citado para el  08/10/2012 DRA. AVILA	2012-06-30			2012-10-08	0
60482	103526	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente citado el 14/09/2012 a cintigrama oseo a las 12:00 HRS .	2012-09-16		cintigrama oseo	2012-09-14	0
60483	103527	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 31/08/2012 citado eco  el dia	2012-09-05		eco	2012-08-31	0
60484	103528	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60488	103532	7	2012-08-24 00:00:00	1		24/08/2012- NSP 22/08/2012. Paciente recitado para el 20/09/2012 con Dra Venezian	\N			2012-09-20	0
60489	103533	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  13/09/2012	2012-08-15			2012-09-13	0
60494	103538	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60495	103539	7	2012-07-24 00:00:00	1		MEDICINA INTERNA-HDGF	\N		Med.Interna	2012-09-05	0
60479	103523	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N			\N	0
60475	103519	7	2012-08-23 00:00:00	10	E	23/08/2012 ficha llega hoy jueves y doctor Trincado viene a hgf los dias lunes, martes ,miercoles se esperara hasta el lunes para gestionar excepcion	\N		ESTRABISMO, TRAT. QUIR. COMPLETO (UNO O AMBOS OJOS)	\N	0
60442	103486	7	2012-08-29 00:00:00	10	J	Paciente aun no operada para saber si requiere Etapificacion	\N		MASTECTOMIA RADICAL O TUMORECTOMIA C/VACIAMIENTO GANGLIONAR O MASTECTOMIA TOTAL C/VACIAMIENTO GANGLIONAR	\N	0
60472	103516	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 18/08/2012 13/09/2012 oi	\N		Segundo ojo	2012-09-13	0
60447	103491	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60477	103521	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60496	103540	7	2012-08-02 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 5/10/2012	\N		FISTULA ARTERIOVENOSA (DE BRESCIA O SIMILAR)	2012-10-05	0
60486	103530	7	2012-08-02 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 05/10/2012	\N		FHD	2012-10-05	0
60480	103524	7	2012-08-21 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 31/08/2012	2012-08-23		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-08-31	0
60499	103543	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60500	103544	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60531	103575	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60503	103547	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60502	103546	7	2012-08-29 00:00:00	11	I	Paciente NSP el 23-08-2012 y NSP el 03-08-2012	\N			2012-08-30	0
60505	103549	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  05/09/2012 DR. VASQUEZ	2012-09-15			2012-09-05	0
60506	103550	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60507	103551	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60508	103552	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60509	103553	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60510	103554	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60511	103555	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  11/10/2012 DRA. VENEZIAN	\N			2012-10-11	0
60416	103460	7	2012-08-24 00:00:00	17	M	24/08/2012 pac si asiste a consulta el 23/08/2012 con dr Llewelyn pero no pudo ser confirmada le faltan examenes, se pide ficha para revisar en el proceso que va	2012-08-25			2012-08-30	0
60513	103557	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente con ECO tomada el  28/08/2012 . Citado  especialista el 11/09/2012	2012-09-15			2012-09-11	0
60514	103558	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 31/08/2012 /citado eco  ACONTROL EL20/09/2012 LLEWELYN/	2012-09-05		eco	2012-08-31	0
60546	103590	7	2012-08-27 00:00:00	17	M	27/08/2012 se pide ficha por 2º vez asistencia no digitada en gis ni en sigges ipd de dr Trincado no ha llegado a secretaria ges, pac no contesta	2012-08-25			2012-08-30	0
60516	103560	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 10/09/2012  TRINCADO	2012-08-30			2012-09-10	0
60548	103592	7	2012-05-02 00:00:00	13	G	02/05/2012- Paciente asiste a control con Psiquiatra, inicia estudio.	\N			\N	0
60544	103588	7	2012-08-27 00:00:00	20	D	Correo Enviado de SDA a Abastecimiento para generar OC día 27-08-2012	\N			2012-08-27	0
60520	103564	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  10/09/2012 DR. RAMIREZ	2012-10-05			2012-09-10	0
60522	103566	7	2012-07-17 00:00:00	1		ADMISION 17/07/2012: Citado para el  05/09/2012 DR.VASQUEZ	2012-10-05			2012-09-05	0
60542	103586	7	2012-08-29 00:00:00	45	G	(29/08/2012) Paciente asiste a citacion ,con indicacion de control en un año mas  PINOCHET	2012-09-15		IPD	2012-08-28	0
60526	103570	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60536	103580	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente  Citado para el  24/09/2012 DR. RAMIREZ	2012-09-30			2012-09-24	0
60538	103582	7	2012-08-08 00:00:00	1		ADMISION 20/08/2012 Citado para el  07/09/2012	\N		Cardiología	2012-09-07	0
60539	103583	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 27/09/2012  VENEZIAN	\N		Oftalmología	2012-09-27	0
60540	103584	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  11/10/2012	\N		Cardiología	2012-10-11	0
60541	103585	7	2012-08-22 00:00:00	1		22/08/2012- Se solicita a Poli de acc. Vascular, Asignacion de hora.  Se asigna hora para el 20/09/2012	\N		MED.NEFRO.ACC.VASCULAR	2012-09-20	0
60545	103589	7	2012-08-21 00:00:00	1		admision 20/08/2012:  03/09/2012 TRINCADO	2012-09-15			2012-09-03	0
60547	103591	7	2012-08-14 00:00:00	1		(14/08/2012) Paciente  CITADO 13/08/2012 DR.TRINCADO asiste a citacion Dr. Indica que no lo puede atender debido a que tiene muchos niños citados , se recita para el 03/09/2012 .	2012-09-15			2012-09-03	0
60549	103593	7	2012-08-07 00:00:00	1		admision 07/08/2012: CITADO 27/09/2012 ACC.VASCULARES	\N			2012-09-27	0
60504	103548	7	2012-08-24 00:00:00	10	J	24/08/2012/ Se revisa Gis pabellón y no aparece operado el paciente. A la espera del tto	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	\N	0
60512	103556	7	2012-08-28 00:00:00	10	A	ADMISION 28/08/2012:	2012-09-15		RETINOPATÍA PROLIFERATIVA, (DIABÉTICA, HIPERTENSIVA, EALES Y OTRAS) PANFOTOCOAGULACIÓN (TRAT. COMPLETO)	\N	0
60534	103578	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012	\N		primer ojo	2012-08-25	0
60527	103571	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012 OPERADO PRIMER OJO	\N		Segundo ojo	2012-08-25	0
60529	103573	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012 OPERADO PRIMER OJO	\N		Segundo ojo	2012-08-25	0
60537	103581	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012	\N		primer ojo	2012-08-25	0
60524	103568	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60533	103577	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60530	103574	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60535	103579	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60528	103572	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60525	103569	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60523	103567	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60521	103565	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60519	103563	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60501	103545	7	2012-08-02 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 12/10/2012	\N		FRI	2012-10-12	0
60532	103576	7	2012-07-23 00:00:00	14	AB	hospitalizacion 01/8/2012: En Tabla el 12/10/2012	\N		FISTULA ARTERIOVENOSA (DE BRESCIA O SIMILAR)	2012-10-12	0
60550	103594	7	2012-08-01 00:00:00	29	F	operado el 27/08/2012	\N		FAP	2012-08-27	0
60552	103596	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente asiste a citacion con Dr. Trincado con indicacion de lentes , documento sin recepcionar en ges .	\N			2012-08-27	0
60576	103622	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60554	103598	7	2012-08-03 00:00:00	1		Citada a Mamografia el dia 24/09/2012  a las 10:00 y control con Dr. Valenzuela el dia 09/11/2012	\N		Mamografia	2012-09-24	0
60555	103599	7	2012-08-07 00:00:00	1		admision 07/08/2012: CITADO 20/09/2012 ACC.VASCULARES	\N		Nefro.Acc.Vascular	2012-09-20	0
60581	103627	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60557	103601	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60558	103602	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60583	103629	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60560	103604	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60584	103630	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
61826	104872	7	2012-08-20 00:00:00	13	G	20/08/2012- Paciente inicia Estudio de EQZ con fecha 16/08	\N			\N	0
61167	104213	7	2012-08-29 00:00:00	8	A	29/08/2012 pac debe ser recitado con dr Macmillan para detgerminar tto a seguir con nuevos exs que doctor le pidio en consulta anterior,pac tenia hora el 20/08/2012 con dr Lira quien lo ve y pone en ficha mal citado y lo citan para el 22/08/2012 y no asis	2012-08-23			2012-08-23	0
60588	103634	7	2012-08-29 00:00:00	5	A	(29/08/2012) Paciente sin citaciones a especialista y eco a la fecha .	2012-09-20		eco	\N	0
60567	103611	7	2012-08-07 00:00:00	1		admision 07/08/2012: CITADO 20/09/2012 ACC.VASCULARES	\N			2012-09-20	0
60568	103612	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60569	103613	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  11/10/2012 DR. SCHIAPPACASSE	\N			2012-10-11	0
60570	103614	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DRA. VENEZIAN	\N			2012-10-18	0
60572	103616	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  31/08/2012 --ECO--20/09/12 BERG	2012-09-10		eco	2012-08-31	0
60573	103619	7	2012-08-14 00:00:00	1		(14/08/2012) Paciente  CITADO 13/08/2012 DR.TRINCADO asiste a citacion Dr. Indica que no lo puede atender debido a que tiene muchos niños citados , se recita para el 03/09/2012 .	2012-09-15			2012-09-03	0
60574	103620	7	2012-07-27 00:00:00	1		MEDICINA INTERNA-HDGF	\N		Med.Interna	2012-09-06	0
60575	103621	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 31/08/2012  PEZO	\N			2012-08-31	0
60577	103623	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente Citado para el  08/10/2012 DRA. AVILA	2012-10-15			2012-10-07	0
60578	103624	7	2012-08-21 00:00:00	1		admision 20/08/2012:  04/09/2012 PINOCHET	\N			2012-09-04	0
60579	103625	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente citado el 30/08/2012  especialista .	2012-09-05			2012-08-30	0
60580	103626	7	2012-08-07 00:00:00	1		admision 07/08/2012: CITADO 20/09/2012 ACC.VASCULARES	\N			2012-09-20	0
60582	103628	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 DR TRINCADO (Se llama para recordar y no contesta)	\N			2012-08-28	0
60585	103631	7	2012-07-27 00:00:00	1		citado 07/09 med int	\N		Med.Interna	2012-09-07	0
60921	103967	7	2012-08-29 00:00:00	45	G	28/08/2012-(Oft. Secre no se encuetra. Informan que no a llegado memo. Sr nuri en curso) asiste  el 09/08 con Dr Pinochet. (Llega ficha 22/08 en la tarde) Registra atencion pero no doc. En poli de OFT. Se encontraban ocupados por lo cual nose pudo gestion	\N		IPD	2012-08-20	0
60587	103633	7	2012-08-27 00:00:00	1		(27/08/2012) Paciente citada a especialista el 13/09/2012	2012-09-15			2012-09-13	0
60589	103635	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 10/09/2012  TRINCADO	\N			2012-09-10	0
60590	103636	7	2012-08-29 00:00:00	22		29/08/2012 falta parametrizacion en sigges	2012-08-30			2012-08-23	0
60591	103637	7	2012-08-29 00:00:00	22		29/08/2012 falta parametrizacion en sigges	2012-08-30			2012-08-23	0
60592	103638	7	2012-07-25 00:00:00	1		ADMISION 25/07/2012: Citado para el  11/10/2012 DR. RAMIREZ	2012-10-05			2012-10-11	0
60593	103639	7	2012-07-25 00:00:00	1		ADMISION 25/07/2012: Citado para el  01/10/2012 DR. RAMIREZ	2012-07-25			2012-10-01	0
60595	103641	7	2012-07-30 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60598	103644	7	2012-07-31 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  NCF 07/08, 24/08 ni 29/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60586	103632	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012	\N		Segundo ojo	2012-08-28	0
60594	103640	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60565	103609	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60571	103615	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60596	103642	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo derecho	2012-08-17	0
60561	103605	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60597	103643	7	2012-08-23 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 19/10/2012	\N		FRD	2012-10-19	0
60564	103608	7	2012-02-28 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 05/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-05	0
60563	103607	7	2012-08-17 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 29/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-08-29	0
60599	103645	7	2012-07-25 00:00:00	1		ADMISION 25/07/2012: Citado para el  22/10/2012 DRA. AVILA	2012-10-05			2012-10-22	0
60644	103690	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60648	103694	7	2012-08-01 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 28/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-28	0
60643	103689	7	2012-08-29 00:00:00	8	U	(29/08/2012)  Paciente nsp , se llama a mama de paciente fono n75158280 indica que no pudo asistir porque no tenian dinero .	2012-08-30			2012-08-27	0
60236	103277	7	2012-08-27 00:00:00	45	G	27/08/2012 se gestionará ipd el 28/08/2012 de dr Harire, el dia lunes el no viene al hgf	2012-08-22		IPD	2012-08-22	0
60611	103657	7	2012-07-30 00:00:00	1		MEDICINA INTERNA-HDGF	\N		Med.Interna	2012-09-06	0
60612	103658	7	2012-08-29 00:00:00	1		admision 29/08/2012: Citado para el  11/09/2012	\N		Oftalmología	2012-09-11	0
60614	103660	7	2012-08-08 00:00:00	1		Citado para el 31/8/2012 MED.CARDIOLOGIA-HDGF	\N		Cardiología	2012-08-31	0
60615	103661	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 10/09/2012	2012-09-15			2012-09-10	0
60616	103662	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60618	103664	7	2012-08-06 00:00:00	1		citado en MED.ENDO.DIABETES-HDGF	\N		Endocrinología	2012-09-20	0
60619	103665	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-11	0
60620	103666	7	2012-08-08 00:00:00	1		Citado para el 8/10/12 MEDICINA INTERNA-HDGF	\N		Med.Interna	2012-10-08	0
60621	103667	7	2012-08-14 00:00:00	1		citado para MED.RES DIAG-HDGF	\N		Med.Inte	2012-09-12	0
60622	103668	7	2012-08-07 00:00:00	1		admision 07/08/2012: CITADO 03/09/2012 DRA-DIAZ	\N		Endocrinología	2012-09-03	0
60623	103669	7	2012-08-29 00:00:00	1		admision 07/08/2012: CITADO 29/08/2012 DR.PIZARRO	\N		Endocrinología	2012-08-29	0
60624	103670	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60625	103671	7	2012-08-07 00:00:00	1		citado en MED.ENDO.DIABETES-HDGF	\N		Endocrinología	2012-09-20	0
60626	103672	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  14/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-14	0
60627	103673	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60628	103674	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  14/09/2012 DR CISTERNAS	\N			2012-09-14	0
60629	103675	7	2012-08-29 00:00:00	1		(29/08/2012) Paciente  Citado para el  30/08/2012	2012-09-05			2012-08-30	0
60630	103676	7	2012-08-02 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Paciente no asiste a exs, conversará con hija. Se llamará prox. Semana para definir	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60633	103679	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60634	103680	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60635	103681	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60636	103682	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60637	103683	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  21/09/2012 DR CISTERNAS	\N			2012-09-21	0
60641	103687	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  18/10/2012 DR. SCHIAPPACASSE	\N			2012-10-18	0
60645	103691	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  21/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-21	0
60646	103692	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 28/09/2012  CONCHA	\N		Endocrinología	2012-09-28	0
60647	103693	7	2012-07-23 00:00:00	1		citado en OFT.NUEVOS AD-HDGF	\N			2012-10-25	0
60799	103845	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  23/10/2012 DR. AMESTICA	2012-09-15			2012-10-23	0
60608	103654	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60610	103656	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60631	103677	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60639	103685	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60605	103651	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60606	103652	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60607	103653	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60609	103655	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60600	103646	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60601	103647	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60602	103648	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo derecho	2012-08-17	0
60604	103650	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60638	103684	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60632	103678	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		ojo izquierdo	2012-08-17	0
60642	103688	7	2012-08-23 00:00:00	21	D	Se envia a Extensión Horaria con fecha 17-08-12 Según Directorio GES 16-08-12	\N		primer ojo	2012-08-17	0
60603	103649	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
60640	103686	7	2012-03-08 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 12/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-12	0
60649	103695	7	2012-08-03 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60690	103736	7	2012-08-23 00:00:00	17	M	23/08/2012 se pide ficha para corroborar asistencia del dia 20/08/2012 con dr Fernandez	2012-08-23			2012-08-30	0
60651	103697	7	2012-08-03 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60679	103725	7	2012-08-29 00:00:00	8	U	(29/08/2012) Paciente no asiste a citacion debido a que se le olvido a la mamà solicita nueva citacion	\N			2012-08-27	0
60657	103703	7	2012-08-29 00:00:00	8	A	admision 29/08/2012: no hay  sic	\N		NEFRO.ACC.VASCULAR	2012-08-23	0
60655	103701	7	2012-07-23 00:00:00	1		citado en OFT.NUEVOS AD-HDGF	\N			2012-10-25	0
60656	103702	7	2012-07-23 00:00:00	1		citado en OFT.NUEVOS AD-HDGF	\N			2012-10-25	0
60678	103724	7	2012-08-29 00:00:00	45	G	(29/08/2012) :  Paciente Citado para el  27/08/2012 DR TRINCADO , indica parches , paciente asiste , documento no ha llegado al ges ,.	2012-09-05		Excepcion	2012-08-27	0
60662	103708	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  citado 28/09/2012  CONCHA	\N		Endocrinología	2012-09-28	0
60665	103711	7	2012-08-29 00:00:00	1		admision 20/08/2012:  30/08/2012 L.CONCHA	\N			2012-08-30	0
60666	103712	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  30/08/2012 DR CISTERNAS	\N		Oftalmología	2012-08-30	0
60667	103713	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  25/10/2012 DR. SCHIAPPACASSE	\N			2012-10-25	0
60668	103714	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  25/10/2012 DR. SCHIAPPACASSE	\N			2012-10-25	0
60669	103715	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  25/10/2012 DR. SCHIAPPACASSE	\N			2012-10-25	0
60670	103716	7	2012-08-07 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60672	103718	7	2012-08-29 00:00:00	1		citado para MED.ENDO.DIABETES-HDGF	\N		Endocrinología	2012-08-30	0
60674	103720	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  25/10/2012 DR. SCHIAPPACASSE	\N			2012-10-25	0
60677	103723	7	2012-07-23 00:00:00	1		ADMISION 23/07/2012: Citado para el  25/10/2012 DR. SCHIAPPACASSE	\N			2012-10-25	0
60680	103726	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	2012-10-15			2012-09-06	0
60682	103728	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  20/09/2012 dr cisternas	\N		Oftalmología	2012-09-20	0
60684	103730	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-06	0
60685	103731	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	2012-10-15			2012-09-06	0
60686	103732	7	2012-08-10 00:00:00	1		CITADO EL 10/09/2012 A MED.RES DIAG-HDGF	\N		Med.Interna	2012-09-10	0
60688	103734	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-06	0
60689	103735	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	2012-10-15			2012-09-06	0
60691	103737	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-06	0
60692	103738	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	\N		Oftalmología	2012-09-06	0
60693	103739	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  30/08/2012 DR CISTERNAS	\N		Oftalmología	2012-08-30	0
60696	103742	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60697	103743	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-30			2012-08-21	0
60658	103704	7	2012-08-16 00:00:00	10	J	hospitalizacion 22/8/2012: En Tabla el 19/10/2012, se cancela, sin oferta	\N		tunelizado	\N	0
60652	103698	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60650	103696	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60654	103700	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60653	103699	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60664	103710	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60661	103707	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60663	103709	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60676	103722	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60659	103705	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60673	103719	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60687	103733	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60671	103717	7	2012-08-23 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 26/10/2012	\N		FHI+ cateter tunelizado	2012-10-26	0
60675	103721	7	2012-08-06 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 26/10/12	\N		Acceso Vascular Simple (mediante FAV)	2012-10-26	0
60694	103740	7	2012-08-13 00:00:00	14	AB	13/08/2012/ UGAC informa que paciente esta en Tabla para el 06/09/2012 PROCESO EXS.	2012-09-06		adenoma o cáncer prostático, resección endoscópica	2012-09-06	0
60718	103764	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60699	103745	7	2012-08-29 00:00:00	1		(29/08/2012)  Citado para el  30/08/2012 DR CISTERNAS , se llama a paciente para recordar citacion	2012-09-02			2012-08-30	0
60728	103774	7	2012-08-29 00:00:00	8	A	29/08/2012 pac nsp a citacion del 21/08/2012, se gestionara nueva hora	2012-08-30			2012-08-21	0
60701	103747	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60702	103748	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  18/10/2012 DR. VERGARA	2012-10-10			2012-10-18	0
60703	103749	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-30			2012-08-21	0
60705	103751	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60706	103752	7	2012-08-29 00:00:00	22		29/08/2012 ya parametrizo en sigges	2012-08-30			2012-08-21	0
60707	103753	7	2012-08-29 00:00:00	1		27/08/2012- Paciente  citado  28/08/2012 (Se llama para recordar)	\N			2012-08-28	0
60710	103756	7	2012-08-29 00:00:00	1		(29/08/2012)  Citado para el  30/08/2012 DR CISTERNAS , se llama a paciente para recordar citacion	2012-09-02			2012-08-30	0
60712	103758	7	2012-08-29 00:00:00	1		27/08/2012- Paciente  citado  28/08/2012 (Se llama para recordar)	\N			2012-08-28	0
60713	103759	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 DR TRINCADO (Se llama para recordar)	\N			2012-08-28	0
60714	103760	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	2012-10-05			2012-09-06	0
60716	103762	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  18/10/2012 DR. VERGARA	2012-10-15			2012-10-18	0
60717	103763	7	2012-08-29 00:00:00	1		(29/08/2012)  Citado para el  30/08/2012 DR CISTERNAS , se llama a paciente para recordar citacion	2012-09-02			2012-08-30	0
60720	103766	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs OK, Eco y EKG 31/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60722	103768	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  20/09/2012 LUIS BECERRA	\N		Cardiología	2012-09-20	0
60723	103769	7	2012-06-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Se solictó envío de carta	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60724	103770	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60725	103771	7	2012-08-29 00:00:00	1		27/08/2012- citado  28/08/2012 (Se llama para recordar y no contesta 14:30)	\N			2012-08-28	0
60726	103772	7	2012-08-10 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y Ekg 31/08/12	\N		intervención quir. integral cataratas	\N	0
60731	103777	7	2012-08-23 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60733	103779	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	\N		Oftalmología	2012-09-06	0
60734	103780	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	2012-10-05			2012-09-06	0
60735	103781	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60737	103783	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	\N		Oftalmología	2012-09-06	0
60738	103784	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  25/10/2012 DR. VERGARA	2012-10-30			2012-10-25	0
60739	103785	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60740	103786	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	\N		Oftalmología	2012-09-06	0
60741	103787	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	2012-10-05			2012-09-06	0
60742	103788	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  13/09/2012 DR CISTERNAS	\N		Oftalmología	2012-09-13	0
60730	103776	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N		Nefro.Acc.Vascular	\N	0
60732	103778	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N		Diabetes	\N	0
60743	103789	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N			\N	0
60715	103761	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo derecho	2012-08-24	0
60729	103775	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
60698	103744	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
60719	103765	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-08-10	0
60727	103773	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
60704	103750	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 07/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-07	0
60721	103767	7	2012-03-15 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 04/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-04	0
60708	103754	7	2012-03-13 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 01/09/2012	\N		oa no especifica	2012-09-01	0
60748	103794	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60778	103824	7	2012-08-29 00:00:00	29	F	(29/08/2012) Paciente asiste a citacion  .	\N		nomina	2012-08-27	0
60750	103796	7	2012-08-22 00:00:00	1		OFT.NUEVOS AD-HDGF	\N			2012-09-04	0
60751	103797	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Eco y Enf 30/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60752	103798	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  24/10/2012 DR. VASQUEZ	2012-10-15			2012-10-24	0
60754	103800	7	2012-08-21 00:00:00	1		admision 20/08/2012: CISTERNAS 06/09/2012	\N		Oftalmología	2012-09-06	0
60756	103802	7	2012-08-27 00:00:00	1		27/08/2012- Paciente solicita cambio de hora, Recitado para el 20/09/2012	\N			2012-09-20	0
60757	103803	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60758	103804	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60759	103805	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60760	103806	7	2012-08-14 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60761	103807	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60762	103808	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60763	103809	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60779	103825	7	2012-08-29 00:00:00	45	G	(29/08/2012) Se llama a paciente e indica que Dr. Le informo que no tenia problemas a la retina , asiste a citacion , documento no recepcionado en ges .	2012-09-10		ipd	2012-08-28	0
60766	103812	7	2012-08-13 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60767	103813	7	2012-08-14 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60769	103815	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60770	103816	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  24/09/2012 DR TRINCADO	2012-09-15			2012-09-24	0
60771	103817	7	2012-08-14 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60772	103818	7	2012-08-16 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, eco y EKG OK. Pend Enf.	\N		intervención quir. integral cataratas	\N	0
60773	103819	7	2012-08-16 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60774	103820	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	\N			2012-09-06	0
60776	103822	7	2012-08-14 00:00:00	1		citado para MED.ENDO.DIABETES-HDGF	\N		Endocrinología	2012-09-14	0
60777	103823	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  25/10/2012 DR. VERGARA	2012-10-15			2012-10-25	0
60780	103826	7	2012-08-16 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60781	103827	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  24/09/2012 DR. RAMIREZ	2012-10-15			2012-09-24	0
60782	103828	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 PINOCHET (Se llama y no contesta)	\N		Oftalmología	2012-08-28	0
60783	103829	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60784	103830	7	2012-08-16 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60785	103831	7	2012-08-16 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG OK, Enf 09/09/12	\N		intervención quir. integral cataratas	\N	0
60786	103832	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  31/10/2012 DR. VASQUEZ	2012-08-15			2012-10-31	0
60787	103833	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  31/08/2012 DRA. AVILA	2012-10-15			2012-08-31	0
60788	103834	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  31/10/2012 DR. VASQUEZ	2012-10-15			2012-10-31	0
60789	103835	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  26/09/2012 DRA VENEZIAN	\N			2012-09-26	0
60790	103836	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  10/09/2012 DR TRINCADO	\N			2012-09-10	0
60791	103837	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  22/10/2012 DR. FERNANDEZ	2012-08-15			2012-10-22	0
60793	103839	7	2012-08-29 00:00:00	22		Paciente Asintomatica, Seguimiento 27/08/2012	\N			2012-08-27	0
60794	103840	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  06/09/2012 CISTERNAS	2012-09-15			2012-09-06	0
60795	103841	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60796	103842	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 DR TRINCADO (Se llama para recordar)	\N			2012-08-28	0
60797	103843	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60798	103844	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  07/11/2012 DR VASQUEZ	\N			2012-11-07	0
60765	103811	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Diabetes	\N	0
60768	103814	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Nefrología	\N	0
60792	103838	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Nefro.Acc.Vascular	\N	0
60749	103795	7	2012-08-13 00:00:00	10	J	hospitalizacion 22/8/2012: En Tabla el 26/10/2012, se cancela, sin oferta	\N		nstalación CatéterTransitorio Tunelizado O De Larga Duración	\N	0
60764	103810	7	2012-05-15 00:00:00	14	AB	HOSPITALIZACION 06/6/2012 En Tabla el 06/09/2012 PROCESO EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-06	0
60800	103846	7	2012-07-30 00:00:00	1		admision 30/07/2012: Citado para el  31/08/2012 DRA. AVILA	2012-10-10			2012-08-31	0
60803	103849	7	2012-08-29 00:00:00	29	F	Descartado 24-08-2012	\N		ISV	2012-08-24	0
60804	103850	7	2012-08-14 00:00:00	1		Hospitalización informa que no puede mandar antecedentes a prestador, porque paciente requiere examen SPECT el cual esta citado para el 10/10/12 y no se pudo adelantar hora	\N		SPECT	2012-10-10	0
60801	103847	7	2012-08-29 00:00:00	8	U	Paciente NSP 21-08-2012	\N		ISV	2012-08-21	0
60807	103853	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60810	103856	7	2012-08-29 00:00:00	8	U	Paciente NSP 21-08-2012	\N		ISV	2012-08-21	0
60809	103855	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-22	0
60811	103857	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60812	103858	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60814	103860	7	2012-08-29 00:00:00	22		Descartado 21-08-12	\N			2012-08-21	0
60815	103861	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60816	103862	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60817	103863	7	2012-08-16 00:00:00	1		Citada a Mx el 16/10/2012 y el 06/11/2012 a Control con Dr. Lobos	\N		Patologia mamaria	2012-11-06	0
60818	103864	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 DR TRINCADO  (Se llama para recordar)Pac, con neumonia	\N			2012-08-28	0
60819	103865	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60820	103866	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60821	103867	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60822	103868	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60823	103869	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60824	103870	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60825	103871	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60826	103872	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60827	103873	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60828	103874	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60829	103875	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  100% preparado	\N		intervención quir. integral cataratas	\N	0
60830	103876	7	2012-08-21 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, eco y EKG OK. Pend Enf.	\N		intervención quir. integral cataratas	\N	0
60831	103877	7	2012-08-21 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, Eco 31/08/12	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60832	103878	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  11/10/2012 DRA VENEZIAN	\N		Oftalmología	2012-10-11	0
60833	103879	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-09-06			2012-10-02	0
60834	103880	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60835	103881	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60836	103882	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente  Citado para el  29/10/2012 DR. FERNANDEZ	2012-10-15			2012-10-29	0
60837	103883	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60838	103884	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60839	103885	7	2012-05-22 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-09-13	0
60840	103886	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60841	103887	7	2012-08-23 00:00:00	1			\N			2012-09-07	0
60842	103888	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60843	103889	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N			2012-10-02	0
60844	103890	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N			2012-10-02	0
60845	103891	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  26/09/2012 DRA VENEZIAN	\N			2012-09-26	0
60847	103893	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60848	103894	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60849	103895	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60850	103896	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60851	103897	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60852	103898	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60853	103899	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, eco y EKG OK. Pend Enf.	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60805	103851	7	2012-05-31 00:00:00	14	AB	HOSPITALIZACION 06/6/2012 En Tabla el 06/09/2012 PROCESO EXS.	\N		ADENOMASTECTOMIA	2012-09-06	0
60808	103854	7	2012-03-20 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 06/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-06	0
60806	103852	7	2012-05-17 00:00:00	14	AB	HOSPITALIZACION 06/6/2012 En Tabla el 06/09/2012 PROCESO EXS.	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-06	0
60802	103848	7	2012-05-15 00:00:00	14	AB	HOSPITALIZACION 06/6/2012 En Tabla el 06/09/2012 PROCESO EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-06	0
60813	103859	7	2012-08-29 00:00:00	45	G	(29/08/2012) se habl con la hija de la paciente e indica que asistio el  28/08/2012  con el Dr,PINOCHET , indica lentes .	2012-09-15		ipd	2012-08-28	0
60854	103900	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, eco y EKG OK. Pend Enf.	\N		intervención quir. integral cataratas	\N	0
60855	103901	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60856	103902	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60857	103903	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60858	103904	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60859	103905	7	2012-08-20 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs y Eco 30/08/12, EKG 31/08/12	\N		intervención quir. integral cataratas	\N	0
60860	103906	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  10/09/2012 DR TRINCADO	2012-10-15			2012-09-10	0
60861	103907	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  26/09/2012 DRA VENEZIAN	\N		Oftalmología	2012-09-26	0
60862	103908	7	2012-08-22 00:00:00	1		admision 22/08/2012: Citado para el  26/09/2012 DRA VENEZIAN	\N		Oftalmología	2012-09-26	0
60863	103909	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  24/09/2012 DR TRINCADO	2012-09-06			2012-09-24	0
60864	103910	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60865	103911	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60866	103912	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60867	103913	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60877	103923	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60894	103940	7	2012-08-27 00:00:00	20	D	Correo Enviado de SDA a Abastecimiento para generar OC día 27-08-2012	\N		Audifonos	2012-08-27	0
60895	103941	7	2012-08-27 00:00:00	20	D	Correo Enviado de SDA a Abastecimiento para generar OC día 27-08-2012	\N		Audifonos	2012-08-27	0
60871	103917	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012  (Se llama para recordar)Pac, con neumonia	\N			2012-08-28	0
60872	103918	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  24/09/2012 DR TRINCADO	2012-10-15			2012-09-24	0
60873	103919	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60874	103920	7	2012-08-22 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
60879	103925	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60880	103926	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60883	103929	7	2012-08-13 00:00:00	1		(13/08/2012) Paciente CITADO 12/11/2012 DR.FERNANDEZ	2012-11-16			2012-11-12	0
60884	103930	7	2012-08-29 00:00:00	22		Confirmado 21-08-2012	\N			2012-08-21	0
60887	103933	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60888	103934	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	2012-09-06			2012-10-17	0
60889	103935	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-09-06			2012-10-02	0
60890	103936	7	2012-08-22 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60891	103937	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/09/2012 DR TRINCADO	2012-09-15			2012-09-10	0
60896	103942	7	2012-08-22 00:00:00	1		MED.ENDO.DIABETES-HDGF	\N		Med.Interna	2012-09-27	0
60897	103943	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  24/10/2012 DRA VENEZIAN	\N		Oftalmología	2012-10-24	0
60898	103944	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-09-15			2012-10-02	0
60899	103945	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-09-15			2012-10-02	0
60900	103946	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-09-15			2012-10-02	0
60901	103947	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N		Oftalmología	2012-10-02	0
60902	103948	7	2012-08-23 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60875	103921	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  Atendido el  13/06/2012 DR LIRA	2012-08-30		CONSULTA ESPECIALISTA	\N	0
60876	103922	7	2012-08-24 00:00:00	7	A	24/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N		Med.Interna	\N	0
60885	103931	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Cardiología	\N	0
60886	103932	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Cardiología	\N	0
60892	103938	7	2012-08-22 00:00:00	7	A	(22/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-09-15			\N	0
60893	103939	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Cardiología	\N	0
60903	103949	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Nefro.Acc.Vascular	\N	0
60904	103950	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		Nefro.Acc.Vascular	\N	0
60881	103927	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60868	103914	7	2012-08-21 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 09/11/2012	\N		FR IZQ	2012-11-09	0
60905	103951	7	2012-08-23 00:00:00	14	AB	hospitalizacion 28/8/2012: En Tabla el 09/11/2012	\N		Acc.vascular.	2012-11-09	0
60882	103928	7	2012-03-27 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 14/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-14	0
60906	103952	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/09/2012 DR TRINCADO	2012-10-15			2012-09-10	0
60914	103960	7	2012-08-14 00:00:00	29	F	Hospitalizacion 14/08/2012 En Tabla el 25/08/2012 OPERADO SEGUNDO OJO	\N		FAP	2012-08-25	0
60908	103954	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60939	103985	7	2012-08-29 00:00:00	29	F	Confirmado 24-08-2012	\N		ISV	2012-08-24	0
60922	103968	7	2012-08-29 00:00:00	8	U	Paciente NSP 24-8-2012	\N		ISV	2012-08-24	0
60941	103987	7	2012-08-29 00:00:00	8	U	Paciente NSP 22-08-2012	\N		ISV	2012-08-22	0
60915	103961	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60916	103962	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60917	103963	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60918	103964	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60923	103969	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/09/2012 DR TRINCADO	2012-10-15			2012-09-10	0
60924	103970	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	2012-10-15			2012-10-02	0
60925	103971	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  24/10/2012 DRA VENEZIAN	2012-10-15			2012-10-24	0
60926	103972	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
60927	103973	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
60928	103974	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
60929	103975	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
60919	103965	7	2012-08-29 00:00:00	45	G	28/08/2012-(Oft. Secre no se encuetra. Informan que no a llegado memo. Sr nuri en curso)asiste  el 09/08 con Dr Pinochet(Llega ficha 22/08 en la tarde)Registra atencion pero no doc. En poli de OFT.Se entrega Doc, a Secre para ser que referente confeccione	\N		IPD	2012-08-20	0
60931	103977	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60932	103978	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60936	103982	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60940	103986	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60943	103989	7	2012-08-23 00:00:00	1		23/08/2012- no puede asistie el  22/08/2012, Le asignan nueva citacion para el 16/10 con Dr Cisternas	\N			2012-10-16	0
60944	103990	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60945	103991	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60946	103992	7	2012-08-29 00:00:00	1		Citado para el  30/08/2012 DR. CISTERNAS	\N			2012-08-30	0
60947	103993	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60948	103994	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60950	103996	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60951	103997	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60952	103998	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60953	103999	7	2012-08-29 00:00:00	10		29/08/2012- Caso confimado, pero O.A no registra en Lec.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60954	104000	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61009	104055	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
60911	103957	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-10-15			\N	0
60912	103958	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-10-15			\N	0
60930	103976	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-10-15			\N	0
60949	103995	7	2012-08-27 00:00:00	7	A	27/08/2012- I.C registra en Gis, pero sin citacion asignada	\N		Oftalmología	\N	0
60913	103959	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60920	103966	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60935	103981	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60938	103984	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60934	103980	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
60937	103983	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
60933	103979	7	2012-06-04 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 13/09/2012PROCESO DE EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-13	0
60942	103988	7	2012-08-13 00:00:00	14	AB	13/08/2012/ UGAC informa que paciente esta en Tabla para  el 13/09/2012 PROCESO DE EXS.	2012-09-13		RTU PROSTATA	2012-09-13	0
60955	104001	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60956	104002	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60957	104003	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60958	104004	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60959	104005	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  07/11/2012 dra Torregrosa	\N		Psiquiatria	2012-11-07	0
60960	104006	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60961	104007	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  06/11/2012 DR AMESTICA	2012-09-05			2012-11-06	0
60998	104044	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60999	104045	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60994	104040	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
60990	104036	7	2012-08-29 00:00:00	29	F	Descartado 24-08-2012	\N		ISV	2012-08-28	0
60966	104012	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
60967	104013	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  21/11/2012 DR VASQUEZ	2012-09-15			2012-11-21	0
61006	104052	7	2012-08-24 00:00:00	2	D	Citado 28-08-12 ISV Dra Garcia	\N		ISV	2012-08-28	0
60970	104016	7	2012-08-21 00:00:00	1		admision 20/08/2012: VEGA 24/09/2012 VERGARA	2012-10-15			2012-09-24	0
60971	104017	7	2012-08-29 00:00:00	1		16/08/2012- Citado a Eda 17/08 (No contactado) Se ubica y se le informa citacion del 29/08 para uro,  y que se gestionara nueva citacion para endoscopia	\N		MED.GASTRO.ENDOSCOPIAS	2012-08-29	0
60973	104019	7	2012-08-29 00:00:00	1		27/08/2012- Paciente citado el 29/08/2012 con PRE.TX.RENAL, para definir que examenes faltan	\N		psiquiatria	2012-08-29	0
60974	104020	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60975	104021	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60977	104023	7	2012-08-29 00:00:00	22		Confirmado 22-08-2012	\N			2012-08-22	0
60978	104024	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60979	104025	7	2012-08-29 00:00:00	1		27/08/2012- Citado para el  28/08/2012 DR PINOCHET  (Se llama para recordar)Pac, con neumonia	\N			2012-08-28	0
60981	104027	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
60984	104030	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  29/08/2012 DRA VENEZIAN	\N			2012-08-29	0
60987	104033	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60988	104034	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
60991	104037	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  29/08/2012 DRA VENEZIAN	\N			2012-08-29	0
60992	104038	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
60993	104039	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
60995	104041	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61000	104046	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
61002	104048	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  29/08/2012 DRA VENEZIAN	\N			2012-08-29	0
61003	104049	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61004	104050	7	2012-08-29 00:00:00	1		admision 14/08/2012: Citado para el  29/08/2012 DRA VENEZIAN	\N			2012-08-29	0
61007	104053	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61008	104054	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
60963	104009	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N		Oftalmología	\N	0
60964	104010	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N		Med.Interna	\N	0
60965	104011	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
60972	104018	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  En Auditoria	\N		psiquiatria	\N	0
60997	104043	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: EN TABLA OI 30/08/12 OD 02/08/12	\N		Segundo ojo	2012-08-30	0
60989	104035	7	2012-06-04 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 20/09/2012PROCESO DE EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-20	0
60996	104042	7	2012-06-04 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 20/09/2012PROCESO DE EXS.	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-20	0
60969	104015	7	2012-03-30 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 11/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-11	0
60980	104026	7	2012-06-04 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 13/09/2012PROCESO DE EXS.	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-13	0
61040	104086	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61011	104057	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61012	104058	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61016	104062	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61017	104063	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
61018	104064	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR CISTERNAS	\N			2012-09-04	0
61019	104065	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales, )	\N			2012-08-29	0
61020	104066	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  25/09/2012 DR CISTERNAS	\N			2012-09-25	0
61023	104069	7	2012-06-07 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		Faco + Lio Bilateral	\N	0
61036	104082	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61053	104099	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61026	104072	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61060	104106	7	2012-08-29 00:00:00	8	U	Paciente NSP 24-8-2012	\N		ISV	2012-08-24	0
61037	104083	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  04/09/2012 DR TRINCADO	\N			2012-09-04	0
61044	104090	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DR PINOCHET	\N			2012-09-11	0
61045	104091	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DR PINOCHET	\N			2012-09-11	0
61048	104094	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DR PINOCHET	\N			2012-09-11	0
61049	104095	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61051	104097	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61055	104101	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61056	104102	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61058	104104	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61061	104107	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DR PINOCHET	\N			2012-09-11	0
61062	104108	7	2012-08-21 00:00:00	1		admision 20/08/2012:  24/09/2012 MAC MILLAN	2012-08-15		Urología	2012-09-24	0
61063	104109	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  20/09/2012 DRA PINO	\N			2012-09-20	0
61064	104110	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	2012-09-30			2012-09-11	0
61065	104111	7	2012-08-29 00:00:00	1		29/08/2012- Paciente citado a MED.CARDIO.ELECTRO el 29/08 (No registra evento en Programas locales, )	\N		MED.CARDIO.ELECTRO.PROC.-HDGF	2012-08-29	0
61025	104071	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61027	104073	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61028	104074	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N		Med.Interna	\N	0
61029	104075	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N		Oftalmología	\N	0
61030	104076	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61031	104077	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61032	104078	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61033	104079	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61034	104080	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61041	104087	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
61042	104088	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
61047	104093	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 30/08/2012	\N		Segundo ojo	2012-08-30	0
61035	104081	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 29/08/2012	\N		Segundo ojo	2012-08-29	0
61046	104092	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 29/08/2012	\N		Segundo ojo	2012-08-29	0
61039	104085	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61050	104096	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61043	104089	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61052	104098	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: En Tabla OD 26/07/12 OI 23/08/12	\N		Segundo ojo	2012-08-23	0
61054	104100	7	2012-08-17 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		Segundo ojo	2012-08-02	0
61038	104084	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		Segundo ojo	2012-07-19	0
61066	104112	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	2012-11-16			2012-09-11	0
61067	104113	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  02/10/2012 DR PINOCHET	\N			2012-10-02	0
61068	104114	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  28/08/2012 TRINCADO  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61073	104119	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61070	104116	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  28/08/2012 TRINCADO  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61077	104123	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61078	104124	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61110	104156	7	2012-08-29 00:00:00	29	F	Confirmado 28-08-2012	\N		ISV	2012-08-28	0
61083	104129	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales, )	\N			2012-08-29	0
61084	104130	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61085	104131	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  24/09/2012 VERGARA	2012-09-30			2012-09-24	0
61086	104132	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61108	104154	7	2012-08-29 00:00:00	2	D	Citado 28-09-2012 paciente no puede asistir antes por estar recien operado	\N		ISV	2012-09-28	0
61079	104125	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61092	104138	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  29/08/2012 DRA VENEZIAN (No registra evento en Programas locales, )	\N			2012-08-29	0
61093	104139	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  31/08/2012 CISTERNAS	\N			2012-08-31	0
61094	104140	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  31/08/2012 CISTERNAS	\N			2012-08-31	0
61095	104141	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  24/09/2012 VERGARA	2012-09-30			2012-09-24	0
61096	104142	7	2012-08-29 00:00:00	1		29/08/2012-  Citado para el  28/08/2012 PINOCHET  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61100	104146	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  04/09/2012 TRINCADO	\N			2012-09-04	0
61090	104136	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61102	104148	7	2012-08-29 00:00:00	1		29/08/2012-  Citado para el  28/08/2012 PINOCHET  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61113	104159	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  28/08/2012 PINOCHET  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61114	104160	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  04/09/2012 PINOCHET	\N			2012-09-04	0
61116	104162	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  14/11/2012 DR VASQUEZ	2012-09-05			2012-11-14	0
61098	104144	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61074	104120	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		Segundo ojo	2012-09-11	0
61117	104163	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		primer ojo	2012-08-24	0
61072	104118	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 22/08/2012	\N		ojo izquierdo	2012-08-22	0
61069	104115	7	2012-06-08 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 11/10/2012 PROC.DE EXAMENES	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-11	0
61091	104137	7	2012-06-06 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 27/09/2012PROCESO DE EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-27	0
61075	104121	7	2012-06-06 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 27/09/2012PROCESO DE EXS.	\N		adenoma o cáncer prostático, resección endoscópica	2012-09-27	0
61104	104150	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61111	104157	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61112	104158	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61088	104134	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61115	104161	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61105	104151	7	2012-08-24 00:00:00	2	D	Citado 28-08-12 ISV Dra Garcia	\N		ISV	2012-08-28	0
61103	104149	7	2012-08-24 00:00:00	2	D	Citado 28-08-12 ISV Dra Garcia	\N		ISV	2012-08-28	0
61097	104143	7	2012-08-24 00:00:00	2	D	Citado 28-08-12 ISV Dra Garcia	\N		ISV	2012-08-28	0
61076	104122	7	2012-08-29 00:00:00	11	I	En llamado del dia 29-08-2012 a las 14:24 telefono 2676357-2676355 Paciente no contesta / En llamado del día 24-08-12 a las 16:13 al telefono 2676357-2676355  no contesta / 08/06/2012  no ha sido enviado a compras	\N			2012-08-30	0
61106	104152	7	2012-08-29 00:00:00	11	I	En llamado del dia 29-08-2012 a las 14:40 al telefono 91302558 Paciente no contesta/ En llamado del día 24-08-12 a las 16:49 al telefono 91302558  no contesta	\N			2012-08-30	0
61109	104155	7	2012-08-29 00:00:00	15	G	27/08/2012- Se llama via telefonica e hija informa que paciente se encuentra Hospitalizado hace 2 meses. Cuando Paciente este mejor  informaran a secretaria Ges para solicitar nueva hora. (Se enviara correo de respaldo)	\N			2012-08-28	0
61071	104117	7	2012-08-29 00:00:00	45	G	29/08/2012- Atendido  el  28/08/2012 TRINCADO  (registra O.A en Lec)	\N		IPD	2012-08-28	0
61120	104166	7	2012-06-11 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Paciente enyesada	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61124	104170	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61119	104165	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61126	104172	7	2012-08-29 00:00:00	22		28/08/2012- Paciente asiste el   24/08/2012 PEZO. IPD registra en Secretaria ges.	\N			2012-08-24	0
61125	104171	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61118	104164	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61137	104183	7	2012-08-29 00:00:00	2	D	Citado 31-08-2012	\N		ISV	2012-08-31	0
61134	104180	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  04/09/2012 TRINCADO	\N			2012-09-04	0
61138	104184	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  12/10/2012 DR VERGARA	2012-08-30			2012-10-12	0
61139	104185	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61140	104186	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  24/09/2012 VERGARA	2012-10-15			2012-09-24	0
61141	104187	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61142	104188	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61145	104191	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61133	104179	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61147	104193	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61130	104176	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61131	104177	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61150	104196	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61154	104200	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61160	104206	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales, )	\N			2012-08-29	0
61161	104207	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61165	104211	7	2012-08-29 00:00:00	1		29/08/2012: Citado para el  29/08/2012 SCHIAPACASE (No registra evento en Programas locales,  Call center  lo contacto)	\N			2012-08-29	0
61166	104212	7	2012-08-29 00:00:00	1		29/08/2012- Citado para el  28/08/2012 TRINCADO  (Se llama para recordar)(No registra eventos en Programas locales, telefonos no disponible)	\N			2012-08-28	0
61121	104167	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: EN TABLA OI 23/08/12 OD 26/07/12	\N		Segundo ojo	2012-08-23	0
61127	104173	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: EN TABLA OI 23/08/12 OD 26/07/12	\N		Segundo ojo	2012-08-23	0
61122	104168	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: En Tabla OD 23/08/12 OI 26/07/12	\N		Segundo ojo	2012-08-23	0
61129	104175	7	2012-06-21 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 25/10/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-25	0
61148	104194	7	2012-06-18 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 18/10/2012 PROC.DE EXAMENES	\N		adenoma o cáncer prostático, resección endoscópica	2012-10-18	0
61128	104174	7	2012-06-12 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 11/10/2012 PROC.DE EXAMENES	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-11	0
61146	104192	7	2012-06-14 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 11/10/2012 PROC.DE EXAMENES	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-11	0
61132	104178	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61135	104181	7	2012-08-29 00:00:00	2	D	Citado 30-08-2012	\N		ISV	2012-08-30	0
61164	104210	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61153	104199	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61155	104201	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61157	104203	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61162	104208	7	2012-08-29 00:00:00	2	D	Citado 03-09-2012	\N		ISV	2012-09-03	0
61152	104198	7	2012-08-29 00:00:00	2	D	Citado 31-08-2012	\N		ISV	2012-08-31	0
61151	104197	7	2012-08-29 00:00:00	2	D	Citado 31-08-2012	\N		ISV	2012-08-31	0
61143	104189	7	2012-08-29 00:00:00	2	D	Citado 31-08-2012	\N		ISV	2012-08-31	0
61123	104169	7	2012-08-29 00:00:00	8	A	27/08/2012- NSP 24/08/2012 PEZO. Se llama a paciente e informa que problema de salud no pudo asistor, pero solicita que le reasignen una nueva citacion	\N			2012-08-24	0
61144	104190	7	2012-08-29 00:00:00	8	A	29/08/2012- Paciente asiste acontrol con Psiquiatra otorga Pase.  NSP a controle de Tx se solicita tratara de contar a paciente para saber su interes y otorgar nueva citacion en el caso que lo decee	\N		Poli Pre.Tx	2012-08-23	0
61156	104202	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 15:08 Paciente no contesta telefono 81351386	\N			2012-08-29	0
61158	104204	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 15:13 Paciente no contesta telefono 32115567	\N			2012-08-29	0
61159	104205	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 15:19 Paciente no contesta telefono 2613950	\N			2012-08-29	0
61163	104209	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 15:22 Paciente no contesta telefono 2683686	\N			2012-08-29	0
61136	104182	7	2012-08-29 00:00:00	45	G	29/08/2012- Paciente atendido el  29/08/2012 SCHIAPACASE. (O.A registra en Lec)	\N		IPD	2012-08-29	0
61171	104217	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61203	104249	7	2012-08-27 00:00:00	29	F	(24/08/2012) Paciente sin indicacion de prostactectomia , se encuentra utilizando inyecciones .	2012-09-15		fap	2012-08-22	0
61173	104219	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61175	104221	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61177	104223	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61178	104224	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61185	104231	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61180	104226	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61181	104227	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  27/09/2012 VERGARA	2012-11-05			2012-09-27	0
61183	104229	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61184	104230	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61186	104232	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61187	104233	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61188	104234	7	2012-08-21 00:00:00	1		admision 20/08/2012: Citado para el  05/09/2012 VENEZIAN	\N			2012-09-05	0
61190	104236	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61191	104237	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N		intervención quir. integral cataratas	2012-10-17	0
61193	104239	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  26/10/2012 DR VERGARA	2012-11-05			2012-10-26	0
61194	104240	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61197	104243	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61182	104228	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61200	104246	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61201	104247	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61169	104215	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61205	104251	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  11/10/2012 DRA VENEZIAN	\N			2012-10-11	0
61170	104216	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61174	104220	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61202	104248	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61189	104235	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61192	104238	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61198	104244	7	2012-08-29 00:00:00	2	D	Citado 04-09-2012	\N		ISV	2012-09-04	0
61212	104258	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61219	104265	7	2012-07-20 00:00:00	13	G	20/07/2012- Paciente inicia estudio en Psiquiatria,  nueva control para el 23/07/2012	\N			\N	0
61220	104266	7	2012-07-03 00:00:00	13	G	03/07/2012- Paciente en Estudio  acualmente en controles.	\N			\N	0
61221	104267	7	2012-06-22 00:00:00	13	G	22/06/2012- Paciente atendido el 18/06/2012, inicia estudio de EZQ	\N			\N	0
61216	104262	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  17/10/2012 DRA VENEZIAN	\N			2012-10-17	0
61224	104270	7	2012-06-22 00:00:00	13	G	22/06/2012- Paciente atendido el 18/06/2012, inicia estudio de EZQ	\N			\N	0
61218	104264	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  26/10/2012 DR VERGARA	2012-10-15			2012-10-26	0
61168	104214	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 15:25 Paciente no contesta telefono 2673344	\N			2012-08-29	0
61223	104269	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61217	104263	7	2012-04-20 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61179	104225	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61199	104245	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61172	104218	7	2012-06-18 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 18/10/2012 PROC.DE EXAMENES	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-18	0
61214	104260	7	2012-06-19 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 18/10/2012 PROC.DE EXAMENES	\N		adenoma o cáncer prostático, resección endoscópica	2012-10-18	0
61208	104254	7	2012-06-19 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 18/10/2012 PROC.DE EXAMENES	\N		Sin  Orden de Atencion	2012-10-18	0
61176	104222	7	2012-04-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 13/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	2012-09-13	0
61195	104241	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 16:15 Paciente no contesta telefono 86333105	\N			2012-08-29	0
61196	104242	7	2012-08-29 00:00:00	6	U	En llamado del dia 29-08-2012 a las 16:17Paciente no contesta telefono 89659529	\N			2012-08-29	0
61232	104278	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61237	104283	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61238	104284	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61239	104285	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61240	104286	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61243	104289	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  11/10/2012 DRA VENEZIAN	\N			2012-10-11	0
61248	104294	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61254	104300	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61255	104301	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61257	104303	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61261	104307	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61262	104308	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61264	104310	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61268	104314	7	2012-06-26 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61270	104316	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61278	104324	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  26/09/2012 DRA VENEZIAN	\N			2012-09-26	0
61279	104325	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  10/10/2012 DRA VENEZIAN	\N			2012-10-10	0
61280	104326	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  16/11/2012 DR VERGARA	2012-11-15			2012-11-16	0
61269	104315	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-11-15			\N	0
61225	104271	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo derecho	2012-09-05	0
61260	104306	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61267	104313	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo derecho	2012-09-05	0
61253	104299	7	2012-08-01 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 29/08/2012	\N		Segundo ojo	2012-08-29	0
61258	104304	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: En Tabla OD 01/08/12 OI 29/08/12	\N		primer ojo	2012-08-29	0
61234	104280	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012	\N		ojo derecho	2012-08-25	0
61256	104302	7	2012-08-01 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 23/08/2012	\N		ojo derecho	2012-08-23	0
61259	104305	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61266	104312	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61241	104287	7	2012-06-22 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 15/11/2012	\N		adenoma o cáncer prostático, resección endoscópica	2012-11-15	0
61274	104320	7	2012-06-26 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 15/11/2012	\N		adenoma o cáncer prostático, resección endoscópica	2012-11-15	0
61281	104327	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  12/10/2012 DR VERGARA	2012-11-15			2012-10-12	0
61282	104328	7	2012-08-28 00:00:00	1		ADMISION 28/08/2012:  Citado para el  26/10/2012 DR VERGARA	2012-11-15			2012-10-26	0
61326	104372	7	2012-07-03 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  Exs, Eco y EKG 31/08/12. Pend Enf	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61283	104329	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-11-15			\N	0
61284	104330	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-11-15			\N	0
61286	104332	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-11-15			\N	0
61288	104334	7	2012-08-27 00:00:00	7	A	(27/08/2012) Paciente sin ciotaciones a especialista a la fecha .	2012-09-15			\N	0
61289	104335	7	2012-06-27 00:00:00	7	A	27/06/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61290	104336	7	2012-06-28 00:00:00	7	A	28/06/2012- No registra I.C en Gis. Por lo cual no registra asistencia.	\N			\N	0
61291	104337	7	2012-07-12 00:00:00	7	A	12/07/2012- I.C registra en Gis , pero no tiene citacion asignada.	\N			\N	0
61293	104339	7	2012-06-27 00:00:00	7	A	27/06/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61294	104340	7	2012-06-29 00:00:00	7	A	29/06/2012- NO registra I.C en GIS, por lo cual no tiene citacion asignda	\N			\N	0
61297	104343	7	2012-07-03 00:00:00	7	A	03/07/2012- I.C registra en Gis, pero aun no tiene citacion asignada.	\N			\N	0
61304	104350	7	2012-06-28 00:00:00	7	A	28/06/2012- No registra I.C en Gis. Por lo cual no registra asistencia.	\N			\N	0
61306	104352	7	2012-06-27 00:00:00	7	A	27/06/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61313	104359	7	2012-06-27 00:00:00	7	A	27/06/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61315	104361	7	2012-06-29 00:00:00	7	A	29/06/2012- NO registra I.C en GIS, por lo cual no tiene citacion asignda	\N			\N	0
61316	104362	7	2012-07-03 00:00:00	7	A	03/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada	\N			\N	0
61320	104366	7	2012-07-05 00:00:00	7	A	05/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61323	104369	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-10-15			\N	0
61287	104333	7	2011-05-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	2012-10-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61299	104345	7	2012-05-10 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61300	104346	7	2012-05-30 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61311	104357	7	2011-05-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	2012-10-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61319	104365	7	2011-05-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	2012-10-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61321	104367	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo derecho	2012-09-05	0
61318	104364	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo derecho	2012-09-05	0
61331	104377	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61329	104375	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
61325	104371	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61310	104356	7	2012-06-29 00:00:00	14	AB	hospitalizacion 11/07/2012: En Tabla el 22/11/2012 PROC.DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-11-22	0
61292	104338	7	2012-06-27 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 21/11/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-11-21	0
61302	104348	7	2012-06-27 00:00:00	14	AB	Hospitalizacion 04/07/2012 En Tabla el 15/11/2012	\N		ADENOMECTOMIA	2012-11-15	0
61342	104388	7	2012-07-05 00:00:00	13	G	05/07/2012- Paciente asiste el 04/07/2012 para su primer control con especialista,  inicia estudio.	\N			\N	0
61378	104424	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 12/09/2012	\N		ojo derecho	2012-09-12	0
61380	104426	7	2012-07-06 00:00:00	7		06/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61381	104427	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61382	104428	7	2012-07-09 00:00:00	7		09/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61437	104483	7	2012-08-14 00:00:00	21		hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61343	104389	7	2012-07-05 00:00:00	7	A	05/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61344	104390	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61345	104391	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61346	104392	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61347	104393	7	2012-07-06 00:00:00	7	A	06/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61354	104400	7	2012-07-09 00:00:00	7	A	09/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61358	104404	7	2012-07-05 00:00:00	7	A	05/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61360	104406	7	2012-07-05 00:00:00	7	A	05/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61366	104412	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61369	104415	7	2012-07-05 00:00:00	7	A	05/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61370	104416	7	2012-07-09 00:00:00	7	A	09/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61372	104418	7	2012-07-09 00:00:00	7	A	09/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61375	104421	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N		intervención quir. integral cataratas	\N	0
61376	104422	7	2012-07-09 00:00:00	7	A	09/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61377	104423	7	2012-07-06 00:00:00	7	A	06/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61351	104397	7	2012-05-10 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61362	104408	7	2012-05-10 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 31/08/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61367	104413	7	2012-05-10 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  R.CLINICA 07/09/2012	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61337	104383	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61333	104379	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61340	104386	7	2012-08-07 00:00:00	21	D	Hospitalizacion 14/08/2012 En Tabla el 05/09/2012	\N		ojo izquierdo	2012-09-05	0
61332	104378	7	2012-08-24 00:00:00	21	D	Se envia a Extensión Horaria con fecha 24-08-12 Según Directorio GES 23-08-12	\N		ojo izquierdo	2012-08-24	0
61341	104387	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
61334	104380	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-08-10	0
61339	104385	7	2012-07-04 00:00:00	14	AB	hospitalizacion 11/07/2012: En Tabla el 29/11/2012 PROC.DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-11-29	0
61379	104425	7	2012-07-10 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 06/12/2012 PROC.DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-12-06	0
61357	104403	7	2012-07-09 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 29/11/2012 PROC.DE EXAMEN	\N		adenoma o cáncer prostático, resección endoscópica	2012-11-29	0
61363	104409	7	2012-07-09 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 29/11/2012 PROC.DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-11-29	0
61355	104401	7	2012-07-09 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 29/11/2012 PROC.DE EXAMEN	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-11-29	0
61383	104429	7	2012-07-09 00:00:00	7		09/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61384	104430	7	2012-07-06 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61385	104431	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61386	104432	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61387	104433	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61388	104434	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61389	104435	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61390	104436	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61391	104437	7	2012-07-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61392	104438	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61393	104439	7	2012-07-06 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61394	104440	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61395	104441	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61411	104457	7	2012-07-13 00:00:00	13	G	13/07/2012- Paciente asiste a su primer contro en Psiquiatria, con tinuara Estudio  seguir indicacion medica	\N			\N	0
61397	104443	7	2012-07-06 00:00:00	7		06/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61398	104444	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61399	104445	7	2012-07-06 00:00:00	7		06/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61431	104477	7	2012-07-17 00:00:00	13	G	17/07/2012-  Paciente atendido el 12/07/2012, Continuara estudio de EQZ segun indicacion medica	\N			\N	0
61401	104447	7	2012-07-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61402	104448	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61403	104449	7	2012-07-10 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61404	104450	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61405	104451	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61406	104452	7	2012-07-17 00:00:00	7		17/07/2012- No registra I.C en Gis , por lo cual no tiene citacion asignada	\N			\N	0
61407	104453	7	2012-07-17 00:00:00	7		17/07/2012- No registra I.C en Gis , por lo cual no tiene citacion asignada	\N			\N	0
61408	104454	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61409	104455	7	2012-08-29 00:00:00	1		29/08/2012/ Se revisa Gis y la hora del 30/08/2012 la cancelan (por no atención del profesional),  la cambian para el 06/09/2012	2012-09-06			2012-09-06	0
61410	104456	7	2011-05-14 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 07/09/2012 R.CLINICA	2012-10-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61412	104458	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61413	104459	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61414	104460	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61415	104461	7	2012-07-11 00:00:00	7		11/07/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61416	104462	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61417	104463	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61418	104464	7	2012-08-14 00:00:00	21		hospitalizacion 28/8/2012: Pendiente	\N		ojo derecho	2012-08-10	0
61419	104465	7	2012-07-12 00:00:00	7		12/07/2012- I.C registra en Gis , pero no tiene citacion asignada.	\N			\N	0
61420	104466	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61421	104467	7	2012-07-12 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61422	104468	7	2012-07-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61423	104469	7	2012-07-12 00:00:00	7		12/07/2012- I.C registra en Gis , pero no tiene citacion asignada.	\N			\N	0
61424	104470	7	2012-07-24 00:00:00	7		24/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61425	104471	7	2012-07-30 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61426	104472	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61427	104473	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61428	104474	7	2012-07-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61429	104475	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61430	104476	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61432	104478	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 29/08/2012 10/10/2012 oi	\N		primer ojo	2012-08-29	0
61433	104479	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 29/08/2012 10/10/2012 oi	\N		primer ojo	2012-08-29	0
61434	104480	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61435	104481	7	2012-07-13 00:00:00	7		13/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada	\N			\N	0
61436	104482	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61852	104898	7	2012-08-24 00:00:00	7		24/08/2012- I.C	\N			\N	0
61400	104446	7	2012-07-10 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 06/12/2012 PROC.DE EXAMEN	\N		RTU PROSTATA	2012-12-06	0
61438	104484	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61439	104485	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61440	104486	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 06/09/2012 10/10/2012 oi	\N		primer ojo	2012-09-06	0
61441	104487	7	2012-08-14 00:00:00	21		hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-08-10	0
61442	104488	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61443	104489	7	2012-07-19 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61444	104490	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61445	104491	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 12/09/2012	\N		ojo derecho	2012-09-12	0
61446	104492	7	2012-07-17 00:00:00	7		17/07/2012- No registra I.C en Gis , por lo cual no tiene citacion asignada	\N			\N	0
61447	104493	7	2012-07-17 00:00:00	7		17/07/2012- No registra I.C en Gis , por lo cual no tiene citacion asignada	\N			\N	0
61448	104494	7	2012-08-14 00:00:00	21		hospitalizacion 28/8/2012: Pendiente	\N		primer ojo	2012-08-10	0
61449	104495	7	2012-07-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61450	104496	7	2012-07-17 00:00:00	7		17/07/2012- No registra I.C en Gis , por lo cual no tiene citacion asignada	\N			\N	0
61451	104497	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61452	104498	7	2012-08-07 00:00:00	21		Hospitalizacion 14/08/2012 En Tabla el 12/09/2012	\N		ojo derecho	2012-09-12	0
61453	104499	7	2012-07-17 00:00:00	10		Hospitalizacion 14/08/2012   Solicita llamar en Septiembre (problemas de salud)	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61454	104500	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61455	104501	7	2012-07-17 00:00:00	10		Hospitalizacion 14/08/2012   Exs, Eco y EKG OK. Enf 16/08/12	\N		intervención quir. integral cataratas	\N	0
61456	104502	7	2012-07-17 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61457	104503	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61458	104504	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61459	104505	7	2012-05-15 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 07/09/2012 R.CLINICA	2012-11-23		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61460	104506	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61461	104507	7	2012-05-15 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 07/09/2012 R.CLINICA	2012-11-20		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61462	104508	7	2012-07-17 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61463	104509	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61464	104510	7	2012-05-17 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 07/09/2012 R.CLINICA	2012-08-20		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61465	104511	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61466	104512	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61467	104513	7	2012-07-19 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61468	104514	7	2012-05-17 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 07/09/2012 R.CLINICA	2012-08-20		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61469	104515	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61470	104516	7	2012-07-19 00:00:00	7		19/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61471	104517	7	2012-07-23 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61472	104518	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61473	104519	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61474	104520	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61475	104521	7	2012-05-23 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	2012-09-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61476	104522	7	2012-07-20 00:00:00	7		20/07/2012- Diagnostico no muy claro en Sigges. I.C no registra en Gis  se eperara que  la evalue medico auditor  para saber si corresponde	\N			\N	0
61477	104523	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61478	104524	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61479	104525	7	2012-07-27 00:00:00	7		Paciente con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61480	104526	7	2012-05-23 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	2012-08-30			\N	0
61481	104527	7	2012-07-23 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61482	104528	7	2012-07-20 00:00:00	7		20/07/2012- No registra I.C en Gis, por lo  cual no tiene citacion asignada	\N			\N	0
61483	104529	7	2012-07-19 00:00:00	7		19/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61484	104530	7	2012-07-20 00:00:00	7		20/07/2012- No registra I.C en Gis, por lo  cual no tiene citacion asignada	\N			\N	0
61485	104531	7	2012-07-23 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61486	104532	7	2012-07-19 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61487	104533	7	2012-07-19 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61488	104534	7	2012-07-18 00:00:00	7		18/07/2012- No registra I.C en Gis, por lo cual no tienhe citacion asignada	\N			\N	0
61489	104535	7	2012-07-19 00:00:00	7		19/07/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61490	104536	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61491	104537	7	2012-07-19 00:00:00	7		19/07/2012- No registra I.c en Gis por lo cual no tiene  citacion asignada.	\N			\N	0
61492	104538	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61493	104539	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61494	104540	7	2012-07-18 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61495	104541	7	2012-07-24 00:00:00	7		24/07/2012- No registra I.c en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61496	104542	7	2012-07-25 00:00:00	7		Paciente  con caso  de vicios   en sigges  se debe  enviar  a compra	\N			\N	0
61497	104543	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61498	104544	7	2012-07-23 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		intervención quir. integral cataratas	\N	0
61499	104545	7	2012-07-23 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		intervención quir. integral cataratas	\N	0
61500	104546	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61501	104547	7	2012-07-20 00:00:00	7		20/07/2012- No registra I.C en Gis, por lo  cual no tiene citacion asignada	\N			\N	0
61502	104548	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61503	104549	7	2012-07-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61505	104551	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61506	104552	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61507	104553	7	2012-07-27 00:00:00	7		Paciente con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61508	104554	7	2012-07-27 00:00:00	7		27/07/2012- No registra I.C en Gis,  por lo cual no tiene citacion asignada.	\N			\N	0
61509	104555	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61510	104556	7	2012-07-23 00:00:00	7		23/07/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61511	104557	7	2012-07-23 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61512	104558	7	2012-07-27 00:00:00	7		Paciente con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61513	104559	7	2012-07-27 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61514	104560	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61515	104561	7	2012-05-25 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	2012-11-30		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61516	104562	7	2012-08-02 00:00:00	7		02/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asiganda	\N			\N	0
61518	104564	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61519	104565	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61520	104566	7	2012-07-31 00:00:00	7		31/07/2012- I.C registra en Gis  pero no tiene citacion asignada	\N			\N	0
61521	104567	7	2012-07-27 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		Faco+Lio	\N	0
61522	104568	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61504	104550	7	2012-07-23 00:00:00	13	G	23/07/2012- Paciente asiste a su primer control en Psiquiatria. Se inciara estudio seguin indique el Especialista	\N			\N	0
61524	104570	7	2012-07-26 00:00:00	7		paciente   con caso  de vicios en sigges  se debe enviar a compra	\N			\N	0
61525	104571	7	2012-07-27 00:00:00	10		Hospitalizacion 14/08/2012   Sin Fono de contacto. Se solicita enviar carta	\N		Faco+Lio	\N	0
61526	104572	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61527	104573	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61528	104574	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61529	104575	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61517	104563	7	2012-07-26 00:00:00	13	G	26/07/2012- Paciente inicia estudio de EQZ, Segú, indique especialista	\N			\N	0
61531	104577	7	2012-07-25 00:00:00	7		25/07/2012- No registra I.C en Gis. Por lo cual no tiene  citacion asignada	\N			\N	0
61532	104578	7	2012-07-26 00:00:00	7		paciente   con caso  de vicios en sigges  se debe enviar a compra	\N			\N	0
61533	104579	7	2012-07-26 00:00:00	7		paciente   con caso  de vicios en sigges  se debe enviar a compra	\N			\N	0
61534	104580	7	2012-07-27 00:00:00	7		27/07/2012- No registra I.C en Gis,  por lo cual no tiene citacion asignada.	\N			\N	0
61535	104581	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61536	104582	7	2012-07-25 00:00:00	7		Paciente  con caso  de vicios   en sigges  se debe  enviar  a compra	\N			\N	0
61537	104583	7	2012-07-26 00:00:00	7		26/07/2012- I.C registra en Gis, pero no tien citacion asignada	\N			\N	0
61538	104584	7	2012-07-25 00:00:00	7		Paciente  con caso  de vicios   en sigges  se debe  enviar  a compra	\N			\N	0
61539	104585	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61540	104586	7	2012-07-24 00:00:00	7		Paciente   con caso   de vicios  en sigges  se debe  mandar  a compras	\N			\N	0
61541	104587	7	2012-07-27 00:00:00	7		Paciente con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61542	104588	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61543	104589	7	2012-07-26 00:00:00	7		paciente   con caso  de vicios en sigges  se debe enviar a compra	\N			\N	0
61544	104590	7	2012-07-27 00:00:00	7		27/07/2012- No registra I.C en Gis,  por lo cual no tiene citacion asignada.	\N			\N	0
61546	104592	7	2012-07-26 00:00:00	7		paciente   con caso  de vicios en sigges  se debe enviar a compra	\N			\N	0
61547	104593	7	2012-07-25 00:00:00	7		25/07/2012- No registra I.C en Gis. Por lo cual no tiene  citacion asignada	\N			\N	0
61548	104594	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61549	104595	7	2012-07-26 00:00:00	7		26/07/2012- No registra I.C en Gis Por lo cual no tiene citacion asignada	\N			\N	0
61550	104596	7	2012-07-27 00:00:00	7		27/07/2012- No registra I.C en Gis,  por lo cual no tiene citacion asignada.	\N			\N	0
61551	104597	7	2012-08-14 00:00:00	21		hospitalizacion 28/8/2012: Pendiente	\N		ojo izquierdo	2012-08-10	0
61552	104598	7	2012-07-25 00:00:00	7		25/07/2012- No registra I.C en Gis. Por lo cual no tiene  citacion asignada	\N			\N	0
61553	104599	7	2012-07-24 00:00:00	7		24/07/2012- No registra I.c en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61554	104600	7	2012-07-30 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61555	104601	7	2012-08-20 00:00:00	10		20/08/2012- Paciente confirmado en Clinica ISV, por Catarata Unilateral.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61556	104602	7	2012-07-31 00:00:00	7		31/07/2012- I.C registra en Gis  pero no tiene citacion asignada	\N			\N	0
61557	104603	7	2012-04-02 00:00:00	7		02/04/2012-Paciente inicia Est.Pre-Tx renal, Citar a Psiquiatra para otorgar pase y poder continuar con estudio	\N		Psiquiatria	\N	0
61558	104604	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61559	104605	7	2012-07-31 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		Faco+Lio	\N	0
61560	104606	7	2012-07-27 00:00:00	7		27/07/2012- No registra I.C en Gis,  por lo cual no tiene citacion asignada.	\N			\N	0
61561	104607	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61562	104608	7	2012-07-31 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61563	104609	7	2012-07-30 00:00:00	10		Hospitalizacion 14/08/2012   100% Preparado	\N		intervención quir. integral cataratas	\N	0
61564	104610	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61565	104611	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61566	104612	7	2012-07-30 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61567	104613	7	2012-06-11 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	\N		Orden de atencion no Especifica	\N	0
61568	104614	7	2012-08-07 00:00:00	7		07/08/2012- No registra I.C en Gis por lo cual no tiene  citacion asignada	\N			\N	0
61569	104615	7	2012-08-01 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61570	104616	7	2012-07-30 00:00:00	7		30/07/2012- NO registra I.C en Gis, Por lo cual no tiene citacion asignada.	\N			\N	0
61571	104617	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61573	104619	7	2012-07-31 00:00:00	7		31/07/2012- I.C registra en Gis  pero no tiene citacion asignada	\N			\N	0
61574	104620	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61575	104621	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61576	104622	7	2012-07-31 00:00:00	7		31/07/2012- I.C registra en Gis  pero no tiene citacion asignada	\N			\N	0
61577	104623	7	2012-07-31 00:00:00	7		31/07/2012- I.C registra en Gis  pero no tiene citacion asignada	\N			\N	0
61578	104624	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61579	104625	7	2012-08-06 00:00:00	7		06/08/2012- I.C registra en Gis pero no tiene citacion asignada.	\N			\N	0
61580	104626	7	2012-06-27 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	\N		Endoprótesis Izquierda	\N	0
61581	104627	7	2012-08-01 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61582	104628	7	2012-06-04 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 14/09/2012 R.CLINICA	2012-10-20		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61585	104631	7	2012-04-02 00:00:00	7		02/04/2012-Paciente inicia Est.Pre-Tx renal, Citar a Psiquiatra para otorgar pase y poder continuar con estudio (I.C registra en Gis)	\N		Psiquiatria	\N	0
61586	104632	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61587	104633	7	2012-08-06 00:00:00	7		06/08/2012- I.C registra en Gis pero no tiene citacion asignada.	\N			\N	0
61588	104634	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61589	104635	7	2012-06-04 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-10-20			\N	0
61590	104636	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61591	104637	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61592	104638	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61593	104639	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61594	104640	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61584	104630	7	2012-08-02 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 27/12/2012	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2012-12-27	0
61572	104618	7	2012-07-31 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 20/12/2012	\N		ADENOMECTOMIA	2012-12-20	0
61595	104641	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61596	104642	7	2012-08-06 00:00:00	7		06/08/2012- I.C registra en Gis pero no tiene citacion asignada.	\N			\N	0
61597	104643	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61598	104644	7	2012-08-02 00:00:00	7		02/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asiganda	\N			\N	0
61599	104645	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61600	104646	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61639	104685	7	2012-08-08 00:00:00	13	G	08/08/2012- Paciente asiste a su primer control el 02/08/2012,  para iniciar Estudio.	\N			\N	0
61602	104648	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61603	104649	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61604	104650	7	2012-08-02 00:00:00	7		02/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asiganda	\N			\N	0
61605	104651	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61607	104653	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61608	104654	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61609	104655	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61610	104656	7	2012-08-02 00:00:00	7		02/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asiganda	\N			\N	0
61611	104657	7	2012-08-03 00:00:00	10		Hospitalizacion 14/08/2012   Exs y Eco OK. EKG 16/08. Enf 17/08	\N		intervención quir. integral cataratas	\N	0
61613	104659	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61614	104660	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61615	104661	7	2012-07-31 00:00:00	7		31/07/2012- No registra O.A en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61617	104663	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61618	104664	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61619	104665	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61620	104666	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61621	104667	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61622	104668	7	2012-07-31 00:00:00	7		Paciente  con caso  en sigges  se debe enviar a compras	\N			\N	0
61623	104669	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61624	104670	7	2012-08-03 00:00:00	10		Hospitalizacion 14/08/2012   EXS, Eco y EKG 16/08. Enf 17/08	\N		intervención quir. integral cataratas	\N	0
61625	104671	7	2012-08-02 00:00:00	7		02/08/2012- I.c registre en Gis pero no tiene citacion signada,	\N			\N	0
61626	104672	7	2012-08-02 00:00:00	10		Hospitalizacion 14/08/2012   EXS, Eco y EKG 16/08. Enf 17/08	\N		intervención quir. integral cataratas	\N	0
61627	104673	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61628	104674	7	2012-08-02 00:00:00	7		02/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asiganda	\N			\N	0
61629	104675	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61630	104676	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61631	104677	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61632	104678	7	2012-08-02 00:00:00	1		OFT.CATARATA-HDGF	\N			2012-10-25	0
61634	104680	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61635	104681	7	2012-08-02 00:00:00	7		Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61636	104682	7	2012-08-02 00:00:00	7		02/08/2012- I.c registre en Gis pero no tiene citacion signada,	\N			\N	0
61637	104683	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61638	104684	7	2012-08-07 00:00:00	7		Paciente  con caso de vicios en  sigges se  debe enviar a compra	\N			\N	0
61640	104686	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61641	104687	7	2012-08-07 00:00:00	7		07/08/2012- No registra I.C en Gis por lo cual no tiene  citacion asignada	\N			\N	0
61642	104688	7	2012-08-07 00:00:00	10		Hospitalizacion 14/08/2012   Exs y EKG 20/08. Eco 21/08, Enf 22/08	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61643	104689	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61644	104690	7	2012-08-03 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61645	104691	7	2012-08-07 00:00:00	10		Hospitalizacion 14/08/2012   Exs y Eco OK. EKG 16/08. Enf 17/08	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61646	104692	7	2012-08-06 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compras.	\N			\N	0
61647	104693	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61616	104662	7	2012-08-02 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 27/12/2012	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2012-12-27	0
61633	104679	7	2012-08-03 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 27/12/2012	\N		ADENOMECTOMIA TU	2012-12-27	0
61606	104652	7	2012-07-31 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 20/12/2012	\N		ADENOMA O CANCER PROSTATICO, RESECCION ENDOSCOPICA	2012-12-20	0
61648	104694	7	2012-08-07 00:00:00	10		Hospitalizacion 14/08/2012   Exs y EKG 20/08. Eco 21/08, Enf 22/08	\N		intervención quir. integral cataratas	\N	0
61649	104695	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61650	104696	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61651	104697	7	2012-08-06 00:00:00	7		06/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61652	104698	7	2012-08-07 00:00:00	10		Hospitalizacion 14/08/2012   Exs y EKG 20/08. Eco 21/08, Enf 22/08	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61653	104699	7	2012-08-06 00:00:00	7		06/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61654	104700	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61655	104701	7	2012-08-03 00:00:00	7		03/08/2012- No registra I.c en Gis, por lo cual no tiene hora asignada	\N			\N	0
61656	104702	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61657	104703	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61658	104704	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61659	104705	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61660	104706	7	2012-08-07 00:00:00	7		Paciente  con caso de vicios en  sigges se  debe enviar a compra	\N			\N	0
61661	104707	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61662	104708	7	2012-08-06 00:00:00	1		CITADO EL 14/09/2012 A OFT.NUEVOS AD-HDGF	\N			2012-09-14	0
61663	104709	7	2012-08-14 00:00:00	7		Paciente con caso de de vicios en sigges se debe enviar a compras.	\N			\N	0
61665	104711	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61666	104712	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61667	104713	7	2012-08-08 00:00:00	10		Hospitalizacion 14/08/2012   Exs y EKG 20/08. Eco 21/08, Enf 22/08	\N		intervención quir. integral cataratas	\N	0
61668	104714	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61670	104716	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61671	104717	7	2012-08-07 00:00:00	7		07/08/2012- No registra I.C en Gis por lo cual no tiene  citacion asignada	\N			\N	0
61672	104718	7	2012-08-27 00:00:00	7		27/08/2012- I.C registra en Gis, pero sin citacion asignada	\N			\N	0
61673	104719	7	2012-06-12 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-12-20		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61674	104720	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61675	104721	7	2012-08-17 00:00:00	7		17/08/2012- No registra I.c en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61676	104722	7	2012-08-16 00:00:00	10		hospitalizacion 22/8/2012:   Exs y EKG 27/08, Eco 28/08 y Enf 29/08	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61677	104723	7	2012-08-14 00:00:00	1		citado para OFT.NUEVOS AD-HDGF	\N			2012-09-05	0
61678	104724	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  20/09/2012 DRA PINO	\N			2012-09-20	0
61679	104725	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61680	104726	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61681	104727	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61682	104728	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61683	104729	7	2012-08-07 00:00:00	7		07/08/2012- No registra I.C en Gis por lo cual no tiene  citacion asignada	\N			\N	0
61684	104730	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61685	104731	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61686	104732	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61687	104733	7	2012-06-13 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-01-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61688	104734	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  11/09/2012 DRA PINO	\N			2012-09-11	0
61689	104735	7	2012-08-14 00:00:00	7		Paciente con caso de de vicios en sigges se debe enviar a compras.	\N			\N	0
61690	104736	7	2012-08-10 00:00:00	7		10/08/2012-I.C registra en Gis, pero no  tiene citacion asignada.	\N			\N	0
61691	104737	7	2012-08-10 00:00:00	10		10/08/2012- O.A registra en Lec. Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61692	104738	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61693	104739	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61694	104740	7	2012-06-08 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-10-30		sin orden de atención	\N	0
61695	104741	7	2012-08-07 00:00:00	7		Paciente  con caso de vicios en  sigges se  debe enviar a compra	\N			\N	0
61696	104742	7	2012-06-12 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-12-30		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61697	104743	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61698	104744	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61699	104745	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61669	104715	7	2012-08-07 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 03/01/2013	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-03	0
61700	104746	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61701	104747	7	2012-08-14 00:00:00	7		Paciente con caso de de vicios en sigges se debe enviar a compras.	\N			\N	0
61735	104781	7	2012-08-13 00:00:00	13	G	13/08/2012- Paciente asiste a su primer control a Psiquiatria el 09/08/2012, estara en Estudio seguin indique especialista	\N			\N	0
61703	104749	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61704	104750	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61705	104751	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61706	104752	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  05/09/2012 DR TRINCADO	\N			2012-09-05	0
61707	104753	7	2012-08-08 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61708	104754	7	2012-08-13 00:00:00	1		admision 14/08/2012: Citado para el  05/09/2012 DR TRINCADO	\N			2012-09-05	0
61709	104755	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61710	104756	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61711	104757	7	2012-08-16 00:00:00	10		hospitalizacion 22/8/2012:   Exs, Eco y EKG 23/08, Enf 24/08/12	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61712	104758	7	2012-08-07 00:00:00	7		Paciente  con caso de vicios en  sigges se  debe enviar a compra	\N			\N	0
61713	104759	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61714	104760	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61715	104761	7	2012-08-10 00:00:00	10		10/08/2012- O.A registra en Lec. Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61716	104762	7	2012-08-13 00:00:00	7		13/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61717	104763	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61718	104764	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61719	104765	7	2012-08-10 00:00:00	10		10/08/2012- O.A registra en Lec. Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61720	104766	7	2012-06-12 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 28/09/2012 R.CLINICA	2012-12-30		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61721	104767	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61722	104768	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61723	104769	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61725	104771	7	2012-08-13 00:00:00	10		13/08/2012- Catarata catarata ojo Izq, confirmado en Clinica ISV.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61726	104772	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61727	104773	7	2012-08-09 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61728	104774	7	2012-08-08 00:00:00	7		08/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61729	104775	7	2012-06-12 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2012-12-30			\N	0
61730	104776	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61731	104777	7	2012-08-16 00:00:00	10		hospitalizacion 22/8/2012:   Exs, Eco y EKG 23/08, Enf 27/08	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61732	104778	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61733	104779	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61734	104780	7	2012-08-20 00:00:00	1		20/08/2012/ Se habla con la mamá de menor e informa que vino a hora del 20/08, que DR. Le indica ecocadiograma, le da nueva citación para el 04/09/2012	2012-09-04			2012-09-04	0
61736	104782	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		intervención quir. integral cataratas	\N	0
61737	104783	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61738	104784	7	2012-08-14 00:00:00	7		Paciente con caso de de vicios en sigges se debe enviar a compras.	\N			\N	0
61739	104785	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61740	104786	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61741	104787	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61742	104788	7	2012-08-10 00:00:00	7		10/08/2012-I.C registra en Gis, pero no  tiene citacion asignada.	\N			\N	0
61743	104789	7	2012-08-14 00:00:00	7		14/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61744	104790	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61745	104791	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		intervención quir. integral cataratas	\N	0
61746	104792	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61747	104793	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61748	104794	7	2012-08-10 00:00:00	7		10/08/2012-I.C registra en Gis, pero no  tiene citacion asignada.	\N			\N	0
61749	104795	7	2012-08-10 00:00:00	7		10/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61702	104748	7	2012-08-08 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 10/01/2013	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-10	0
61750	104796	7	2012-08-13 00:00:00	7		13/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61751	104797	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		intervención quir. integral cataratas	\N	0
61752	104798	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61753	104799	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61754	104800	7	2012-08-10 00:00:00	7		Paciente con caso creado en sigges , se debe enviar a compra	\N			\N	0
61755	104801	7	2012-08-13 00:00:00	7		13/08/2012- I.C registra en Gis, pero no tiene citacion asignada	\N			\N	0
61756	104802	7	2012-08-13 00:00:00	10		13/08/2012- O.A registra en Gis sin Prioridad de atencion.	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61757	104803	7	2012-08-10 00:00:00	1		citado para OFT.NUEVOS AD-HDGF	\N			2012-09-27	0
61758	104804	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61759	104805	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61760	104806	7	2012-08-17 00:00:00	7		17/08/2012- No registra I.c en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61761	104807	7	2012-08-13 00:00:00	7		13/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61762	104808	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61763	104809	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61764	104810	7	2012-08-13 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61765	104811	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61766	104812	7	2012-08-14 00:00:00	7		14/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61767	104813	7	2012-08-14 00:00:00	7		14/08/2012- I.C registra en Gis pero no tiene citacion asignada	\N			\N	0
61768	104814	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61769	104815	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61770	104816	7	2012-08-21 00:00:00	10		21/08/2012- O.A no registra en Lec,	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61771	104817	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61772	104818	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61773	104819	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61774	104820	7	2012-06-14 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2012-12-25		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61775	104821	7	2012-08-20 00:00:00	7		20/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61776	104822	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61777	104823	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61778	104824	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61780	104826	7	2012-08-29 00:00:00	8	A	29/08/2012/ Paciente NSP el 27/08/2012. Se enviará a UGAA para dación de nueva hora	\N			2012-08-27	0
61783	104829	7	2012-08-14 00:00:00	7		Paciente con caso de de vicios en sigges se debe enviar a compras.	\N			\N	0
61784	104830	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61785	104831	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61786	104832	7	2012-08-14 00:00:00	7		14/08/2012- I.C registra en Gis pero no tiene citacion asignada	\N			\N	0
61787	104833	7	2012-08-16 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61788	104834	7	2012-08-14 00:00:00	7		14/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61790	104836	7	2012-06-19 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2012-11-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61779	104825	7	2012-08-29 00:00:00	45	G	Paciente dado de Alta. Control en 1 año. Aun no llega IPD a Secretaria GES	\N		IPD	2012-08-27	0
61792	104838	7	2012-08-14 00:00:00	7		14/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61793	104839	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61795	104841	7	2012-06-19 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2013-01-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61796	104842	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61797	104843	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61798	104844	7	2012-08-16 00:00:00	7		16/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada	\N			\N	0
61799	104845	7	2012-08-20 00:00:00	7		20/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61800	104846	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61782	104828	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 31/01/2013 PROCESO DE EXAMEN	\N		RTU PROSTATA	2013-01-31	0
61789	104835	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 31/01/2013 PROCESO DE EXAMEN	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-31	0
61781	104827	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 04/01/2013 PROCESO DE EXAMEN	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-04	0
61801	104847	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61802	104848	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61803	104849	7	2012-08-20 00:00:00	10		20/08/2012- Catarata unilateral, O.A no registra en Gis.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61804	104850	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61805	104851	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61806	104852	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61807	104853	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61808	104854	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61809	104855	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61810	104856	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61811	104857	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61812	104858	7	2012-08-21 00:00:00	10		21/08/2012- O.A registra en Lec, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61813	104859	7	2012-08-21 00:00:00	10		21/08/2012- O.A registra en Lec, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61814	104860	7	2012-08-21 00:00:00	10		21/08/2012- O.A registra en Lec, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61815	104861	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61816	104862	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61817	104863	7	2012-08-29 00:00:00	10		29/08/2012- O.A registra en Lec. Sin Prioridad de Atencion	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61818	104864	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61819	104865	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61820	104866	7	2012-08-17 00:00:00	7		Paciente con caso de vicios SIGGES se debe enviar a compra.	\N			\N	0
61821	104867	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61822	104868	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61823	104869	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61824	104870	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		intervención quir. integral cataratas	\N	0
61825	104871	7	2012-08-20 00:00:00	10		20/08/2012- O.A registra en Gis, Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61827	104873	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61828	104874	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61829	104875	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61830	104876	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61831	104877	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61832	104878	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61833	104879	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61834	104880	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61835	104881	7	2012-06-21 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2012-01-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61836	104882	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61837	104883	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61838	104884	7	2012-08-20 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61839	104885	7	2012-08-21 00:00:00	7		21/08/2012- I.C registra en Gis pero no tiene atencion asignada	\N			\N	0
61840	104886	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61841	104887	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61842	104888	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61843	104889	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61844	104890	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61845	104891	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61846	104892	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61847	104893	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61848	104894	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61849	104895	7	2012-06-22 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 05/10/2012 R.CLINICA	2012-08-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61850	104896	7	2012-08-27 00:00:00	7		Pac con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61851	104897	7	2012-08-24 00:00:00	7		24/08/2012- I.C	\N			\N	0
61853	104899	7	2012-08-24 00:00:00	7		24/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61854	104900	7	2012-08-24 00:00:00	7		24/08/2012- I.C	\N			\N	0
61855	104901	7	2012-08-24 00:00:00	7		24/08/2012- I.C	\N			\N	0
61856	104902	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61857	104903	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61858	104904	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61859	104905	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61860	104906	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61861	104907	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61862	104908	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61863	104909	7	2012-04-19 00:00:00	7		19/04/2012- inicio de Estudio Pre-Tx Renal, requiere control con Psiquiatra para poder continuar con estudio.	\N		Psiquiatria	\N	0
61864	104910	7	2012-06-27 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	2012-01-08		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61865	104911	7	2012-06-27 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	2012-01-08		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61866	104912	7	2012-08-21 00:00:00	7		21/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61867	104913	7	2012-08-21 00:00:00	7		21/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61868	104914	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61869	104915	7	2012-08-22 00:00:00	7		22/08/2012- (En Gis hay una I.c con misma fecha pero derivando a Glaucoma) No registra I.C en Gid, por lo cual no tiene citacion asignada	\N		Oftalmología	\N	0
61870	104916	7	2012-08-22 00:00:00	10		22/08/2012- O.A registra en Lec. Sin Prioridad de atencion	\N		FACOÉRESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (NO INCLUYEEL VALOR DE LA PRÓTESIS)	\N	0
61871	104917	7	2012-08-22 00:00:00	10		22/08/2012/ Paciente a la espera del tto. OA no ingresada en LEC del Gis. IPD del Sigges aparece "ADENOMECTOMIA "	\N		ADENOMECTOMIA	\N	0
61872	104918	7	2012-08-22 00:00:00	10		22/08/2012/ Paciente a la espera del tto. OA no ingresada en LEC del Gis. IPD del Sigges aparece "ADENOMECTOMIA "	\N		ADENOMECTOMIA	\N	0
61873	104919	7	2012-08-22 00:00:00	10		22/08/2012/ Paciente a la espera del tto. OA no ingresada en LEC del Gis. IPD del Sigges aparece "RTU PROSTATA  "	\N		RTU PROSTATA	\N	0
61874	104920	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61875	104921	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61876	104922	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61877	104923	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61878	104924	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61879	104925	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61880	104926	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61881	104927	7	2012-08-22 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61882	104928	7	2012-08-23 00:00:00	7		23/08/2012/ Paciente no registra hora en el Gis. IC no registrada en GIS	\N			\N	0
61883	104929	7	2012-08-23 00:00:00	7		23/08/2012- No registra I.C en Gis. Por lo cual no tiene citacion signada,	\N			\N	0
61884	104930	7	2012-08-23 00:00:00	7		23/08/2012- No registra I.C en Gis. Por lo cual no tiene citacion signada,	\N			\N	0
61885	104931	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61886	104932	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61887	104933	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61888	104934	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61889	104935	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61890	104936	7	2012-08-23 00:00:00	7		Paciente en caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61891	104937	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61892	104938	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61893	104939	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
61894	104940	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
61895	104941	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
61896	104942	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
61897	104943	7	2012-08-27 00:00:00	10		27/08/2012- O.A registra en Lec sin prioridad de atencion.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR (	\N	0
61898	104944	7	2012-08-27 00:00:00	7		Pac con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61899	104945	7	2012-08-27 00:00:00	7		Pac con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61900	104946	7	2012-08-27 00:00:00	7		Pac con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61901	104947	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61902	104948	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61903	104949	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61904	104950	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61905	104951	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61906	104952	7	2012-08-24 00:00:00	7		24/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61907	104953	7	2012-08-24 00:00:00	7		24/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
61908	104954	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61909	104955	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61910	104956	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61911	104957	7	2012-08-24 00:00:00	7		Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61912	104958	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61913	104959	7	2012-08-27 00:00:00	7		Pac con caso de vicios en SIGGES se debe enviar a compras.	\N			\N	0
61914	104960	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61915	104961	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61916	104962	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61917	104963	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61918	104964	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61919	104965	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61920	104966	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61921	104967	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61922	104968	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61923	104969	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61924	104970	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61925	104971	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61926	104972	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61927	104973	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61928	104974	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61929	104975	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61930	104976	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61931	104977	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61932	104978	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61933	104979	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61934	104980	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61935	104981	7	2012-08-27 00:00:00	7		27/08/2012- No registra I.C en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61936	104982	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61937	104983	7	2012-07-03 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	2012-09-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61938	104984	7	2012-07-04 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	2012-01-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61939	104985	7	2012-08-29 00:00:00	7		29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
61940	104986	7	2012-08-29 00:00:00	10		29/08/2012- Caso confimado, pero O.A no registra en Lec.	\N		FACOERESIS EXTRACAPSULAR CON IMPLANTE DE LENTE INTRAOCULAR	\N	0
61941	104987	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61942	104988	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61943	104989	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61944	104990	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61945	104991	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61946	104992	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61947	104993	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61948	104994	7	2012-08-29 00:00:00	7		29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
61949	104995	7	2012-08-29 00:00:00	10		29/08/2012/ Paciente a la espera del tto. OA no ingresada en LEC del GIS. IPD del SIGGES sale en tto. "RTU "	\N		RTU	\N	0
61950	104996	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61951	104997	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61952	104998	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61953	104999	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61954	105000	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61955	105001	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61956	105002	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61957	105003	7	2012-08-29 00:00:00	7		Paciente con caso de vicios en sigges se debe enviar a compra.	\N			\N	0
61958	105004	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61959	105005	7	2012-07-06 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 12/10/2012 R.CLINICA	2012-08-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61960	105006	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61961	105007	7	2012-05-07 00:00:00	7		07/05/2012- Paciente inicia Estudio Pre-Tx. Requiere control con Psiquiatra para el Pase y continuar con otros examenes	\N		Psiquiatria	\N	0
61962	105008	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61963	105009	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61964	105010	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61965	105011	7	2012-07-10 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61966	105012	7	2012-05-15 00:00:00	7		15/04/2012- Paciente inicia estudio Pre.Tx,  debe ser citado a Psiquiatria para poder continuar su estudio.	\N		Psiquiatria	\N	0
61967	105013	7	2012-07-17 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 19/10/2012 R.CLINICA	2012-02-10		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61968	105014	7	2012-07-18 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-02-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61969	105015	7	2012-07-17 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-02-10		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61970	105016	7	2012-07-18 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-02-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61971	105017	7	2012-07-18 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-02-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61972	105018	7	2012-05-18 00:00:00	7		18/05/2012- Paciente inicia Estudio Pre-Tx debe ser atendido por Psiquiatra, para poder continuar con Estudio.	\N		Psiquiatria	\N	0
61973	105019	7	2012-07-23 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-01-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61974	105020	7	2012-07-20 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 09/11/2012 R.CLINICA	2012-01-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61975	105021	7	2012-07-23 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	2012-01-15			\N	0
61976	105022	7	2012-07-25 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61977	105023	7	2012-07-25 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61978	105024	7	2012-08-01 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	\N			\N	0
61979	105025	7	2012-08-01 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	\N			\N	0
61980	105026	7	2012-06-01 00:00:00	7		01/06/2012- Paciente debe ser citado a Psiquiatria para poder continuar su Estudio Pre.Tx	\N		Psiquiatria	\N	0
61981	105027	7	2012-05-29 00:00:00	7		29/05/2012- Paciente inicia Estudio Pre-Trasplante, por lo cual debe ser citado a Psiquiatria para poder continuar con su estudio.	\N		Psiquiatria	\N	0
61982	105028	7	2012-08-07 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 16/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61983	105029	7	2012-08-09 00:00:00	10		Hospitalizacion 14/08/2012 Pendiente 23/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61984	105030	7	2012-08-08 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 23/11/2012 R.CLINICA	2012-11-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61985	105031	7	2012-08-09 00:00:00	10		Hospitalizacion 14/08/2012 Pendiente 23/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61986	105032	7	2012-08-10 00:00:00	10		(10/08/2012) Paciente a la espera de IQ .	2012-03-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61987	105033	7	2012-08-10 00:00:00	10		(10/08/2012) Paciente a la espera de IQ .	2012-03-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61988	105034	7	2012-08-13 00:00:00	10		(13/08/2012) Paciente a la espera de IQ , con OH , sin registro de IQ .	2012-02-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61989	105035	7	2012-08-16 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 23/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61990	105036	7	2012-08-13 00:00:00	10		(13/08/2012) Paciente a la espera de IQ , con OH , sin registro de IQ .	2012-02-05		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61991	105037	7	2012-08-16 00:00:00	10		hospitalizacion 22/8/2012: Pendiente 23/11/2012 R.CLINICA	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61992	105038	7	2012-08-17 00:00:00	10		(17/08/2012) Paciente a la espera de IQ , con orden de hospitalizacion	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61993	105039	7	2012-08-17 00:00:00	10		(17/08/2012) Paciente a la espera de IQ , con orden de hospitalizacion	\N			\N	0
61994	105040	7	2012-08-21 00:00:00	10		(21/08/2012) Paciente a la espera de IQ .	2012-12-06		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61995	105041	7	2012-08-23 00:00:00	10		hospitalizacion 28/8/2012: Pendiente  R.CLINICA 07/09/2012	\N		sin orden de atencion	\N	0
61996	105042	7	2012-08-29 00:00:00	10		(29/08/2012) Paciente a la espera de IQ .	2012-12-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61997	105043	7	2012-06-28 00:00:00	7		28/06/2012- No registra I.C en Gis. Por lo cual no registra asistencia.	\N		Psiquiatria	\N	0
61998	105044	7	2012-08-23 00:00:00	7		23/08/2012- Paciente inicia Estudio Pre-Tx, por lo cual debe ser evaluado por Psiquiatra para asi poder continuar examenes	\N		Psiquiatria	\N	0
61999	105045	7	2012-08-23 00:00:00	7		23/08/2012- Paciente inicia Estudio Pre-Tx, por lo cual debe ser evaluado por Psiquiatra para asi poder continuar examenes	\N		Psiquiatria	\N	0
62000	105046	7	2012-08-23 00:00:00	7		23/08/2012- Paciente inicia Estudio Pre-Tx, por lo cual debe ser evaluado por Psiquiatra para asi poder continuar examenes	\N		Psiquiatria	\N	0
62001	105047	7	2012-07-27 00:00:00	1		27/07/2012/ Paciente registra hora en el Gis para el 03/10/2012	2012-10-03			2012-10-03	0
62002	105048	7	2012-08-17 00:00:00	7		17/08/2012/ Paciente no registra hora en el GIS. IC no registrada en Gis.	\N			\N	0
59803	102844	7	2012-08-29 00:00:00	29	F	hospitalizacion 28/8/2012: Operado el 28/08/2012 segundo ojo	\N		FAP	2012-08-28	0
59794	102835	7	2012-08-29 00:00:00	1	B	(29/08/2012)  Paciente citado el 03/09/2012 a cintigrama, se habla con secretaria de servicio de med nuclear para poder adelantar hora informa que hay que hablar con dr jefe servicio.	2012-09-05		cintigrama oseo	2012-09-03	0
59978	103019	7	2012-08-29 00:00:00	1	B	29/08/2012 Pac citada para el 20/09/2012 con dr bergh, se pide adelantamiento de hora	2012-09-20			2012-09-20	0
60058	103099	7	2012-08-29 00:00:00	1	B	29/08/2012 Pac citada para el 20/09/2012 con dr bergh, se pide adelantamiento de hora	2012-09-20			2012-09-20	0
60390	103434	7	2012-08-29 00:00:00	1	B	admision 29/08/2012: Citado para el  04/10/2012	\N		Endocrinología	2012-10-04	0
60846	103892	7	2012-08-29 00:00:00	7	U	En llamado del dia 29-08-2012 al fono 82471194 familiar refiere que paciente aun esta hospitalizado en HGF, solicita atencion pero en un tiempo mas	\N			\N	0
61204	104250	7	2012-06-20 00:00:00	7	U	20/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61206	104252	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61207	104253	7	2012-06-18 00:00:00	7	U	18/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61209	104255	7	2012-06-18 00:00:00	7	U	18/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61210	104256	7	2012-06-19 00:00:00	7	U	19/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61211	104257	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61213	104259	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61215	104261	7	2012-06-18 00:00:00	7	U	18/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61222	104268	7	2012-06-20 00:00:00	7	U	20/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61226	104272	7	2012-06-22 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61227	104273	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61228	104274	7	2012-06-22 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61229	104275	7	2012-06-25 00:00:00	7	U	25/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61230	104276	7	2012-06-21 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61231	104277	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61233	104279	7	2012-06-21 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61235	104281	7	2012-06-20 00:00:00	7	U	20/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61236	104282	7	2012-06-20 00:00:00	7	U	20/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61242	104288	7	2012-06-20 00:00:00	7	U	20/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61244	104290	7	2012-06-22 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61245	104291	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61246	104292	7	2012-06-22 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61247	104293	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61249	104295	7	2012-06-21 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61250	104296	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61251	104297	7	2012-06-21 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61252	104298	7	2012-06-21 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61263	104309	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61265	104311	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61271	104317	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61272	104318	7	2012-06-25 00:00:00	7	U	25/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61273	104319	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61275	104321	7	2012-06-25 00:00:00	7	U	25/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61276	104322	7	2012-06-25 00:00:00	7	U	25/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61277	104323	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61285	104331	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61295	104341	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61296	104342	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61298	104344	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61301	104347	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61303	104349	7	2012-06-26 00:00:00	7	U	26/06/2012 Paciente no ha sido enviado a compras	\N			\N	0
61305	104351	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61307	104353	7	2012-07-18 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61308	104354	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61309	104355	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61312	104358	7	2012-06-27 00:00:00	7	U	27/06/2012 Paciente no ha sido enviado a compra	\N			\N	0
61314	104360	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
59831	102872	7	2012-08-29 00:00:00	1	G	admision 29/08/2012: Citado para el  07/09/2012	\N		BET	2012-09-07	0
61317	104363	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61322	104368	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61324	104370	7	2012-08-02 00:00:00	7	U	Paciente   con caso de vicios  en sigges se debe  enviar  a compra	\N			\N	0
61327	104373	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61328	104374	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61330	104376	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61335	104381	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61336	104382	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61338	104384	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61348	104394	7	2012-07-06 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61349	104395	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61350	104396	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61352	104398	7	2012-07-05 00:00:00	7	U	05/07/2012 Paciente no ha sido enviado a compra	\N			\N	0
61353	104399	7	2012-07-05 00:00:00	7	U	05/07/2012 Paciente no ha sido enviado a compra	\N			\N	0
61356	104402	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61359	104405	7	2012-07-05 00:00:00	7	U	05/07/2012 Paciente no ha sido enviado a compra	\N			\N	0
61361	104407	7	2012-07-06 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61364	104410	7	2012-07-05 00:00:00	7	U	05/07/2012 Paciente no ha sido enviado a compra	\N			\N	0
61365	104411	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61368	104414	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61371	104417	7	2012-07-10 00:00:00	7	U	Paciente en llamado del dia 10-07-2012 responde en espera se gestionara hora en cuanto Isv mande agenda	\N			\N	0
61373	104419	7	2012-07-06 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
61374	104420	7	2012-07-09 00:00:00	7	U	Paciente con caso de vicio en sigges se debe enviar a compra	\N			\N	0
59872	102913	7	2012-08-29 00:00:00	7	A	admision 29/08/2012: HORAS LAS ASIGNA LA NEO	\N			\N	0
59964	103005	7	2012-08-24 00:00:00	7	A	24/08/2012- No registra I.C en Gis, por lo cual no tiene citacion asignada.	\N			\N	0
60078	103119	7	2012-08-29 00:00:00	7	A	29/08/2012/ Paciente no registra hora en el GIS. IC no registrada en Gis	\N			\N	0
60211	103252	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60234	103275	7	2012-08-29 00:00:00	7	A	admision 29/08/2012: SE imprime sic se envia auditoria	\N			\N	0
60285	103326	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N			\N	0
60388	103432	7	2012-08-29 00:00:00	7	A	29/08/2012/ Paciente no registra hora en el GIS. IC no registrada en Gis	\N			\N	0
60441	103485	7	2012-08-27 00:00:00	7	A	27/08/2012- I.C registra en Gis, pero sin citacion asignada	\N			\N	0
60444	103488	7	2012-08-29 00:00:00	7	A	29/08/2012/ Paciente no registra hora en el GIS. IC no registrada en Gis	\N			\N	0
60445	103489	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N			\N	0
60515	103559	7	2012-08-29 00:00:00	7	A	(29/08/2012) Paciente sin citaciones a especialista a la fecha	2012-09-05			\N	0
60543	103587	7	2012-08-29 00:00:00	7	A	29/08/2012 se llama a pac y se habla con sra quien indica que el día de mañana 30/08/2012 se acercara a med nuclear de hgf a pedir la hora del cintigrama oseo	2012-08-30			\N	0
60695	103741	7	2012-08-27 00:00:00	7	A	27/08/2012- Citacion eliminada del 01/08 y 28/08/2012	\N			\N	0
60700	103746	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N		Med.Acc.Vascular	\N	0
60753	103799	7	2012-08-28 00:00:00	7	A	ADMISION 28/08/2012:  AUDITORIA	\N		Diabetes	\N	0
60755	103801	7	2012-08-27 00:00:00	7	A	(27/08/2012) Paciente con BIOPSIA y CINTIGRAMA tomado  , no tiene citaciones a epecialista  para determinar IQ .	2012-08-25		ESPECIALISTA	\N	0
60869	103915	7	2012-08-24 00:00:00	7	A	(24/08/2012) Paciente sin citaciones a especialista a la fecha .	2012-10-15			\N	0
60907	103953	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N		Oftalmología	\N	0
60962	104008	7	2012-08-29 00:00:00	7	A	29/08/2012- I.c registra en gis, pero no tiene citacion asiganada	\N		Oftalmología	\N	0
61024	104070	7	2012-08-29 00:00:00	7	A	29/08/2012- No registra I.c en Gis por lo cual no tiene citacion asignada.	\N		Cardiología	\N	0
61149	104195	7	2012-08-29 00:00:00	7	A	27/08/2012- Enfermera informa que cita a paciente el 21/08 y el indica que el dia 27/06  no lo atendio Psiquiatra, por lo cual se solicitara que le asignen hora lo antes posible, para poder continuar con estudio	\N		psiquiatria	\N	0
59848	102889	7	2012-08-27 00:00:00	10	J	27/08/2012 hospitalizacion indicó que pac se operaba el 24/08/2012, se llama a pac se habla con hermana indica que no ha sido llamado para la iq	\N		GASTRECTOMIA TOTAL	\N	0
59878	102919	7	2012-08-27 00:00:00	10	J	27/08/2012/ Informa enf. María paz que paciente esta a la espera de tto, hospitalizada en medicina del 20/08/12. En proceso de exámenes	\N		Quimioterapia	\N	0
59893	102934	7	2012-08-29 00:00:00	10	J	Hospitalizacion 27/06/2012: En Tabla el 24/08/2012, no se opera según gis	\N		acceso vascular simple (mediante fav) para hemodiálisis	\N	0
59963	103004	7	2012-08-29 00:00:00	10	J	Paciente no registra como operado	\N		IMPLANTACION DE MARCAPASO C/ELECTROD. INTRAVEN. O EPICARDICO (INCLUYE EL VALOR DE LA PROTESIS)	\N	0
59998	103039	7	2012-08-17 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Se reevaluará caso	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	\N	0
59999	103040	7	2012-08-24 00:00:00	10	J	24/08/2012/ Se revisa Gis pabellón y no aparece operado el paciente. A la espera del tto	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	\N	0
60072	103113	7	2012-08-22 00:00:00	10	J	hospitalizacion 28/8/2012: En Tabla el 24/08/2012 , no se opera según gis	\N		implantación marcapasos unicameral vvi	\N	0
60227	103268	7	2012-08-29 00:00:00	10	J	29/08/2012/ Paciente a la espera del tto. OA ingresada en LEC del GIS del 23/08/2012	\N		IMPLANTACION DE MARCAPASO C/ELECTROD. INTRAVEN. O EPICARDICO (INCLUYE EL VALOR DE LA PROTESIS	\N	0
60228	103269	7	2012-08-29 00:00:00	10	J	29/08/2012/ Paciente a la espera del tto. OA ingresada en LEC del GIS del 23/08/2012	\N		IMPLANTACION DE MARCAPASO C/ELECTROD. INTRAVEN. O EPICARDICO (INCLUYE EL VALOR DE LA PROTESIS	\N	0
60272	103313	7	2012-08-27 00:00:00	10	J	(27/08/2012) Paciente a la espera de IQ , con orden de hospitalizacion	2012-09-15		GASTRECTOMIA TOTAL	\N	0
60279	103320	7	2012-08-29 00:00:00	10	J	Paciente a la espera de Operación	\N			\N	0
60280	103321	7	2012-08-29 00:00:00	10	J	Paciente a la espera de Operación	\N			\N	0
60296	103337	7	2012-08-29 00:00:00	10	J	29/08/2012/ Paciente a la espera del tto. OA no ingresada en LEC del GIS	\N		CONO-LEEP	\N	0
60297	103338	7	2012-08-29 00:00:00	10	J	29/08/2012/ Paciente a la espera del tto. OA ingresada en LEC del GIS del 23/08/2012	\N		CONIZACION Y/O AMPUTACION DEL CUELLO, DIAGNOSTICA Y/O TERAPEUTICA C/S BIOPSIA	\N	0
60320	103362	7	2012-07-04 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Lista para operar	2012-08-20		colecistectomía por video laparoscopia	\N	0
60389	103433	7	2012-07-10 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  No UCAM	2012-09-10		colecistectomía por video laparoscopia	\N	0
60421	103465	7	2012-08-24 00:00:00	10	J	24/08/2012/ Se revisa Gis pabellón y no aparece operado el paciente. A la espera del tto	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	\N	0
60443	103487	7	2012-08-29 00:00:00	10	J	Paciente aun no operada para saber si requiere Etapificacion	\N			\N	0
60460	103504	7	2012-07-18 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  29/08/12 Poli Enf UCAM	2012-09-15		colecistectomía por video laparoscopia	\N	0
60487	103531	7	2012-08-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  NCF: 27/08 - 28/08	2012-09-15		colecistectomía por video laparoscopia	\N	0
60490	103534	7	2012-07-19 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Pendiente Pase Anestesista	2012-09-15		COLECISTECTOMIA POR VIDEOLAPAROSCOPIA, PROC. COMPLETO	\N	0
60492	103536	7	2012-08-24 00:00:00	10	J	24/08/2012/ Se revisa Gis pabellón y no aparece operado el paciente. A la espera del tto	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	\N	0
60493	103537	7	2012-02-28 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60517	103561	7	2012-08-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Enf. UCAM 0269/09/12	2012-09-15		colecistectomía por video laparoscopia	\N	0
60518	103562	7	2012-07-23 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Dr Oyarce 30/08/2012	2012-09-15		colecistectomía por video laparoscopia	\N	0
60553	103597	7	2012-08-02 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Dra. Baeza solicitó nuevos exs. Tomados 29/08/12	\N		colecistectomía por video laparoscopia	\N	0
60613	103659	7	2012-08-10 00:00:00	10	J	hospitalizacion 22/8/2012: En Tabla el 12/10/2012, se cancela, no hay oferta	\N		TUNELIZADO	\N	0
60617	103663	7	2012-08-09 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  EKG y Exs 03/09/12. NSP 27/08/12	2012-09-05		colecistectomía por video laparoscopia	\N	0
60660	103706	7	2012-08-07 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Dr Oyarce 30/08/2012	2012-08-30		colecistectomía por video laparoscopia	\N	0
60681	103727	7	2012-08-09 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Enf. UCAM 30/08/2012	2012-09-05		colecistectomía por video laparoscopia	\N	0
60683	103729	7	2012-08-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Enf. UCAM 30/08/2012	2012-09-15		colecistectomía por video laparoscopia	\N	0
60709	103755	7	2012-08-20 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60711	103757	7	2012-03-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60736	103782	7	2012-08-07 00:00:00	10	J	hospitalizacion 28/8/2012: Se informa a jefe servicio para programacion IQ  PROCESO DE EXAMEN	2012-08-30		TRAT.QUIR.RADICAL	\N	0
60744	103790	7	2012-08-13 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  EKG y Exs 03/09/12. NSP 27/08/12	\N		colecistectomía por video laparoscopia	\N	0
60745	103791	7	2012-08-13 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Enf. UCAM 29/08/2012	\N		colecistectomía por video laparoscopia	\N	0
60746	103792	7	2012-08-16 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Dr Oyarce 30/08/2012	\N		colecistectomía por video laparoscopia	\N	0
60747	103793	7	2012-08-13 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  Poli Dr Oyarce 30/08/2012	\N		colecistectomía por video laparoscopia	\N	0
60775	103821	7	2012-07-20 00:00:00	10	J	hospitalizacion 28/8/2012: Se informa a jefe servicio para programacion IQ  PROCESO DE EXAMEN	\N		PROTOTECTOMIA RADICAL	\N	0
60870	103916	7	2012-03-30 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60878	103924	7	2012-03-26 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60909	103955	7	2012-08-29 00:00:00	10	J	(29/08/2012) Paciente a la espera de tto  , sin IQ y orden de hospitalizacion	2012-10-05		COLECISTECTOMIA POR VIDEOLAPAROSCOPIA, PROC. COMPLETO	\N	0
60910	103956	7	2012-08-29 00:00:00	10	J	(29/08/2012) Paciente a la espera de tto  , sin IQ y orden de hospitalizacion	2012-10-05		COLECISTECTOMIA POR VIDEOLAPAROSCOPIA, PROC. COMPLETO	\N	0
60968	104014	7	2011-05-14 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	2012-10-15		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60985	104031	7	2012-03-30 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
61101	104147	7	2012-04-11 00:00:00	10	J	hospitalizacion 28/8/2012: Pendiente  POR MEDICO EN CONGRESO	\N		ENDOPROTESIS TOTAL DE CADERA (INCLUYE PROTESIS)	\N	0
60150	103191	7	2012-08-01 00:00:00	21	D	hospitalizacion 08/8/2012: En Tabla OD 15/08/12 OI 12/09/12	\N		Segundo ojo	2012-09-12	0
60151	103192	7	2012-08-14 00:00:00	21	D	hospitalizacion 28/8/2012: En Tabla el 25/08/2012 OPERADO PRIMER OJO	\N		Segundo ojo	2012-08-25	0
61794	104840	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 07/02/2013 PROCESO DE EXAMEN	\N		ADENOMECTMOIA	2013-02-07	0
61791	104837	7	2012-08-16 00:00:00	14	AB	hospitalizacion 22/8/2012: En Tabla el 31/01/2013 PROCESO DE EXAMEN	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-31	0
61664	104710	7	2012-08-07 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 10/01/2013	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2013-01-10	0
61583	104629	7	2012-08-03 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 03/01/2013	\N		ADENOMA O CANCER PROSTATICO, RESECCION ENDOSCOPICA	2013-01-03	0
61612	104658	7	2012-08-03 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 03/01/2013	\N		ADENOMA O CANCER PROSTATICO, RESECCION ENDOSCOPICA	2013-01-03	0
61724	104770	7	2012-08-09 00:00:00	14	AB	Hospitalizacion 14/08/2012 En Tabla el 10/01/2013	\N		ADENOMA O CANCER PROSTATICO, RESECCION ENDOSCOPICA	2013-01-10	0
61396	104442	7	2012-07-10 00:00:00	14	AB	hospitalizacion 18/07/2012: En Tabla el 06/12/2012 PROC.DE EXAMEN	\N		adenoma o cáncer prostático, resección endoscópica	2012-12-06	0
61530	104576	7	2012-07-27 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 13/12/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-12-13	0
61523	104569	7	2012-07-27 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 13/12/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-12-13	0
61545	104591	7	2012-07-27 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 13/12/2012	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-12-13	0
61601	104647	7	2012-08-01 00:00:00	14	AB	hospitalizacion 08/8/2012: EN TABLA 20/12/2012	\N		ADENOMA PROSTATICO, TRAT. QUIR. CUALQUIER VIA O TECNICA ABIERTA	2012-12-20	0
60556	103600	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para  el 30/08/2012 PROCESO EXS.	2012-08-31		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60559	103603	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para  el 30/08/2012 PROCESO EXS.	2012-08-31		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60562	103606	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para  el 30/08/2012 PROCESO EXS.	2012-08-31		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
60566	103610	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para  el 30/08/2012 PROCESO EXS.	2012-08-31		ADONOMASTECTOMIA	2012-08-30	0
60551	103595	7	2012-08-29 00:00:00	14	AB	29/08/2012/ UGAC informa que paciente esta en Tabla para  el 30/08/2012 PROCESO EXS.	2012-08-31		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-08-30	0
61089	104135	7	2012-06-08 00:00:00	14	AB	Hospitalizacion 27/06/2012: En Tabla el 11/10/2012 PROC.DE EXAMENES	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-10-11	0
61010	104056	7	2012-06-04 00:00:00	14	AB	Hospitalizacion 13/06/2012: En Tabla el 20/09/2012PROCESO DE EXS.	\N		adenoma prostático, trat. quir. cualquier vía o técnica abierta	2012-09-20	0
\.


--
-- Name: lista_dinamica_bandejas_codigo_bandeja_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY lista_dinamica_bandejas
    ADD CONSTRAINT lista_dinamica_bandejas_codigo_bandeja_key PRIMARY KEY (codigo_bandeja);


--
-- Name: monitoreo_ges_mon_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY monitoreo_ges
    ADD CONSTRAINT monitoreo_ges_mon_id_key PRIMARY KEY (mon_id);


--
-- Name: monitoreo_ges_registro_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY monitoreo_ges_registro
    ADD CONSTRAINT monitoreo_ges_registro_pkey PRIMARY KEY (monr_id);


--
-- Name: monitoreo_ges_registro_monr_clase_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX monitoreo_ges_registro_monr_clase_idx ON monitoreo_ges_registro USING btree (monr_clase, monr_subclase);


--
-- Name: monitoreo_ges_registro_monr_estado_idx; Type: INDEX; Schema: public; Owner: postgres; Tablespace: 
--

CREATE INDEX monitoreo_ges_registro_monr_estado_idx ON monitoreo_ges_registro USING btree (monr_estado);


--
-- PostgreSQL database dump complete
--

