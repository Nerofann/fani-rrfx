<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Welcome To Dashboard</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-widget">
                    <label class="main-content-label mb-3 pt-1">Total Users</label>
                    <div class="d-flex justify-content-between">
                        <div>Register</div>
                        <div><span class="" id="user_regester">Loading...</span></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Actived</div>
                        <div><span class="" id="user_actived">Loading...</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-widget">
                    <label class="main-content-label mb-3 pt-1">Int. Trans</label>
                    <div class="d-flex justify-content-between">
                        <div>Count</div>
                        <div><span class="" id="it_count">Loading...</span></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>Total</div>
                        <div><span class="" id="it_total">Loading...</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-widget">
                    <label class="main-content-label mb-3 pt-1">Deposit</label>
                    <div class="d-flex justify-content-between">
                        <div>IDR</div>
                        <div><span class="" id="dp_idr">Loading...</span></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>USD</div>
                        <div><span class="" id="dp_usd">Loading...</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-3">
        <div class="card custom-card">
            <div class="card-body">
                <div class="card-widget">
                    <label class="main-content-label mb-3 pt-1">Widthdrawal</label>
                    <div class="d-flex justify-content-between">
                        <div>IDR</div>
                        <div><span class="" id="wd_idr">Loading...</span></div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>USD</div>
                        <div><span class="" id="wd_usd">Loading...</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="assets/plugins/chart.js/Chart.bundle.min.js"></script>
<div class="row">
    <div class="col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <label class="main-content-label mb-3 pt-1">Deposit/Withdrawal (IDR)</label>
                <div class="chartjs-wrapper-demo">
                    <canvas id="chartIdrDpWdIt"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card custom-card">
            <div class="card-body">
                <label class="main-content-label mb-3 pt-1">Deposit/Withdrawal/Int. Trans (USD)</label>
                <div class="chartjs-wrapper-demo">
                    <canvas id="chartUsdDpWdIt"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <?php 
        $rangkingViews = [
            "view.lostrangking"    => "view_lostrangking.php",
            "view.profitrangking"  => "view_profitrangking.php",
            "view.volumerangking"  => "view_volumerangking.php",
            "view.symbolrangking"  => "view_symbolrangking.php",
            "view.balancerangking" => "view_balancerangking.php",
            "view.depositrangking" => "view_depositrangking.php",
        ];

        foreach ($rangkingViews as $permission => $file) {
            if ($adminPermissionCore->isHavePermission($moduleId, $permission)) {
                require_once __DIR__ . "/$file";
            }
        }
    ?>
</div>

