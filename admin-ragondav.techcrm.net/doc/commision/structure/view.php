
<div class="page-header">
	<div>
		<h2 class="main-content-title tx-24 mg-b-5">Stucture</h2>
		<ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= pathbreadcrumb(0) ?>/dashboard">Home</a></li>
			<li class="breadcrumb-item active">Commision</li>
			<li class="breadcrumb-item active" aria-current="page"><a href="javascript:void(0);">Stucture</a></li>
		</ol>
	</div>
</div>
<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Form Add</h5>
            </div>
            <form method="post" action="" id="addStucture">
                <div class="card-body">
                    <div class="form-group">
                        <label for="struc_upline" class="form-control-label">Upline</label>
                        <select name="struc_upline" id="struc_upline" class="form-control" required>
                            <?php 
                                $query_upline = mysqli_query($db, "SELECT ID_SLSSTRC, SLSSTRC_NAME FROM tb_salesstuc ORDER BY ID_SLSSTRC");
                                if($query_upline){
                                    while($row_upline = mysqli_fetch_assoc($query_upline)){
                            ?>
                            <option value="<?= $row_upline['ID_SLSSTRC'] ?>"><?= $row_upline['SLSSTRC_NAME'] ?></option>
                            <?php };}; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="struc_name" class="form-control-label">Nama</label>
                        <input type="text" name="struc_name" id="struc_name" class="form-control" required>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-8 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title text-priamry">Structure</h5>
            </div>
            <div class="card-body">
                <?php
                    
                    $res = mysqli_query($db, "SELECT ID_SLSSTRC, SLSSTRC_UP, SLSSTRC_NAME
                            FROM tb_salesstuc
                            ORDER BY (SLSSTRC_UP IS NULL) DESC, SLSSTRC_UP, SLSSTRC_NAME");
                    if (!$res) {
                        echo "Query error: " . htmlspecialchars($mysqli->error);
                        exit;
                    }

                    $nodes = [];
                    $children = []; // parentId => [childId,...]
                    while ($row = $res->fetch_assoc()) {
                        $id     = (int)$row['ID_SLSSTRC'];
                        $parent = ($row['SLSSTRC_UP'] === null || (int)$row['SLSSTRC_UP'] === 0) ? null : (int)$row['SLSSTRC_UP'];
                        $name   = $row['SLSSTRC_NAME'] ?? ('Node ' . $id);

                        $nodes[$id] = ['id' => $id, 'parent' => $parent, 'name' => $name];
                        $children[$parent][] = $id;
                    }

                    function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }

                    /**
                     * Render tree rekursif.
                     * $rootOpen: level teratas dibuka default.
                     */
                    function renderTree($parentId, $children, $nodes, $rootOpen = true, $isRoot = true) {
                        if (empty($children[$parentId])) return '';
                        $html = "<ul>\n";
                        foreach ($children[$parentId] as $id) {
                            $name    = e($nodes[$id]['name']);
                            $hasKids = !empty($children[$id]);
                            if ($hasKids) {
                                $openAttr = ($isRoot && $rootOpen) ? ' open' : '';
                                $html .= "<li><details class=\"sls-tv__folder\"{$openAttr}><summary>{$name}</summary>";
                                $html .= renderTree($id, $children, $nodes, false, false);
                                $html .= "</details></li>\n";
                            } else {
                                $html .= "<li class=\"sls-tv__file\">{$name}</li>\n";
                            }
                        }
                        $html .= "</ul>\n";
                        return $html;
                    }
                ?>
                <style>
                    /* ==== Semua styling di-scope ke .sls-tv supaya tidak bentrok ==== */
                    .sls-tv { --sls-indent: 1rem; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
                    .sls-tv__title { margin: 0 0 8px; font-size: 18px; font-weight: 600; }

                    .sls-tv__actions { margin: 12px 0 20px; }
                    .sls-tv__actions button { margin-right: 8px; }

                    .sls-tv__tree { max-width: 640px; }
                    .sls-tv__tree ul { margin: .25rem 0 .5rem var(--sls-indent); padding: 0; list-style: none; }

                    .sls-tv__tree summary { cursor: pointer; user-select: none; padding: 2px 0; }
                    .sls-tv__tree summary:focus { outline: 2px solid #7aa2ff; outline-offset: 2px; }

                    .sls-tv__folder > summary::before { content: "📁 "; }
                    .sls-tv__folder[open] > summary::before { content: "📂 "; }
                    .sls-tv__file::before { content: "📄 "; margin-right: .25rem; }

                    .sls-tv__tree li { position: relative; padding-left: .5rem; }
                    .sls-tv__tree li::before {
                        content: "";
                        position: absolute;
                        left: -0.65rem;
                        top: 0.9rem;
                        width: .5rem;
                        height: 0;
                        border-top: 1px solid #e3e3e3;
                    }
                    .sls-tv__tree ul { border-left: 1px solid #e3e3e3; }
                </style>
                <div class="sls-tv">
                    <div class="">
                        <div class="sls-tv__actions">
                            <button class="btn btn-primary" id="sls-tv-expand">Expand All</button>
                            <button class="btn btn-success" id="sls-tv-collapse">Collapse All</button>
                        </div>
                        <div class="sls-tv__tree">
                            <?= renderTree(null, $children, $nodes) ?: '<em>Data kosong.</em>'; ?>
                        </div>
                    </div>
                </div>

                <script>
                    // Tombol expand/collapse all (di-scope ke .sls-tv supaya aman)
                    document.getElementById('sls-tv-expand')?.addEventListener('click', () => {
                        document.querySelectorAll('.sls-tv .sls-tv__tree details').forEach(d => d.open = true);
                    });
                    document.getElementById('sls-tv-collapse')?.addEventListener('click', () => {
                        document.querySelectorAll('.sls-tv .sls-tv__tree details').forEach(d => d.open = false);
                    });
                </script>
                <script>
                    
                    $('#addStucture').on('submit', function(ev){
                        ev.preventDefault();
                        $(this).find(':submit').addClass('loading');
                        let data = new FormData(this);
                        $.ajax({
                            url         : '/ajax/post/commision/structure/create',
                            type        : 'POST',
                            dataType    : 'JSON',
                            enctype     : 'multipart/form-data',
                            data        : data,
                            contentType : false,
                            chache      : false,
                            processData : false
                        }).done((resp) => {
                            Swal.fire(resp.alert).then(() => {
                                if(resp.success) {
                                    if(resp?.data?.reloc?.length){
                                        location.href = resp?.data?.reloc;
                                    }else{ location.reload(); }
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>
    </div>
</div>