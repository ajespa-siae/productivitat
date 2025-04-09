<?php

namespace App\Imports;

use App\Models\MandoEmpleado;
use App\Models\Mando;
use App\Models\Empleado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class MandoEmpleadosImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Validar que existan tanto el mando como el empleado
        $mando = Mando::where('nif', $row['nif_comandament'])->first();
        $empleado = Empleado::where('nif', $row['nif_empleat'])->first();

        if (!$mando || !$empleado) {
            throw new \Exception('No s\'ha trobat el comandament o l\'empleat');
        }

        return new MandoEmpleado([
            'mando_id' => $mando->id,
            'empleado_id' => $empleado->id,
        ]);
    }

    public function rules(): array
    {
        return [
            'nif_comandament' => [
                'required',
                'string',
                Rule::exists('mandos', 'nif'),
            ],
            'nif_empleat' => [
                'required',
                'string',
                Rule::exists('empleados', 'nif'),
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nif_comandament.required' => 'El NIF del comandament és obligatori',
            'nif_comandament.exists' => 'El comandament no existeix',
            'nif_empleat.required' => 'El NIF de l\'empleat és obligatori',
            'nif_empleat.exists' => 'L\'empleat no existeix',
        ];
    }
}
