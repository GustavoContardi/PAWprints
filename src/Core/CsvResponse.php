<?php

namespace Core;

class CsvResponse
{
    /**
     * Envía una respuesta CSV al navegador.
     *
     * @param string $filename Nombre del archivo (ej: "catalogo.csv")
     * @param array $headers Encabezados de las columnas
     * @param array $rows Filas de datos
     */
    public static function send(string $filename, array $headers, array $rows): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // BOM para que Excel interprete UTF-8 correctamente
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if (!empty($headers)) {
            fputcsv($output, $headers);
        }

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
