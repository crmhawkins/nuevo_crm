@if (isset($row['keyword']) && isset($row['metrics']))
    <div class="keyword-card bg-white p-4">
        <h3 class="font-medium text-lg mb-2">{{ $row['keyword'] }}</h3>
        <div class="grid grid-cols-2 gap-4 mb-2">
            <div>
                <span class="text-sm text-gray-500">Resultados totales:</span>
                <div class="metric">
                    {{ number_format($row['metrics']['last_result']) }}
                    <span class="trend-{{ $row['metrics']['trend'] }}">
                        {{ $row['metrics']['trend'] === 'up' ? '↑' : ($row['metrics']['trend'] === 'down' ? '↓' : '→') }}
                        {{ $row['metrics']['change_percent'] }}%
                    </span>
                </div>
            </div>
            <div>
                <span class="text-sm text-gray-500">Posición actual:</span>
                <div class="metric">
                    {{ $row['metrics']['last_position'] ? number_format($row['metrics']['last_position'], 1) : 'N/A' }}
                    @if ($row['metrics']['last_position'])
                        <span
                            class="trend-{{ $row['metrics']['position_change'] > 0 ? 'up' : ($row['metrics']['position_change'] < 0 ? 'down' : 'neutral') }}">
                            {{ $row['metrics']['position_change'] > 0 ? '↑' : ($row['metrics']['position_change'] < 0 ? '↓' : '→') }}
                            {{ abs($row['metrics']['position_change']) }} pos
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="chart-container">
            <canvas id="{{ $row['metrics']['chart_id'] }}"></canvas>
        </div>
    </div>
@endif
