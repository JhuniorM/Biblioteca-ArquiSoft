<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Pago;
use App\Models\Prestamo;
use App\Models\User;
use App\Services\PrestamoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrestamoController extends Controller
{
    public function __construct(private readonly PrestamoService $prestamos) {}

    public function devolver(Request $request, Prestamo $prestamo): RedirectResponse
    {
        $datos = $request->validate([
            'estado_material' => ['required', 'in:bueno,danado,perdido'],
            'observacion_devolucion' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->prestamos->devolver(
            $prestamo,
            null,
            $request->user(),
            $datos['estado_material'],
            $datos['observacion_devolucion'] ?? null,
        );

        return redirect()->route('prestamos.todos')->with('success', 'Devolución registrada correctamente.');
    }

    public function index(Request $request): View
    {
        return $this->vistaPrestamosRegistrados($request, 'prestamos.index');
    }

    public function clientes(Request $request): View
    {
        $buscar = $request->string('buscar')->trim()->toString();
        $mostrarRegistro = $request->boolean('registrar');
        $cliente = null;

        if ($request->filled('cliente')) {
            $cliente = User::query()
                ->with(['prestamos.libro', 'prestamos.multa', 'prestamos.pago', 'prestamos.devueltoPor'])
                ->findOrFail($request->integer('cliente'));
        }

        $clientes = User::query()
            ->where('role', 'estudiante')
            ->when($buscar !== '', fn ($query) => $query->where(function ($query) use ($buscar) {
                $query->where('name', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            }))
            ->withCount([
                'prestamos',
                'prestamos as prestamos_activos' => fn ($query) => $query->where('estado', 'activo'),
            ])
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $librosDisponibles = Libro::query()
            ->where('ejemplares_disponibles', '>', 0)
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'autor', 'ejemplares_disponibles']);

        return view('prestamos.clientes', compact('buscar', 'clientes', 'cliente', 'librosDisponibles', 'mostrarRegistro'));
    }

    public function todos(Request $request): View
    {
        return $this->vistaPrestamosRegistrados($request, 'prestamos.todos');
    }

    private function vistaPrestamosRegistrados(Request $request, string $rutaFiltros): View
    {
        $buscar = $request->string('buscar')->trim()->toString();
        $estado = $request->string('estado')->toString();

        $prestamos = Prestamo::query()
            ->with(['usuario', 'libro', 'multa', 'devueltoPor', 'pago'])
            ->when($buscar !== '', fn ($query) => $query->whereHas('usuario', function ($query) use ($buscar) {
                $query->where('name', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            }))
            ->when(in_array($estado, ['activo', 'pendiente_pago', 'devuelto'], true), fn ($query) => $query->where('estado', $estado))
            ->latest('fecha_prestamo')
            ->paginate(15)
            ->withQueryString();

        return view('prestamos.todos', compact('prestamos', 'buscar', 'estado', 'rutaFiltros'));
    }

    public function registrarParaCliente(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'cliente_id' => ['required', 'integer', 'exists:users,id'],
            'libro_id' => ['required', 'integer', 'exists:libros,id'],
        ]);

        $cliente = User::query()
            ->where('role', 'estudiante')
            ->findOrFail($datos['cliente_id']);
        $libro = Libro::query()->findOrFail($datos['libro_id']);

        $prestamo = $this->prestamos->registrarPendiente($cliente, $libro);

        Pago::create([
            'prestamo_id' => $prestamo->id,
            'registrado_por_user_id' => $request->user()->id,
            'monto_centavos' => config('biblioteca.precio_alquiler_centavos'),
            'moneda' => 'PEN',
            'metodo_pago' => 'pendiente',
            'proveedor' => 'caja',
            'estado' => 'pendiente',
            'referencia' => 'ARQ-'.str()->upper(str()->random(12)),
        ]);

        return redirect()->route('prestamos.clientes', ['cliente' => $cliente->id])
            ->with('success', 'Solicitud creada. El cliente debe pagar en caja antes de recibir el libro.');
    }

    public function registrarCliente(Request $request): RedirectResponse
    {
        $datos = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
        ]);

        $cliente = User::create([
            'name' => $datos['name'],
            'email' => $datos['email'],
            'password' => str()->random(40),
            'role' => 'estudiante',
        ]);

        return redirect()->route('prestamos.clientes', ['cliente' => $cliente->id])
            ->with('success', 'Cliente registrado. Ahora selecciona el libro para crear su préstamo.');
    }
}
