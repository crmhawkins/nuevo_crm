<nav class="navbar">
    <header class="top-burguer">
        <a href="#" class="burger-btn d-block">
            <i class="bi bi-list"></i>
        </a>
    </header>
    <ul class="topbar-menu d-flex align-items-center gap-3">
        <li class="dropdown notification-list">
            <a href="#" type="button" class="nav-link position-relative">
                <i class="fa-regular fa-envelope"></i>
                <span class="position-absolute top-10 start-80 translate-middle px-2 bg-info rounded-pill">
                  <span class="text-white" style="font-size: 0.85rem">0</span>
                </span>
              </a>

            {{-- <a class="nav-link dropdown-toggle arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
            </a> --}}
            {{-- <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 fs-16 fw-semibold">Messages</h6>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="text-dark text-decoration-underline">
                                <small>Clear All</small>
                            </a>
                        </div>
                    </div>
                </div>

                <div style="max-height: 300px;" data-simplebar="init">
                    <div class="simplebar-wrapper" style="margin: 0px;">
                        <div class="simplebar-height-auto-observer-wrapper">
                            <div class="simplebar-height-auto-observer"></div>
                        </div>
                        <div class="simplebar-mask">
                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                    <div class="simplebar-content" style="padding: 0px;">

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-1.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Cristina Pride <small class="fw-normal text-muted float-end ms-1">1 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-2.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Sam Garret <small class="fw-normal text-muted float-end ms-1">2 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Yeah everything is fine</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-3.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Karen Robinson <small class="fw-normal text-muted float-end ms-1">2 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Wow that's great</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-4.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Sherry Marshall <small class="fw-normal text-muted float-end ms-1">3 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Hi, How are you? What about our next meeting</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item p-0 notify-item read-noti card m-0 shadow-none">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <div class="notify-icon">
                                                            <img src="assets/images/users/avatar-5.jpg" class="img-fluid rounded-circle" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 text-truncate ms-2">
                                                        <h5 class="noti-item-title fw-semibold fs-14">Shawn Millard <small class="fw-normal text-muted float-end ms-1">4 day ago</small></h5>
                                                        <small class="noti-item-subtitle text-muted">Yeah everything is fine</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                    </div>
                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                    </div>
                    <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                    </div>
                </div>

                <!-- All-->
                <a href="javascript:void(0);" class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                    View All
                </a>

            </div> --}}
        </li>

        <li class="dropdown notification-list">
            <a class="nav-link arrow-none" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fa-regular fa-bell"></i>
                <span class="position-absolute top-10 start-80 translate-middle px-2 bg-danger rounded-pill">
                    <span class="text-white" style="font-size: 0.85rem">0</span>
                  </span>            </a>

            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated dropdown-lg py-0">
                <div class="p-2 border-top-0 border-start-0 border-end-0 border-dashed border">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-0 fs-16 fw-semibold">Notification</h6>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="text-dark text-decoration-underline">
                                <small>Clear All</small>
                            </a>
                        </div>
                    </div>
                </div>

                <div style="max-height: 300px;" data-simplebar="init">
                    <div class="simplebar-wrapper" style="margin: 0px;">
                        <div class="simplebar-height-auto-observer-wrapper">
                            <div class="simplebar-height-auto-observer"></div>
                        </div>
                        <div class="simplebar-mask">
                            <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                                <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;">
                                    <div class="simplebar-content" style="padding: 0px;">
                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-primary-subtle">
                                                <i class="mdi mdi-comment-account-outline text-primary"></i>
                                            </div>
                                            <p class="notify-details">Caleb Flakelar commented on Admin
                                                <small class="noti-time">1 min ago</small>
                                            </p>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-warning-subtle">
                                                <i class="mdi mdi-account-plus text-warning"></i>
                                            </div>
                                            <p class="notify-details">New user registered.
                                                <small class="noti-time">5 hours ago</small>
                                            </p>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-danger-subtle">
                                                <i class="mdi mdi-heart text-danger"></i>
                                            </div>
                                            <p class="notify-details">Carlos Crouch liked
                                                <small class="noti-time">3 days ago</small>
                                            </p>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-pink-subtle">
                                                <i class="mdi mdi-comment-account-outline text-pink"></i>
                                            </div>
                                            <p class="notify-details">Caleb Flakelar commented on Admin
                                                <small class="noti-time">4 days ago</small>
                                            </p>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-purple-subtle">
                                                <i class="mdi mdi-account-plus text-purple"></i>
                                            </div>
                                            <p class="notify-details">New user registered.
                                                <small class="noti-time">7 days ago</small>
                                            </p>
                                        </a>

                                        <!-- item-->
                                        <a href="javascript:void(0);" class="dropdown-item notify-item">
                                            <div class="notify-icon bg-success-subtle">
                                                <i class="mdi mdi-heart text-success"></i>
                                            </div>
                                            <p class="notify-details">Carlos Crouch liked <b>Admin</b>.
                                                <small class="noti-time">Carlos Crouch liked</small>
                                            </p>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div>
                    </div>
                    <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                    </div>
                    <div class="simplebar-track simplebar-vertical" style="visibility: hidden;">
                        <div class="simplebar-scrollbar" style="height: 0px; display: none;"></div>
                    </div>
                </div>

                <!-- All-->
                <a href="javascript:void(0);" class="dropdown-item text-center text-primary text-decoration-underline fw-bold notify-item border-top border-light py-2">
                    View All
                </a>

            </div>
        </li>
        <li class="d-none d-sm-inline-block">
            <div class="nav-link" >
                <i id="light-dark-mode" class="bi @if($isDarkMode) bi-brightness-high @else bi-moon @endif" style="cursor: pointer;"></i>
            </div>
        </li>

       {{-- <li class="dropdown">
            <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <span class="account-user-avatar">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/User-avatar.svg/2048px-User-avatar.svg.png" alt="user-image" width="32" class="rounded-circle">
                </span>
                <span class="d-lg-block d-none">
                    <h5 class="my-0 fw-normal">Thomson <i class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Welcome!</h6>
                </div>

                <!-- item-->
                <a href="pages-profile.html" class="dropdown-item">
                    <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                    <span>My Account</span>
                </a>

                <!-- item-->
                <a href="pages-profile.html" class="dropdown-item">
                    <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                    <span>Settings</span>
                </a>

                <!-- item-->
                <a href="pages-faq.html" class="dropdown-item">
                    <i class="ri-customer-service-2-line fs-18 align-middle me-1"></i>
                    <span>Support</span>
                </a>

                <!-- item-->
                <a href="auth-lock-screen.html" class="dropdown-item">
                    <i class="ri-lock-password-line fs-18 align-middle me-1"></i>
                    <span>Lock Screen</span>
                </a>

                <!-- item-->
                <a href="auth-logout-2.html" class="dropdown-item">
                    <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                    <span>Logout</span>
                </a>
            </div>
        </li> --}}
    </ul>
</nav>

<script>
    document.getElementById('light-dark-mode').addEventListener('click', function() {
        const body = document.body;
        body.classList.toggle('dark-mode');
        const isDarkMode = body.classList.contains('dark-mode');

        // Guardar preferencia en la base de datos
        fetch('/save-theme-preference', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ is_dark: isDarkMode })
        }).then(response => {
            if (response.ok) {
                console.log('Preferencia de tema guardada.');
                console.log(isDarkMode)
                console.log(this)
                window.location.reload();
            } else {
                console.error('Error al guardar la preferencia de tema.');
            }
        });
    });
</script>

