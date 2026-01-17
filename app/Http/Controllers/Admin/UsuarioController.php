<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UsuarioController extends Controller
{
    public function index()
    {
        return view('index'); 
    }

    public function listado()
    {
        // Lógica de acceso: Solo Superadmin ve todos, el resto solo se ve a sí mismo
        $usuarioLogueado = Auth::guard('admin')->user();
       
        if (Gate::allows('es-superadmin')) {
            // Superadmin: obtiene todos los registros de la tabla 'usuarios'
            $usuarios = Usuario::all(); 
        } else {
            // Admin Normal: solo su propio registro
            $usuarios = collect([$usuarioLogueado]); 
        }
        return view('listausuarios', compact('usuarios'));
    }

    public function store(Request $request)
    {
        // 1. Validar los datos
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'user' => 'required|string|max:20|unique:usuarios', // 'unique:usuarios' asegura que el user no se repita
            'rol' => 'required|string|max:50',
            'password' => 'required|string|min:6',
        ]);

        // 2. Hashear la contraseña
        $data['password'] = Hash::make($data['password']);

        // 3. Crear el usuario
        Usuario::create($data);

        // 4. Redirigir de vuelta a la lista con un mensaje de éxito
        return redirect()->back()->with('success', '¡Usuario creado exitosamente!');
    }

    public function update(Request $request, Usuario $usuario)
    {
        // 1. Validar los datos que llegan del formulario
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'user'=> 'required|string|max:20',
            'rol' => 'required|string|max:50',
            'password' => 'nullable|string|min:6' // 'nullable' = opcional, Aca decimos que el min de digitos en la contraseña es de 6, si pones uno menos no guardara cambios
        ]);

        // 2. Preparar los datos (sin la contraseña)
        $updateData = [
            'nombre' => $data['nombre'],
            'apellido' => $data['apellido'],
            'user' => $data['user'],
            'rol' => $data['rol'],
        ];

        // 3. (AQUÍ LA MAGIA) Hashear y añadir la contraseña
        //    SÓLO SI el campo 'password' no vino vacío.
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // 4. Actualizar el usuario en la base de datos
        $usuario->update($updateData);

        // 5. Redirigir de vuelta a la página anterior con un mensaje de éxito
       return redirect()->back()->with('success', '¡Usuario actualizado exitosamente!');
    }

    public function destroy(Usuario $usuario)
    {
        // 1. (Opcional) Evitar que el superadmin se borre a sí mismo
        if (Auth::guard('admin')->id() === $usuario->id_administrador) {
            return redirect()->back()->withErrors(['error' => 'No puedes eliminarte a ti mismo.']);
        }

        // 2. Gracias al Trait SoftDeletes en el modelo Usuario,
        //    esto no lo borra, solo llena la columna 'deleted_at'.
        $usuario->delete();

        // 3. Redirige de vuelta a la lista con un mensaje
        return redirect()->back()->with('success', '¡Usuario eliminado exitosamente!');
    }
}