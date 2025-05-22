<style>
/* Sidebar base */
.portal-sidebar {
    width: 250px;
    background: #fff;
    transition: transform 0.3s ease-in-out;
    height: 100vh;
    overflow-y: auto;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 999;
    transform: translateX(-100%);
}

/* Contenido interno del sidebar separado del borde superior */
.portal-sidebar--header {
    padding-top: 60px; /* separamos el contenido (como el logo) del botón burger */
}

/* Sidebar visible */
.portal-sidebar.active {
    transform: translateX(0);
}

/* Botón burger */
#toggle-sidebar {
    position: fixed;
    top: 10px;
    left: 10px;
    z-index: 1001;
    background: none;
    border: none;
    color: #333;
    cursor: pointer;
}

/* Overlay que oscurece el fondo cuando sidebar está activo */
#sidebar-overlay {
    display: none;
}

#sidebar-overlay.active {
    display: block;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 998;
    width: 100%;
    height: 100%;
    background

</style>

<!-- Botón burger -->
<button id="toggle-sidebar">
  <i class="fa fa-bars fa-2x"></i>
</button>

<!-- Overlay -->
<div id="sidebar-overlay"></div>

<div class="portal-sidebar">
  <div class="portal-sidebar--header hidden-xs">
    
    <div class="portal-sidebar--logo">
      <div class="portal-sidebar--logo-image">
        <img src="{{ $cliente->logo ?? asset('assets/images/guest.webp') }}" alt="CLECEIM,S.L.">
      </div>
    </div>
    <div class="portal-sidebar--content">
      <ul class="portal-sidebar-menu">
        <li class="">
          <a href="/portal/dashboard" class="active">
            <span class="portal-sidebar__item-icon">
              <i class="fa-solid fa-house"></i>
            </span>
            <span class="portal-sidebar__item-label">
              Inicio
            </span>
          </a>
        </li>
        <li class="">
            <a href="/portal/facturas" class="">
              <span class="portal-sidebar__item-icon">
                <i class="fa-solid fa-file-invoice"></i>
              </span>
              <span class="portal-sidebar__item-label">
                Facturas
              </span>
            </a>
          </li>
          <li class="">
            <a href="/portal/presupuestos" class="">
              <span class="portal-sidebar__item-icon">
                <i class="fa-solid fa-file-invoice-dollar"></i>
              </span>
              <span class="portal-sidebar__item-label">
                Presupuestos
              </span>
            </a>
          </li>
          <li class="">
            <a href="/portal/taskview" class="">
              <span class="portal-sidebar__item-icon">
                <i class="fa-solid fa-clock"></i>
              </span>
              <span class="portal-sidebar__item-label">
                Proyectos
              </span>
            </a>
          </li>
          <li class="">
            <a href="/portal/compras" class="">
              <span class="portal-sidebar__item-icon">
                <i class="fa-solid fa-shopping-cart"></i>
              </span>
              <span class="portal-sidebar__item-label">
                Compras
              </span>
            </a>
          </li>
        <li class="has-submenu">
          <a>
            <span class="portal-sidebar__item-icon">
              <i class="fa-solid fa-user"></i>
            </span>
            <span class="portal-sidebar__item-label">
              {{ $cliente->company ?? $cliente->name }}
            </span>
            <span class="portal-sidebar__item-arrow">
              <i class="fa-solid fa-angle-right"></i>
            </span>
          </a>
          <ul class="portal-sidebar-menu">
            <li>
              <a href="{{ route('portal.changePin') }}" class="portal__js-password">
                <span class="portal-sidebar__item-icon">
                  <i class="fa-solid fa-lock"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Establecer pin
                </span>
              </a>
            </li>
            <li>
              <a href="/portal?logout=true">
                <span class="portal-sidebar__item-icon">
                  <i class="fa-solid fa-power-off"></i>
                </span>
                <span class="portal-sidebar__item-label">Desconectar</span>
              </a>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </div>
</div>

@section('scriptsSidebar')
<script>
  $(document).ready(function() {
    $(document).ready(function () {
    // Activar item actual del menú
    function activateMenuItem() {
        const currentPath = window.location.pathname;
        $('.portal-sidebar-menu a').removeClass('active');
        $('.portal-sidebar-menu li').removeClass('open');

        $('.portal-sidebar-menu a').each(function () {
            const linkPath = $(this).attr('href');
            if (linkPath && currentPath.includes(linkPath)) {
                $(this).addClass('active');
                if ($(this).closest('.has-submenu').length > 0) {
                    $(this).closest('.has-submenu').addClass('open');
                    $(this).closest('.has-submenu').children('a').first().addClass('active');
                }
            }
        });
    }

    // Toggle submenús
    $('.has-submenu > a').on('click', function (e) {
        e.preventDefault();
        $(this).parent().toggleClass('open').siblings().removeClass('open');
    });

    // Toggle sidebar
    $('#toggle-sidebar').on('click', function () {
        $('.portal-sidebar').toggleClass('active');
        $('#sidebar-overlay').toggleClass('active');
    });

    // Cerrar sidebar al hacer clic fuera
    $('#sidebar-overlay').on('click', function () {
        $('.portal-sidebar').removeClass('active');
        $(this).removeClass('active');
    });

    activateMenuItem();
});
  });
</script>
@endsection
