<?php

namespace App\Http\Controllers\Administrador\Sistema;

use App\Http\Controllers\Controller;
use App\Models\Administrador\Sistema\CategoriasModulos;
use App\Models\Administrador\Sistema\Modulos;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;


class ModulosController extends Controller {

    public function index() {
        $Categorias = CategoriasModulos::with('Modulos.Submodulos')->orderBy('id', 'desc')->get();
        $Modulos = Modulos::with('Categoria')->orderBy('id', 'desc')->get();
        $ListaCategorias = $this->ObtenCategoriasModulos();
        $ListaIconos = $this->IconosFontAwesome();
        return Inertia::render('Administrador/Sistema/Modulos', compact('Categorias', 'Modulos', 'ListaIconos', 'ListaCategorias'));
    }

    private function IconosFontAwesome() {
        $Iconos = [
            ['value' => 'fa-solid fa-house', 'label' => 'House'],
            ['value' => 'fa-solid fa-industry', 'label' => 'industry'],
            ['value' => 'fa-solid fa-user', 'label' => 'User'],
            ['value' => 'fa-solid fa-check', 'label' => 'Check'],
            ['value' => 'fa-solid fa-edit', 'label' => 'Edit'],
            ['value' => 'fa-solid fa-trash-alt', 'label' => 'Trash'],
            ['value' => 'fa-solid fa-camera', 'label' => 'Camera'],
            ['value' => 'fa-solid fa-car', 'label' => 'Car'],
            ['value' => 'fa-solid fa-envelope', 'label' => 'Envelope'],
            ['value' => 'fa-solid fa-phone', 'label' => 'Phone'],
            ['value' => 'fa-solid fa-bell', 'label' => 'Bell'],
            ['value' => 'fa-solid fa-map', 'label' => 'Map'],
            ['value' => 'fa-solid fa-clock', 'label' => 'Clock'],
            ['value' => 'fa-solid fa-key', 'label' => 'Key'],
            ['value' => 'fa-solid fa-lock', 'label' => 'Lock'],
            ['value' => 'fa-solid fa-heart', 'label' => 'Heart'],
            ['value' => 'fa-solid fa-briefcase', 'label' => 'Briefcase'],
            ['value' => 'fa-solid fa-book', 'label' => 'Book'],
            ['value' => 'fa-solid fa-music', 'label' => 'Music'],
            ['value' => 'fa-solid fa-film', 'label' => 'Film'],
            ['value' => 'fa-solid fa-coffee', 'label' => 'Coffee'],
            ['value' => 'fa-solid fa-fire', 'label' => 'Fire'],
            ['value' => 'fa-solid fa-cloud', 'label' => 'Cloud'],
            ['value' => 'fa-solid fa-sun', 'label' => 'Sun'],
            ['value' => 'fa-solid fa-moon', 'label' => 'Moon'],
            ['value' => 'fa-solid fa-star', 'label' => 'Star'],
            ['value' => 'fa-solid fa-paper-plane', 'label' => 'Paper Plane'],
            ['value' => 'fa-solid fa-rocket', 'label' => 'Rocket'],
            ['value' => 'fa-solid fa-tree', 'label' => 'Tree'],
            ['value' => 'fa-solid fa-anchor', 'label' => 'Anchor'],
            ['value' => 'fa-solid fa-bolt', 'label' => 'Bolt'],
            ['value' => 'fa-solid fa-bug', 'label' => 'Bug'],
            ['value' => 'fa-solid fa-bullhorn', 'label' => 'Bullhorn'],
            ['value' => 'fa-solid fa-calendar', 'label' => 'Calendar'],
            ['value' => 'fa-solid fa-chart-bar', 'label' => 'Chart Bar'],
            ['value' => 'fa-solid fa-chart-pie', 'label' => 'Chart Pie'],
            ['value' => 'fa-solid fa-code', 'label' => 'Code'],
            ['value' => 'fa-solid fa-cog', 'label' => 'Cog'],
            ['value' => 'fa-solid fa-compass', 'label' => 'Compass'],
            ['value' => 'fa-solid fa-desktop', 'label' => 'Desktop'],
            ['value' => 'fa-solid fa-database', 'label' => 'Database'],
            ['value' => 'fa-solid fa-download', 'label' => 'Download'],
            ['value' => 'fa-solid fa-dumbbell', 'label' => 'Dumbbell'],
            ['value' => 'fa-solid fa-eye', 'label' => 'Eye'],
            ['value' => 'fa-solid fa-fighter-jet', 'label' => 'Fighter Jet'],
            ['value' => 'fa-solid fa-flag', 'label' => 'Flag'],
            ['value' => 'fa-solid fa-gift', 'label' => 'Gift'],
            ['value' => 'fa-solid fa-globe', 'label' => 'Globe'],
            ['value' => 'fa-solid fa-graduation-cap', 'label' => 'Graduation Cap'],
            ['value' => 'fa-solid fa-handshake', 'label' => 'Handshake'],
            ['value' => 'fa-solid fa-hospital', 'label' => 'Hospital'],
            ['value' => 'fa-solid fa-laptop', 'label' => 'Laptop'],
            ['value' => 'fa-solid fa-lightbulb', 'label' => 'Lightbulb'],
            ['value' => 'fa-solid fa-list', 'label' => 'List'],
            ['value' => 'fa-solid fa-lock-open', 'label' => 'Lock Open'],
            ['value' => 'fa-solid fa-microphone', 'label' => 'Microphone'],
            ['value' => 'fa-solid fa-mobile', 'label' => 'Mobile'],
            ['value' => 'fa-solid fa-palette', 'label' => 'Palette'],
            ['value' => 'fa-solid fa-paperclip', 'label' => 'Paperclip'],
            ['value' => 'fa-solid fa-parachute-box', 'label' => 'Parachute Box'],
            ['value' => 'fa-solid fa-paw', 'label' => 'Paw'],
            ['value' => 'fa-solid fa-pencil-alt', 'label' => 'Pencil Alt'],
            ['value' => 'fa-solid fa-plane', 'label' => 'Plane'],
            ['value' => 'fa-solid fa-plug', 'label' => 'Plug'],
            ['value' => 'fa-solid fa-print', 'label' => 'Print'],
            ['value' => 'fa-solid fa-road', 'label' => 'Road'],
            ['value' => 'fa-solid fa-rocket', 'label' => 'Rocket'],
            ['value' => 'fa-solid fa-ruler', 'label' => 'Ruler'],
            ['value' => 'fa-solid fa-save', 'label' => 'Save'],
            ['value' => 'fa-solid fa-search', 'label' => 'Search'],
            ['value' => 'fa-solid fa-shield-alt', 'label' => 'Shield Alt'],
            ['value' => 'fa-solid fa-ship', 'label' => 'Ship'],
            ['value' => 'fa-solid fa-shopping-bag', 'label' => 'Shopping Bag'],
            ['value' => 'fa-solid fa-shopping-cart', 'label' => 'Shopping Cart'],
            ['value' => 'fa-solid fa-sign-in-alt', 'label' => 'Sign In Alt'],
            ['value' => 'fa-solid fa-sign-out-alt', 'label' => 'Sign Out Alt'],
            ['value' => 'fa-solid fa-signal', 'label' => 'Signal'],
            ['value' => 'fa-solid fa-sitemap', 'label' => 'Sitemap'],
            ['value' => 'fa-solid fa-snowflake', 'label' => 'Snowflake'],
            ['value' => 'fa-solid fa-sort', 'label' => 'Sort'],
            ['value' => 'fa-solid fa-space-shuttle', 'label' => 'Space Shuttle'],
            ['value' => 'fa-solid fa-spinner', 'label' => 'Spinner'],
            ['value' => 'fa-solid fa-stopwatch', 'label' => 'Stopwatch'],
            ['value' => 'fa-solid fa-suitcase', 'label' => 'Suitcase'],
            ['value' => 'fa-solid fa-sun', 'label' => 'Sun'],
            ['value' => 'fa-solid fa-tablet', 'label' => 'Tablet'],
            ['value' => 'fa-solid fa-tag', 'label' => 'Tag'],
            ['value' => 'fa-solid fa-tasks', 'label' => 'Tasks'],
            ['value' => 'fa-solid fa-thermometer', 'label' => 'Thermometer'],
            ['value' => 'fa-solid fa-thumbtack', 'label' => 'Thumbtack'],
            ['value' => 'fa-solid fa-ticket-alt', 'label' => 'Ticket Alt'],
            ['value' => 'fa-solid fa-toolbox', 'label' => 'Toolbox'],
            ['value' => 'fa-solid fa-tools', 'label' => 'Tools'],
            ['value' => 'fa-solid fa-trophy', 'label' => 'Trophy'],
            ['value' => 'fa-solid fa-truck', 'label' => 'Truck'],
            ['value' => 'fa-solid fa-umbrella', 'label' => 'Umbrella'],
            ['value' => 'fa-solid fa-unlock', 'label' => 'Unlock'],
            ['value' => 'fa-solid fa-user-shield', 'label' => 'User Shield'],
            ['value' => 'fa-solid fa-users', 'label' => 'Users'],
            ['value' => 'fa-solid fa-vial', 'label' => 'Vial'],
            ['value' => 'fa-solid fa-wifi', 'label' => 'WiFi'],
            ['value' => 'fa-solid fa-hand-point-right', 'label' => 'Hand Point Right'],
            ['value' => 'fa-solid fa-hand-scissors', 'label' => 'Scissors'],
            ['value' => 'fa-solid fa-street-view', 'label' => 'Street View'],
            ['value' => 'fa-solid fa-basketball-ball', 'label' => 'Basketball'],
            ['value' => 'fa-solid fa-ambulance', 'label' => 'Ambulance'],
            ['value' => 'fa-solid fa-apple-alt', 'label' => 'Apple'],
            ['value' => 'fa-solid fa-arrow-circle-left', 'label' => 'Arrow Circle Left'],
            ['value' => 'fa-solid fa-arrow-circle-right', 'label' => 'Arrow Circle Right'],
            ['value' => 'fa-solid fa-asterisk', 'label' => 'Asterisk'],
            ['value' => 'fa-solid fa-at', 'label' => 'At'],
            ['value' => 'fa-solid fa-award', 'label' => 'Award'],
            ['value' => 'fa-solid fa-bacon', 'label' => 'Bacon'],
            ['value' => 'fa-solid fa-band-aid', 'label' => 'Band Aid'],
            ['value' => 'fa-solid fa-barcode', 'label' => 'Barcode'],
            ['value' => 'fa-solid fa-bath', 'label' => 'Bath'],
            ['value' => 'fa-solid fa-battery-half', 'label' => 'Battery Half'],
            ['value' => 'fa-solid fa-beer', 'label' => 'Beer'],
            ['value' => 'fa-solid fa-bicycle', 'label' => 'Bicycle'],
            ['value' => 'fa-solid fa-binoculars', 'label' => 'Binoculars'],
            ['value' => 'fa-solid fa-blind', 'label' => 'Blind'],
            ['value' => 'fa-solid fa-bolt-lightning', 'label' => 'Bolt Lightning'],
            ['value' => 'fa-solid fa-bomb', 'label' => 'Bomb'],
            ['value' => 'fa-solid fa-bone', 'label' => 'Bone'],
            ['value' => 'fa-solid fa-book-medical', 'label' => 'Book Medical'],
            ['value' => 'fa-solid fa-book-open', 'label' => 'Book Open'],
            ['value' => 'fa-solid fa-book-reader', 'label' => 'Book Reader'],
            ['value' => 'fa-solid fa-bowling-ball', 'label' => 'Bowling Ball'],
            ['value' => 'fa-solid fa-box-open', 'label' => 'Box Open'],
            ['value' => 'fa-solid fa-bread-slice', 'label' => 'Bread Slice'],
            ['value' => 'fa-solid fa-briefcase-medical', 'label' => 'Briefcase Medical'],
            ['value' => 'fa-solid fa-brush', 'label' => 'Brush'],
            ['value' => 'fa-solid fa-bullseye', 'label' => 'Bullseye'],
            ['value' => 'fa-solid fa-burn', 'label' => 'Burn'],
            ['value' => 'fa-solid fa-calculator', 'label' => 'Calculator'],
            ['value' => 'fa-solid fa-campground', 'label' => 'Campground'],
            ['value' => 'fa-solid fa-candy-cane', 'label' => 'Candy Cane'],
            ['value' => 'fa-solid fa-capsules', 'label' => 'Capsules'],
            ['value' => 'fa-solid fa-cat', 'label' => 'Cat'],
            ['value' => 'fa-solid fa-chair', 'label' => 'Chair'],
            ['value' => 'fa-solid fa-chess', 'label' => 'Chess'],
            ['value' => 'fa-solid fa-church', 'label' => 'Church'],
            ['value' => 'fa-solid fa-circle-notch', 'label' => 'Circle Notch'],
            ['value' => 'fa-solid fa-clinic-medical', 'label' => 'Clinic Medical'],
            ['value' => 'fa-solid fa-comment-medical', 'label' => 'Comment Medical'],
            ['value' => 'fa-solid fa-compass', 'label' => 'Compass'],
            ['value' => 'fa-solid fa-couch', 'label' => 'Couch'],
            ['value' => 'fa-solid fa-dog', 'label' => 'Dog'],
            ['value' => 'fa-solid fa-door-open', 'label' => 'Door Open'],
            ['value' => 'fa-solid fa-drum', 'label' => 'Drum'],
            ['value' => 'fa-solid fa-drumstick-bite', 'label' => 'Drumstick Bite'],
            ['value' => 'fa-solid fa-server', 'label' => 'Server'],
            ['value' => 'fa-solid fa-warehouse', 'label' => 'warehouse'],
            ['value' => 'fa-solid fa-boxes-stacked', 'label' => 'Boxes Stacked'],
            ['value' => 'fa-solid fa-building', 'label' => 'Building'],
            ['value' => 'fa-solid fa-hotel', 'label' => 'Hotel'],
            ['value' => 'fa-solid fa-file-contract', 'label' => 'File Contract'],
            ['value' => 'fa-solid fa-location-pin', 'label' => 'Location Pin'],
            ['value' => 'fa-solid fa-user-plus', 'label' => 'User Plus'],
            ['value' => 'fa-solid fa-cake-candles', 'label' => 'Cake Candles'],
            ['value' => 'fa-solid fa-address-card', 'label' => 'Address Card'],
            ['value' => 'fa-solid fa-address-book', 'label' => 'Address Book'],
            ['value' => 'fa-solid fa-id-card', 'label' => 'Id Card'],
            ['value' => 'fa-solid fa-message', 'label' => 'Message'],
            ['value' => 'fa-solid fa-envelope', 'label' => 'Envelope'],
            ['value' => 'fa-solid fa-comment', 'label' => 'Comment'],
            ['value' => 'fa-solid fa-business-time', 'label' => 'Business Time'],
            ['value' => 'fa-regular fa-hourglass-half', 'label' => 'Hourglass Half'],
        ];

        return $Iconos;
    }

