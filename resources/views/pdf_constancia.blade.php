<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        /* 1. Configuración de página base */
        @page {
            margin: 0cm 0cm; /* Importante para la barra azul pegada al borde */
        }

        /* Estilo para la marca de agua */
        #watermark {
            position: fixed;
            top: 25%;    /* Ajusta la posición vertical */
            left: 15%;   /* Ajusta la posición horizontal */
            width: 70%;  /* Tamaño de la imagen */
            /** * El z-index debe ser muy bajo para quedar detrás del texto 
            * La opacidad ideal para marcas de agua es entre 0.1 y 0.2
            **/
            opacity: 0.1; 
            z-index: -2000;
        }
        
        /* Si necesito que la imagen esté centrada y rotada */
        #watermark img {
            width: 100%;
            /*transform: rotate(-45deg);*/ /* Opcional: rotación clásica de marca de agua */
        }

        body {
            font-family: 'Helvetica', sans-serif;
            /* El margen superior debe ser estrictamente mayor que el alto del header */
            margin-top: 4.2cm;
            margin-bottom: 2cm;
            margin-left: 2cm;
            margin-right: 2cm;
            line-height: 1.4;
            font-size: 11pt;
            color: #333;
        }

        /* 2. Header Fijo (Sin floats para evitar bucles) */
        #header {
            position: fixed;
            top: 0px;
            left: 0px;
            right: 0px;
            height: 3.5cm; /* Altura fija definida */
            width: 100%;
            z-index: -1000;
        }

        .header-img {
            width: 100%;
        }

        /* 3. Footer Fijo */
        #footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 1.5cm; /* Altura ajustada para el texto de contacto */
            padding: 0 2cm;
            z-index: -1000;
        }

        .footer-line {
            border-top: 1px solid #999;
            margin-bottom: 5px;
        }

        .footer-table {
            width: 100%;
            font-size: 9pt;
            color: #666;
            border-collapse: collapse; /* Evita espacios extra */
        }

        .footer-table td {
            vertical-align: top;
            line-height: 1.2;
        }

        /* 4. Estilos de Contenido */
        .doc-title {
            text-align: center;
            font-weight: bold;
            font-size: 15pt;
            margin-bottom: 5px;
            margin-top: 25px;
        }
        .decano-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 25px;
        }
        .nombre-contador {
            color: black;
            font-size: 18pt;
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            text-transform: uppercase;
        }
        .data-highlight { color: black; font-weight: bold; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <div id="watermark">
        <img src="{{ public_path('assets/img/Logo_marca_agua.png') }}" alt="Watermark">
    </div>
    <div id="header">
        <img src="{{ public_path('assets/img/logo_colegio_full.png') }}" class="header-img">
    </div>
    <div class="doc-title">CONSTANCIA DE HABILITACIÓN PROFESIONAL</div>
    <div class="decano-title">EL DECANO DEL COLEGIO DE CONTADORES PÚBLICOS DE HUÁNUCO</div>

    <div class="content">
        <p style="font-size: 18px;">Hace constar que:</p>

        <div class="nombre-contador">{{ $nombres }}</div>

        <p style="text-align: justify; font-size: 18px" >Contador Público Colegiado con matrícula N° <span class="data-highlight">{{ $matricula }}</span> se encuentra <span class="data-highlight">{{ $estado }}</span> para el ejercicio de la profesión de Contador Público en concordancia con el Art. 2 de la ley N° 28951 de Profesionalización del Contador Público y el Art. 6° del estatuto de nuestro Colegio.</p>

        <p style="font-size: 18px;">La presente constancia de habilitación profesional tiene vigencia hasta el <span class="data-highlight">{{ $dia_fin }}</span> de <span class="data-highlight">{{ $mes_fin }}</span> del <span class="data-highlight">{{ $año_fin }}</span>.</p>

        <p style="font-size: 18px;">Se expide la presente, para los fines que estime conveniente.</p>

        <p style="text-align: right; margin-top: 40px; font-size: 18px">
            Huánuco, <span class="data-highlight">{{ $fecha_hoy }}</span>
        </p>
    </div>

    <div id="footer">
        <div class="footer-line"></div>
        <table class="footer-table">
            <tr>
                <td style="text-align: left; width: 60%;">
                    Jr. Tarapacá N° 766 - Huánuco<br>
                    www.colegiocontadoreshco.org.pe
                </td>
                <td style="text-align: right; width: 40%;">
                    Cel: 962 648 190<br>
                    ccphuanuco2324@gmail.com
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