<script type="text/javascript">
    (() => {
        const fmt2 = (num) => Number(num ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        const moneyTick = (value) => fmt2(value);
        const moneyTooltip = (label, raw) => `${label}: ${fmt2(raw)}`;
        const isV2 = typeof Chart !== 'undefined' && /^2\./.test(Chart.version || '');

        function makeLineChart(canvasId, { labels = [], datasets = [] }, opts = {}) {
            const el = document.getElementById(canvasId);
            if (!el) return null;

            const cleaned = datasets.map(ds => ({
            ...ds,
            data: (ds.data ?? []).map(v => Number(v ?? 0))
            }));

            const base = {
            type: 'line',
            data: { labels, datasets: cleaned }
            };

            if (isV2) {
                base.options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        callbacks: { label: (ctx) => moneyTooltip(ctx.dataset.label || '', ctx.yLabel) }
                    },
                    legend: { display: true },
                    scales: {
                        xAxes: [{ 
                            ticks: { 
                                autoSkip: true, maxRotation: 0 
                            },
                            gridLines: {
                                color: "rgba(119, 119, 142, 0.2)"
                            }
                        }],
                        yAxes: [{ 
                            ticks: { 
                                callback: (value) => moneyTick(value), beginAtZero: true 
                            },
                            gridLines: {
                                color: "rgba(119, 119, 142, 0.2)"
                            }
                        }]
                    },
                    ...(opts || {})
                };
            } else {
                base.options = {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { intersect: false, mode: 'index' },
                    plugins: {
                        legend: { display: true },
                        tooltip: { callbacks: { label: (ctx) => moneyTooltip(ctx.dataset.label ?? '', ctx.raw) } },
                        ...((opts && opts.plugins) || {})  // <- aman kalau undefined
                    },
                    scales: {
                        x: { 
                            ticks: { 
                                autoSkip: true, maxRotation: 0 
                            },
                            gridLines: {
                                color: "rgba(119, 119, 142, 0.2)"
                            }
                        },
                        y: { 
                            beginAtZero: true, 
                            ticks: { 
                                callback: (value) => moneyTick(value) 
                            },
                            gridLines: {
                                color: "rgba(119, 119, 142, 0.2)"
                            }
                        }
                    },
                    ...(opts || {})
                };
            }

            return new Chart(el, base);
        }

        const palette = {
            blue:   'rgba(54, 162, 235, 0.7)',
            green:  'rgba(75, 192, 192, 0.7)',
            red:    'rgba(255, 99, 132, 0.7)',
            purple: 'rgba(153, 102, 255, 0.7)'
        };

        async function fetchSummary() {
            const tryUrls = [
                '/ajax/post/dashboard/summary'
            ];
            for (const url of tryUrls) {
                try {
                    const res = await fetch(url, { cache: 'no-store' });
                    if (!res.ok) continue;
                    const json = await res.json();
                    if (json && typeof json === 'object') return json;
                } catch (_) {}
            }
            return null;
        }

        async function init() {
            const json = await fetchSummary();
            if (!json?.success) {
                console.error('Gagal load summary:', json);
                return;
            }

            const data = json.data ?? {};
            const setText = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.innerText = val ?? '';
            };
            setText('user_regester',data.user_regester);
            setText('user_actived', data.user_actived);
            setText('it_count',     data.it_count);
            setText('it_total',     data.it_total);
            setText('dp_idr',       data.dp_idr);
            setText('dp_usd',       data.dp_usd);
            setText('wd_idr',       data.wd_idr);
            setText('wd_usd',       data.wd_usd);

            const cidIdr = json.chartIdrDpWdIt ?? {};
            const labelsIdr = cidIdr.labels ?? [];
            const dpIdrSeries = cidIdr.dp_series ?? [];
            const wdIdrSeries = cidIdr.wd_series ?? [];

            makeLineChart('chartIdrDpWdIt', {
                labels: labelsIdr,
                datasets: [
                    {
                        label: 'Deposit IDR',
                        borderColor: palette.green,
                        backgroundColor: palette.green,
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.25,
                        data: dpIdrSeries
                    },
                    {
                        label: 'Withdrawal IDR',
                        borderColor: palette.red,
                        backgroundColor: palette.red,
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.25,
                        data: wdIdrSeries
                    }
                ]
            });

            const cidUsd = json.chartUsdDpWdIt ?? {};
            const labelsUsd = cidUsd.labels ?? [];
            const dpUsdSeries = cidUsd.dp_series ?? [];
            const wdUsdSeries = cidUsd.wd_series ?? [];
            const itUsdSeries = cidUsd.it_series ?? [];

            makeLineChart('chartUsdDpWdIt', {
                labels: labelsUsd,
                datasets: [
                    {
                        label: 'Deposit USD',
                        borderColor: palette.green,
                        backgroundColor: palette.green,
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.25,
                        data: dpUsdSeries
                    },
                    {
                        label: 'Withdrawal USD',
                        borderColor: palette.red,
                        backgroundColor: palette.red,
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.25,
                        data: wdUsdSeries
                    },
                    {
                        label: 'Internal Transfer USD',
                        borderColor: palette.blue,
                        backgroundColor: palette.blue,
                        borderWidth: 2,
                        pointRadius: 2,
                        tension: 0.25,
                        data: itUsdSeries
                    }
                ]
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>