    private function ObtenCategoriasModulos() {
        return CategoriasModulos::pluck('nombre', 'id')->map(function ($label, $value) {
            return compact('value', 'label');
        })->values();
    }

    public function ModulosSistema(){
        return Modulos::with('Categoria')->orderBy('id', 'desc')->get();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:modulos,nombre',
            'ruta' => 'required|string|max:255',
            'icono' => 'required|string|max:255',
            'descripcion' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias_modulos,id',
        ], [
            'nombre.required' => 'El nombre del módulo es obligatorio',
            'nombre.unique' => 'El nombre del módulo ya existe',
            'ruta.required' => 'La ruta del módulo es obligatoria',
            'icono.required' => 'El ícono del módulo es obligatorio',
            'descripcion.required' => 'La descripción del módulo es obligatoria',
            'categoria_id.required' => 'La categoría del módulo es obligatoria',
            'categoria_id.exists' => 'La categoría seleccionada no existe'
        ]);
        $RolAdministrador = Role::findByName('Administrador');

        $modulo = Modulos::create([
            'categoria_modulo_id' => $request->categoria_id,
            'nombre' => $request->nombre,
            'ruta' => $request->ruta,
            'icono' => $request->icono,
            'descripcion' => $request->descripcion,
        ]);

        Permission::create(['name' => $modulo->nombre])->assignRole($RolAdministrador);

        return redirect()->back()->with('success', 'Módulo creado y permiso asignado.');
    }

    public function update(Request $request) {
        $request->validate([
            'categoria_id' => 'required|integer',
            'nombre' => 'nullable|string|max:100',
            'icono' => 'nullable|string|max:100',
            'ruta' => 'nullable|string|max:100',
        ], [
            'categoria_id.required' => 'La categoría es obligatoria',
            'categoria_id.integer' => 'La categoría debe ser un número entero',
            'nombre.string' => 'El nombre debe ser texto',
            'nombre.max' => 'El nombre no debe exceder los 100 caracteres',
            'icono.string' => 'El ícono debe ser texto',
            'icono.max' => 'El ícono no debe exceder los 100 caracteres',
            'ruta.string' => 'La ruta debe ser texto',
            'ruta.max' => 'La ruta no debe exceder los 100 caracteres'
        ]);

        $modulo = Modulos::find($request->id);
        $PermisoAnterior = $modulo->nombre;

        Modulos::where('id', $request->id)->update([
            'categoria_modulo_id' => $request->categoria_id,
            'nombre' => $request->nombre,
            'ruta' => $request->ruta,
            'icono' => $request->icono,
            'descripcion' => $request->descripcion,
        ]);

        $permission = Permission::where('name', $PermisoAnterior)->first();
        if ($permission) {
            $permission->update(['name' => $request->nombre]);
        }

        return redirect()->back()->with('success', 'Message');
    }

    public function destroy($id) {
        Modulos::where('id', $id)->delete();
        return redirect()->back()->with('success', 'Registro eliminado correctamente');
    }
}
