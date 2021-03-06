--
-- PostgreSQL database dump
--

-- Started on 2010-06-24 15:13:00 CLT

SET client_encoding = 'LATIN1';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1969 (class 1259 OID 33908)
-- Dependencies: 6
-- Name: especialidades; Type: TABLE; Schema: public; Owner: sistema; Tablespace: 
--

CREATE TABLE especialidades (
    esp_id bigint NOT NULL,
    esp_desc character varying(100),
    esp_codigo_ifl integer,
    esp_padre_id bigint,
    esp_codigo_ifl_usuario character varying(20),
    esp_codigo_int character varying(50)
);


ALTER TABLE public.especialidades OWNER TO sistema;

--
-- TOC entry 1970 (class 1259 OID 33911)
-- Dependencies: 1969 6
-- Name: especialidades_esp_id_seq; Type: SEQUENCE; Schema: public; Owner: sistema
--

CREATE SEQUENCE especialidades_esp_id_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.especialidades_esp_id_seq OWNER TO sistema;

--
-- TOC entry 2427 (class 0 OID 0)
-- Dependencies: 1970
-- Name: especialidades_esp_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: sistema
--

ALTER SEQUENCE especialidades_esp_id_seq OWNED BY especialidades.esp_id;


--
-- TOC entry 2428 (class 0 OID 0)
-- Dependencies: 1970
-- Name: especialidades_esp_id_seq; Type: SEQUENCE SET; Schema: public; Owner: sistema
--

SELECT pg_catalog.setval('especialidades_esp_id_seq', 1495, true);


--
-- TOC entry 2421 (class 2604 OID 34472)
-- Dependencies: 1970 1969
-- Name: esp_id; Type: DEFAULT; Schema: public; Owner: sistema
--

ALTER TABLE especialidades ALTER COLUMN esp_id SET DEFAULT nextval('especialidades_esp_id_seq'::regclass);


--
-- TOC entry 2424 (class 0 OID 33908)
-- Dependencies: 1969
-- Data for Name: especialidades; Type: TABLE DATA; Schema: public; Owner: sistema
--

