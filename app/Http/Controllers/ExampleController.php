<?php

namespace App\Http\Controllers;

use App\Models\Examples;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ExampleController extends Controller {

    public function index() {
        $Examples = Examples::get();

        $NivelesPeligro = collect(['bajo', 'medio', 'alto'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1 // Empieza desde 1
            ];
        });

        $Generos = collect(['macho', 'hembra', 'otro'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1
            ];
        });

        $Estatus = collect(['activo', 'inactivo', 'pendiente'])->map(function ($v, $i) {
            return [
                'label' => ucfirst($v),
                'value' => $i + 1
            ];
        });


        return Inertia::render('Example/Examples', compact('Examples', 'NivelesPeligro', 'Generos', 'Estatus'));
    }

    public function store(Request $request) {
        // return $request;
        $validated = $request->validate([
            'nombre' => 'required',
            'edad_aproximada' => 'required',
            'especie' => 'required',
            'color_principal' => 'required',
            'origen' => 'required',
            'fecha_descubrimiento' => 'required|date',
            'rango.start' => 'required|date',
            'rango.end' => 'required|date',
            'nivel_peligro' => 'required',
            'descripcion' => 'required',
            'es_invisible' => 'required',
            'tiene_alas' => 'required',
            'genero' => 'required',
            'clave_identificacion' => 'required',
            'correo_contacto' => 'required',
            'confirmacion' => 'required',
            'estatus' => 'required',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $fotoNombre = null;

        if ($request->hasFile('foto')) {
            $fotoNombre = $request->file('foto')->store('public/fotos');
            $fotoNombre = basename($fotoNombre); // Solo el nombre del archivo
        }

        Examples::create([
            'nombre' => $validated['nombre'],
            'edad_aproximada' => $validated['edad_aproximada'],
            'especie' => $validated['especie'],
            'color_principal' => $validated['color_principal'],
            'origen' => $validated['origen'],
            'fecha_descubrimiento' => $validated['fecha_descubrimiento'],
            'rango_inicio' => $validated['rango']['start'],
            'rango_fin' => $validated['rango']['end'],
            'nivel_peligro' => $validated['nivel_peligro'],
            'descripcion' => $validated['descripcion'],
            'es_invisible' => $validated['es_invisible'],
            'tiene_alas' => $validated['tiene_alas'],
            'genero' => $validated['genero'],
            'clave_identificacion' => $validated['clave_identificacion'],
            'correo_contacto' => $validated['correo_contacto'],
            'confirmacion' => $validated['confirmacion'],
            'estatus' => $validated['estatus'],
            'foto' => $fotoNombre,
        ]);

        return redirect()->back()->with('success', 'Ejemplo creado correctamente.');
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'nombre' => 'required',
            'edad_aproximada' => 'required',
            'especie' => 'required',
            'color_principal' => 'required',
            'origen' => 'required',
            'fecha_descubrimiento' => 'required|date',
            'rango.start' => 'required|date',
            'rango.end' => 'required|date',
            'nivel_peligro' => 'required',
            'descripcion' => 'required',
            'es_invisible' => 'required',
            'tiene_alas' => 'required',
            'genero' => 'required',
            'clave_identificacion' => 'required',
            'correo_contacto' => 'required',
            'confirmacion' => 'required',
            'estatus' => 'required',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $example = Examples::findOrFail($id);

        if ($request->hasFile('foto')) {
            // Opcional: borrar anterior si existe
            if ($example->foto && Storage::exists("public/fotos/{$example->foto}")) {
                Storage::delete("public/fotos/{$example->foto}");
            }

            $fotoNombre = $request->file('foto')->store('public/fotos');
            $example->foto = basename($fotoNombre);
        }

        $example->update([
            'nombre' => $validated['nombre'],
            'edad_aproximada' => $validated['edad_aproximada'],
            'especie' => $validated['especie'],
            'color_principal' => $validated['color_principal'],
            'origen' => $validated['origen'],
            'fecha_descubrimiento' => $validated['fecha_descubrimiento'],
            'rango_inicio' => $validated['rango']['start'],
            'rango_fin' => $validated['rango']['end'],
            'nivel_peligro' => $validated['nivel_peligro'],
            'descripcion' => $validated['descripcion'],
            'es_invisible' => $validated['es_invisible'],
            'tiene_alas' => $validated['tiene_alas'],
            'genero' => $validated['genero'],
            'clave_identificacion' => $validated['clave_identificacion'],
            'correo_contacto' => $validated['correo_contacto'],
            'confirmacion' => $validated['confirmacion'],
            'estatus' => $validated['estatus'],
        ]);

        return redirect()->back()->with('success', 'Ejemplo actualizado correctamente.');
    }

    public function destroy($id) {
        $example = Examples::findOrFail($id);

        if ($example->foto && Storage::exists("public/fotos/{$example->foto}")) {
            Storage::delete("public/fotos/{$example->foto}");
        }

        $example->delete();

        return redirect()->back()->with('success', 'Ejemplo eliminado correctamente.');
    }


}
