<style>
    @page {
        margin: 12mm 14mm 16mm 14mm;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        font-size: 10pt;
        color: #1a1a1a;
        margin: 0;
        padding: 0;
        line-height: 1.35;
    }

    .page {
        width: 100%;
        position: relative;
        padding: 6mm 4mm;
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
        top: 2.5mm;
        left: 2.5mm;
        right: 2.5mm;
        bottom: 2.5mm;
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
        margin-bottom: 4mm;
        padding-bottom: 3mm;
        border-bottom: 1.5px solid #1e3a5f;
    }

    .header .logo-area img {
        max-height: 18mm;
        width: auto;
        object-fit: contain;
    }

    .header .institution {
        text-align: right;
    }

    .header .institution .name {
        font-size: 9pt;
        font-weight: 700;
        color: #1e3a5f;
        text-transform: uppercase;
        letter-spacing: 0.3pt;
        margin: 0 0 0.5mm 0;
    }

    .header .institution .subname {
        font-size: 8.5pt;
        font-weight: 600;
        color: #1e3a5f;
        margin: 0 0 0.5mm 0;
    }

    .header .institution .meta {
        font-size: 7pt;
        color: #555;
        margin: 0;
    }

    /* Título */
    .title-area {
        text-align: center;
        margin: 4mm 0 4mm 0;
    }

    .title-area h1 {
        font-size: 12pt;
        font-weight: 700;
        color: #1e3a5f;
        text-transform: uppercase;
        letter-spacing: 0.8pt;
        margin: 0 0 1.5mm 0;
        border-bottom: 1px solid #bfa15f;
        display: inline-block;
        padding-bottom: 1mm;
    }

    .title-area .codigo {
        font-size: 8.5pt;
        color: #444;
        margin: 0;
    }

    .title-area .codigo strong {
        color: #1e3a5f;
        font-size: 9pt;
    }

    /* Texto introductorio */
    .intro {
        font-size: 9.5pt;
        text-align: justify;
        margin-bottom: 3mm;
        text-indent: 6mm;
    }

    /* Cajas de datos */
    .data-box {
        border: 1px solid #c9d1d9;
        margin-bottom: 3mm;
        background: #fafbfc;
    }

    .data-box .box-title {
        background: #1e3a5f;
        color: #fff;
        font-size: 8pt;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.4pt;
        padding: 1.5mm 2.5mm;
        margin: 0;
    }

    .data-box .box-body {
        padding: 2mm 2.5mm;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8.5pt;
    }

    .data-table td {
        padding: 1mm 1.5mm;
        vertical-align: top;
    }

    .data-table td.label {
        width: 22%;
        font-weight: 600;
        color: #1e3a5f;
    }

    .data-table td.value {
        width: 28%;
        color: #222;
    }

    /* Tablas de libros */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 8pt;
        margin-top: 1mm;
    }

    .items-table th {
        background: #1e3a5f;
        color: #fff;
        font-weight: 700;
        text-align: left;
        padding: 1.5mm 2mm;
        border: 1px solid #1e3a5f;
        text-transform: uppercase;
        font-size: 7.5pt;
        letter-spacing: 0.2pt;
    }

    .items-table td {
        padding: 1.5mm 2mm;
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
        gap: 2mm;
        margin: 3mm 0;
    }

    .summary-item {
        flex: 1;
        border: 1px solid #c9d1d9;
        text-align: center;
        padding: 2mm 1.5mm;
        background: #fff;
    }

    .summary-item .number {
        font-size: 12pt;
        font-weight: 700;
        color: #1e3a5f;
        display: block;
    }

    .summary-item .label {
        font-size: 7pt;
        color: #555;
        text-transform: uppercase;
        letter-spacing: 0.2pt;
    }

    /* Nota legal */
    .legal-note {
        font-size: 8pt;
        font-weight: 700;
        color: #1e3a5f;
        text-align: center;
        margin: 3mm 0;
        padding: 1.5mm;
        border-top: 1px solid #bfa15f;
        border-bottom: 1px solid #bfa15f;
    }

    /* Firmas */
    .signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 6mm;
        gap: 8mm;
    }

    .signature {
        flex: 1;
        text-align: center;
    }

    .signature .line {
        border-top: 1px solid #1a1a1a;
        margin: 0 auto 1.5mm auto;
        width: 80%;
        height: 8mm;
    }

    .signature .name {
        font-size: 8pt;
        font-weight: 600;
        color: #1e3a5f;
        margin: 0;
    }

    .signature .role {
        font-size: 7.5pt;
        color: #555;
        margin: 0;
    }

    /* Pie de página */
    .footer {
        text-align: center;
        font-size: 7pt;
        color: #666;
        border-top: 0.5px solid #ccc;
        padding-top: 1.5mm;
        margin-top: 5mm;
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
