<?php

namespace App\Http\Controllers;

use App\Models\FichaInfante;

use App\Http\Requests\StoreFichaInfanteRequest;

class FichaInfanteController extends Controller
{

     private function departamentosMunicipios()
    {
        return [
            'Alta Verapaz' => [
                'Cobán',
                'Santa Cruz Verapaz',
                'San Cristóbal Verapaz',
                'Tactic',
                'Tamahú',
                'Tucurú',
                'Panzós',
                'Senahú',
                'San Pedro Carchá',
                'San Juan Chamelco',
                'Lanquín',
                'Cahabón',
                'Chisec',
                'Chahal',
                'Fray Bartolomé de las Casas',
                'Raxruhá',
            ],

            'Baja Verapaz' => [
                'Salamá',
                'San Miguel Chicaj',
                'Rabinal',
                'Cubulco',
                'Granados',
                'El Chol',
                'San Jerónimo',
                'Purulhá',
            ],

            'Chimaltenango' => [
                'Chimaltenango',
                'San José Poaquil',
                'San Martín Jilotepeque',
                'Comalapa',
                'Santa Apolonia',
                'Tecpán Guatemala',
                'Patzún',
                'Pochuta',
                'Patzicía',
                'Santa Cruz Balanyá',
                'Acatenango',
                'San Pedro Yepocapa',
                'San Andrés Itzapa',
                'Parramos',
                'Zaragoza',
                'El Tejar',
            ],

            'Chiquimula' => [
                'Chiquimula',
                'San José La Arada',
                'San Juan Ermita',
                'Jocotán',
                'Camotán',
                'Olopa',
                'Esquipulas',
                'Concepción Las Minas',
                'Quezaltepeque',
                'San Jacinto',
                'Ipala',
            ],

            'El Progreso' => [
                'Guastatoya',
                'Morazán',
                'San Agustín Acasaguastlán',
                'San Cristóbal Acasaguastlán',
                'El Jícaro',
                'Sansare',
                'Sanarate',
                'San Antonio La Paz',
            ],

            'Escuintla' => [
                'Escuintla',
                'Santa Lucía Cotzumalguapa',
                'La Democracia',
                'Siquinalá',
                'Masagua',
                'Tiquisate',
                'La Gomera',
                'Guanagazapa',
                'Puerto San José',
                'Iztapa',
                'Palín',
                'San Vicente Pacaya',
                'Nueva Concepción',
                'Sipacate',
            ],

            'Guatemala' => [
                'Guatemala',
                'Santa Catarina Pinula',
                'San José Pinula',
                'San José del Golfo',
                'Palencia',
                'Chinautla',
                'San Pedro Ayampuc',
                'Mixco',
                'San Pedro Sacatepéquez',
                'San Juan Sacatepéquez',
                'San Raymundo',
                'Chuarrancho',
                'Fraijanes',
                'Amatitlán',
                'Villa Nueva',
                'Villa Canales',
                'San Miguel Petapa',
            ],

            'Huehuetenango' => [
                'Huehuetenango',
                'Chiantla',
                'Malacatancito',
                'Cuilco',
                'Nentón',
                'San Pedro Necta',
                'Jacaltenango',
                'Soloma',
                'San Ildefonso Ixtahuacán',
                'Santa Bárbara',
                'La Libertad',
                'La Democracia',
                'San Miguel Acatán',
                'San Rafael La Independencia',
                'Todos Santos Cuchumatán',
                'San Juan Atitán',
                'Santa Eulalia',
                'San Mateo Ixtatán',
                'Colotenango',
                'San Sebastián Coatán',
                'San Rafael Petzal',
                'San Gaspar Ixchil',
                'Santiago Chimaltenango',
                'Santa Ana Huista',
                'Concepción Huista',
                'San Antonio Huista',
                'Unión Cantinil',
                'Petatán',
            ],

            'Izabal' => [
                'Puerto Barrios',
                'Livingston',
                'El Estor',
                'Morales',
                'Los Amates',
            ],

            'Jalapa' => [
                'Jalapa',
                'San Pedro Pinula',
                'San Luis Jilotepeque',
                'San Manuel Chaparrón',
                'San Carlos Alzatate',
                'Monjas',
                'Mataquescuintla',
            ],

            'Jutiapa' => [
                'Jutiapa',
                'El Progreso',
                'Santa Catarina Mita',
                'Agua Blanca',
                'Asunción Mita',
                'Yupiltepeque',
                'Atescatempa',
                'Jerez',
                'El Adelanto',
                'Zapotitlán',
                'Comapa',
                'Jalpatagua',
                'Conguaco',
                'Moyuta',
                'Pasaco',
                'Quesada',
            ],

            'Petén' => [
                'Flores',
                'San José',
                'San Benito',
                'San Andrés',
                'La Libertad',
                'San Francisco',
                'Santa Ana',
                'Dolores',
                'San Luis',
                'Sayaxché',
                'Melchor de Mencos',
                'Poptún',
                'Las Cruces',
                'El Chal',
            ],

            'Quetzaltenango' => [
                'Quetzaltenango',
                'Salcajá',
                'Olintepeque',
                'San Carlos Sija',
                'Sibilia',
                'Cabricán',
                'Cajolá',
                'San Miguel Sigüilá',
                'San Juan Ostuncalco',
                'San Mateo',
                'Concepción Chiquirichapa',
                'Almolonga',
                'Cantel',
                'Huitán',
                'Zunil',
                'Colomba',
                'San Francisco La Unión',
                'El Palmar',
                'Coatepeque',
                'Génova',
                'Flores Costa Cuca',
                'La Esperanza',
                'Palestina de Los Altos',
            ],

            'Quiché' => [
                'Santa Cruz del Quiché',
                'Chiché',
                'Chinique',
                'Zacualpa',
                'Chajul',
                'Chichicastenango',
                'Patzité',
                'San Antonio Ilotenango',
                'San Pedro Jocopilas',
                'Cunén',
                'San Juan Cotzal',
                'Joyabaj',
                'Nebaj',
                'San Andrés Sajcabajá',
                'Uspantán',
                'Sacapulas',
                'San Bartolomé Jocotenango',
                'Canillá',
                'Chicamán',
                'Ixcán',
                'Pachalum',
            ],

            'Retalhuleu' => [
                'Retalhuleu',
                'San Sebastián',
                'Santa Cruz Muluá',
                'San Martín Zapotitlán',
                'San Felipe',
                'San Andrés Villa Seca',
                'Champerico',
                'Nuevo San Carlos',
                'El Asintal',
            ],

            'Sacatepéquez' => [
                'Antigua Guatemala',
                'Jocotenango',
                'Pastores',
                'Sumpango',
                'Santo Domingo Xenacoj',
                'Santiago Sacatepéquez',
                'San Bartolomé Milpas Altas',
                'San Lucas Sacatepéquez',
                'Santa Lucía Milpas Altas',
                'Magdalena Milpas Altas',
                'Santa María de Jesús',
                'Ciudad Vieja',
                'San Miguel Dueñas',
                'Alotenango',
                'San Antonio Aguas Calientes',
                'Santa Catarina Barahona',
            ],

            'San Marcos' => [
                'San Marcos',
                'San Pedro Sacatepéquez',
                'San Antonio Sacatepéquez',
                'Comitancillo',
                'San Miguel Ixtahuacán',
                'Concepción Tutuapa',
                'Tacaná',
                'Sibinal',
                'Tajumulco',
                'Tejutla',
                'San Rafael Pie de la Cuesta',
                'Nuevo Progreso',
                'El Tumbador',
                'El Rodeo',
                'Malacatán',
                'Catarina',
                'Ayutla',
                'Ocós',
                'San Pablo',
                'El Quetzal',
                'La Reforma',
                'Pajapita',
                'Ixchiguán',
                'San José Ojetenam',
                'Río Blanco',
                'San Lorenzo',
            ],

            'Santa Rosa' => [
                'Cuilapa',
                'Barberena',
                'Santa Rosa de Lima',
                'Casillas',
                'San Rafael Las Flores',
                'Oratorio',
                'San Juan Tecuaco',
                'Chiquimulilla',
                'Taxisco',
                'Santa María Ixhuatán',
                'Guazacapán',
                'Santa Cruz Naranjo',
                'Pueblo Nuevo Viñas',
                'Nueva Santa Rosa',
            ],

            'Sololá' => [
                'Sololá',
                'San José Chacayá',
                'Santa María Visitación',
                'Santa Lucía Utatlán',
                'Nahualá',
                'Santa Catarina Ixtahuacán',
                'Santa Clara La Laguna',
                'Concepción',
                'San Andrés Semetabaj',
                'Panajachel',
                'Santa Catarina Palopó',
                'San Antonio Palopó',
                'San Lucas Tolimán',
                'Santa Cruz La Laguna',
                'San Pablo La Laguna',
                'San Marcos La Laguna',
                'San Juan La Laguna',
                'San Pedro La Laguna',
                'Santiago Atitlán',
            ],

            'Suchitepéquez' => [
                'Mazatenango',
                'Cuyotenango',
                'San Francisco Zapotitlán',
                'San Bernardino',
                'San José El Ídolo',
                'Santo Domingo Suchitepéquez',
                'San Lorenzo',
                'Samayac',
                'San Pablo Jocopilas',
                'San Antonio Suchitepéquez',
                'San Miguel Panán',
                'San Gabriel',
                'Chicacao',
                'Patulul',
                'Santa Bárbara',
                'San Juan Bautista',
                'Santo Tomás La Unión',
                'Zunilito',
                'Pueblo Nuevo',
                'Río Bravo',
            ],

            'Totonicapán' => [
                'Totonicapán',
                'San Cristóbal Totonicapán',
                'San Francisco El Alto',
                'San Andrés Xecul',
                'Momostenango',
                'Santa María Chiquimula',
                'Santa Lucía La Reforma',
                'San Bartolo',
            ],

            'Zacapa' => [
                'Zacapa',
                'Estanzuela',
                'Río Hondo',
                'Gualán',
                'Teculután',
                'Usumatlán',
                'Cabañas',
                'San Diego',
                'La Unión',
                'Huité',
                'San Jorge',
            ],
        ];
    }
    /**
     * Display a listing of the resource.
     */
    // 1. LISTAR: Mostrar todos los infantes registrados
    public function index()
    {
        $infantes = FichaInfante::latest()->paginate(10);
        return view('ficha-infantes.index',[
                'infantes' => $infantes
            ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $departamentosMunicipios = $this->departamentosMunicipios();
        return view('ficha-infantes.create', compact('departamentosMunicipios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFichaInfanteRequest $request)
    {
        // El código_infante se genera solo en el modelo gracias al evento booted()
        FichaInfante::create($request->validated());
        return redirect()->route('ficha-infantes.index')
            ->with('success', 'Ficha del infante creada exitosamente.');
    }
    /**
     * Display the specified resource.
     */
    public function show(FichaInfante $fichaInfante)
    {
           return view('ficha-infantes.show', compact('fichaInfante'));
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FichaInfante $fichaInfante)
    {
        $departamentosMunicipios = $this->departamentosMunicipios();
        return view('ficha-infantes.edit', compact('fichaInfante','departamentosMunicipios'));
    }
    // 6. ACTUALIZAR: Guardar los cambios editados
    public function update(StoreFichaInfanteRequest $request, FichaInfante $fichaInfante)
    {
        $fichaInfante->update($request->validated());
        return redirect()->route('ficha-infantes.index')
            ->with('success', 'Ficha del infante actualizada correctamente.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FichaInfante $fichaInfante)
    {
        $fichaInfante->delete();
        return redirect()->route('ficha-infantes.index')
            ->with('success', 'Ficha eliminada correctamente.');
    }
}
