<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class SibiService
{
    protected $url;
    protected $token;

    public function __construct()
    {
        // Jalamos la configuración del .env
        //$this->url = env('SIBI_API_URL', 'https://test.api.sibi.pe/graphql'); //pruebas
        $this->url = env('SIBI_API_URL', 'https://api.sibi.pe/graphql'); //producción
        $this->token = env('SIBI_TOKEN');
    }

    /**
     * Lógica central para emitir boletas o facturas con series dinámicas
     */
    public function emitirComprobante($pago, $tipoDocSibi)
    {
        // 1. MAPEAMOS LA LETRA SEGÚN EL TRÁMITE
        //$identificadores = [
        //  'Habilitacion' => 'H', // Para Cuotas de Habilidad
        //  'Constancia'   => 'C', // Para Trámites de Constancias
        //  'Carnet'       => 'A', // Para Solicitud de Carnet
        //];

        $mesesNombres = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // --- LÓGICA DE DESCRIPCIÓN DINÁMICA ---
        $descripcionFinal = "";

        if ($pago->tipo_pago === 'Habilitacion') {
            // Años: Si es el mismo año solo pone uno, sino pone el rango
            $anioTxt = ($pago->año_inicio == $pago->año_final) ? $pago->año_inicio : $pago->año_inicio . ' al ' . $pago->año_final;

            // Meses: Si es el mismo mes solo uno, sino pone "Mes A y Mes B"
            $mesTxt = ($pago->mes_inicio == $pago->mes_final)
                ? $mesesNombres[$pago->mes_inicio]
                : $mesesNombres[$pago->mes_inicio] . ' y ' . $mesesNombres[$pago->mes_final];

            $descripcionFinal = "PAGO POR HABILITACIÓN DEL PERIODO: " . $anioTxt . " EN EL MES(ES) " . $mesTxt;

        } elseif ($pago->tipo_pago === 'Constancia') {
            $descripcionFinal = "PAGO POR HABILITACIÓN DE CONSTANCIA";

        } elseif ($pago->tipo_pago === 'Carnet') {
            // Si existe la relación carnet usamos el tipo_tramite (Colegiatura/Duplicado), sino por defecto DUPLICADO
            $tipoTramite = isset($pago->carnet) ? strtoupper($pago->carnet->tipo_tramite) : 'DUPLICADO';
            $descripcionFinal = "PAGO POR " . $tipoTramite . " DE CARNET";
        }

        // Convertimos $pago a objeto si viene como array para que el resto del código no falle
        if (is_array($pago)) {
            $pago = (object) $pago;
        }

        // 2. CONSTRUIMOS LA SERIE (Ejemplo: BH01 o FH01)
        $letraDoc = ($tipoDocSibi === '03') ? 'B' : 'F'; // B = Boleta, F = Factura
        //$letraTipo = $identificadores[$pago->tipo_pago] ?? 'X';
        //$serieFinal = $letraDoc . $letraTipo . '01';
        $serieFinal = $letraDoc.'102';

        // Cálculos requeridos por la documentación
        $igvType = '30';
        $montoTotal = (float) $pago->monto;

        if ($igvType === '30') {
            // Lógica para INAFECTO: La base es el total y el IGV es cero
            $valorVenta = $montoTotal;
            $igvTotal = 0.00;
            $opGravada = 0.00;
            $opInafecta = $montoTotal;
        } else {
            // Lógica para GRAVADO: Se desglosa el 18%
            $valorVenta = round($montoTotal / 1.18, 2);
            $igvTotal = round($montoTotal - $valorVenta, 2);
            $opGravada = $valorVenta;
            $opInafecta = 0.00;
        }
        //$valorVenta = round($montoTotal / 1.18, 2);
        //$igvTotal = round($montoTotal - $valorVenta, 2);

        // 3. DEFINIMOS LA MUTACIÓN GRAPHQL
        $query = '
            mutation Sales(
                $coin: String!,
                $document_type: String!,
                $serie: String!,
                $total_price: Float!,
                $pdf: String!,
                $proforma: Boolean!,
                $created_from: Float!,
                $contact_identity: String,
                $contact_name: String,
                $taxable_operations: Float,
                $unaffected_operations: Float,
                $total_igv: Float,
                $details: [SaleDetailsInput!]!
            ) {
                sales(
                    coin: $coin,
                    document_type: $document_type,
                    serie: $serie,
                    total_price: $total_price,
                    pdf: $pdf,
                    proforma: $proforma,
                    created_from: $created_from,
                    contact_identity: $contact_identity,
                    contact_name: $contact_name,
                    taxable_operations: $taxable_operations,
                    total_igv: $total_igv,
                    details: $details
                ) {
                    serie
                    number
                    total_price
                }
            }
        ';

        // 4. PREPARAMOS LOS DATOS PARA SIBI
        $variables = [
            'coin' => 'PEN', // Requerido [cite: 6]
            'document_type' => $tipoDocSibi, // Requerido [cite: 6]
            'serie' => $serieFinal, // Requerido [cite: 6]
            'total_price' => $montoTotal, // Requerido [cite: 7]
            'pdf' =>'comprobante_' . $pago->id . '.pdf', // Requerido [cite: 7]
            'proforma' => false, // Requerido [cite: 7]
            'created_from' => 1, // Requerido [cite: 7]
            'contact_identity' => ($tipoDocSibi === '01') ? $pago->agremiado->ruc : $pago->agremiado->dni, // [cite: 9]
            'contact_name' => $pago->agremiado->nombres . ' ' . $pago->agremiado->apellidos,
            'taxable_operations' => $opGravada, // [cite: 12]
            'unaffected_operations' => $opInafecta,
            'total_igv' => $igvTotal, // [cite: 12]
            'details' => [
                [
                    'item_id' => null,
                    'description' => $descripcionFinal,
                    'quantity' => 1,
                    'unit_measure' => 'NIU', // Unidad ZZ para Servicios [cite: 21]
                    'unit_value' => $valorVenta,
                    'unit_price' => $montoTotal,
                    'sale_value' => $valorVenta,
                    'igv' => $igvTotal,
                    'igv_type' => $igvType, // 10 = Gravado 20=Exonerado, 30=Inafecto, 21=Gratuito
                ]
            ]
        ];

        // 5. ENVIAMOS LA PETICIÓN CON SEGURIDAD (Try-Catch)
        try {
            $response = Http::withToken($this->token)->post($this->url, [
                'query' => $query,
                'variables' => $variables
            ]);

            if ($response->failed()) {
                Log::error("SIBI API Error: " . $response->body());
                return ['error' => 'Error en la respuesta de SIBI'];
            }

            return $response->json();

        } catch (\Exception $e) {
            Log::error("Excepción en SibiService: " . $e->getMessage());
            return ['error' => 'No se pudo conectar con el servidor de facturación'];
        }
    }
}
