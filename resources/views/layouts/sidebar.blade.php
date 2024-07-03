<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="/dashboard"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS1JSTbvPQy4RdU-Av5a1Rv6JdYIZZrRrhbCA&s" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <a href="{{route('dashboard')}}" class='sidebar-link'>
                        <i class="bi bi-grid-fill fs-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-title">Empresa</li>

                @php
                    $clientesActive = request()->routeIs('clientes.index') || request()->routeIs('clientes.create') || request()->routeIs('clientes.show') || request()->routeIs('cliente.createFromBudget') || request()->routeIs('clientes.edit');
                    $presupuestoActive = request()->routeIs('presupuestos.index') || request()->routeIs('presupuesto.create') || request()->routeIs('clientes.show');
                    $dominiosActive = request()->routeIs('dominios.index') || request()->routeIs('dominios.create') || request()->routeIs('dominios.edit');
                    $tesoreriaActive = request()->routeIs('ingresos.index') 
                    || request()->routeIs('ingreso.create') 
                    || request()->routeIs('ingreso.edit');
                @endphp

                <li class="sidebar-item has-sub {{ $clientesActive ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-people-group fs-5"></i>
                        <span>Clientes</span>
                    </a>
                    <ul class="submenu" style="{{ $clientesActive ? 'display:block;' : 'display:none' }}">
                        <li class="submenu-item {{ request()->routeIs('clientes.index') ? 'active' : 'no' }} ">
                            <a href="{{route('clientes.index')}}">
                                <i class="fa-solid fa-list"></i>
                                <span>
                                    Ver todos
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('clientes.create') ? 'active' : '' }} {{ request()->routeIs('cliente.createFromBudget') ? 'active' : ''}}">
                            <a href="{{route('clientes.create')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Crear cliente
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub {{ $presupuestoActive ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                        <span>Presupuestos</span>
                    </a>
                    <ul class="submenu" style="{{ $presupuestoActive ? 'display:block;' : 'display:none;' }}">
                        <li class="submenu-item {{ request()->routeIs('presupuestos.index') ? 'active' : '' }}">
                            <a href="{{route('presupuestos.index')}}">
                                <i class="fa-solid fa-list"></i>
                                <span>
                                    Ver todos
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('presupuesto.create') ? 'active' : '' }}">
                            <a href="{{route('presupuesto.create')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Crear presupuesto
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item {{ request()->routeIs('facturas.index') ? 'active' : '' }}">
                    <a href="{{route('facturas.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-file-invoice-dollar fs-5"></i>
                        <span>Facturas</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('campania.index') ? 'active' : '' }}">
                    <a href="{{route('campania.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-diagram-project fs-5"></i>
                        <span>Campañas</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('servicios.index') ? 'active' : '' }}">
                    <a href="{{route('servicios.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-sliders fs-5"></i>
                        <span>Servicios</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('serviciosCategoria.index') ? 'active' : '' }}">
                    <a href="{{route('serviciosCategoria.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-gears fs-5"></i>
                        <span>Categorias de Servicio</span>
                    </a>
                </li>
                <li class="sidebar-item {{ request()->routeIs('tareas.index') ? 'active' : '' }}">
                    <a href="{{route('tareas.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-list-check fs-5"></i>
                        <span>Tareas</span>
                    </a>
                </li>
                <li class="sidebar-item {{ $isActive('users.*') }}">
                    <a href="{{route('users.index')}}" class='sidebar-link'>
                        <i class="fa-solid fa-user-group"></i>
                        <span>Personal</span>
                    </a>
                </li>

                <li class="sidebar-item has-sub {{ $dominiosActive ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-globe fs-5"></i>
                        <span>Dominios</span>
                    </a>
                    <ul class="submenu" style="{{ $dominiosActive ? 'display:block;' : 'display:none;' }}">
                        <li class="submenu-item {{ request()->routeIs('dominios.index') ? 'active' : '' }}">
                            <a href="{{route('dominios.index')}}">
                                <i class="fa-solid fa-list"></i>
                                <span>
                                    Ver todos
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('dominios.create') ? 'active' : '' }}">
                            <a href="{{route('dominios.create')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Crear domino
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="sidebar-item has-sub {{ $tesoreriaActive ? 'active' : '' }}">
                    <a href="#" class='sidebar-link'>
                        <i class="fa-solid fa-coins fs-5"></i>
                        <span>Tesorería</span>
                    </a>
                    <ul class="submenu" style="{{ $tesoreriaActive ? 'display:block;' : 'display:none;' }}">
                        <li class="submenu-item {{ request()->routeIs('ingresos.index') ? 'active' : '' }}">
                            <a href="{{route('ingresos.index')}}">
                                <i class="fa-solid fa-list"></i>
                                <span>
                                    Ver Ingresos
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('ingreso.create') ? 'active' : '' }}">
                            <a href="{{route('ingreso.create')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Añadir Ingreso
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('dominios.index') ? 'active' : '' }}">
                            <a href="{{route('dominios.index')}}">
                                <i class="fa-solid fa-list"></i>
                                <span>
                                    Ver Gastos
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('dominios.index') ? 'active' : '' }}">
                            <a href="{{route('dominios.index')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Añadir Gasto
                                </span>
                            </a>
                        </li>
                        <li class="submenu-item {{ request()->routeIs('dominios.index') ? 'active' : '' }}">
                            <a href="{{route('dominios.index')}}">
                                <i class="fa-solid fa-plus"></i>
                                <span>
                                    Añadir Gasto Asociado
                                </span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        {{-- <button class="sidebar-toggler btn x"><i data-feather="x"></i></button> --}}
    </div>
</div>
