<div class="portal-sidebar">
  <div class="portal-sidebar--header hidden-xs">
    <div class="portal-sidebar--logo">
      <div class="portal-sidebar--logo-image">
        <img src="https://app.holded.com/box/account/6360e6fa07d20dd46b06ff8b?p=6360e6fa07d20dd46b06ff8b" alt="CLECEIM,S.L.">
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
        <li class="has-submenu">
          <a class="">
            <span class="portal-sidebar__item-icon">
              <i class="fa-solid fa-star"></i>
            </span>
            <span class="portal-sidebar__item-label">
              Ventas
            </span>
            <span class="portal-sidebar__item-arrow">
              <i class="fa-solid fa-angle-right"></i>
            </span>
          </a>
          <ul class="portal-sidebar-menu">
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
              <a href="/portal/campanias" class="">
                <span class="portal-sidebar__item-icon">
                  <i class="fa-solid fa-diagram-project"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Campañas
                </span>
              </a>
            </li>
            {{-- <li class="">
              <a href="/portal/proforms" class="">
                <span class="portal-sidebar__item-icon">
                  <i class="fal fa-file-alt"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Proformas
                </span>
              </a>
            </li> --}}
            {{-- <li class="">
              <a href="/portal/waybills" class="">
                <span class="portal-sidebar__item-icon">
                  <i class="fal fa-indent"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Albaranes
                </span>
              </a>
            </li> --}}
            {{-- <li class="">
              <a href="/portal/salesorders" class="">
                <span class="portal-sidebar__item-icon">
                  <i class="fal fa-shopping-bag"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Pedidos de venta
                </span>
              </a>
            </li> --}}
          </ul>
        </li>
        {{-- <li class="has-submenu">
          <a class="">
            <span class="portal-sidebar__item-icon">
              <i class="fal fa-shopping-bag"></i>
            </span>
            <span class="portal-sidebar__item-label">
              Compras
            </span>
            <span class="portal-sidebar__item-arrow">
              <i class="fa-solid fa-angle-right"></i>
            </span>
          </a>
          <ul class="portal-sidebar-menu">
            <li class="">
              <a href="/portal/orders" class="">
                <span class="portal-sidebar__item-icon">
                  <i class="fal fa-shopping-basket"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Pedidos de compra
                </span>
              </a>
            </li>
          </ul>
        </li> --}}
        <li class="has-submenu">
          <a>
            <span class="portal-sidebar__item-icon">
              <i class="fa-solid fa-user"></i>
            </span>
            <span class="portal-sidebar__item-label">
              THWORK 3000,S.L.
            </span>
            <span class="portal-sidebar__item-arrow">
              <i class="fa-solid fa-angle-right"></i>
            </span>
          </a>
          <ul class="portal-sidebar-menu">
            {{-- <li>
              <a class="portal__js-language">
                <span class="portal-sidebar__item-icon">
                  <i class="fal fa-globe"></i>
                </span>
                <span class="portal-sidebar__item-label">
                  Idioma
                </span>
              </a>
            </li> --}}
            <li>
              <a class="portal__js-password">
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
    // Función para activar el ítem de menú correspondiente
    function activateMenuItem() {
      const currentPath = window.location.pathname; // Obtener el path actual
      // Desactivar todos los ítems antes de activar el correcto
      $('.portal-sidebar-menu a').removeClass('active');  // Solo se remueve 'active' de los enlaces
      $('.portal-sidebar-menu li').removeClass('open');  // 'open' se maneja en los <li>

      $('.portal-sidebar-menu a').each(function() {
        const linkPath = $(this).attr('href');

        if (linkPath && currentPath.includes(linkPath)) {
          // Activar el enlace correspondiente
          $(this).addClass('active');

          // Si es parte de un submenú, mantener el submenú abierto y activar también el enlace padre
          if ($(this).closest('.has-submenu').length > 0) {
            $(this).closest('.has-submenu').addClass('open');
            $(this).closest('.has-submenu').children('a').first().addClass('active'); // Añade active al enlace padre
          }
        }
      });
    }

    // Evento para manejar el clic y alternar la clase 'open' en submenús
    $('.has-submenu > a').on('click', function(e) {
      e.preventDefault();  // Prevenir la navegación si solo se quiere abrir el submenú
      $(this).parent().toggleClass('open').siblings().removeClass('open'); // Asegura que solo un submenú esté abierto a la vez
    });

    activateMenuItem(); // Llamar la función al cargar la página
  });
</script>
@endsection
