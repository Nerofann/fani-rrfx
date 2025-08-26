<?php
    $SQL_DASHBOARD = mysqli_query($db, '
        SELECT
        IFNULL((
            SELECT
					SUM(1)
            FROM tb_member
            LIMIT 1
        ), 0) AS TTL_USR,
        IFNULL((
      		SELECT
      			SUM(1)
      		FROM tb_racc
      		WHERE tb_racc.ACC_DERE = 1
      		AND tb_racc.ACC_STS = -1
      		LIMIT 1
		  ), 0) AS TTL_RACC,
        IFNULL((
            SELECT
                SUM(1)
            FROM tb_racc
            JOIN tb_member
            ON(tb_member.MBR_ID = tb_racc.ACC_MBR)
            WHERE tb_racc.ACC_DERE = 1
            AND tb_racc.ACC_STS = 1    
            LIMIT 1
        ), 0) AS TTL_UNRACC,
        IFNULL((
            SELECT
                SUM(1)
            FROM tb_dpwd
            JOIN tb_member
            JOIN tb_racc
            ON(tb_member.MBR_ID = tb_dpwd.DPWD_MBR
            AND tb_member.MBR_ID = tb_racc.ACC_MBR
            AND tb_racc.ID_ACC = tb_dpwd.DPWD_RACC)
            WHERE tb_dpwd.DPWD_TYPE = 1
            AND tb_dpwd.DPWD_STS = 0
            LIMIT 1
        ), 0) AS PEND_DP,
        IFNULL((
            SELECT
                SUM(1)
            FROM tb_dpwd
            JOIN tb_member
            JOIN tb_racc
            ON(tb_member.MBR_ID = tb_dpwd.DPWD_MBR
            AND tb_member.MBR_ID = tb_racc.ACC_MBR
            AND tb_racc.ID_ACC = tb_dpwd.DPWD_RACC)
            WHERE tb_dpwd.DPWD_TYPE = 2
            AND tb_dpwd.DPWD_STS = 0
            LIMIT 1
        ), 0) AS PEND_WD,
        (
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "date", MBR_DATETIME,
                        "name", MBR_NAME,
                        "email", MBR_EMAIL
                    )
                ) 
            FROM (
                SELECT
                    tb_member.MBR_DATETIME,
                    tb_member.MBR_NAME,
                    tb_member.MBR_EMAIL
                FROM tb_member
                ORDER BY tb_member.MBR_DATETIME DESC
                LIMIT 10
            ) AS tb_jsnmbr
        ) AS JSNDT_MBR,
        (
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "date", DPWD_DATETIME,
                        "name", MBR_NAME,
                        "amnt", AMNT
                    )
                )
            FROM (
                SELECT
                    tb_dpwd.DPWD_DATETIME,
                    tb_member.MBR_NAME,
                    CAST(FORMAT(tb_dpwd.DPWD_AMOUNT, 0) AS CHAR) AS AMNT
                FROM tb_dpwd
                JOIN tb_member
                ON(tb_dpwd.DPWD_MBR = tb_member.MBR_ID)
                WHERE tb_dpwd.DPWD_TYPE = 1
                AND tb_dpwd.DPWD_STS = 0
                ORDER BY tb_dpwd.DPWD_DATETIME DESC
                LIMIT 10
            ) AS tb_jsndp
        ) AS JSNDT_DP,
        (
            SELECT
                JSON_ARRAYAGG(
                    JSON_OBJECT(
                        "date", DPWD_DATETIME,
                        "name", MBR_NAME,
                        "amnt", AMNT
                    )
                )
            FROM (
                SELECT
                    tb_dpwd.DPWD_DATETIME,
                    tb_member.MBR_NAME,
                    CAST(FORMAT(tb_dpwd.DPWD_AMOUNT, 0) AS CHAR) AS AMNT
                FROM tb_dpwd
                JOIN tb_member
                ON(tb_dpwd.DPWD_MBR = tb_member.MBR_ID)
                WHERE tb_dpwd.DPWD_TYPE = 2
                AND tb_dpwd.DPWD_STS = 0
                ORDER BY tb_dpwd.DPWD_DATETIME DESC
                LIMIT 10
            ) AS tb_jsnwd
        ) AS JSNDT_WD
    ');
    if($SQL_DASHBOARD && mysqli_num_rows($SQL_DASHBOARD) > 0){
        $RSLT_DASHBOARD = mysqli_fetch_assoc($SQL_DASHBOARD);
        $ttl_usr        = $RSLT_DASHBOARD["TTL_USR"];
        $ttl_racc       = $RSLT_DASHBOARD["TTL_RACC"];
        $ttl_unracc     = $RSLT_DASHBOARD["TTL_UNRACC"];
        $pend_jadtem    = 0;
        $pend_dp        = $RSLT_DASHBOARD["PEND_DP"];
        $pend_wd        = $RSLT_DASHBOARD["PEND_WD"];

        $JSNDT_MBR      = (!empty($RSLT_DASHBOARD["JSNDT_MBR"])) ? $RSLT_DASHBOARD["JSNDT_MBR"] : '[]';
        $JSNDT_DP       = (!empty($RSLT_DASHBOARD["JSNDT_DP"])) ? $RSLT_DASHBOARD["JSNDT_DP"] : '[]';
        $JSNDT_WD       = (!empty($RSLT_DASHBOARD["JSNDT_WD"])) ? $RSLT_DASHBOARD["JSNDT_WD"] : '[]';
    }else{
        $ttl_usr        = 0;
        $ttl_racc       = 0;
        $ttl_unracc     = 0;
        $pend_jadtem    = 0;
        $pend_dp        = 0;
        $pend_wd        = 0;

        $JSNDT_MBR      = '[]';
        $JSNDT_DP       = '[]';
        $JSNDT_WD       = '[]';
    }

    
    // Mendapatkan penggunaan CPU dalam persentase
    $cpu_usage_output = shell_exec("top -bn1 | grep 'Cpu(s)'");
    preg_match('/(\d+\.\d+)\s*id/', $cpu_usage_output, $matches);
    $cpu_usage = round(100 - floatval($matches[1]), 2);

    // Mendapatkan total dan penggunaan RAM dalam MB
    $free_output = shell_exec('free -m');
    preg_match_all('/\d+/', $free_output, $matches);
    $total_ram = $matches[0][0];
    $used_ram = $matches[0][2];
    $ram_usage = round(($used_ram / $total_ram) * 100, 2);

    // Mendapatkan total dan penggunaan storage dalam MB
    $df_output = shell_exec('df -BM --total | grep total');
    preg_match_all('/\d+/', $df_output, $matches);
    $total_storage = $matches[0][0];
    $used_storage = $matches[0][1];
    $storage_usage = round(($used_storage / $total_storage) * 100, 2);
