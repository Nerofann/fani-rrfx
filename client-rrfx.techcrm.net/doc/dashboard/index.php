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
                <!-- <div class="btn-box">
                    <button class="btn btn-sm btn-outline-primary">Week</button>
                    <button class="btn btn-sm btn-outline-primary">Month</button>
                    <button class="btn btn-sm btn-outline-primary">Year</button>
                </div> -->
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
    (function($) {
    'use strict';
    $(document).ready(function() {
        $.get("/ajax/post/dashboard/summary", (resp) => {
            if(resp.success) {
                $('.account').text(resp.data.account);
                $('.dp-idr').text(resp.data.deposit.idr);
                $('.dp-usd').text(resp.data.deposit.usd);
                $('.wd-idr').text(resp.data.withdrawal.idr);
                $('.wd-usd').text(resp.data.withdrawal.usd);
            }
        }, 'json')

        /** Chart */
        if($('#saleAnalytics').length) {
            $.get("/ajax/post/dashboard/history_dpwd", async (resp) => {
                if(!resp.success) {
                    $('#saleAnalytics').html(`${resp?.message || "Gagal memuat history"}`)
                    return false;
                }

                if(!resp.data.chart) {
                    $('#saleAnalytics').html(`${resp?.message || "Gagal memuat data"}`)
                    return false
                }

                let listDate = await function() {
                    let list = [];
                    for(let i = 0; i < 7; i++) {
                        list.push( new Date((Date.now() - (i * 24 * 60 * 60 * 1000))).toISOString() )
                    }
                    return list
                }

                let defaultValue = [0, 0, 0, 0, 0, 0, 0];
                let data = resp.data.chart;
                var saleAnalyticsoptions = {
                    series: [
                        {   
                            name: 'Deposit (IDR)',
                            color: '#a0c0ff',
                            data: data.DP_IDR || defaultValue
                        }, 
                        {
                            name: 'Deposit (USD)',
                            color: '#1a5ddb',
                            data: data.DP_USD || defaultValue
                        },
                        {
                            name: 'Withdrawal (IDR)',
                            color: '#ff8080',
                            data: data.WD_IDR || defaultValue
                        },
                        {
                            name: 'Withdrawal (USD)',
                            color: '#ff1414',
                            data: data.WD_USD || defaultValue
                        },
                    ],
                    chart: {
                        height: 354,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 1,
                        curve: 'smooth'
                    },
                    xaxis: {
                        fill: '#FFFFFF',
                        type: 'datetime',
                        categories: await listDate(),
                        labels: {
                            format: 'dddd',
                        },
                        axisBorder: {
                            show: false,
                        },
                        axisTicks: {
                            show: false,
                        },
                    },
                    grid: {
                        borderColor: '#334652',
                        strokeDashArray: 3,
                        xaxis: {
                            lines: {
                                show: true,
                            }
                        },
                        padding: {
                            bottom: 15
                        }
                    },
                    responsive: [{
                        breakpoint: 479,
                        options: {
                            chart: {
                                height: 250,
                            },
                        },
                    }]
                };

                var saleAnalytics = new ApexCharts(document.querySelector("#saleAnalytics"), saleAnalyticsoptions);
                saleAnalytics.render();

            }, 'json')
        }
    });
})(jQuery);
</script>