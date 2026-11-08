<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidaModoCapturaPagos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RegistrarPagoVentaRequest extends FormRequest
{
    use ValidaModoCapturaPagos;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'idempotency_key' => ['required', 'uuid'],
            'pagos' => ['required', 'array', 'min:1'],
            'pagos.*.metodo_pago_id' => [
                'required',
                'integer',
                Rule::exists('metodos_pago', 'id')->where('estado', true),
            ],
            'pagos.*.monto' => ['required', 'numeric', 'decimal:0,2'],
            'pagos.*.monto_recibido' => ['nullable', 'numeric', 'min:0', 'decimal:0,2'],
            'pagos.*.referencia' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $pagos = $validator->getData()['pagos'];
                $this->validarModoCapturaPagos($validator, $pagos);
            },
        ];
    }
}
