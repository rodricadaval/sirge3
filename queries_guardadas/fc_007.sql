INSERT INTO estadisticas.fc_007(id_provincia,periodo,periodo_prestacion,cantidad_dr,cantidad_total_dr,cantidad)
	(
		SELECT *
		FROM dblink('dbname=sirge host=192.6.0.118 user=postgres password=PN2012\$',
		    'SELECT 
				p.id_provincia as "PROVINCIA"
				, extract(''year'' from l.fecha_cierre_lote)::varchar || lpad(extract(''mons'' from l.fecha_cierre_lote)::varchar, 2,''0'') as "PRESENTADO"
				, extract(''year'' from fecha_prestacion)::varchar || lpad(extract(''mons'' from fecha_prestacion)::varchar, 2,''0'') as "PERIODO PRESTACION"								
				, sum(case 
					when datos_reportables IS NOT NULL 
					     AND datos_reportables NOT IN (''{"":""}'',''{" ":" "}'',''[""]'',''{"": ""}'',''{" ": " "}'') 
					     AND codigo_prestacion IN (select dr.codigo_prestacion FROM pss.codigos_datos_reportables dr)
					     THEN 1
					ELSE 0 END) as "DATOS REPORTABLES"
				, sum(case 
					when codigo_prestacion IN (select codigo_prestacion FROM pss.codigos_datos_reportables)
				     THEN 1
				ELSE 0 END) as "TOTAL QUE DEBERIAN TENER D. REPORTABLES"
				, count(p.codigo_prestacion) as "TOTAL PRESTACIONES REPORTADAS"
				FROM prestaciones.prestaciones p
				INNER join sistema.lotes l
				on l.lote = p.lote 											
				AND l.id_estado = ''1''
				GROUP BY 1,2,3
				ORDER BY 1,2,3')
		    AS migracion( id_provincia character(2),
				  periodo integer,
				  periodo_prestacion integer,				  
				  cantidad_dr bigint, 
				  cantidad_total_dr bigint,
				  cantidad bigint				 
				 )			
	); 	