<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_historial_precios_update
            AFTER UPDATE ON presentaciones
            FOR EACH ROW
            BEGIN
                IF NOT (OLD.precioUnitario <=> NEW.precioUnitario)
                    OR NOT (OLD.precioUnitarioDocena <=> NEW.precioUnitarioDocena)
                    OR NOT (OLD.precioUnitarioCiento <=> NEW.precioUnitarioCiento)
                THEN
                    INSERT INTO historial_precios (
                        presentacion_id,
                        precio_unitario_anterior,
                        precio_docena_anterior,
                        precio_ciento_anterior,
                        precio_unitario_nuevo,
                        precio_docena_nuevo,
                        precio_ciento_nuevo,
                        created_at,
                        updated_at
                    )
                    VALUES (
                        OLD.id,
                        OLD.precioUnitario,
                        OLD.precioUnitarioDocena,
                        OLD.precioUnitarioCiento,
                        NEW.precioUnitario,
                        NEW.precioUnitarioDocena,
                        NEW.precioUnitarioCiento,
                        CURRENT_TIMESTAMP,
                        CURRENT_TIMESTAMP
                    );
                END IF;
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared(
            'DROP TRIGGER IF EXISTS trg_historial_precios_update'
        );
    }
};
