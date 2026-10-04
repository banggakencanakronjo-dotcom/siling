<?php
session_start();
include "koneksi.php";
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$filter_tahun = $_GET['tahun'] ?? '';
$filter_bulan = $_GET['bulan'] ?? '';
$filter_kecamatan = $_GET['kecamatan'] ?? '';
$filter_desa = $_GET['desa'] ?? '';

$where_clauses = [];
if ($filter_tahun != '') { $where_clauses[] = "YEAR(tanggal_input) = '$filter_tahun'"; }
if ($filter_bulan != '') { $where_clauses[] = "MONTH(tanggal_input) = '$filter_bulan'"; }
if ($filter_kecamatan != '') { $where_clauses[] = "kecamatan = '$filter_kecamatan'"; }
if ($filter_desa != '') { $where_clauses[] = "desa = '$filter_desa'"; }

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

$total_krs = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM keluarga_sasaran $where_sql"));
$kader_aktif = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='kader'"));
$dapat_intervensi = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM keluarga_sasaran $where_sql " . ($where_sql ? "AND" : "WHERE") . " intervensi != ''"));

$opt_tahun = mysqli_query($conn, "SELECT DISTINCT YEAR(tanggal_input) as tahun FROM keluarga_sasaran WHERE tanggal_input IS NOT NULL ORDER BY tahun DESC");
$opt_kecamatan = mysqli_query($conn, "SELECT DISTINCT kecamatan FROM master_wilayah ORDER BY kecamatan ASC");

