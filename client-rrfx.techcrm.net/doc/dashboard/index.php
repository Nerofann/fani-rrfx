<!-- <div class="dashboard-breadcrumb mb-25">
    <h2>Dashboard</h2>
</div> -->

<div class="row mb-25">
    <div class="col-lg-3 col-6 col-xs-12">
        <div class="dashboard-top-box dashboard-top-box-2 rounded border-0 panel-bg h-100">
            <div class="left h-100 d-flex flex-column">
                <p class="d-flex justify-content-between mb-2">Total Deposit</p>
                <div class="d-flex flex-column">
                    <small class="fw-normal mb-0 dp-idr">Loading...</small>
                    <small class="fw-normal dp-usd">Loading...</small>
                </div>
                <p class="text-muted mt-auto"><a href="deposit"><small>Deposit History</small></a></p>
            </div>
            <div class="right">
                <a href="deposit">
                    <div class="part-icon text-light rounded">
                        <span><i class="fa-light fa-arrow-right-to-bracket"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 col-xs-12">
        <div class="dashboard-top-box dashboard-top-box-2 rounded border-0 panel-bg h-100">
            <div class="left h-100 d-flex flex-column">
                <p class="d-flex justify-content-between mb-2">Total Withdrawal</p>
                <div class="d-flex flex-column mb-2">
                    <small class="fw-normal mb-0 wd-idr">Loading...</small>
                    <small class="fw-normal wd-usd">Loading...</small>
                </div>
                <p class="text-muted mt-auto"><a href="withdrawal"><small>Withdrawal History</small></a></p>
            </div>
            <div class="right">
                <a href="withdrawal">
                    <div class="part-icon text-light rounded">
                        <span><i class="fa-light fa-arrow-right-from-bracket"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 col-xs-12">
        <div class="dashboard-top-box dashboard-top-box-2 rounded border-0 panel-bg h-100">
            <div class="left h-100 d-flex flex-column">
                <p class="d-flex justify-content-between mb-2">Total Account</p>
                <h3 class="fw-normal account">Loading...</h3>
                <p class="text-muted mt-auto"><a href="account"><small>View Account</small></a></p>
            </div>
            <div class="right">
                <a href="/account">
                    <div class="part-icon text-light rounded">
                        <span><i class="fa-light fa-user-tie"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-6 col-xs-12">
        <div class="dashboard-top-box dashboard-top-box-2 rounded border-0 panel-bg h-100">
            <div class="left h-100 d-flex flex-column">
                <p class="d-flex justify-content-between mb-2">Date Reg.</p>
                <h3 class="fw-normal"><?= $user['MBR_DATETIME'] ?></h3>
                <p class="text-muted mt-auto"><a href="personal-information"><small>View Profile</small></a></p>
            </div>
            <div class="right">
                <a href="personal-information">
                    <div class="part-icon text-light rounded">
                        <span><i class="fa-light fa-user"></i></span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
<div class="row mb-25">
    <div class="col-md-8">
        <div class="panel h-100 chart-panel-1">
            <div class="panel-header">
                <h5>Net In Out</h5>
                <div class="btn-box" id="rangeFilters" class="mb-3">
                    <button type="button" class="btn btn-sm btn-primary" data-range="7d">1 Minggu</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-range="1m">1 Bulan</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-range="1y">1 Tahun</button>
                </div>
            </div>
            <div class="panel-body">
                <div id="saleAnalytics" class="chart-dark"></div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel h-100">
            <div class="card">
                <div class="card-body">
                    <div id="economicCalendarWidget"></div>
                    <script async type="text/javascript" data-type="calendar-widget" src="https://www.tradays.com/c/js/widgets/calendar/widget.js?v=13">{"width":"100%","height":"400","mode":"2","theme":0}</script>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $formatNews = App\Models\Blog::formatGrouped(App\Models\Blog::get(), 4); ?>
<?php foreach($formatNews as $type) : ?>
    <div class="row mt-2">
        <div class="col-md-12">
            <div class="panel">
                <div class="card p-2">
                    <div class="text-center">
                        <h5 class="mb-0"><?= $type['alias']; ?></h5>
                    </div>
                </div>
            </div>
        </div>

        <?php foreach($type['data'] as $news) : ?>
            <div class="col-md-3">
                <div class="card h-100">
                    <img src="<?= App\Models\FileUpload::awsFile($news['BLOG_IMG']);?>" class="card-img-top" alt="Blog Image">
                    <div class="card-body">
                        <div class="d-flex flex-column h-100">
                            <p class="small"><?php echo str_replace(['\r\n', '&amp;nbsp;'], ["<br>", ' '],substr(strip_tags(html_entity_decode($news['BLOG_MESSAGE'])), 0, 200)) ?>...</p>
                            <div class="mt-auto">
                                <a href="/news?detail=<?php echo $news['BLOG_SLUG'] ?>" class="mt-auto btn btn-sm btn-primary">Detail</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>

