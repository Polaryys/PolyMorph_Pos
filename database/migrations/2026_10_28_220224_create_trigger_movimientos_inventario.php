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
            CREATE TRIGGER trg_inventario_movimientos_after_update
            AFTER UPDATE ON inventario
            FOR EACH ROW
            BEGIN
                IF NOT (OLD.cantidad <=> NEW.cantidad) THEN

                    INSERT INTO movimientos (
                        usuario_id,
                        inventario_id,
                        tipo_movimiento,
                        cantidad_anterior,
                        cantidad,
                        cantidad_posterior,
                        motivo,
                        created_at,
                        updated_at
                    )
                    VALUES (
                        @usuario_id,
                        NEW.id,
                        CASE
                            WHEN COALESCE(NEW.cantidad, 0)
                               > COALESCE(OLD.cantidad, 0)
                            THEN 'ENTRADA'
                            ELSE 'SALIDA'
                        END,
                        COALESCE(OLD.cantidad, 0),
                        ABS(
                            COALESCE(NEW.cantidad, 0)
                            - COALESCE(OLD.cantidad, 0)
                        ),
                        COALESCE(NEW.cantidad, 0),
                        @motivo,
                        NOW(),
                        NOW()
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
            'DROP TRIGGER IF EXISTS trg_inventario_movimientos_after_update'
        );
    }
};
