<nav class="bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            {{-- 🔹 Identidad: chip con el rol y link al dashboard correcto --}}
            @php
                $user = Auth::user();
                $rol  = $user?->tipoUsuario ? strtolower($user->tipoUsuario) : null;

                $label = $rol ? ucfirst($rol) : 'Invitado';

                // Ruta de dashboard según rol
                $dashRoute = match($rol) {
                    'administrador' => 'admin.dashboard',
                    'medico'        => 'medico.dashboard',
                    'paciente'      => 'paciente.dashboard',
                    default         => 'dashboard',
                };

                // Colores del chip por rol
                $chipClasses = match($rol) {
                    'administrador' => 'bg-purple-100 text-purple-700 border-purple-300',
                    'medico'        => 'bg-emerald-100 text-emerald-700 border-emerald-300',
                    'paciente'      => 'bg-blue-100 text-blue-700 border-blue-300',
                    default         => 'bg-gray-100 text-gray-600 border-gray-300',
                };
            @endphp

            <div class="flex items-center space-x-2">
                <a href="{{ route($dashRoute) }}" class="flex items-center">
                    {{-- Puedes dejar el logo o quitarlo si no lo quieres --}}
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    <span class="ml-2 text-sm font-semibold px-3 py-1 rounded-full border {{ $chipClasses }}">
                        {{ $label }}
                    </span>
                </a>
            </div>

            {{-- 🔹 Botones de usuario --}}
            <div class="flex items-center space-x-3">
                @auth
                    {{-- (Opcional) Nombre: si tu tabla es Usuarios, usa nombre/apellido --}}
                    {{-- <span class="text-gray-700 font-medium text-sm">
                        {{ $user->nombre ?? '' }} {{ $user->apellido ?? '' }}
                    </span> --}}

                    <a href="{{ route('profile.edit') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Perfil
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white rounded-md hover:opacity-90 transition"
                                style="background-color:#b5c8e1;">
                            Cerrar sesión
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition">
                        Iniciar sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
