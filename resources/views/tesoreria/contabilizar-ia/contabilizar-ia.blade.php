@extends('layouts.app')

@section('titulo', 'Contabilizar IA')

@section('content')

<div class="page-heading card" style="box-shadow: none !important">
        <div class="bg-image overflow-hidden mb-10">
            <div class="content content-narrow content-full">
                <div class="text-center mt-5 mb-2">
                    <h2 class="h2 text-white mb-0">Bienvenido {{ $client->name }}</h2>
                    <br>
                   
                    <div class="mt-4 row d-flex justify-content-center ">
                        <div class="col-6 mb-3">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5 mb-2">
            <form action="{{ route('tesoreria.contabilizar-ia.upload') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                @csrf
                <div class="mb-3">
                    <label for="excel_file" class="form-label text-white">Selecciona archivo Excel</label>
                    <input class="form-control" type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls" required>
                </div>
                <button type="submit" class="btn btn-success">Subir y Procesar</button>
            </form>
            
    </div>
            
</div>
@endsection
@section('scripts')

    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.css"
        rel="stylesheet">

    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.6/b-3.1.2/b-colvis-3.1.2/r-3.0.3/datatables.min.js">
    </script>

    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        $("#topbar").remove();
            $('#sendLogout').click(function(e) {
                e.preventDefault(); // Esto previene que el enlace navegue a otra página.
                $('#logout-form').submit(); // Esto envía el formulario.
            });
            $('#kit_submit').click(function(e) {
                e.preventDefault(); // Esto previene que el enlace navegue a otra página.
                $('#kit_form').submit(); // Esto envía el formulario.
            });
        // Inicializar DataTables para la tabla de Kit Digital
        $('#kitDigitalTable').DataTable({
            paging: true,
                lengthMenu: [
                    [10, 25, 50],
                    [10, 25, 50]
                ],
            language: {
                decimal: "",
                emptyTable: "No hay datos disponibles",
                info: "_TOTAL_ entradas en total",
                infoEmpty: "0 entradas",
                infoFiltered: "(filtrado de _MAX_ entradas en total)",
                lengthMenu: "Nº de entradas  _MENU_",
                loadingRecords: "Cargando...",
                processing: "Procesando...",
                search: "Buscar:",
                zeroRecords: "No hay entradas que cumplan el criterio",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            }
        });

        // Filtro para el dropdown de Estado
        $('#estadoFilter').on('change', function() {
            var table = $('#kitDigitalTable').DataTable();
            table.column(2).search(this.value).draw();
        });

        // Botón de logout
        $('#sendLogout').on('click', function(e) {
            e.preventDefault();
                $.post('/admin/logout', {
                    _token: '{{ csrf_token() }}'
                }, function(data) {
                window.location.href = '/admin';
            });
        });

            // Common chart options for maintaining aspect ratio
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false
            };

            // Initialize Pie Chart
            const ctx = document.getElementById('messageStatsChart').getContext('2d');
            const messageStatsChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Enviados', 'Recibidos', 'Leídos'],
                    datasets: [{
                        data: [150, 120, 90], // Example data - replace with actual data
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 14
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Estadísticas de Mensajes',
                            font: {
                                size: 16
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // Initialize Bar Chart
            const ctx2 = document.getElementById('campaignStatsChart').getContext('2d');
            const campaignStatsChart = new Chart(ctx2, {
                type: 'bar',
                data: {
                    labels: ['Campaña 1', 'Campaña 2', 'Campaña 3'],
                    datasets: [{
                        label: 'Tasa de Respuesta',
                        data: [75, 45, 60],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(255, 205, 86, 0.8)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 159, 64, 1)',
                            'rgba(255, 205, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...commonOptions,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Rendimiento de Campañas',
                            font: {
                                size: 16
                            },
                            padding: {
                                top: 10,
                                bottom: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Porcentaje (%)'
                            }
                        }
                    }
                }
            });

            // Example function to update pie chart data
            function updateChartData(sent, received, read) {
                messageStatsChart.data.datasets[0].data = [sent, received, read];
                messageStatsChart.update();
            }

            // Example function to update bar chart data
            function updateCampaignData(campaign1, campaign2, campaign3) {
                campaignStatsChart.data.datasets[0].data = [campaign1, campaign2, campaign3];
                campaignStatsChart.update();
            }
        });
    </script>
    <script>
        let timerState = 'stopped';
        let timerTime = 0;
        let timerInterval;

    function updateTime() {
        let hours = Math.floor(timerTime / 3600);
        let minutes = Math.floor((timerTime % 3600) / 60);
        let seconds = timerTime % 60;

        hours = hours < 10 ? '0' + hours : hours;
        minutes = minutes < 10 ? '0' + minutes : minutes;
        seconds = seconds < 10 ? '0' + seconds : seconds;

        document.getElementById('timer').textContent = `${hours}:${minutes}:${seconds}`;
    }

    function startTimer() {
            timerState = 'running';
            timerInterval = setInterval(() => {
                timerTime++;
                updateTime();
            }, 1000);
    }

    function stopTimer() {
            clearInterval(timerInterval);
            timerState = 'stopped';
    }

    function startJornada() {
                    startTimer();
                    document.getElementById('startJornadaBtn').style.display = 'none';
                    document.getElementById('startPauseBtn').style.display = 'block';
                    document.getElementById('endJornadaBtn').style.display = 'block';
    }

    function endJornada() {
                stopTimer();
                document.getElementById('startJornadaBtn').style.display = 'block';
                document.getElementById('startPauseBtn').style.display = 'none';
                document.getElementById('endJornadaBtn').style.display = 'none';
                document.getElementById('endPauseBtn').style.display = 'none';
    }

    function startPause() {
                    stopTimer();
                    document.getElementById('startPauseBtn').style.display = 'none';
                    document.getElementById('endPauseBtn').style.display = 'block';
    }

    function endPause() {
                    startTimer();
                    document.getElementById('startPauseBtn').style.display = 'block';
            document.getElementById('endPauseBtn').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateTime();
        });
</script>
@endsection
