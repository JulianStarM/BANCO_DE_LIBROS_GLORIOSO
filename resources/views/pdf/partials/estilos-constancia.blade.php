<style>
    @page {
        margin: 10mm 12mm 12mm 12mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-size: 9.5pt;
        color: #1a1a1a;
        margin: 0;
        padding: 0;
        line-height: 1.25;
    }

    .page {
        width: 100%;
        position: relative;
        padding: 4mm 3mm;
    }

    /* Borde decorativo institucional */
    .border-outer {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: 1.5px solid #1e3a5f;
        pointer-events: none;
        z-index: 0;
    }

    .border-inner {
        position: absolute;
        top: 2mm;
        left: 2mm;
        right: 2mm;
        bottom: 2mm;
        border: 0.5px solid #bfa15f;
        pointer-events: none;
        z-index: 0;
    }

    .content-wrapper {
        position: relative;
        z-index: 1;
    }

    /* Encabezado */
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2mm;
        padding-bottom: 2mm;
        border-bottom: 1.5px solid #1e3a5f;
    }

    .header .logo-area img {
        max-height: 16mm;
        width: auto;
        object-fit: contain;
    }

    .header .institution {
        text-align: right;
    }

    .header .institution .name {
        font-size: 8.5pt;
        font-weight: 700;
        color: #1e3a5f;
        text-transform: uppercase;
        letter-spacing: 0.3pt;
        margin: 0 0 0.3mm 0;
    }

    .header .institution .subname {
        font-size: 8pt;
        font-weight: 600;
        color: #1e3a5f;
        margin: 0 0 0.3mm 0;
    }

    .header .institution .meta {
        font-size: 6.5pt;
        color: #555;
        margin: 0;
    }

    /* Título */
    .title-area {
        text-align: center;
        margin: 2mm 0;
    }

    .title-area h1 {
        font-size: 12pt;
        font-weight: 700;
        color: #1e3a5f;
        text-transform: uppercase;
        letter-spacing: 0.6pt;
        margin: 0 0 1mm 0;
        border-bottom: 1px solid #bfa15f;
        display: inline-block;
        padding-bottom: 0.5mm;
    }

    .title-area .codigo {
        font-size: 8pt;
        color: #444;
        margin: 0;
    }

    .title-area .codigo strong {
        color: #1e3a5f;
        font-size: 8.5pt;
    }

    /* Texto introductorio */
    .intro {
        font-size: 9pt;
        text-align: justify;
        margin-bottom: 2mm;
        text-indent: 4mm;
    }

    /* Cajas de datos */
    .data-box {
        border: 1px solid #c9d1d9;
        margin-bottom: 2mm;
        background: #fafbfc;
    }

    .data-box .box-title {
        background: #1e3a5f;
        color: #fff;
        font-size: 7.5pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3pt;
        padding: 1mm 2mm;
        margin: 0;
    }

    .data-box .box-body {
        padding: 1.5mm 2mm;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
    }

    .data-table td {
        padding: 0.75mm 1mm;
        vertical-align: top;
    }

    .data-table td.label {
        width: 20%;
        font-weight: 600;
        color: #1e3a5f;
    }

    .data-table td.value {
        width: 30%;
        color: #222;
    }

    /* Tablas de libros */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
    }

    .items-table th {
        background: #1e3a5f;
        color: #fff;
        font-weight: 700;
        text-align: left;
        padding: 0.8mm 1.5mm;
        border: 1px solid #1e3a5f;
        text-transform: uppercase;
        font-size: 6.5pt;
        letter-spacing: 0.2pt;
        white-space: nowrap;
    }

    .items-table td {
        padding: 0.8mm 1.5mm;
        border: 1px solid #c9d1d9;
        vertical-align: top;
    }

    .items-table tr:nth-child(even) {
        background: #f5f7fa;
    }

    .items-table .text-center {
        text-align: center;
    }

    .items-table .text-right {
        text-align: right;
    }

    /* Resumen de devolución */
    .summary-box {
        display: flex;
        gap: 1.5mm;
        margin: 2mm 0;
    }

    .summary-item {
        flex: 1;
        border: 1px solid #c9d1d9;
        text-align: center;
        padding: 1.5mm 1mm;
        background: #fff;
    }

    .summary-item .number {
        font-size: 11pt;
        font-weight: 700;
        color: #1e3a5f;
        display: block;
    }

    .summary-item .label {
        font-size: 6.5pt;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.2pt;
    }

    /* Tabla resumen de estados */
    .summary-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 7.5pt;
        margin: 2mm 0;
    }

    .summary-table th {
        background: #1e3a5f;
        color: #fff;
        font-weight: 700;
        text-align: left;
        padding: 1mm 1.5mm;
        border: 1px solid #1e3a5f;
        text-transform: uppercase;
        font-size: 6.5pt;
        letter-spacing: 0.2pt;
        white-space: nowrap;
    }

    .summary-table td {
        padding: 0.8mm 1.5mm;
        border: 1px solid #c9d1d9;
        vertical-align: top;
    }

    .summary-table tr:nth-child(even) {
        background: #f5f7fa;
    }

    .summary-table .concepto {
        font-weight: 700;
        color: #1e3a5f;
        width: 22%;
        white-space: nowrap;
    }

    .summary-table .cantidad {
        font-weight: 700;
        text-align: center;
        width: 12%;
    }

    .summary-table .detalle {
        color: #444;
        font-size: 7pt;
    }

    /* Nota legal */
    .legal-note {
        font-size: 7.5pt;
        font-weight: 700;
        color: #1e3a5f;
        text-align: center;
        margin: 2mm 0;
        padding: 1mm;
        border-top: 1px solid #bfa15f;
        border-bottom: 1px solid #bfa15f;
    }

    /* Firmas */
    .signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 3mm;
        gap: 6mm;
    }

    .signature {
        flex: 1;
        text-align: center;
    }

    .signature .line {
        border-top: 1px solid #1a1a1a;
        margin: 0 auto 1mm auto;
        width: 80%;
        height: 5mm;
    }

    .signature .name {
        font-size: 7.5pt;
        font-weight: 600;
        color: #1e3a5f;
        margin: 0;
    }

    .signature .role {
        font-size: 7pt;
        color: #555;
        margin: 0;
    }

    /* Pie de página */
    .footer {
        text-align: center;
        font-size: 6.5pt;
        color: #666;
        border-top: 0.5px solid #ccc;
        padding-top: 1mm;
        margin-top: 3mm;
    }

    .footer strong {
        color: #1e3a5f;
    }

    /* Utilidades */
    .uppercase {
        text-transform: uppercase;
    }

    .fw-bold {
        font-weight: 700;
    }

    .mb-0 {
        margin-bottom: 0;
    }

    .mt-0 {
        margin-top: 0;
    }

    .text-muted {
        color: #666;
    }

    .text-center {
        text-align: center;
    }

    .w-100 {
        width: 100%;
    }
</style>