// Data untuk Grafik Batang per Kecamatan
$q_chart = mysqli_query($conn, "SELECT kecamatan, COUNT(*) as total FROM keluarga_sasaran $where_sql GROUP BY kecamatan ORDER BY total DESC");
$chart_kecamatan = [];
$chart_total = [];
while($row_c = mysqli_fetch_assoc($q_chart)){
    if($row_c['kecamatan']) {
        $chart_kecamatan[] = $row_c['kecamatan'];
        $chart_total[] = $row_c['total'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin Monitoring - SILING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #f4f7f6; }
        .header-top { background: linear-gradient(90deg, #9c27b0, #673ab7); color: white; padding: 12px 25px; }
        .card-stat { border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); background: white; border: none; }
    </style>
    <script>
        function filterLoadDesa(kecamatan) {
            var desaSelect = document.getElementById("filter_desa");
            desaSelect.innerHTML = '<option value="">Memuat desa...</option>';
            if (kecamatan === "") {
                desaSelect.innerHTML = '<option value="">Semua Desa / Kelurahan</option>';
                return;
            }
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "get_desa.php?kecamatan=" + encodeURIComponent(kecamatan), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    desaSelect.innerHTML = '<option value="">Semua Desa / Kelurahan</option>' + xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <div class="header-top d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0"><b>Sistem Informasi Lingkup Keluarga Resiko Stunting</b></h5>
            <small>DPPKB Kabupaten Tangerang</small>
        </div>
        <div>
            <span class="badge bg-success p-2">ADMIN PANEL</span>
            <a href="logout.php" class="btn btn-danger btn-sm ms-2"><i class="fa fa-sign-out"></i> Logout</a>
            <a href="admin_users.php" class="btn btn-warning btn-sm ms-2 fw-bold text-dark"><i class="fa fa-users-gear"></i> Kelola Akun Kader</a>
        </div>
        
    </div>

    <div class="container-fluid px-4 mt-4">
        
        <!-- Filter Monitoring Pendampingan -->
        <div class="card p-3 mb-4 shadow-sm border-0 rounded-4">
            <h6 class="fw-bold text-secondary mb-3"><i class="fa fa-filter"></i> FILTER MONITORING PENDAMPINGAN</h6>
            <form method="GET" action="">
                <div class="row g-2">
                    <div class="col-md">
                        <select name="tahun" class="form-select form-select-sm">
                            <option value="">Semua Tahun</option>
                            <?php while($t = mysqli_fetch_assoc($opt_tahun)): if($t['tahun']): ?>
                                <option value="<?= $t['tahun']; ?>" <?= ($filter_tahun == $t['tahun']) ? 'selected' : ''; ?>><?= $t['tahun']; ?></option>
                            <?php endif; endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md">
                        <select name="bulan" class="form-select form-select-sm">
                            <option value="">Semua Bulan</option>
                            <?php 
                            $bulan_arr = [1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April', 5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus', 9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'];
                            foreach($bulan_arr as $num => $nama):
                            ?>
                                <option value="<?= $num; ?>" <?= ($filter_bulan == $num) ? 'selected' : ''; ?>><?= $nama; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md">
                        <select name="kecamatan" class="form-select form-select-sm" onchange="filterLoadDesa(this.value)">
                            <option value="">Semua Kecamatan</option>
                            <?php while($kec = mysqli_fetch_assoc($opt_kecamatan)): ?>
                                <option value="<?= $kec['kecamatan']; ?>" <?= ($filter_kecamatan == $kec['kecamatan']) ? 'selected' : ''; ?>><?= $kec['kecamatan']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md">
                        <select name="desa" id="filter_desa" class="form-select form-select-sm">
                            <option value="">Semua Desa / Kelurahan</option>
                            <?php 
                            if($filter_kecamatan != '') {
                                $q_ds = mysqli_query($conn, "SELECT desa FROM master_wilayah WHERE kecamatan = '$filter_kecamatan' ORDER BY desa ASC");
                                while($ds = mysqli_fetch_assoc($q_ds)){
                                    $selected = ($filter_desa == $ds['desa']) ? 'selected' : '';
                                    echo "<option value='".$ds['desa']."' $selected>".$ds['desa']."</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-primary btn-sm px-3"><i class="fa fa-search"></i> Filter</button>
                        <a href="admin_dashboard.php" class="btn btn-secondary btn-sm"><i class="fa fa-rotate-left"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistik Kartu -->
        <div class="row g-3 mb-4">
            <div class="col-md-3"><div class="card card-stat p-3"><span class="text-muted">Jumlah Sasaran KRS</span><h3 class="fw-bold text-primary mb-0"><?= $total_krs; ?></h3></div></div>
            <div class="col-md-3"><div class="card card-stat p-3"><span class="text-muted">Kader Aktif TPK</span><h3 class="fw-bold text-success mb-0"><?= $kader_aktif; ?></h3></div></div>
            <div class="col-md-3"><div class="card card-stat p-3"><span class="text-muted">Harus Intervensi</span><h3 class="fw-bold text-warning mb-0"><?= $total_krs; ?></h3></div></div>
            <div class="col-md-3"><div class="card card-stat p-3"><span class="text-muted">Telah Intervensi</span><h3 class="fw-bold text-success mb-0"><?= $dapat_intervensi; ?></h3></div></div>
        </div>

        <!-- Grafik Batang per Kecamatan -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card card-stat p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa fa-chart-bar text-primary"></i> Grafik Pencapaian Pendampingan TPK Per-Kecamatan</h5>
                    <hr>
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="barChartKecamatan"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Hasil Pendampingan TPK -->
        <div class="card card-stat p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-dark m-0"><i class="fa fa-table"></i> Daftar Rekapitulasi Hasil Pendampingan TPK</h5>
                <div>
                    <a href="export_excel.php?tahun=<?= $filter_tahun; ?>&bulan=<?= $filter_bulan; ?>&kecamatan=<?= $filter_kecamatan; ?>&desa=<?= $filter_desa; ?>" class="btn btn-success btn-sm me-1" target="_blank">
                        <i class="fa fa-file-excel"></i> Unduh Excel
                    </a>
                    <a href="export_pdf.php?tahun=<?= $filter_tahun; ?>&bulan=<?= $filter_bulan; ?>&kecamatan=<?= $filter_kecamatan; ?>&desa=<?= $filter_desa; ?>" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fa fa-file-pdf"></i> Cetak PDF
                    </a>
                </div>
            </div>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center align-middle" style="font-size: 11px;">
                    <thead class="table-success align-middle">
                        <tr>
                            <th rowspan="2">Tanggal</th>
                            <th rowspan="2">Kecamatan / Desa</th>
                            <th colspan="5">Jumlah sasaran</th>
                            <th colspan="5">Penapisan KRS</th>
                            <th colspan="7">Jenis Intervensi</th>
                            <th colspan="5">Sumber Intervensi</th>
                        </tr>
                        <tr>
                            <th>Catin</th><th>Bumil</th><th>Bupas</th><th>Baduta</th><th>Balita</th>
                            <th>ASFR (<21 Thn)</th><th>Anemia</th><th>Kek</th><th>Jamban Tidak layak</th><th>Air Minum Tidak Layak</th>
                            <th>KIE</th><th>Genting</th><th>Rujukan</th><th>Bimbingan Perkawinan</th><th>Bansos</th><th>EPPGBM</th><th>PMT</th>
                            <th>Dana Desa</th><th>APBD Kabupaten</th><th>APBN / Kementerian</th><th>Swadaya / Mandiri</th><th>CSR / Perusahaan</th>
                        </tr>
                    </thead>
                    
<?php
include "koneksi.php";

$query = "SELECT 
    tanggal_input,
    kecamatan,
    desa,
    SUM(CASE WHEN jenis_sasaran = 'catin' THEN 1 ELSE 0 END) AS catin,
    SUM(CASE WHEN jenis_sasaran = 'ibu_hamil' THEN 1 ELSE 0 END) AS bumil,
    SUM(CASE WHEN jenis_sasaran = 'ibu_menyusui' THEN 1 ELSE 0 END) AS bupas,
    SUM(CASE WHEN jenis_sasaran = 'baduta' THEN 1 ELSE 0 END) AS baduta,
    SUM(CASE WHEN jenis_sasaran = 'balita' THEN 1 ELSE 0 END) AS balita,
    
    -- Penapisan KRS (sesuaikan kolom jika ada)
    0 AS asfr,
    0 AS anemia,
    0 AS kek,
    0 AS jamban_tidak_layak,
    0 AS air_tidak_layak,
    
    -- Jenis Intervensi
    0 AS intervensi_kie,
    0 AS intervensi_genting,
    0 AS intervensi_rujukan,
    0 AS intervensi_bimwin,
    0 AS intervensi_bansos,
    0 AS intervensi_eppgbm,
    0 AS intervensi_pmt,
    
    -- Sumber Intervensi
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%Dana Desa%' OR sumber_intervensi_air LIKE '%Dana Desa%' THEN 1 ELSE 0 END) AS dana_desa,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%APBD Kabupaten%' OR sumber_intervensi_air LIKE '%APBD Kabupaten%' THEN 1 ELSE 0 END) AS apbd_kab,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%APBN%' OR sumber_intervensi_air LIKE '%APBN%' THEN 1 ELSE 0 END) AS apbn,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%Swadaya%' OR sumber_intervensi_air LIKE '%Swadaya%' THEN 1 ELSE 0 END) AS swadaya,
    SUM(CASE WHEN sumber_intervensi_jamban LIKE '%CSR%' OR sumber_intervensi_air LIKE '%CSR%' OR sumber_intervensi_jamban LIKE '%Perusahaan%' OR sumber_intervensi_air LIKE '%Perusahaan%' THEN 1 ELSE 0 END) AS csr
FROM keluarga_sasaran 
GROUP BY tanggal_input, kecamatan, desa 
ORDER BY tanggal_input DESC";

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}
?>
    <tbody>
    <?php 
    // Pastikan variabel hasil query dieksekusi dengan benar
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) { 
    ?>
    <tr>
        <td><?php echo isset($row['tanggal_input']) ? $row['tanggal_input'] : '-'; ?></td>
        <td><?php echo (isset($row['kecamatan']) ? $row['kecamatan'] : '') . ' / ' . (isset($row['desa']) ? $row['desa'] : ''); ?></td>
        
        <!-- Jumlah Sasaran -->
        <td><?php echo isset($row['catin']) ? $row['catin'] : '-'; ?></td>
        <td><?php echo isset($row['bumil']) ? $row['bumil'] : '-'; ?></td>
        <td><?php echo isset($row['bupas']) ? $row['bupas'] : '-'; ?></td>
        <td><?php echo isset($row['baduta']) ? $row['baduta'] : '-'; ?></td>
        <td><?php echo isset($row['balita']) ? $row['balita'] : '-'; ?></td>
        
        <!-- Penapisan KRS -->
        <td><?php echo isset($row['asfr']) ? $row['asfr'] : '-'; ?></td>
        <td><?php echo isset($row['anemia']) ? $row['anemia'] : '-'; ?></td>
        <td><?php echo isset($row['kek']) ? $row['kek'] : '-'; ?></td>
        <td><?php echo isset($row['jamban_tidak_layak']) ? $row['jamban_tidak_layak'] : '-'; ?></td>
        <td><?php echo isset($row['air_tidak_layak']) ? $row['air_tidak_layak'] : '-'; ?></td>
        
        <!-- Jenis Intervensi -->
        <td><?php echo isset($row['intervensi_kie']) ? $row['intervensi_kie'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_genting']) ? $row['intervensi_genting'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_rujukan']) ? $row['intervensi_rujukan'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_bimwin']) ? $row['intervensi_bimwin'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_bansos']) ? $row['intervensi_bansos'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_eppgbm']) ? $row['intervensi_eppgbm'] : '-'; ?></td>
        <td><?php echo isset($row['intervensi_pmt']) ? $row['intervensi_pmt'] : '-'; ?></td>
        
        <!-- Sumber Intervensi -->
        <td><?php echo isset($row['dana_desa']) ? $row['dana_desa'] : '-'; ?></td>
        <td><?php echo isset($row['apbd_kab']) ? $row['apbd_kab'] : '-'; ?></td>
        <td><?php echo isset($row['apbn']) ? $row['apbn'] : '-'; ?></td>
        <td><?php echo isset($row['swadaya']) ? $row['swadaya'] : '-'; ?></td>
        <td><?php echo isset($row['csr']) ? $row['csr'] : '-'; ?></td>
    </tr>
    <?php 
        } 
    } else {
        echo "<tr><td colspan='22' style='text-align:center;'>Belum ada data rekapitulasi.</td></tr>";
    }
    ?>
</tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Script Grafik Batang -->
    <script>
        const ctx = document.getElementById('barChartKecamatan').getContext('2d');
        const barChartKecamatan = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_kecamatan); ?>,
                datasets: [{
                    label: 'Jumlah Keluarga Didampingi',
                    data: <?= json_encode($chart_total); ?>,
                    backgroundColor: 'rgba(103, 59, 183, 0.7)',
                    borderColor: 'rgba(103, 59, 183, 1)',
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    </script>
</body>
</html>