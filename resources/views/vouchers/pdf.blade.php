<!doctype html>
<html lang="es-AR">
<head>
    <meta charset="utf-8">
    <title>Vouchers Cantina La 35</title>
    <style>
        @page { margin: 12mm 10mm; }
        body { margin: 0; font: 10pt Helvetica, sans-serif; color: #1e2633; }
        table { width: 100%; table-layout: fixed; border-collapse: collapse; }
        td { width: 50%; height: 44mm; padding: 0; vertical-align: middle; }
        .voucher { border: 1px dashed #687483; }
        .content { padding: 2mm 5mm; overflow-wrap: break-word; }
        h1 { margin: 0 0 2mm; font-size: 13pt; }
        p { margin: 0 0 1mm; }
        .validity { margin-top: 2mm; font-size: 8pt; }
        .next-page { page-break-before: always; }
    </style>
</head>
<body>
    @foreach ($pages as $page)
        <div @class(['next-page' => !$loop->first])>
            <table>
                @foreach ($page->chunk(2) as $row)
                    <tr>
                        @foreach ($row as $voucher)
                            <td class="voucher">
                                <div class="content">
                                    <h1>Voucher Cantina «La 35»</h1>
                                    <p><strong>Nombre:</strong> {{ $voucher['name'] }}</p>
                                    <p><strong>Fecha:</strong> {{ $voucher['date'] }}</p>
                                    <p><strong>Comida:</strong> {{ $voucher['meal'] }}</p>
                                    <p class="validity">No transferible · Sujeto a actividad escolar</p>
                                </div>
                            </td>
                        @endforeach
                        @if ($row->count() === 1)
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</body>
</html>