?>
<div class="page-header">
    <div>
        <h2 class="main-content-title tx-24 mg-b-5">Home</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0);">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </div>
</div>

<div class="row row-sm">
	<div class="col-md-8 col-sm-12 col-lg-8 col-xl-8 col-xxl-8">
		<div class="card custom-card">
			<div class="card-body pt-2 pb-0">
                <label class="main-content-label my-auto pt-2">System Usage Graph</label>
                <hr>
                <div class="chart-container">
                    <canvas id="lineAreaChart" style="min-height:600px;"></canvas>
                </div>

                <script>
                    function fetchData(callback) {
                        fetch('system_usage_log.txt')
                            .then(response => response.text())
                            .then(text => {
                                const lines = text.trim().split('\n');
                                const labels = [];
                                const data1 = [];
                                const data2 = [];
                                const data3 = [];

                                lines.forEach(line => {
                                    const [time, value1, value2, value3] = line.split(',');
                                    labels.push(time);
                                    data1.push(parseFloat(value1));
                                    data2.push(parseFloat(value2));
                                    data3.push(parseFloat(value3));
                                });

                                callback({ labels, data1, data2, data3 });
                            });
                    }

                    fetchData(data => {
                        const ctx = document.getElementById('lineAreaChart').getContext('2d');
                        const chartData = {
                            labels: data.labels,
                            datasets: [
                                {
                                    label: 'CPU',
                                    data: data.data1,
                                    fill: true,
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                },
                                {
                                    label: 'RAM',
                                    data: data.data2,
                                    fill: true,
                                    borderColor: 'rgba(153, 102, 255, 1)',
                                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                },
                                {
                                    label: 'Storage',
                                    data: data.data3,
                                    fill: true,
                                    borderColor: 'rgba(255, 159, 64, 1)',
                                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                }
                            ]
                        };

                        const config = {
                            type: 'line',
                            data: chartData,
                            options: {
                                responsive: true,
                                scales: {
                                    x: {
                                        type: 'time',
                                        time: {
                                            unit: 'hour'
                                        }
                                    },
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        };

                        const lineAreaChart = new Chart(ctx, config);
                    });
                </script>
            </div>
        </div>

	</div>
	<div class="col-md-4 col-sm-12 col-lg-4 col-xl-4 col-xxl-4">
		<div class="card custom-card overflow-hidden">
			<div class="card-header border-bottom-0">
				<label class="main-content-label my-auto pt-2">Activity</label>
			</div>
			<ul class="crypto-transcation list-unstyled mg-b-0 mt-2">
				<li class="list-item mb-0 px-3 mt-0 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15">Total User</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($ttl_usr, 0); ?></h5>
					</div>
				</li>
				<li class="list-item mb-0 px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15">Active Real Account</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($ttl_racc, 0) ?></h5>
					</div>
				</li>
				<li class="list-item mb-0 px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15">Un-active Real Account</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($ttl_unracc, 0); ?></h5>
					</div>
				</li>
				<li class="list-item px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15">Pending Top-Up</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($pend_dp, 0); ?></h5>
					</div>
				</li>
				<li class="list-item px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15">Pending Withdrawal</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($pend_wd, 0); ?></h5>
					</div>
				</li>
				<li class="list-item px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15"><i>Current</i> CPU Usage</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($cpu_usage, 2); ?>%</h5>
					</div>
				</li>
				<li class="list-item px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15"><i>Current</i> RAM Usage</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($ram_usage, 2); ?>%</h5>
					</div>
				</li>
				<li class="list-item px-3 pb-3">
					<div class="media align-items-center">
						<div class="media-body ms-3">
							<p class="tx-medium mg-b-3 tx-15"><i>Current</i> Storage Usage</p>
						</div>
					</div>
					<div class="text-end ms-auto my-auto">
						<h5 class="font-weight-semibold tx-16 mb-0"><?php echo number_format($storage_usage, 2); ?>%</h5>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-header custom-card-header border-bottom-0">
                <h4 class="main-content-label tx-dark tx-medium mb-1">10 Last User Registration</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table-usr">
                        <thead>
                            <tr>
                                <th class="text-center">Date Time</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-header custom-card-header border-bottom-0">
                <h4 class="main-content-label tx-dark tx-medium mb-1">10 Last Pending Topup</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table-dp">
                        <thead>
                            <tr>
                                <th class="text-center">Date Time</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card custom-card">
            <div class="card-header custom-card-header border-bottom-0">
                <h4 class="main-content-label tx-dark tx-medium mb-1">10 Last Pending Widthrawal</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="table-wthdrwl">
                        <thead>
                            <tr>
                                <th class="text-center">Date Time</th>
                                <th class="text-center">Name</th>
                                <th class="text-center">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(() => {
        let tableUser = $('#table-usr').DataTable({
            data : <?= $JSNDT_MBR ?>,
            columns: [
                { data: 'date' },
                { data: 'name' },
                { data: 'email' }
            ],
            order: [[0, 'desc']],
            paging: false,
            info: false,
            searching: false,
            responsive: true
        });
        let tableDp = $('#table-dp').DataTable({
            data : <?= $JSNDT_DP ?>,
            columns: [
                { data: 'date' },
                { data: 'name' },
                { 
                    data: 'amnt',
                    className: 'text-end'
                }
            ],
            order: [[0, 'desc']],
            paging: false,
            info: false,
            searching: false,
            responsive: true
        });
        let tableWd = $('#table-wthdrwl').DataTable({
            data : <?= $JSNDT_WD ?>,
            columns: [
                { data: 'date' },
                { data: 'name' },
                { 
                    data: 'amnt',
                    className: 'text-end'
                }
            ],
            order: [[0, 'desc']],
            paging: false,
            info: false,
            searching: false,
            responsive: true
        });
    });
</script>