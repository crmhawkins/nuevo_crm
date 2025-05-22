<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="/dashboard"><img src={{ asset('assets/images/logo/logo.png') }} alt="Logo" srcset=""
                            class="img-fluid"></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle fs-5"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ request()->routeIs('plataforma.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.dashboard') }}" class='sidebar-link'>
                        <i class="bi bi-grid-fill  fs-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-title">Whatsapp</li>
                <li class="sidebar-item {{ request()->routeIs('plataforma.clientes') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.clientes') }}" class='sidebar-link'>
                        <i class="fa-solid fa-people-group fs-5"></i>
                        <span>Contactos</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('plataforma.campanias') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.campanias') }}" class='sidebar-link'>
                        <i class="fa-solid fa-bullhorn fs-5"></i>
                        <span>Campañas</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('plataforma.templates') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.templates') }}" class='sidebar-link'>
                        <i class="fa-solid fa-file-lines fs-5"></i>
                        <span>Plantillas</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('plataforma.logs') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.logs') }}" class='sidebar-link'>
                        <i class="fa-solid fa-binoculars fs-5"></i>
                        <span>Logs</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('plataforma.configuracion') ? 'active' : '' }}">
                    <a href="{{ route('plataforma.configuracion') }}" class='sidebar-link'>
                        <i class="fa-solid fa-gear fs-5"></i>
                        <span>Configuración</span>
                    </a>
                </li>
            </ul>
        </div>
        {{-- <button class="sidebar-toggler btn x"><i data-feather="x fs-5"></i></button> --}}
    </div>
</div>
