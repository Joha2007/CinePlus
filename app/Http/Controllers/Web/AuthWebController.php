<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Administrador;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthWebController extends Controller
{
    // ─── Vistas ───────────────────────────────────

    public function showLoginCliente(): View
    {
        return view('auth.login');
    }

    public function showRegisterCliente(): View
    {
        return view('auth.register');
    }

    public function showLoginAdmin(): View
    {
        return view('auth.admin-login');
    }

    // ─── Acciones Cliente ─────────────────────────

    /**
     * Procesa el formulario de registro de un nuevo cliente web.
     * Valida los datos, encripta la contraseña, crea el cliente en la BD
     * e inicia sesión automáticamente redirigiendo al home.
     */
    public function registerCliente(Request $request): RedirectResponse
    {
        // Validación de todos los campos del formulario de registro
        $data = $request->validate([
            'nombre_cliente'   => 'required|string|max:100',              // Nombre obligatorio, solo texto, máximo 100 caracteres
            'apellido_cliente' => 'required|string|max:100',              // Apellido obligatorio, solo texto, máximo 100 caracteres
            'correo_cli'       => 'required|email|unique:clientes,correo_cli', // Correo válido y único (no puede repetirse en la tabla clientes)
            'edad_cli'         => 'required|integer|min:15|max:85',
            'contrasena_cli'   => 'required|string|min:6|confirmed',      // Contraseña mínimo 6 caracteres, debe confirmarse con contrasena_cli_confirmation
            'contacto_cli'     => 'required|string|max:20',               // Número de contacto obligatorio, máximo 20 caracteres
        ]);

        // Encriptar la contraseña antes de guardarla en la base de datos
        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);

        // Crear el cliente en la base de datos
        $cliente = Cliente::create($data);

        // Guardar los datos del cliente en la sesión para mantenerlo autenticado
        session(['cliente' => $cliente->toArray()]);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente! Bienvenido/a, ' . $cliente->nombre_cliente . '.');
    }

    /**
     * Procesa el inicio de sesión de un cliente web.
     * Verifica las credenciales y guarda los datos en la sesión.
     */
    public function loginCliente(Request $request): RedirectResponse
    {
        // Validación de los campos de inicio de sesión
        $request->validate([
            'correo_cli'     => 'required|email',    // Correo obligatorio con formato de email válido
            'contrasena_cli' => 'required|string',   // Contraseña obligatoria
        ]);

        // Buscar al cliente por su correo electrónico
        $cliente = Cliente::where('correo_cli', $request->correo_cli)->first();

        // Verificar que el cliente exista y que la contraseña sea correcta
        if (! $cliente || ! Hash::check($request->contrasena_cli, $cliente->contrasena_cli)) {
            return back()->withErrors(['correo_cli' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        // Guardar los datos del cliente en la sesión
        session(['cliente' => $cliente->toArray()]);

        return redirect()->intended(route('home'))->with('success', '¡Bienvenido/a, ' . $cliente->nombre_cliente . '!');
    }

    /**
     * Cierra la sesión del cliente eliminando sus datos de la sesión.
     */
    public function logoutCliente(Request $request): RedirectResponse
    {
        // Eliminar los datos del cliente de la sesión activa
        $request->session()->forget('cliente');
        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
    }

    // ─── Acciones Admin ───────────────────────────

    /**
     * Procesa el inicio de sesión de un administrador web.
     * Verifica credenciales, carga la sucursal asociada y guarda en sesión.
     */
    public function loginAdmin(Request $request): RedirectResponse
    {
        // Validación de los campos de acceso del administrador
        $request->validate([
            'correo_adm'     => 'required|email',    // Correo del administrador obligatorio con formato válido
            'contrasena_adm' => 'required|string',   // Contraseña del administrador obligatoria
        ]);

        // Buscar al administrador junto con su sucursal asignada
        $admin = Administrador::with('sucursal')
            ->where('correo_adm', $request->correo_adm)
            ->first();

        // Verificar que el administrador exista y que la contraseña sea correcta
        if (! $admin || ! Hash::check($request->contrasena_adm, $admin->contrasena_adm)) {
            return back()->withErrors(['correo_adm' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        // Guardar los datos del administrador en la sesión
        session(['admin' => $admin->toArray()]);

        return redirect()->route('admin.dashboard')->with('success', '¡Bienvenido, ' . $admin->nombre_adm . '!');
    }

    public function logoutAdmin(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        return redirect()->route('home')->with('success', 'Sesión de administrador cerrada.');
    }
}