<!-- <script src="/assets/vendor/js/apexcharts.js"></script> -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function($){
'use strict';

$(document).ready(function(){
  // summary tetap
  $.get("/ajax/post/dashboard/summary", (resp) => {
    if(resp.success) {
      $('.account').text(resp.data.account);
      $('.dp-idr').text(resp.data.deposit.idr);
      $('.dp-usd').text(resp.data.deposit.usd);
      $('.wd-idr').text(resp.data.withdrawal.idr);
      $('.wd-usd').text(resp.data.withdrawal.usd);
    }
  }, 'json');

  // ====== CONFIG ======
  const RANGES = {
    '7d': { days: 7,  label: '1 Minggu' },
    '1m': { days: 30, label: '1 Bulan'  },
    '1y': { days: 365,label: '1 Tahun'  },
  };

  // generator list tanggal ISO (urut dari lama -> terbaru)
  const generateDates = (nDays) => {
    const now = new Date();
    const start = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // strip time
    const arr = [];
    for (let i = nDays - 1; i >= 0; i--) {
      const d = new Date(start);
      d.setDate(d.getDate() - i);
      arr.push(d.toISOString());
    }
    return arr;
  };

  // formatter angka 2 desimal + thousand separator
  const numFmt = (val) => {
    const v = Number(val) || 0;
    return v.toLocaleString(undefined, {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2
    });
  };

  let chart = null;

  const baseOptions = (categories, series) => ({
    series,
    chart: { height: 354, type: 'area', toolbar: { show: false } },
    dataLabels: { enabled: false },
    stroke: { width: 1, curve: 'smooth' },
    xaxis: {
      type: 'datetime',
      categories,
      labels: { format: 'dd/MM' }, // tampil tanggal singkat; bebas diganti 'dddd' kalau mau nama hari
      axisBorder: { show: false }, axisTicks: { show: false }
    },
    yaxis: {
      labels: { formatter: numFmt }
    },
    tooltip: {
      x: { format: 'dd MMM yyyy' },
      y: { formatter: numFmt }
    },
    grid: {
      borderColor: '#334652', strokeDashArray: 3,
      xaxis: { lines: { show: true } }, padding: { bottom: 15 }
    },
    responsive: [{ breakpoint: 479, options: { chart: { height: 250 } } }]
  });

  // normalisasi panjang data: kalau server kirim kurang dari nDays, pad 0 di depan
  const normalize = (arr, n) => {
    const a = Array.isArray(arr) ? arr.slice(-n) : [];
    if (a.length < n) {
      return Array(n - a.length).fill(0).concat(a);
    }
    return a;
  };

  // fetch + render/update chart
  const fetchAndRender = (rangeKey='7d') => {
    const { days } = RANGES[rangeKey] || RANGES['7d'];
    const categories = generateDates(days);
    const url = `/ajax/post/dashboard/history_dpwd?range=${encodeURIComponent(rangeKey)}`;

    $.get(url, (resp) => {
      if (!resp?.success || !resp?.data?.chart) {
        $('#saleAnalytics').html(`${resp?.message || "Gagal memuat data"}`);
        return;
      }

      const data = resp.data.chart;
      const series = [
        { name: 'Deposit (IDR)',    color: '#a0c0ff', data: normalize(data.DP_IDR, days) },
        { name: 'Deposit (USD)',    color: '#1a5ddb', data: normalize(data.DP_USD, days) },
        { name: 'Withdrawal (IDR)', color: '#ff8080', data: normalize(data.WD_IDR, days) },
        { name: 'Withdrawal (USD)', color: '#ff1414', data: normalize(data.WD_USD, days) },
      ];

      if (!chart) {
        chart = new ApexCharts(document.querySelector("#saleAnalytics"), baseOptions(categories, series));
        chart.render();
      } else {
        chart.updateOptions({ xaxis: { categories } }, false, true);
        chart.updateSeries(series, true);
      }
    }, 'json').fail(() => {
      $('#saleAnalytics').html("Gagal memuat history");
    });
  };

  // inisialisasi default 1 minggu
  if ($('#saleAnalytics').length) {
    fetchAndRender('7d');
  }

  // handler tombol filter
  $('#rangeFilters').on('click', 'button[data-range]', function(){
    const range = $(this).data('range');
    // styling active button (opsional)
    $('#rangeFilters button').removeClass('btn-primary').addClass('btn-outline-primary');
    $(this).removeClass('btn-outline-primary').addClass('btn-primary');
    fetchAndRender(range);
  });

});
})(jQuery);
</script>