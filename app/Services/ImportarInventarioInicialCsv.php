<?php

namespace App\Services;

use App\Models\Inventario;
use App\Models\Marca;
use App\Models\Movimiento;
use App\Models\Presentacion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ImportarInventarioInicialCsv
{
    // Este contrato fija el encabezado del archivo exportado; las demás columnas se ignoran.
    private const COLUMNAS_REQUERIDAS = [
        'SKU',
        'Tipo',
        'Nombre',
        'Marca',
        'Existencias actuales',
        'Costo unitario',
        'Precio unitario',
        'Precio unitario por docena',
        'Precio unitario por ciento',
        'Stock mínimo',
    ];

    public function importar(UploadedFile $archivo, $usuarioId): int
    {
        // La importación solo abre una transacción después de validar todas las filas del CSV.
        $filas = $this->leerFilas($archivo);

        // SKU, marcas, inventario, presentación y movimiento se guardan como una sola operación atómica.
        return DB::transaction(function () use ($filas, $usuarioId) {
            $this->validarSkusDisponibles($filas);
            $marcasExistentes = $this->marcasExistentes();
            $marcasNuevas = [];
            $creadas = 0;

            foreach ($filas as $fila) {
                $claveMarca = $fila['marca'] === ''
                    ? null
                    : $this->claveNombre($fila['marca']);
                $marcaId = null;

                if ($claveMarca !== null) {
                    $coincidencias = $marcasExistentes[$claveMarca] ?? [];
                    if (count($coincidencias) > 1) {
                        throw ValidationException::withMessages([
                            'archivo' => [
                                "Fila {$fila['numero']}: la marca \"{$fila['marca']}\" coincide con varias marcas existentes. Corrige el catálogo antes de importar.",
                            ],
                        ]);
                    }

                    if (count($coincidencias) === 1) {
                        $marcaId = $coincidencias[0];
                    } elseif (isset($marcasNuevas[$claveMarca])) {
                        $marcaId = $marcasNuevas[$claveMarca];
                    } else {
                        $marca = Marca::create(['nombre' => $fila['marca']]);
                        $marcaId = $marca->id;
                        $marcasNuevas[$claveMarca] = $marcaId;
                    }
                }

                $inventario = Inventario::create([
                    'tipo' => $fila['tipo'],
                    'costoPromedioUnitario' => $fila['costo_unitario'],
                    'cantidad' => $fila['cantidad'],
                    'umbral' => $fila['umbral'],
                    'marca_id' => $marcaId,
                    'categoria_id' => null,
                    'estado' => true,
                ]);

                $inventario->presentaciones()->create([
                    'codigoSKU' => $fila['sku'],
                    'nombre' => $fila['nombre'],
                    'ubicacion' => $fila['ubicacion'],
                    'factor' => 1,
                    'base' => true,
                    'precioUnitario' => $fila['precio_unitario'],
                    'precioUnitarioDocena' => $fila['precio_docena'],
                    'precioUnitarioCiento' => $fila['precio_ciento'],
                    'estado' => true,
                ]);

                if ($fila['cantidad'] > 0) {
                    Movimiento::create([
                        'usuario_id' => $usuarioId,
                        'inventario_id' => $inventario->id,
                        'tipo_movimiento' => 'ENTRADA',
                        'cantidad_anterior' => 0,
                        'cantidad' => $fila['cantidad'],
                        'cantidad_posterior' => $fila['cantidad'],
                        'motivo' => 'Inventario inicial importado desde CSV',
                    ]);
                }

                $creadas++;
            }

            return $creadas;
        });
    }

    private function leerFilas(UploadedFile $archivo): array
    {
        // Lee UTF-8/CSV, valida encabezados y acumula errores por fila antes de permitir escrituras.
        $ruta = $archivo->getRealPath();
        if ($ruta === false) {
            throw ValidationException::withMessages([
                'archivo' => 'No se pudo leer el archivo CSV.',
            ]);
        }

        $stream = fopen($ruta, 'rb');
        if ($stream === false) {
            throw ValidationException::withMessages([
                'archivo' => 'No se pudo leer el archivo CSV.',
            ]);
        }

        try {
            $encabezados = fgetcsv($stream, 0, ',', '"', '');
            if ($encabezados === false) {
                throw ValidationException::withMessages([
                    'archivo' => 'El archivo CSV está vacío.',
                ]);
            }

            $encabezados[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $encabezados[0]);
            $encabezados = array_map(
                static fn ($encabezado) => trim((string) $encabezado),
                $encabezados
            );

            if (count($encabezados) !== count(array_unique($encabezados))) {
                throw ValidationException::withMessages([
                    'archivo' => 'El archivo contiene nombres de columna repetidos.',
                ]);
            }

            $faltantes = array_diff(self::COLUMNAS_REQUERIDAS, $encabezados);
            if ($faltantes !== []) {
                throw ValidationException::withMessages([
                    'archivo' => 'Faltan estas columnas requeridas: '.implode(', ', $faltantes).'.',
                ]);
            }

            $filas = [];
            $errores = [];
            $numeroFila = 1;
            $clavesSku = [];

            while (($valores = fgetcsv($stream, 0, ',', '"', '')) !== false) {
                $numeroFila++;
                if (count(array_filter($valores, static fn ($valor) => trim((string) $valor) !== '')) === 0) {
                    continue;
                }

                if (count($valores) !== count($encabezados)) {
                    $this->agregarError(
                        $errores,
                        "Fila {$numeroFila}: la cantidad de valores no coincide con los encabezados."
                    );

                    continue;
                }

                $datos = array_combine($encabezados, $valores);
                if ($datos === false) {
                    $this->agregarError($errores, "Fila {$numeroFila}: no se pudo leer el registro.");

                    continue;
                }

                foreach ($datos as $valor) {
                    if (! mb_check_encoding((string) $valor, 'UTF-8')) {
                        $this->agregarError($errores, "Fila {$numeroFila}: el archivo debe estar codificado en UTF-8.");

                        continue 2;
                    }
                }

                $fila = $this->validarFila($datos, $numeroFila, $errores);
                if ($fila === null) {
                    continue;
                }

                $claveSku = $this->claveSku($fila['sku']);
                if (isset($clavesSku[$claveSku])) {
                    $this->agregarError(
                        $errores,
                        "Fila {$numeroFila}: el SKU \"{$fila['sku']}\" está repetido en el archivo (fila {$clavesSku[$claveSku]})."
                    );

                    continue;
                }

                $clavesSku[$claveSku] = $numeroFila;
                $filas[] = $fila;
            }

            if ($filas === [] && $errores === []) {
                $this->agregarError($errores, 'El archivo no contiene registros para importar.');
            }

            if ($errores !== []) {
                throw ValidationException::withMessages(['archivo' => $errores]);
            }

            return $filas;
        } finally {
            fclose($stream);
        }
    }

    private function validarFila(array $datos, int $numeroFila, array &$errores): ?array
    {
        // Los campos de texto se limpian; los numéricos conservan espacios para que el validador los rechace.
        $erroresAntes = count($errores);
        $valor = static fn (string $columna) => trim((string) ($datos[$columna] ?? ''));
        $valorNumerico = static fn (string $columna) => (string) ($datos[$columna] ?? '');

        $sku = $valor('SKU');
        if ($sku === '' || mb_strlen($sku, 'UTF-8') > 50) {
            $this->agregarError($errores, "Fila {$numeroFila}: SKU es obligatorio y debe tener hasta 50 caracteres.");
        }

        $nombre = $valor('Nombre');
        if ($nombre === '' || mb_strlen($nombre, 'UTF-8') > 100) {
            $this->agregarError($errores, "Fila {$numeroFila}: Nombre es obligatorio y debe tener hasta 100 caracteres.");
        }

        $tipo = mb_strtoupper($valor('Tipo'), 'UTF-8');
        if (! in_array($tipo, ['PRODUCTO', 'SERVICIO'], true)) {
            $this->agregarError($errores, "Fila {$numeroFila}: Tipo debe ser Producto o Servicio.");
        }

        $marca = $this->normalizarEspacios($valor('Marca'));
        if (mb_strlen($marca, 'UTF-8') > 255) {
            $this->agregarError($errores, "Fila {$numeroFila}: Marca debe tener hasta 255 caracteres.");
        }

        $ubicacion = $this->normalizarEspacios($valor('Ubicación'));
        if (mb_strlen($ubicacion, 'UTF-8') > 100) {
            $this->agregarError($errores, "Fila {$numeroFila}: Ubicación debe tener hasta 100 caracteres.");
        }

        $permitirCero = $tipo === 'SERVICIO';
        $cantidad = $this->entero(
            $valorNumerico('Existencias actuales'),
            'Existencias actuales',
            $numeroFila,
            $errores,
            $permitirCero
        );
        $umbral = $this->entero(
            $valorNumerico('Stock mínimo'),
            'Stock mínimo',
            $numeroFila,
            $errores,
            $permitirCero
        );
        $costo = $this->decimal($valorNumerico('Costo unitario'), 'Costo unitario', $numeroFila, $errores);
        $precioUnitario = $this->decimal($valorNumerico('Precio unitario'), 'Precio unitario', $numeroFila, $errores);
        $precioDocena = $this->decimal(
            $valorNumerico('Precio unitario por docena'),
            'Precio unitario por docena',
            $numeroFila,
            $errores,
            true
        );
        $precioCiento = $this->decimal(
            $valorNumerico('Precio unitario por ciento'),
            'Precio unitario por ciento',
            $numeroFila,
            $errores,
            true
        );
        if (count($errores) !== $erroresAntes) {
            return null;
        }

        return [
            'numero' => $numeroFila,
            'sku' => $sku,
            'nombre' => $nombre,
            'tipo' => $tipo,
            'marca' => $marca,
            'ubicacion' => $ubicacion === '' ? null : $ubicacion,
            'cantidad' => $cantidad,
            'costo_unitario' => $costo,
            'precio_unitario' => $precioUnitario,
            'precio_docena' => $precioDocena,
            'precio_ciento' => $precioCiento,
            'umbral' => $umbral,
        ];
    }

    private function entero(
        string $valor,
        string $columna,
        int $fila,
        array &$errores,
        bool $permitirCero = false
    ): ?int
    {
        if (! preg_match('/^\d+$/D', $valor) || (float) $valor > 2147483647) {
            $this->agregarError(
                $errores,
                "Fila {$fila}: {$columna} debe ser un entero "
                    .($permitirCero ? 'no negativo' : 'positivo')
                    .' sin espacios ni otros caracteres.'
            );

            return null;
        }

        $entero = (int) $valor;
        if ($entero < ($permitirCero ? 0 : 1)) {
            $this->agregarError(
                $errores,
                "Fila {$fila}: {$columna} debe ser "
                    .($permitirCero ? 'mayor o igual que cero.' : 'mayor que cero.')
            );

            return null;
        }

        return $entero;
    }

    private function decimal(
        string $valor,
        string $columna,
        int $fila,
        array &$errores,
        bool $nullable = false
    ): ?string {
        if ($valor === '' && $nullable) {
            return null;
        }

        if (! preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', $valor) || (float) $valor <= 0) {
            $this->agregarError(
                $errores,
                "Fila {$fila}: {$columna} debe ser un decimal positivo sin espacios ni otros caracteres y con hasta 2 decimales."
            );

            return null;
        }

        return number_format((float) $valor, 2, '.', '');
    }

    private function validarSkusDisponibles(array $filas): void
    {
        foreach (array_chunk($filas, 500) as $grupo) {
            $existentes = Presentacion::query()
                ->whereIn('codigoSKU', array_column($grupo, 'sku'))
                ->lockForUpdate()
                ->pluck('codigoSKU')
                ->all();
            $clavesExistentes = array_fill_keys(
                array_map(fn ($sku) => $this->claveSku($sku), $existentes),
                true
            );

            foreach ($grupo as $fila) {
                if (
                    isset($clavesExistentes[$this->claveSku($fila['sku'])])
                    || ($existentes !== []
                        && Presentacion::query()->where('codigoSKU', $fila['sku'])->exists())
                ) {
                    throw ValidationException::withMessages([
                        'archivo' => [
                            "Fila {$fila['numero']}: el SKU \"{$fila['sku']}\" ya existe. No se importó ningún registro.",
                        ],
                    ]);
                }
            }
        }
    }

    private function marcasExistentes(): array
    {
        $marcasPorClave = [];

        foreach (Marca::query()->lockForUpdate()->get(['id', 'nombre']) as $marca) {
            $marcasPorClave[$this->claveNombre($marca->nombre)][] = $marca->id;
        }

        return $marcasPorClave;
    }

    private function claveNombre(string $nombre): string
    {
        $nombre = $this->normalizarEspacios($nombre);
        if (class_exists(\Normalizer::class)) {
            $normalizado = \Normalizer::normalize($nombre, \Normalizer::FORM_C);
            if ($normalizado !== false) {
                $nombre = $normalizado;
            }
        }

        return mb_strtolower($nombre, 'UTF-8');
    }

    private function claveSku(string $sku): string
    {
        return mb_strtoupper(trim($sku), 'UTF-8');
    }

    private function normalizarEspacios(string $valor): string
    {
        $espaciosNormalizados = preg_replace('/\s+/u', ' ', trim($valor));

        return $espaciosNormalizados ?? trim($valor);
    }

    private function agregarError(array &$errores, string $mensaje): void
    {
        if (count($errores) < 100) {
            $errores[] = $mensaje;
        } elseif (count($errores) === 100) {
            $errores[] = 'Hay más errores en el archivo; corrige los primeros y vuelve a intentar.';
        }
    }
}
