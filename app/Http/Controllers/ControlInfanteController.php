<?php

namespace App\Http\Controllers;

use App\Models\ControlInfante;
use App\Models\FichaInfante;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ControlInfanteController extends Controller
{
    /*
     * Display a listing of the resource.
     
    public function index()
    {

        $controles = ControlInfante::with('infante')->get();

        return view('control-infantes.index', compact(
            'controles'
      
        ));
        
    }

        public function index()
    {
        $controles = ControlInfante::with('infante')
            ->orderBy('numero_control')
            ->get();

        $infantes = $controles
            ->groupBy('codigo_infante');

        return view('control-infantes.index', compact('infantes'));
    }*/
    public function index(Request $request)
    {
        // Obtener todos los infantes para el SELECT
        $infantes = FichaInfante::orderBy('nombre_infante')->get();

        // Infante seleccionado
        $id = $request->get('id');

        $controles = collect();

        // Solo consultar controles cuando se selecciona un infante
        if ($id) {
            $controles = ControlInfante::with('infante')
                ->where('ficha_infante_id', $id)
                ->orderBy('numero_control')
                ->get();
        }
            return view('control-infantes.index', compact(
        'infantes',
        'controles',
        'id'
    ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
         $id = $request->input('id');
        $infante = FichaInfante::find($id);
        
        return view('control-infantes.create', compact(
            'infante',
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FichaInfante $infante)
    {
       // dd($request);
        $request->validate([
            'peso_actual_control' => [
                'required',
                'numeric',
                'min:0',
                'max:999.99',
            ],

            'peso_ideal_control' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],

            'indicador_pe_control' => [
                'nullable',
                'in:PB Severo,PB Moderado,Riesgo BP,Normal,Sobrepeso,Obesidad',
            ],

            'talla_actual_control' => [
                'required',
                'numeric',
                'min:0',
                'max:999.99',
            ],

            'talla_ideal_control' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999.99',
            ],

            'indicador_te_control' => [
                'nullable',
                'in:RC. Severo,RC. Moderado,Riesgo RC,C. Adecuado,Riesgo DC,DC. Moderada,DC. Severa',
            ],

            'indicador_pt_control' => [
                'nullable',
                'in:DA Severa,DA Moderada,Riesgo DA,Normal,Riesgo SP,Sobrepeso,Obesidad',
            ],

            'evolucion_control' => [
                'required',
                'in:Mejoria,Normal,Sin mejora,Desnutricion',
            ],

            'producto_control' => [
                'nullable',
                'in:Leche,Incaparina,Atol',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Buscar infante
        |--------------------------------------------------------------------------
        */

        $infante = FichaInfante::where(
            'id',
            $infante->id
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Calcular edad automáticamente
        |--------------------------------------------------------------------------
        */

        $fechaNacimiento = Carbon::parse(
            $infante->fecha_nacimiento_infante
        );

        $edad = ControlInfante::calcularEdad(
            $fechaNacimiento
        );


        /*
        |--------------------------------------------------------------------------
        | Generar número de control
        |--------------------------------------------------------------------------
        */

        $ultimoControl = ControlInfante::where(
            'ficha_infante_id',
            $infante->id
        )
            ->max('numero_control');

        $numeroControl = ($ultimoControl ?? 0) + 1;


        /*
        |--------------------------------------------------------------------------
        | Crear control
        |--------------------------------------------------------------------------
        */

        ControlInfante::create([

            'ficha_infante_id' => $infante->id,

            'numero_control' => $numeroControl,

            'anios_edad_control' => $edad['anios'],

            'meses_edad_control' => $edad['meses'],

            'dia_edad_control' => $edad['dias'],

            'peso_actual_control' => $request->peso_actual_control,

            'peso_ideal_control' => $request->peso_ideal_control,

            'indicador_pe_control' => $request->indicador_pe_control,

            'talla_actual_control' => $request->talla_actual_control,

            'talla_ideal_control' => $request->talla_ideal_control,

            'indicador_te_control' => $request->indicador_te_control,

            'indicador_pt_control' => $request->indicador_pt_control,

            'evolucion_control' => $request->evolucion_control,

            'producto_control' => $request->producto_control,
        ]);


        return redirect()
            ->route('control-infantes.index')
            ->with(
                'success',
                "Control #{$numeroControl} registrado correctamente."
            );
    }


    /**
     * Display the specified resource.
     */
    public function show(ControlInfante $controlInfante)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ControlInfante $controlInfante)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ControlInfante $controlInfante)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ControlInfante $controlInfante)
    {
        //
    }
}
