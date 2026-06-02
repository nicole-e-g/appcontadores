<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ParticipanteCongreso;
use Illuminate\Support\Facades\Storage;

class ImportarParticipantesCongreso extends Command
{
    protected $signature = 'congreso:importar {archivo}';
    protected $description = 'Importa los participantes del congreso desde un archivo CSV';

    public function handle()
    {
        $nombreArchivo = $this->argument('archivo');
        $rutaCompleta = storage_path('app/' . $nombreArchivo);

        if (!file_exists($rutaCompleta)) {
            $this->error("El archivo no existe en la ruta: storage/app/{$nombreArchivo}");
            return 1;
        }

        $this->info("Iniciando la importación de: {$nombreArchivo}...");

        $gestor = fopen($rutaCompleta, 'r');

        // 1. Saltamos la cabecera y usamos PUNTO Y COMA (;)
        fgetcsv($gestor, 1000, ";");

        $insertados = 0;
        $actualizados = 0;

        // 2. Recorremos fila por fila con separador ";"
        while (($datos = fgetcsv($gestor, 1000, ";")) !== FALSE) {

            // Si la fila está vacía o no tiene DNI (posición 0), la saltamos
            if (!isset($datos[0]) || empty(trim($datos[0]))) {
                continue;
            }

            // Orden real según tu archivo CSV:
            // 0: DNI | 1: Nombres | 2: Apellidos | 3: Correo | 4: Celular | 5: Modalidad

            // Función auxiliar para limpiar y arreglar tildes/eñes
            $limpiar = function($texto) {
                return trim(mb_convert_encoding($texto ?? '', 'UTF-8', 'ISO-8859-1'));
            };

            $dni = $limpiar($datos[0]);

            $participante = ParticipanteCongreso::updateOrCreate(
                ['dni' => $dni],
                [
                    'nombres'   => strtoupper($limpiar($datos[1])),
                    'apellidos' => strtoupper($limpiar($datos[2])),
                    'email'     => strtolower($limpiar($datos[3])) ?: null,
                    'celular'   => $limpiar($datos[4]) ?: null,
                    'modalidad' => $limpiar($datos[5]) ?: 'Presencial',
                ]
            );

            if ($participante->wasRecentlyCreated) {
                $insertados++;
            } else {
                $actualizados++;
            }
        }

        fclose($gestor);

        $this->info("¡Importación finalizada con éxito!");
        $this->line("<info>[+] Nuevos participantes:</info> {$insertados}");
        $this->line("<info>[~] Registros actualizados:</info> {$actualizados}");

        return 0;
    }
}
