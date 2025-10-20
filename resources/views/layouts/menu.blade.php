
<li class="nav-item">
    <a href="{{ route('usuarios.index') }}" class="nav-link {{ Request::is('usuarios*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Usuarios</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('medicamentos.index') }}" class="nav-link {{ Request::is('medicamentos*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Medicamentos</p>
    </a>
</li>

<li class="nav-item">
    <a href="{{ route('actividadesTeraps.index') }}" class="nav-link {{ Request::is('actividadesTeraps*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-home"></i>
        <p>Actividades Teraps</p>
    </a>
</li>
