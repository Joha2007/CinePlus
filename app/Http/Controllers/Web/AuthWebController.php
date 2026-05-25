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

    public function registerCliente(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre_cliente'            => 'required|string|max:100',
            'apellido_cliente'          => 'required|string|max:100',
            'correo_cli'                => 'required|email|unique:clientes,correo_cli',
            'edad_cli'                  => 'required|integer|min:1|max:120',
            'contrasena_cli'            => 'required|string|min:6|confirmed',
            'contacto_cli'              => 'required|string|max:20',
        ]);

        $data['contrasena_cli'] = Hash::make($data['contrasena_cli']);
        $cliente = Cliente::create($data);

        session(['cliente' => $cliente->toArray()]);

        return redirect()->route('home')->with('success', '¡Cuenta creada exitosamente! Bienvenido/a, ' . $cliente->nombre_cliente . '.');
    }

    public function loginCliente(Request $request): RedirectResponse
    {
        $request->validate([
            'correo_cli'     => 'required|email',
            'contrasena_cli' => 'required|string',
        ]);

        $cliente = Cliente::where('correo_cli', $request->correo_cli)->first();

        if (! $cliente || ! Hash::check($request->contrasena_cli, $cliente->contrasena_cli)) {
            return back()->withErrors(['correo_cli' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        session(['cliente' => $cliente->toArray()]);

        return redirect()->intended(route('home'))->with('success', '¡Bienvenido/a, ' . $cliente->nombre_cliente . '!');
    }

    public function logoutCliente(Request $request): RedirectResponse
    {
        $request->session()->forget('cliente');
        return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
    }

    // ─── Acciones Admin ───────────────────────────

    public function loginAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'correo_adm'     => 'required|email',
            'contrasena_adm' => 'required|string',
        ]);

        $admin = Administrador::with('sucursal')
            ->where('correo_adm', $request->correo_adm)
            ->first();

        if (! $admin || ! Hash::check($request->contrasena_adm, $admin->contrasena_adm)) {
            return back()->withErrors(['correo_adm' => 'Correo o contraseña incorrectos.'])->withInput();
        }

        session(['admin' => $admin->toArray()]);

        return redirect()->route('admin.dashboard')->with('success', '¡Bienvenido, ' . $admin->nombre_adm . '!');
    }

    public function logoutAdmin(Request $request): RedirectResponse
    {
        $request->session()->forget('admin');
        return redirect()->route('home')->with('success', 'Sesión de administrador cerrada.');
    }
}