COPY especialidades (esp_id, esp_desc, esp_codigo_ifl, esp_padre_id, esp_codigo_ifl_usuario, esp_codigo_int) FROM stdin;
444	JJJJJJ	0	0		001
445	CIRUGIA	0	0		0100
446	CIRUGIA INFANTIL	0	0		0200
447	FISURADOS	0	0		0208
448	MEDICINA	0	0		0300
449	PEDIATRIA	0	0		0400
450	TRAUMATOLOGIA ADULTO	0	0		0500
451	TRAUMATOLOGIA INFANTIL	0	0		0600
452	OBST-GINECOLOGIA	0	0		0700
453	OFTALMOLOGIA	0	0		1200
454	UROLOGIA	0	0		1400
455	PSIQUIATRIA	0	0		1500
456	CIRUGIA CARDIO VASCULAR	0	0		1900
457	PERSONAL HOSPITAL	0	0		2000
458	ANESTESIOLOGIA	0	0		2001
459	OTORRINO	0	0		2002
460	OTORRINO	0	0		2003
461	DERMATOLOGIA	0	0		2100
462	NEUROCIRUGIA	0	0		2205
463	FONOAUDIOLOGA	0	0		2206
464	ODONTOLOGIA	0	0		6000
465	FISIATRIA	0	0		7000
466	IMAGENOLOGIA	0	0		7500
467	CIR. CARDIO VASCULAR	0	0		CCV
468	CIR. ADULTO	0	0		CIAD
469	CIR. INFANTIL	0	0		CIIN
470	CIRUGIA ADULTO	0	0		CIR01
471	CUIDADOS INTERMEDIO	0	0		CIR02
472	CUIDADOS MINIMOS	0	0		CUMI
473	GINECOLOGIA	0	0		GIN
474	INCUBADORAS	0	0		INCU
475	INFECCIOSOS	0	0		INFEC
476	LACTANTES	0	0		LACT
477	03 MEDICINA	0	0		MED
478	SEGUNDA INFANCIA	0	0		NEON
479	OBSTETRICIA	0	0		OBGI
480	PEDIATRIA	0	0		PEDI
481	PENSIONADO	0	0		PENS
482	OFTALMOLOGIA	0	0		SOFTA
483	SOTA	0	0		SOTA
484	SOTI	0	0		SOTI
485	TRAUMATOLOGIA ADULTO	0	0		TRAAD
486	TRAUMATOLOGIA INFANTIL	0	0		TRIN
487	UCI ADULTO	0	0		UCAD
488	UCIN CCV	0	0		UCCV
489	UCIM CIRUGIA CARDIOVASCULAR	0	0		UCICA
490	UCI PEDIATRICA	0	0		UCII
491	UCIN MEDICINA	0	0		UCIN
492	UCIN CIRUGIA	0	0		UCINC
493	UCI INDEFERENCIADA	0	0		UCIND
494	UCIN PEDIATRICA	0	0		UCINP
495	UNIDAD EMERGENCIA INFANTIL	0	0		UEI
496	UCI NEO	0	0		UNEO
497	UNIDAD EMERGENCIA ADULTO	0	0		URG01
498	JJJJJJ	0	0		001
499	CIRUGIA	0	0		0100
500	CIRUGIA INFANTIL	0	0		0200
501	FISURADOS	0	0		0208
502	MEDICINA	0	0		0300
503	PEDIATRIA	0	0		0400
504	TRAUMATOLOGIA ADULTO	0	0		0500
505	TRAUMATOLOGIA INFANTIL	0	0		0600
506	OBST-GINECOLOGIA	0	0		0700
507	OFTALMOLOGIA	0	0		1200
508	UROLOGIA	0	0		1400
509	PSIQUIATRIA	0	0		1500
510	CIRUGIA CARDIO VASCULAR	0	0		1900
511	PERSONAL HOSPITAL	0	0		2000
512	ANESTESIOLOGIA	0	0		2001
513	OTORRINO	0	0		2002
514	OTORRINO	0	0		2003
515	DERMATOLOGIA	0	0		2100
516	NEUROCIRUGIA	0	0		2205
517	FONOAUDIOLOGA	0	0		2206
518	ODONTOLOGIA	0	0		6000
519	FISIATRIA	0	0		7000
520	IMAGENOLOGIA	0	0		7500
521	CIR. CARDIO VASCULAR	0	0		CCV
522	CIR. ADULTO	0	0		CIAD
523	CIR. INFANTIL	0	0		CIIN
524	CIRUGIA ADULTO	0	0		CIR01
525	CUIDADOS INTERMEDIO	0	0		CIR02
526	CUIDADOS MINIMOS	0	0		CUMI
527	GINECOLOGIA	0	0		GIN
528	INCUBADORAS	0	0		INCU
529	INFECCIOSOS	0	0		INFEC
530	LACTANTES	0	0		LACT
531	03 MEDICINA	0	0		MED
532	SEGUNDA INFANCIA	0	0		NEON
533	OBSTETRICIA	0	0		OBGI
534	PEDIATRIA	0	0		PEDI
535	PENSIONADO	0	0		PENS
536	OFTALMOLOGIA	0	0		SOFTA
537	SOTA	0	0		SOTA
538	SOTI	0	0		SOTI
539	TRAUMATOLOGIA ADULTO	0	0		TRAAD
540	TRAUMATOLOGIA INFANTIL	0	0		TRIN
541	UCI ADULTO	0	0		UCAD
542	UCIN CCV	0	0		UCCV
543	UCIM CIRUGIA CARDIOVASCULAR	0	0		UCICA
544	UCI PEDIATRICA	0	0		UCII
545	UCIN MEDICINA	0	0		UCIN
546	UCIN CIRUGIA	0	0		UCINC
547	UCI INDEFERENCIADA	0	0		UCIND
548	UCIN PEDIATRICA	0	0		UCINP
549	UNIDAD EMERGENCIA INFANTIL	0	0		UEI
550	UCI NEO	0	0		UNEO
551	UNIDAD EMERGENCIA ADULTO	0	0		URG01
552	MED.HEMOFILIA ADULTOS	0	0		00
553	NEUROCIRUGIA AD.	0	0		000
554	MED.L ESPERA ECOCARDIO	0	0		0001
555	NEUROCIRUGIA INF.	0	0		001
556	MED.H.T.L.V	0	0		003
557	MED.GASTRO.HEPATITIS	0	0		01
558	CITOSCOPIA	0	0		01
559	PERSONAL PLASTICA AD.	0	0		01
560	FONOAUDIOLOGA	0	0		01
561	CIR.DIGESTIVA	0	0		0101
562	CIR.TORAX	0	0		0102
563	CIR.LISTAS DE ESPERAS	0	0		0103
564	POLICLINICO-PIE DIABETICO	0	0		0104
565	CIR.MAMA-ENDOCRINO	0	0		0105
566	CIR.VASCULAR PERIFERICO	0	0		0107
567	CIR.MAMA AUGE	0	0		0108
568	CIR.REEV.CIRUGIA MENOR	0	0		0109
569	CIR.ANESTESIA UPM	0	0		0111
570	CIR.GENERAL	0	0		0112
571	CIRUGIA MENOR	0	0		0113
572	CIR.MENOR PLAN 90 DIAS	0	0		0114
573	CIR.MATRONA UPM	0	0		0115
574	CIR.REUNION CLINICA ONCOLOGIA	0	0		0116
575	CIR.MENOR CATETER	0	0		0117
576	CIR.UCAM	0	0		0118
577	CIR.EXAMEN DOPPLER	0	0		0119
578	CIR.PROCTOLOGIA	0	0		0120
579	CIR.R.CL DIGESTIVA ONCO	0	0		0122
580	CIR.LISTA ESPERA	0	0		0123
581	CIR.R.IMAGENES UPM	0	0		0124
582	MED.L ESPERA ECOCARDIO	0	0		017
583	URO.UCAM	0	0		02
584	PERSONAL CIRUGIA C.V.	0	0		02
585	FONO.BERA	0	0		02
586	CIRUGIA INF.	0	0		0200
587	CIR.INF.POLI C.MENOR	0	0		0201
588	CIR.CIRUGIA MENOR INF.	0	0		0202
589	CIR.INFANTIL ALTAS	0	0		0203
590	CIR.INF.QUEMADOS ENFERMERA	0	0		0204
591	CIR.INF.LISTA DE ESPERA	0	0		0205
592	CIR.INF.REUNION CL.GENERAL	0	0		0206
593	CIR.INF.UROLOGIA	0	0		0207
594	CIR.INF.EVAL C.MENOR	0	0		0208
595	FISURADOS	0	0		0208
596	FISUR.PSICOLOGOS	0	0		0209
597	FISUR.FONOAUDIOLOGO	0	0		0210
598	FISUR.PSICOL.INT.QUIR.	0	0		0211
599	CIR.INF.POLI QUEMADOS	0	0		0216
600	PERSONAL CIR.DIGESTIVA	0	0		03
601	FONO.AUDIO/IMPADENCIOMETRIA	0	0		03
602	MED.RESP.R.CLINICA	0	0		0300
603	MED.RESP.R.CLINICA	0	0		03000
604	MED.REUMATOLOGIA	0	0		0301
605	MED.NEFROLOGIA	0	0		0302
606	MED.HEMATOLOGIA	0	0		0304
607	MED.CARDIOLOGIA	0	0		0305
608	MED.GASTROENTEROLOGIA	0	0		0306
609	MED.ENDOCRINOLOGIA	0	0		0307
610	MED.NEUMOLOGIA	0	0		0308
611	MEDICINA  GENERAL	0	0		0309
612	MED.NUTRICION	0	0		0310
613	MED.ENDO NODULOS TIROIDEOS	0	0		0311
614	MED.POLI.ARRITMIA	0	0		0313
615	MED.POLI CORONARIO	0	0		0314
616	MED.URG.TEST ESFUERZO	0	0		0315
617	MED.CARDIO MARCAPASO.	0	0		0316
618	MED.CARDIO-CHAGAS	0	0		0317
619	MED.REUNION CL.CARDIO	0	0		0318
620	MED.CIR.MENOR A.VASCULAR	0	0		0319
621	MEDICINA DOCENCIA	0	0		0320
622	MED.PARASITOLOGIA	0	0		0321
623	MED.CRONICO NEUMO	0	0		0322
624	MED.CARDIO.COMPIN	0	0		0323
625	MED.POLI DIABETES	0	0		0324
626	MED.A INSULINA TIPO 2	0	0		0325
627	MED.INMUNOLOGIA AD.	0	0		0326
628	MED.D.M.GESTACIONAL	0	0		0327
629	MED.ALIVIO DEL DOLOR	0	0		0328
630	MED.R.CLINICA ENDO-GINE	0	0		0329
631	MED.NEUMO.TABAQUISMO	0	0		0330
632	MED.SEGUIMIENTO ACC.VASCULARES	0	0		0332
633	MED.NEURO.AVE	0	0		0340
634	MED.EEG (A) AD E INF.	0	0		0341
635	MED.CARDIO.INSUFICIENCIA	0	0		0342
636	MED.HEMA UMA	0	0		0343
637	MED.NEUROL PLAN 90 DIAS	0	0		0344
638	MEDICINA PLAN 90	0	0		0345
639	MEDICINA PLAN 90 DIAS	0	0		0345-01
640	MED.FISTULAS EXTENSION HORARIA	0	0		0350
641	MED.NEFRO PREV.	0	0		0351
642	MED.HEMA.500 ESPECIALISTAS	0	0		0352
643	URO.PRE TX	0	0		04
644	PERSONAL CIR.GENERAL	0	0		04
645	PED.NEUROLOGIA INF.	0	0		0401
646	PED.NEFROLOGIA INF.	0	0		0402
647	PED.HEMATOLOGIA INF.	0	0		0403
648	PED.CARDIOLOGIA INF.	0	0		0404
649	PED.GASTROENTEROLOGIA INF.	0	0		0405
650	PED.ENDOCRINO INF.	0	0		0406
651	PED.RESPIRATORIO INF.	0	0		0407
652	PED.NEONATOLOGIA	0	0		0408
653	PED.NUTRICION INF.	0	0		0409
654	PEDIATRIA GENERAL	0	0		0410
655	PED.SIND.DOWN	0	0		0411
656	PED.NEFRO I.T.U.	0	0		0412
657	PED.POLI HEMOFILIA	0	0		0413
658	PED.HEPATOLOGIA	0	0		0414
659	PED.GENETICA	0	0		0415
660	PED.INFECTOLOGIA INF.	0	0		0416
661	PED.INMUNOLOGIA INF.	0	0		0417
662	PED.DIABETES INF.	0	0		0418
663	PED.RESPIR.CRONICO	0	0		0419
664	PED.RESPIR.URGENCIA	0	0		0420
665	PED.NEFRO CRONICO	0	0		0421
666	PED.FIBROSIS QUISTICA	0	0		0422
667	PED.NEUMO I.REU.CLINICA	0	0		0423
668	PED.ONCO SEGUIMIENTO	0	0		0424
669	NEO.BRONCOPULMONAR	0	0		0425
670	PED.PSICOLOGAS	0	0		0426
671	PED.NEURO INF.RECETAS	0	0		0427
672	PED.ENFERMERA DIABETES	0	0		0428
673	PED.R CL.DIABETES INF.	0	0		0430
674	PERSONAL URO.INF.	0	0		05
675	TRAUMA.FRACTURAS AD.	0	0		0501
676	TRAUMA.CONTROL AD.	0	0		0502
677	TRAUMA.PIE-TOBILLO AD.	0	0		0503
678	TRAUMA.UCAM	0	0		0505
679	TRAUMA RODILLA AD.	0	0		0506
680	TRAUM.CADERA AUGE.AD.	0	0		0507
681	TRAUMA.ESCOLIOSIS AD.	0	0		0508
682	TRAUMA.LISTA ESPERA AD.	0	0		0509
683	TRAUMA.COLUMNA AD.	0	0		0510
684	TRAUMA.TUMOR AD.	0	0		0511
685	TRAUMA.MANO-MU�ECA AD.	0	0		0512
686	TRAUMA.HOMBRO AD.	0	0		0513
687	TRAUMA.CADERA-PELVIS AD.	0	0		0514
688	TRAUM.CADERA OP U.CHILE	0	0		0515
689	TRAUMA.REUN.CLINICA AD.	0	0		0516
690	TRAUMA.VISADOR AUGE	0	0		0517
691	TRAUMA.POLI CHOQUE AD.	0	0		0518
692	TRAUMA.ORTOGERIATRIA	0	0		0519
693	PERSONAL CIR.VASC.PERIFERICA.	0	0		06
694	TRAUMA.FRACTURAS INF.	0	0		0601
695	TRAUMA.CONTROL INF.	0	0		0602
696	TRAUMA.COLUMNA INF.	0	0		0603
697	TRAUMA.PIE BOT INF.	0	0		0604
698	TRAUMA.FRACTURAS UEI	0	0		0605
699	TRAUMA.DISPLACIA INF.	0	0		0607
700	TRAUMA.CADERA OP.INF.	0	0		0608
701	TRAUMA.NUEVOS INF.	0	0		0609
702	TRAUMA.REUNION CL.INF.	0	0		0615
703	CLINICA CIRUGIA INF.	0	0		07
704	OBST Y GINE.GINECOLOGIA AD.	0	0		0701
705	OBST Y GINE-ENDOCRINO	0	0		0702
706	OBST Y GINE.ESTERELIDAD	0	0		0703
707	OBST Y GINE.ONCOLOGIA	0	0		0704
708	OBST Y GINE INFANTIL-ENDO	0	0		0705
709	OBST Y GINE POLICLINICO N�3	0	0		0706
710	OBST Y GINE.ARO PREMATURO	0	0		0707
711	OBST Y GINE.ARO DIABETICA	0	0		0708
712	OBST Y GINE R CLINICA PLAN 90 DIAS	0	0		0709
713	OBST Y GINE UCAM	0	0		0710
714	OBST Y GINE R CLINICA ONCO-GINE	0	0		0711
715	PERSONAL DERMATOLOGIA	0	0		08
716	OBST Y GINE. A.R.O.	0	0		0800
717	PERSONAL NEUMO AD.	0	0		09
718	MED.ENDO.L.ESPERA	0	0		1
719	MED.MIELOGRAMAS	0	0		10
720	PERSONAL FISIATRIA	0	0		10
721	MED.PUNCION-ECOTOMO	0	0		100
722	MED.NEUROLOGIA AD.	0	0		1000
723	MED.MARCAPASO ALTAS	0	0		1001
724	MED.REUNION CL.NEUROLOGIA	0	0		1002
725	MED.CONSULTA REUMA.	0	0		1003
726	MED.GASTRO.BIOPSIAS.	0	0		1004
727	MED.CARDIO PALN 90 DIAS	0	0		1005
728	MED.CARDIO ALTAS	0	0		11
729	CLINICA MATRONA	0	0		11
730	MED.TEST CUTANEO	0	0		12
731	PERSONAL ENDO AD.	0	0		12
732	OFTALMOLOGIA	0	0		1200
733	OFT.ECO.EXTENSION HORARIA	0	0		1201
734	OFT.ESTRABISMO INF.	0	0		1202
735	OFL.PLAN 90 DIAS	0	0		1203
736	OFT.CURVA TENSION	0	0		1204
737	OFT.CAMPO VISUAL	0	0		1205
738	OFT.DEPARTAMENTO RETINA	0	0		1207
739	OFT.COMPIN	0	0		1208
740	OFT.PREMATUROS	0	0		1209
741	OFT.ENFERMERA	0	0		1210
742	OFT.ANGIOGRAFIA	0	0		1212
743	OFTALMOLOGIA AD.	0	0		1213
744	OFT.GLAUCOMA	0	0		1214
745	OFT.CATARATA	0	0		1216
746	OFT.POLI.EXTENSION HORARIA	0	0		1217
747	OFT.VITRECTOMIA	0	0		1218
748	OFT.RETINA.EXTENSION HORARIA.	0	0		1219
749	OFT.CONTROL .INF	0	0		1220
750	OFT.ADULTO NUEVOS	0	0		1221
751	MED.ELECTROCADIOGRAMA	0	0		13
752	CLINICA GASTRO AD.	0	0		13
753	MED.NEUMO.TEST METACOLINA	0	0		14
754	PED.TEST CUTANEO	0	0		14
755	PERSONAL HEMATO AD.	0	0		14
756	UROLOGIA	0	0		1400
757	URO.GES ALTA	0	0		1401
758	URO.NEFRO R.CLINICA	0	0		1402
759	URO.POLI CHOQUE	0	0		1403
760	URO.ONCO R.CLINICA	0	0		1404
761	URO LITOTRIPSIA	0	0		1405
762	URO.URGENCIA	0	0		1406
763	URO.RECUNION CLINICA	0	0		1407
764	URO.GES NUEVO	0	0		1408
765	URO.PROSTATA GES	0	0		1410
766	URO.GENITAL AMB	0	0		1411
767	URO.PROSTATA GES.	0	0		1412
768	URO.GENITAL AMB.	0	0		1413
769	MED.CARDIO EKG 90 DIAS	0	0		15
770	PERSONAL NEFRO AD.	0	0		15
771	PSIQ.FARMACOTERAPIA	0	0		1501
772	PSIQ.PSICOTERAPIA	0	0		1502
773	PSIQ.PERITAJES	0	0		1503
774	PSIQ.TERAPIA GRUPAL	0	0		1505
775	PSIQUIATRIA	0	0		1506
776	PSIQ.ASISTENTE SOCIAL	0	0		1507
777	PSIQ.TERAPIA FAMILIAR	0	0		1508
778	PSIQ.CRONICOS	0	0		1509
779	PSIQ.PSICOLOGIA CRONICOS	0	0		1510
780	PSIQ.PSICOMETRIA	0	0		1512
781	PSIQ.REU.SEGUIM.INF-JUV	0	0		1513
782	PSIQUIATRIA DEPRESION	0	0		1514
783	PSIQ.ENFERMERA	0	0		1515
784	PSIQ.AUGE 1ER BROTE INF-JUV	0	0		1516
785	PSIQ.CONTROL INF-JUV	0	0		1517
786	PSIQ.PSICOTERAPIA I.	0	0		1518
787	PSIQ.TERAPIA FAMILIAR INF.	0	0		1519
788	PSIQ.TALLER OBESIDAD	0	0		1520
789	PSIQ.RECETAS INF.	0	0		1521
790	PSIQ.INGRESO ALCOHOL-DROGAS	0	0		1522
791	PAIQ.TERAP.GRUPAL AD.	0	0		1523
792	PSIQ.H.D.CONTROLES	0	0		1524
793	PSIQ.NI�OS HIPERACTIVOS	0	0		1525
794	PSIQUIATRIA GENERAL	0	0		1526
795	PSIQ.ENFERMERA INF.	0	0		1528
796	PSIQ.T.OCUPACIONAL	0	0		1529
797	PSIQ.NUTRICION	0	0		1530
798	PSQ.TERAPIA ADOLECENTE	0	0		1531
799	PSIQUIATRIA INF.	0	0		1532
800	PSIQ.CUIDADOS CONTINUOS	0	0		1533
801	PSIQ.GRUPO MULTI.FAMILIAR	0	0		1534
802	PSIQ.MULTI FAMILIAR	0	0		1535
803	PSIQ.PSICOMETRIA AD.	0	0		1536
804	PSQ.PSICOPEDAGOGIA	0	0		1537
805	PRAIS	0	0		1538
806	PSIQ.FONOAUDIOLOGIA	0	0		1539
807	PSIQ.ASISTENTE SOCIAL INF.	0	0		1540
808	PSIQ.POLI.RECETAS	0	0		1541
809	PSIQ.PSIQUIATRIA TPS	0	0		1542
810	PSIQ.PLAN 500 ESPECIALISTAS	0	0		1543
811	PSIQ.CRONICO SALUD MENTAL TPS	0	0		1544
812	PSIQ.AUGE EZQ.AD.	0	0		1545
813	PSIQ.R.CLINICA	0	0		1546
814	PSIQ.ALCOHOL 90 DIAS	0	0		1547
815	MED.CARDIO-MCH	0	0		16
816	PERSONAL NEURO AD.	0	0		16
817	MED.L ESPERA ECOCARDIO	0	0		17
818	PED.TEST ESFUERZO INF	0	0		17
819	PERSONAL POLI DIABETES	0	0		17
820	OBST Y GINE.MONIT.FOLICULAR	0	0		18
821	PERSONAL REUMA AD.	0	0		18
822	OBST Y GINE.ECOGRAFIA R.CLINICA	0	0		19
823	PERSONAL MED.INTERNA.	0	0		19
824	CIR.CARDIO VASCULAR	0	0		1900
825	CIR.CCV.ANESTESIA	0	0		1901
826	CIR.CCV CONTROL	0	0		1902
827	CIR.CCV TRANSPLANTADOS	0	0		1903
828	MED.ENDOSCOPIAS PROC.	0	0		2
829	OBST Y GINE.ECOTOMO.GINE	0	0		20
830	PERSONAL ESTERELIDAD AD.	0	0		20
831	ANESTESIOLOGIA	0	0		2001
832	ANEST.TRAUMA AUGE	0	0		2002
833	OTORRINO	0	0		2002
834	ANEST.OFTALMOLOGIA	0	0		2003
835	ORL.POLI CHOQUE	0	0		2003
836	OTORRINO	0	0		2003
837	ANESTESIOLOGIA	0	0		2004
838	OTORRINO INFANTIL	0	0		2004
839	OTORRINO.CIR.MENOR	0	0		2005
840	OTORRINO PLAN 90 DIAS	0	0		2006
841	ORL.CONTROL	0	0		2007
842	ORL.NUEVO AD.	0	0		2008
843	ORL.NUEVO INF.	0	0		2009
844	ORL.TECNOLOGO MEDICO	0	0		2010
845	ORL.PREMATUROS	0	0		2011
846	ORL.AUDIOMETRIA	0	0		2012
847	ORL.VII PAR	0	0		2013
848	MED.NEURO.FONOAUDIOLOGIA	0	0		2014
849	ORL.IMPADENCIOMETRIA	0	0		2015
850	ORL.LARINGOFIBROSCOPIA	0	0		2016
851	ORL.POLI CHOQUE	0	0		2017
852	OBST Y GINE.ECOTOMO II NIVEL	0	0		21
853	PERSONAL ESTERELIDAD	0	0		21
854	DERMATOLOGIA	0	0		2100
855	DERM. PLAN 90 DIAS	0	0		2101
856	DERM.CIRUGIA MENOR	0	0		2102
857	DERM.REUNION CLINICA	0	0		2103
858	DERMATOLOGIA INF.	0	0		2104
859	DERMA.ETS.	0	0		2105
860	OBST Y GINE.ECO I TRIMESTRE	0	0		22
861	PERSONAL GINECOLOGIA.	0	0		22
862	OBST Y GINE.HISTEROSCOPIA	0	0		23
863	PERSONAL CIRUGIA ORAL M.F.	0	0		23
864	OBST Y GINE.CONIZ.ELECTR.	0	0		24
865	PERSONAL OFTALMOLOGIA	0	0		24
866	OBST Y GINE ECOTOMO OBST.	0	0		25
867	PERSONAL ENDOCRINO INF.	0	0		25
868	PERSONAL GASTRO INF.	0	0		26
869	OBST Y GINE.ECO CARDIO FETAL	0	0		27
870	PERSONAL GENETICA	0	0		27
871	OBST Y GINE ECO.GEMELARES	0	0		28
872	PERSONAL HEMATO INF.	0	0		28
873	OBST Y GINE-REEVALUACION	0	0		29
874	PERSONAL NEFROLOGIA INF.	0	0		29
875	MED.RECTOSCOPIAS PROC	0	0		3
876	CITOSCOPIA.PLAN 90 DIAS	0	0		3
877	MED.INFILTRACION	0	0		30
878	OBST Y GINE.ECOGRAFIA 4TO PISO	0	0		30
879	PERSONAL NEUMO INF.	0	0		30
880	OBST Y GINE.ECOGRAFIA 4TO PISO	0	0		30-1
881	OBST Y GINE ECO GINE 4TO PISO	0	0		31
882	PERSONAL NEURO INF.	0	0		31
883	MED.ECOCARDIOGRAMA	0	0		32
884	PERSONAL NUTRICION INF.	0	0		32
885	MED.TEST.ESFUERZO AD.	0	0		33
886	CLINICA PEDIATRIA	0	0		33
887	PDE.ECOCARDIOGRAMA INF.	0	0		34
888	PERSONAL CARDIO CV.	0	0		34
889	CIR.LISTA ESPERA	0	0		35
890	PED.ONCO.SEGUIMIENTO	0	0		35
891	CLINICA PSIQUIATRIA	0	0		35
892	CIR.BIOPSIAS CORE UPM	0	0		36
893	PED.L ESPERA ENDO INF	0	0		36
894	PERSONAL UROLOGIA	0	0		36
895	MED.CARDIO HOLTER	0	0		37
896	PERSONAL PSIQUIATRIA INF.	0	0		37
897	MED.ESPIROMETRIAS	0	0		38
898	CLINCICA TRAUMA INF.	0	0		38
899	MED.PROGRAMA EDUCACION	0	0		39
900	MED.ENDO.URGENCIA TIROIDE	0	0		4
901	PED.LISTA ESPERA NEURO	0	0		4
902	MED.ECOCARDIO.CHAGAS	0	0		40
903	PERSONAL OTORRINO	0	0		40
904	MED.COLONOSCOPIAS	0	0		41
905	CLINICA  PREVENTIVA	0	0		42
906	MED.GASTRO RESTOSCOPIA PROC..	0	0		43
907	MED.RECTO-LIGADURA	0	0		44
908	CLINICA CARDIO AD.	0	0		44
909	MED.ENDO.URG.TIROIDE	0	0		5
910	MED.EEG SUE�O	0	0		50
911	MED.EEG (D) AD E INF.	0	0		51
912	MED.EEG OTROS HOSPITALES	0	0		52
913	MED.ELECTROMIOGRAFIA	0	0		53
914	MED.QUILLOTA INF.	0	0		54
915	MED.EEG HOSP.QUILLOTA	0	0		55
916	MED.E.M.G.TUNEL CARPO	0	0		56
917	MED.EEG.QUILPUE	0	0		57
918	MED.HEMA.BIOPSIA	0	0		58
919	MED.HEMA.L.ESPERA	0	0		59
920	MED.HEMA.AUGE	0	0		6
921	PSIQ.R.CLINICA	0	0		60
922	ODONT.ENDODONCIA	0	0		61
923	ODONT.CIRUGIA ORAL	0	0		62
924	ODONTOPEDIATRIA	0	0		63
925	APOYO ESPECIALIDADES	0	0		64
926	ODONT.PROTESIS	0	0		65
927	ODONT.PERIDONCIA	0	0		66
928	ODONT.DISFUNCION ATM	0	0		67
929	ODONT.OPERATORIA FUNCIONARIOS	0	0		68
930	RADIOLOGIA	0	0		69
931	MED.FIBROBRONCOSCOPIAS	0	0		7
932	ODONT.ANESTESIA GRAL	0	0		70
933	FISIATRIA	0	0		7000
934	MED.REHABILITACION	0	0		7001
935	FISIATRIA-INFILTRACION	0	0		7002
936	ODONT.CIR.AMBULATORIA	0	0		71
937	ODONT.CIRUGIA ORAL INF.	0	0		72
938	IMAG.ECOT.ABD.PEL.RENAL	0	0		90
939	IMAG.ECOT.PARTES BLANDAS	0	0		91
940	MED.CARDIO PREVENSION	0	0		971
941	MED.NEFRO ACC.VASCULAR	0	0		972
942	MED.CARDIO RECETAS	0	0		973
943	CIR.INF.BRONCO Y SIALOGRAFIA	0	0		980
944	CIR.INF.EVAL C.MENOR	0	0		981
945	MED.PROG.SALUD CCV	0	0		985
946	MED.TRANSPLANTE RENAL	0	0		991
947	MED.PRE TX.RENAL	0	0		992
948	CIR.CENTRO OSTOMIZADOS	0	0		999
949	IMAG.PROYECCIONES ESP.	0	0		A0
950	IMAG.ECOT.CEREBRAL	0	0		A1
951	IMAG.ECO.GENIT-TIROIDES-CERV.	0	0		A2
952	IMAG.ECOT.MAMARIA	0	0		A3
953	IMAG.ECOT.CORE MAMA	0	0		A4
954	IMAG.DOPLER VARIOS	0	0		A5
955	IMAG.ECOT.TIROIDEA	0	0		A6
956	IMAG.MAMOGRAFIA	0	0		A7
957	IMAG.COL.TOTAL MENOR 30 A	0	0		A8
958	IMAG.DOPLER CAROTIDEAS	0	0		A9
959	CIRUGIA CARDIO VASCULAR	0	0		CCV
960	CIRUGIA ADULTO	0	0		CIAD
961	UCIM CIRUGIA	0	0		CIR01
962	UCIM CIRUGIA	0	0		CIR01-1
963	CUIDADOS ESPECIALES	0	0		CUI01
964	CUIDADOS MINIMOS	0	0		CUMI
965	IMAG.HUESOS ADULTOS	0	0		D1
966	IMAG.ECOT.CERVICAL	0	0		F
967	IMAG.ECOTOMOGRAFIA INF.	0	0		F0
968	IMAG.SOTI CONTROL	0	0		F1
969	IMAG.MAMOGRAFIA FUNCIONARIAS	0	0		F2
970	GINECOLOGIA	0	0		GIN
971	OFT.NUEVOS INF.	0	0		L1
972	OFT.ECOGRAFIA-LASER	0	0		L2
973	03 MEDICINA	0	0		MED
974	UCI NEONATOLOGIA	0	0		NE01
975	INCUBADORA	0	0		NE02
976	CUIDADO MINIMO	0	0		NE03
977	SEGUNDA INFANCIA	0	0		NEON
978	OBSTETRICIA	0	0		OBGI
979	LACTANTES	0	0		PED01
980	2A INFANCIA	0	0		PED02
981	ONCOLOGIA	0	0		PED03
982	INFECCIOSO	0	0		PED04
983	UCI PEDIATRICA	0	0		PED05
984	UCIM PEDIATRICA	0	0		PED06
985	SOTA	0	0		SOTA
986	SOTI	0	0		SOTI15
987	UCI ADULTO	0	0		UCAD
988	UCIN CCV	0	0		UCCV
989	UCI PEDIATRICA	0	0		UCII
990	UCIN MEDICINA	0	0		UCIN
991	UCIN CIRUGIA	0	0		UCINC
992	UCI INDEFERENCIADA	0	0		UCIND
993	UCIN PEDIATRICA	0	0		UCINP
994	UNIDAD EMERGENCIA INFANTIL	0	0		UEI
995	UCI NEO	0	0		UNEO
996	XXXX	0	0		XXXX
997	JJJJJJ	0	0		001
998	CIRUGIA	0	0		0100
999	CIRUGIA INFANTIL	0	0		0200
1000	FISURADOS	0	0		0208
1001	MEDICINA	0	0		0300
1002	PEDIATRIA	0	0		0400
1003	TRAUMATOLOGIA ADULTO	0	0		0500
1004	TRAUMATOLOGIA INFANTIL	0	0		0600
1005	OBST-GINECOLOGIA	0	0		0700
1006	OFTALMOLOGIA	0	0		1200
1007	UROLOGIA	0	0		1400
1008	PSIQUIATRIA	0	0		1500
1009	CIRUGIA CARDIO VASCULAR	0	0		1900
1010	PERSONAL HOSPITAL	0	0		2000
1011	ANESTESIOLOGIA	0	0		2001
1012	OTORRINO	0	0		2002
1013	OTORRINO	0	0		2003
1014	DERMATOLOGIA	0	0		2100
1015	NEUROCIRUGIA	0	0		2205
1016	FONOAUDIOLOGA	0	0		2206
1017	ODONTOLOGIA	0	0		6000
1018	FISIATRIA	0	0		7000
1019	IMAGENOLOGIA	0	0		7500
1020	CIR. CARDIO VASCULAR	0	0		CCV
1021	CIR. ADULTO	0	0		CIAD
1022	CIR. INFANTIL	0	0		CIIN
1023	CIRUGIA ADULTO	0	0		CIR01
1024	CUIDADOS INTERMEDIO	0	0		CIR02
1025	CUIDADOS MINIMOS	0	0		CUMI
1026	GINECOLOGIA	0	0		GIN
1027	INCUBADORAS	0	0		INCU
1028	INFECCIOSOS	0	0		INFEC
1029	LACTANTES	0	0		LACT
1030	03 MEDICINA	0	0		MED
1031	SEGUNDA INFANCIA	0	0		NEON
1032	OBSTETRICIA	0	0		OBGI
1033	PEDIATRIA	0	0		PEDI
1034	PENSIONADO	0	0		PENS
1035	OFTALMOLOGIA	0	0		SOFTA
1036	SOTA	0	0		SOTA
1037	SOTI	0	0		SOTI
1038	TRAUMATOLOGIA ADULTO	0	0		TRAAD
1039	TRAUMATOLOGIA INFANTIL	0	0		TRIN
1040	UCI ADULTO	0	0		UCAD
1041	UCIN CCV	0	0		UCCV
1042	UCIM CIRUGIA CARDIOVASCULAR	0	0		UCICA
1043	UCI PEDIATRICA	0	0		UCII
1044	UCIN MEDICINA	0	0		UCIN
1045	UCIN CIRUGIA	0	0		UCINC
1046	UCI INDEFERENCIADA	0	0		UCIND
1047	UCIN PEDIATRICA	0	0		UCINP
1048	UNIDAD EMERGENCIA INFANTIL	0	0		UEI
1049	UCI NEO	0	0		UNEO
1050	UNIDAD EMERGENCIA ADULTO	0	0		URG01
1051	MED.HEMOFILIA ADULTOS	0	448		00
1052	NEUROCIRUGIA AD.	0	462		000
1053	MED.L ESPERA ECOCARDIO	0	448		0001
1054	NEUROCIRUGIA INF.	0	462		001
1055	MED.H.T.L.V	0	448		003
1056	MED.GASTRO.HEPATITIS	0	448		01
1057	CITOSCOPIA	0	454		01
1058	PERSONAL PLASTICA AD.	0	457		01
1059	FONOAUDIOLOGA	0	463		01
1060	CIR.DIGESTIVA	0	445		0101
1061	CIR.TORAX	0	445		0102
1062	CIR.LISTAS DE ESPERAS	0	445		0103
1063	POLICLINICO-PIE DIABETICO	0	445		0104
1064	CIR.MAMA-ENDOCRINO	0	445		0105
1065	CIR.VASCULAR PERIFERICO	0	445		0107
1066	CIR.MAMA AUGE	0	445		0108
1067	CIR.REEV.CIRUGIA MENOR	0	445		0109
1068	CIR.ANESTESIA UPM	0	445		0111
1069	CIR.GENERAL	0	445		0112
1070	CIRUGIA MENOR	0	445		0113
1071	CIR.MENOR PLAN 90 DIAS	0	445		0114
1072	CIR.MATRONA UPM	0	445		0115
1073	CIR.REUNION CLINICA ONCOLOGIA	0	445		0116
1074	CIR.MENOR CATETER	0	445		0117
1075	CIR.UCAM	0	445		0118
1076	CIR.EXAMEN DOPPLER	0	445		0119
1077	CIR.PROCTOLOGIA	0	445		0120
1078	CIR.R.CL DIGESTIVA ONCO	0	445		0122
1079	CIR.LISTA ESPERA	0	445		0123
1080	CIR.R.IMAGENES UPM	0	445		0124
1081	MED.L ESPERA ECOCARDIO	0	448		017
1082	URO.UCAM	0	454		02
1083	PERSONAL CIRUGIA C.V.	0	457		02
1084	FONO.BERA	0	463		02
1085	CIRUGIA INF.	0	446		0200
1086	CIR.INF.POLI C.MENOR	0	446		0201
1087	CIR.CIRUGIA MENOR INF.	0	446		0202
1088	CIR.INFANTIL ALTAS	0	446		0203
1089	CIR.INF.QUEMADOS ENFERMERA	0	446		0204
1090	CIR.INF.LISTA DE ESPERA	0	446		0205
1091	CIR.INF.REUNION CL.GENERAL	0	446		0206
1092	CIR.INF.UROLOGIA	0	446		0207
1093	CIR.INF.EVAL C.MENOR	0	446		0208
1094	FISURADOS	0	447		0208
1095	FISUR.PSICOLOGOS	0	447		0209
1096	FISUR.FONOAUDIOLOGO	0	447		0210
1097	FISUR.PSICOL.INT.QUIR.	0	447		0211
1098	CIR.INF.POLI QUEMADOS	0	446		0216
1099	PERSONAL CIR.DIGESTIVA	0	457		03
1100	FONO.AUDIO/IMPADENCIOMETRIA	0	463		03
1101	MED.RESP.R.CLINICA	0	448		0300
1102	MED.RESP.R.CLINICA	0	448		03000
1103	MED.REUMATOLOGIA	0	448		0301
1104	MED.NEFROLOGIA	0	448		0302
1105	MED.HEMATOLOGIA	0	448		0304
1106	MED.CARDIOLOGIA	0	448		0305
1107	MED.GASTROENTEROLOGIA	0	448		0306
1108	MED.ENDOCRINOLOGIA	0	448		0307
1109	MED.NEUMOLOGIA	0	448		0308
1110	MEDICINA  GENERAL	0	448		0309
1111	MED.NUTRICION	0	448		0310
1112	MED.ENDO NODULOS TIROIDEOS	0	448		0311
1113	MED.POLI.ARRITMIA	0	448		0313
1114	MED.POLI CORONARIO	0	448		0314
1115	MED.URG.TEST ESFUERZO	0	448		0315
1116	MED.CARDIO MARCAPASO.	0	448		0316
1117	MED.CARDIO-CHAGAS	0	448		0317
1118	MED.REUNION CL.CARDIO	0	448		0318
1119	MED.CIR.MENOR A.VASCULAR	0	448		0319
1120	MEDICINA DOCENCIA	0	448		0320
1121	MED.PARASITOLOGIA	0	448		0321
1122	MED.CRONICO NEUMO	0	448		0322
1123	MED.CARDIO.COMPIN	0	448		0323
1124	MED.POLI DIABETES	0	448		0324
1125	MED.A INSULINA TIPO 2	0	448		0325
1126	MED.INMUNOLOGIA AD.	0	448		0326
1127	MED.D.M.GESTACIONAL	0	448		0327
1128	MED.ALIVIO DEL DOLOR	0	448		0328
1129	MED.R.CLINICA ENDO-GINE	0	448		0329
1130	MED.NEUMO.TABAQUISMO	0	448		0330
1131	MED.SEGUIMIENTO ACC.VASCULARES	0	448		0332
1132	MED.NEURO.AVE	0	448		0340
1133	MED.EEG (A) AD E INF.	0	448		0341
1134	MED.CARDIO.INSUFICIENCIA	0	448		0342
1135	MED.HEMA UMA	0	448		0343
1136	MED.NEUROL PLAN 90 DIAS	0	448		0344
1137	MEDICINA PLAN 90	0	448		0345
1138	MEDICINA PLAN 90 DIAS	0	448		0345-01
1139	MED.FISTULAS EXTENSION HORARIA	0	448		0350
1140	MED.NEFRO PREV.	0	448		0351
1141	MED.HEMA.500 ESPECIALISTAS	0	448		0352
1142	URO.PRE TX	0	454		04
1143	PERSONAL CIR.GENERAL	0	457		04
1144	PED.NEUROLOGIA INF.	0	449		0401
1145	PED.NEFROLOGIA INF.	0	449		0402
1146	PED.HEMATOLOGIA INF.	0	449		0403
1147	PED.CARDIOLOGIA INF.	0	449		0404
1148	PED.GASTROENTEROLOGIA INF.	0	449		0405
1149	PED.ENDOCRINO INF.	0	449		0406
1150	PED.RESPIRATORIO INF.	0	449		0407
1151	PED.NEONATOLOGIA	0	449		0408
1152	PED.NUTRICION INF.	0	449		0409
1153	PEDIATRIA GENERAL	0	449		0410
1154	PED.SIND.DOWN	0	449		0411
1155	PED.NEFRO I.T.U.	0	449		0412
1156	PED.POLI HEMOFILIA	0	449		0413
1157	PED.HEPATOLOGIA	0	449		0414
1158	PED.GENETICA	0	449		0415
1159	PED.INFECTOLOGIA INF.	0	449		0416
1160	PED.INMUNOLOGIA INF.	0	449		0417
1161	PED.DIABETES INF.	0	449		0418
1162	PED.RESPIR.CRONICO	0	449		0419
1163	PED.RESPIR.URGENCIA	0	449		0420
1164	PED.NEFRO CRONICO	0	449		0421
1165	PED.FIBROSIS QUISTICA	0	449		0422
1166	PED.NEUMO I.REU.CLINICA	0	449		0423
1167	PED.ONCO SEGUIMIENTO	0	449		0424
1168	NEO.BRONCOPULMONAR	0	449		0425
1169	PED.PSICOLOGAS	0	449		0426
1170	PED.NEURO INF.RECETAS	0	449		0427
1171	PED.ENFERMERA DIABETES	0	449		0428
1172	PED.R CL.DIABETES INF.	0	449		0430
1173	PERSONAL URO.INF.	0	457		05
1174	TRAUMA.FRACTURAS AD.	0	450		0501
1175	TRAUMA.CONTROL AD.	0	450		0502
1176	TRAUMA.PIE-TOBILLO AD.	0	450		0503
1177	TRAUMA.UCAM	0	450		0505
1178	TRAUMA RODILLA AD.	0	450		0506
1179	TRAUM.CADERA AUGE.AD.	0	450		0507
1180	TRAUMA.ESCOLIOSIS AD.	0	450		0508
1181	TRAUMA.LISTA ESPERA AD.	0	450		0509
1182	TRAUMA.COLUMNA AD.	0	450		0510
1183	TRAUMA.TUMOR AD.	0	450		0511
1184	TRAUMA.MANO-MU�ECA AD.	0	450		0512
1185	TRAUMA.HOMBRO AD.	0	450		0513
1186	TRAUMA.CADERA-PELVIS AD.	0	450		0514
1187	TRAUM.CADERA OP U.CHILE	0	450		0515
1188	TRAUMA.REUN.CLINICA AD.	0	450		0516
1189	TRAUMA.VISADOR AUGE	0	450		0517
1190	TRAUMA.POLI CHOQUE AD.	0	450		0518
1191	TRAUMA.ORTOGERIATRIA	0	450		0519
1192	PERSONAL CIR.VASC.PERIFERICA.	0	457		06
1193	TRAUMA.FRACTURAS INF.	0	451		0601
1194	TRAUMA.CONTROL INF.	0	451		0602
1195	TRAUMA.COLUMNA INF.	0	451		0603
1196	TRAUMA.PIE BOT INF.	0	451		0604
1197	TRAUMA.FRACTURAS UEI	0	451		0605
1198	TRAUMA.DISPLACIA INF.	0	451		0607
1199	TRAUMA.CADERA OP.INF.	0	451		0608
1200	TRAUMA.NUEVOS INF.	0	451		0609
1201	TRAUMA.REUNION CL.INF.	0	451		0615
1202	CLINICA CIRUGIA INF.	0	457		07
1203	OBST Y GINE.GINECOLOGIA AD.	0	452		0701
1204	OBST Y GINE-ENDOCRINO	0	452		0702
1205	OBST Y GINE.ESTERELIDAD	0	452		0703
1206	OBST Y GINE.ONCOLOGIA	0	452		0704
1207	OBST Y GINE INFANTIL-ENDO	0	452		0705
1208	OBST Y GINE POLICLINICO N�3	0	452		0706
1209	OBST Y GINE.ARO PREMATURO	0	452		0707
1210	OBST Y GINE.ARO DIABETICA	0	452		0708
1211	OBST Y GINE R CLINICA PLAN 90 DIAS	0	452		0709
1212	OBST Y GINE UCAM	0	452		0710
1213	OBST Y GINE R CLINICA ONCO-GINE	0	452		0711
1214	PERSONAL DERMATOLOGIA	0	457		08
1215	OBST Y GINE. A.R.O.	0	452		0800
1216	PERSONAL NEUMO AD.	0	457		09
1217	MED.ENDO.L.ESPERA	0	448		1
1218	MED.MIELOGRAMAS	0	448		10
1219	PERSONAL FISIATRIA	0	457		10
1220	MED.PUNCION-ECOTOMO	0	448		100
1221	MED.NEUROLOGIA AD.	0	448		1000
1222	MED.MARCAPASO ALTAS	0	448		1001
1223	MED.REUNION CL.NEUROLOGIA	0	448		1002
1224	MED.CONSULTA REUMA.	0	448		1003
1225	MED.GASTRO.BIOPSIAS.	0	448		1004
1226	MED.CARDIO PALN 90 DIAS	0	448		1005
1227	MED.CARDIO ALTAS	0	448		11
1228	CLINICA MATRONA	0	457		11
1229	MED.TEST CUTANEO	0	448		12
1230	PERSONAL ENDO AD.	0	457		12
1231	OFTALMOLOGIA	0	453		1200
1232	OFT.ECO.EXTENSION HORARIA	0	453		1201
1233	OFT.ESTRABISMO INF.	0	453		1202
1234	OFL.PLAN 90 DIAS	0	453		1203
1235	OFT.CURVA TENSION	0	453		1204
1236	OFT.CAMPO VISUAL	0	453		1205
1237	OFT.DEPARTAMENTO RETINA	0	453		1207
1238	OFT.COMPIN	0	453		1208
1239	OFT.PREMATUROS	0	453		1209
1240	OFT.ENFERMERA	0	453		1210
1241	OFT.ANGIOGRAFIA	0	453		1212
1242	OFTALMOLOGIA AD.	0	453		1213
1243	OFT.GLAUCOMA	0	453		1214
1244	OFT.CATARATA	0	453		1216
1245	OFT.POLI.EXTENSION HORARIA	0	453		1217
1246	OFT.VITRECTOMIA	0	453		1218
1247	OFT.RETINA.EXTENSION HORARIA.	0	453		1219
1248	OFT.CONTROL .INF	0	453		1220
1249	OFT.ADULTO NUEVOS	0	453		1221
1250	MED.ELECTROCADIOGRAMA	0	448		13
1251	CLINICA GASTRO AD.	0	457		13
1252	MED.NEUMO.TEST METACOLINA	0	448		14
1253	PED.TEST CUTANEO	0	449		14
1254	PERSONAL HEMATO AD.	0	457		14
1255	UROLOGIA	0	454		1400
1256	URO.GES ALTA	0	454		1401
1257	URO.NEFRO R.CLINICA	0	454		1402
1258	URO.POLI CHOQUE	0	454		1403
1259	URO.ONCO R.CLINICA	0	454		1404
1260	URO LITOTRIPSIA	0	454		1405
1261	URO.URGENCIA	0	454		1406
1262	URO.RECUNION CLINICA	0	454		1407
1263	URO.GES NUEVO	0	454		1408
1264	URO.PROSTATA GES	0	454		1410
1265	URO.GENITAL AMB	0	454		1411
1266	URO.PROSTATA GES.	0	454		1412
1267	URO.GENITAL AMB.	0	454		1413
1268	MED.CARDIO EKG 90 DIAS	0	448		15
1269	PERSONAL NEFRO AD.	0	457		15
1270	PSIQ.FARMACOTERAPIA	0	455		1501
1271	PSIQ.PSICOTERAPIA	0	455		1502
1272	PSIQ.PERITAJES	0	455		1503
1273	PSIQ.TERAPIA GRUPAL	0	455		1505
1274	PSIQUIATRIA	0	455		1506
1275	PSIQ.ASISTENTE SOCIAL	0	455		1507
1276	PSIQ.TERAPIA FAMILIAR	0	455		1508
1277	PSIQ.CRONICOS	0	455		1509
1278	PSIQ.PSICOLOGIA CRONICOS	0	455		1510
1279	PSIQ.PSICOMETRIA	0	455		1512
1280	PSIQ.REU.SEGUIM.INF-JUV	0	455		1513
1281	PSIQUIATRIA DEPRESION	0	455		1514
1282	PSIQ.ENFERMERA	0	455		1515
1283	PSIQ.AUGE 1ER BROTE INF-JUV	0	455		1516
1284	PSIQ.CONTROL INF-JUV	0	455		1517
1285	PSIQ.PSICOTERAPIA I.	0	455		1518
1286	PSIQ.TERAPIA FAMILIAR INF.	0	455		1519
1287	PSIQ.TALLER OBESIDAD	0	455		1520
1288	PSIQ.RECETAS INF.	0	455		1521
1289	PSIQ.INGRESO ALCOHOL-DROGAS	0	455		1522
1290	PAIQ.TERAP.GRUPAL AD.	0	455		1523
1291	PSIQ.H.D.CONTROLES	0	455		1524
1292	PSIQ.NI�OS HIPERACTIVOS	0	455		1525
1293	PSIQUIATRIA GENERAL	0	455		1526
1294	PSIQ.ENFERMERA INF.	0	455		1528
1295	PSIQ.T.OCUPACIONAL	0	455		1529
1296	PSIQ.NUTRICION	0	455		1530
1297	PSQ.TERAPIA ADOLECENTE	0	455		1531
1298	PSIQUIATRIA INF.	0	455		1532
1299	PSIQ.CUIDADOS CONTINUOS	0	455		1533
1300	PSIQ.GRUPO MULTI.FAMILIAR	0	455		1534
1301	PSIQ.MULTI FAMILIAR	0	455		1535
1302	PSIQ.PSICOMETRIA AD.	0	455		1536
1303	PSQ.PSICOPEDAGOGIA	0	455		1537
1304	PRAIS	0	455		1538
1305	PSIQ.FONOAUDIOLOGIA	0	455		1539
1306	PSIQ.ASISTENTE SOCIAL INF.	0	455		1540
1307	PSIQ.POLI.RECETAS	0	455		1541
1308	PSIQ.PSIQUIATRIA TPS	0	455		1542
1309	PSIQ.PLAN 500 ESPECIALISTAS	0	455		1543
1310	PSIQ.CRONICO SALUD MENTAL TPS	0	455		1544
1311	PSIQ.AUGE EZQ.AD.	0	455		1545
1312	PSIQ.R.CLINICA	0	455		1546
1313	PSIQ.ALCOHOL 90 DIAS	0	455		1547
1314	MED.CARDIO-MCH	0	448		16
1315	PERSONAL NEURO AD.	0	457		16
1316	MED.L ESPERA ECOCARDIO	0	448		17
1317	PED.TEST ESFUERZO INF	0	449		17
1318	PERSONAL POLI DIABETES	0	457		17
1319	OBST Y GINE.MONIT.FOLICULAR	0	452		18
1320	PERSONAL REUMA AD.	0	457		18
1321	OBST Y GINE.ECOGRAFIA R.CLINICA	0	452		19
1322	PERSONAL MED.INTERNA.	0	457		19
1323	CIR.CARDIO VASCULAR	0	456		1900
1324	CIR.CCV.ANESTESIA	0	456		1901
1325	CIR.CCV CONTROL	0	456		1902
1326	CIR.CCV TRANSPLANTADOS	0	456		1903
1327	MED.ENDOSCOPIAS PROC.	0	448		2
1328	OBST Y GINE.ECOTOMO.GINE	0	452		20
1329	PERSONAL ESTERELIDAD AD.	0	457		20
1330	ANESTESIOLOGIA	0	458		2001
1331	ANEST.TRAUMA AUGE	0	458		2002
1332	OTORRINO	0	459		2002
1333	ANEST.OFTALMOLOGIA	0	458		2003
1334	ORL.POLI CHOQUE	0	459		2003
1335	OTORRINO	0	460		2003
1336	ANESTESIOLOGIA	0	458		2004
1337	OTORRINO INFANTIL	0	460		2004
1338	OTORRINO.CIR.MENOR	0	460		2005
1339	OTORRINO PLAN 90 DIAS	0	460		2006
1340	ORL.CONTROL	0	460		2007
1341	ORL.NUEVO AD.	0	460		2008
1342	ORL.NUEVO INF.	0	460		2009
1343	ORL.TECNOLOGO MEDICO	0	460		2010
1344	ORL.PREMATUROS	0	460		2011
1345	ORL.AUDIOMETRIA	0	460		2012
1346	ORL.VII PAR	0	460		2013
1347	MED.NEURO.FONOAUDIOLOGIA	0	460		2014
1348	ORL.IMPADENCIOMETRIA	0	460		2015
1349	ORL.LARINGOFIBROSCOPIA	0	460		2016
1350	ORL.POLI CHOQUE	0	460		2017
1351	OBST Y GINE.ECOTOMO II NIVEL	0	452		21
1352	PERSONAL ESTERELIDAD	0	457		21
1353	DERMATOLOGIA	0	461		2100
1354	DERM. PLAN 90 DIAS	0	461		2101
1355	DERM.CIRUGIA MENOR	0	461		2102
1356	DERM.REUNION CLINICA	0	461		2103
1357	DERMATOLOGIA INF.	0	461		2104
1358	DERMA.ETS.	0	461		2105
1359	OBST Y GINE.ECO I TRIMESTRE	0	452		22
1360	PERSONAL GINECOLOGIA.	0	457		22
1361	OBST Y GINE.HISTEROSCOPIA	0	452		23
1362	PERSONAL CIRUGIA ORAL M.F.	0	457		23
1363	OBST Y GINE.CONIZ.ELECTR.	0	452		24
1364	PERSONAL OFTALMOLOGIA	0	457		24
1365	OBST Y GINE ECOTOMO OBST.	0	452		25
1366	PERSONAL ENDOCRINO INF.	0	457		25
1367	PERSONAL GASTRO INF.	0	457		26
1368	OBST Y GINE.ECO CARDIO FETAL	0	452		27
1369	PERSONAL GENETICA	0	457		27
1370	OBST Y GINE ECO.GEMELARES	0	452		28
1371	PERSONAL HEMATO INF.	0	457		28
1372	OBST Y GINE-REEVALUACION	0	452		29
1373	PERSONAL NEFROLOGIA INF.	0	457		29
1374	MED.RECTOSCOPIAS PROC	0	448		3
1375	CITOSCOPIA.PLAN 90 DIAS	0	454		3
1376	MED.INFILTRACION	0	448		30
1377	OBST Y GINE.ECOGRAFIA 4TO PISO	0	452		30
1378	PERSONAL NEUMO INF.	0	457		30
1379	OBST Y GINE.ECOGRAFIA 4TO PISO	0	452		30-1
1380	OBST Y GINE ECO GINE 4TO PISO	0	452		31
1381	PERSONAL NEURO INF.	0	457		31
1382	MED.ECOCARDIOGRAMA	0	448		32
1383	PERSONAL NUTRICION INF.	0	457		32
1384	MED.TEST.ESFUERZO AD.	0	448		33
1385	CLINICA PEDIATRIA	0	457		33
1386	PDE.ECOCARDIOGRAMA INF.	0	449		34
1387	PERSONAL CARDIO CV.	0	457		34
1388	CIR.LISTA ESPERA	0	445		35
1389	PED.ONCO.SEGUIMIENTO	0	449		35
1390	CLINICA PSIQUIATRIA	0	457		35
1391	CIR.BIOPSIAS CORE UPM	0	445		36
1392	PED.L ESPERA ENDO INF	0	449		36
1393	PERSONAL UROLOGIA	0	457		36
1394	MED.CARDIO HOLTER	0	448		37
1395	PERSONAL PSIQUIATRIA INF.	0	457		37
1396	MED.ESPIROMETRIAS	0	448		38
1397	CLINCICA TRAUMA INF.	0	457		38
1398	MED.PROGRAMA EDUCACION	0	448		39
1399	MED.ENDO.URGENCIA TIROIDE	0	448		4
1400	PED.LISTA ESPERA NEURO	0	449		4
1401	MED.ECOCARDIO.CHAGAS	0	448		40
1402	PERSONAL OTORRINO	0	457		40
1403	MED.COLONOSCOPIAS	0	448		41
1404	CLINICA  PREVENTIVA	0	457		42
1405	MED.GASTRO RESTOSCOPIA PROC..	0	448		43
1406	MED.RECTO-LIGADURA	0	448		44
1407	CLINICA CARDIO AD.	0	457		44
1408	MED.ENDO.URG.TIROIDE	0	448		5
1409	MED.EEG SUE�O	0	448		50
1410	MED.EEG (D) AD E INF.	0	448		51
1411	MED.EEG OTROS HOSPITALES	0	448		52
1412	MED.ELECTROMIOGRAFIA	0	448		53
1413	MED.QUILLOTA INF.	0	448		54
1414	MED.EEG HOSP.QUILLOTA	0	448		55
1415	MED.E.M.G.TUNEL CARPO	0	448		56
1416	MED.EEG.QUILPUE	0	448		57
1417	MED.HEMA.BIOPSIA	0	448		58
1418	MED.HEMA.L.ESPERA	0	448		59
1419	MED.HEMA.AUGE	0	448		6
1420	PSIQ.R.CLINICA	0	455		60
1421	ODONT.ENDODONCIA	0	464		61
1422	ODONT.CIRUGIA ORAL	0	464		62
1423	ODONTOPEDIATRIA	0	464		63
1424	APOYO ESPECIALIDADES	0	464		64
1425	ODONT.PROTESIS	0	464		65
1426	ODONT.PERIDONCIA	0	464		66
1427	ODONT.DISFUNCION ATM	0	464		67
1428	ODONT.OPERATORIA FUNCIONARIOS	0	464		68
1429	RADIOLOGIA	0	464		69
1430	MED.FIBROBRONCOSCOPIAS	0	448		7
1431	ODONT.ANESTESIA GRAL	0	464		70
1432	FISIATRIA	0	465		7000
1433	MED.REHABILITACION	0	465		7001
1434	FISIATRIA-INFILTRACION	0	465		7002
1435	ODONT.CIR.AMBULATORIA	0	464		71
1436	ODONT.CIRUGIA ORAL INF.	0	464		72
1437	IMAG.ECOT.ABD.PEL.RENAL	0	466		90
1438	IMAG.ECOT.PARTES BLANDAS	0	466		91
1439	MED.CARDIO PREVENSION	0	448		971
1440	MED.NEFRO ACC.VASCULAR	0	448		972
1441	MED.CARDIO RECETAS	0	448		973
1442	CIR.INF.BRONCO Y SIALOGRAFIA	0	446		980
1443	CIR.INF.EVAL C.MENOR	0	446		981
1444	MED.PROG.SALUD CCV	0	448		985
1445	MED.TRANSPLANTE RENAL	0	448		991
1446	MED.PRE TX.RENAL	0	448		992
1447	CIR.CENTRO OSTOMIZADOS	0	445		999
1448	IMAG.PROYECCIONES ESP.	0	466		A0
1449	IMAG.ECOT.CEREBRAL	0	466		A1
1450	IMAG.ECO.GENIT-TIROIDES-CERV.	0	466		A2
1451	IMAG.ECOT.MAMARIA	0	466		A3
1452	IMAG.ECOT.CORE MAMA	0	466		A4
1453	IMAG.DOPLER VARIOS	0	466		A5
1454	IMAG.ECOT.TIROIDEA	0	466		A6
1455	IMAG.MAMOGRAFIA	0	466		A7
1456	IMAG.COL.TOTAL MENOR 30 A	0	466		A8
1457	IMAG.DOPLER CAROTIDEAS	0	466		A9
1458	CIRUGIA CARDIO VASCULAR	0	467		CCV
1459	CIRUGIA ADULTO	0	468		CIAD
1460	UCIM CIRUGIA	0	445		CIR01
1461	UCIM CIRUGIA	0	470		CIR01-1
1462	CUIDADOS ESPECIALES	0	448		CUI01
1463	CUIDADOS MINIMOS	0	472		CUMI
1464	IMAG.HUESOS ADULTOS	0	466		D1
1465	IMAG.ECOT.CERVICAL	0	466		F
1466	IMAG.ECOTOMOGRAFIA INF.	0	466		F0
1467	IMAG.SOTI CONTROL	0	466		F1
1468	IMAG.MAMOGRAFIA FUNCIONARIAS	0	466		F2
1469	GINECOLOGIA	0	473		GIN
1470	OFT.NUEVOS INF.	0	453		L1
1471	OFT.ECOGRAFIA-LASER	0	453		L2
1472	03 MEDICINA	0	477		MED
1473	UCI NEONATOLOGIA	0	478		NE01
1474	INCUBADORA	0	478		NE02
1475	CUIDADO MINIMO	0	478		NE03
1476	SEGUNDA INFANCIA	0	478		NEON
1477	OBSTETRICIA	0	479		OBGI
1478	LACTANTES	0	480		PED01
1479	2A INFANCIA	0	480		PED02
1480	ONCOLOGIA	0	480		PED03
1481	INFECCIOSO	0	480		PED04
1482	UCI PEDIATRICA	0	480		PED05
1483	UCIM PEDIATRICA	0	480		PED06
1484	SOTA	0	483		SOTA
1485	SOTI	0	484		SOTI15
1486	UCI ADULTO	0	487		UCAD
1487	UCIN CCV	0	488		UCCV
1488	UCI PEDIATRICA	0	490		UCII
1489	UCIN MEDICINA	0	491		UCIN
1490	UCIN CIRUGIA	0	492		UCINC
1491	UCI INDEFERENCIADA	0	493		UCIND
1492	UCIN PEDIATRICA	0	494		UCINP
1493	UNIDAD EMERGENCIA INFANTIL	0	495		UEI
1494	UCI NEO	0	496		UNEO
1495	XXXX	0	444		XXXX
\.


--
-- TOC entry 2423 (class 2606 OID 41579)
-- Dependencies: 1969 1969
-- Name: esp_id; Type: CONSTRAINT; Schema: public; Owner: sistema; Tablespace: 
--

ALTER TABLE ONLY especialidades
    ADD CONSTRAINT esp_id PRIMARY KEY (esp_id);


-- Completed on 2010-06-24 15:13:01 CLT

--
-- PostgreSQL database dump complete
--

