<?php
include "koneksi.php";

// Header untuk memberitahu browser bahwa file ini adalah Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Rekapitulasi_Pendampingan_Stunting.xls");

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

$data = mysqli_query($conn, "SELECT * FROM keluarga_sasaran $where_sql ORDER BY id DESC");
?>
<h3 align="center">Daftar Rekapitulasi Hasil Pendampingan TPK - SILING</h3>
<table border="1">
    <thead>
        <tr style="background-color: #d4edda;">
            <th rowspan="2">Tanggal</th>
            <th rowspan="2">Kecamatan / Desa</th>
            <th colspan="5">Jumlah sasaran</th>
            <th colspan="5">Penapisan KRS</th>
            <th colspan="7">Jenis Intervensi</th>
            <th colspan="5">Sumber Intervensi</th>
        </tr>
        <tr style="background-color: #e2e3e5;">
            <th>Catin</th><th>Bumil</th><th>Bupas</th><th>Baduta</th><th>Balita</th>
            <th>ASFR (<21 Thn)</th><th>Anemia</th><th>Kek</th><th>Jamban Tidak layak</th><th>Air Minum Tidak Layak</th>
            <th>KIE</th><th>Genting</th><th>Rujukan</th><th>Bimbingan Perkawinan</th><th>Bansos</th><th>EPPGBM</th><th>PMT</th>
            <th>Dana Desa</th><th>APBD Kabupaten</th><th>APBN / Kementerian</th><th>Swadaya / Mandiri</th><th>CSR / Perusahaan</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while($row = mysqli_fetch_assoc($data)): 
            $is_catin = ($row['jenis_sasaran'] == 'catin') ? '1' : '-';
            $is_bumil = ($row['jenis_sasaran'] == 'ibu_hamil') ? '1' : '-';
            $is_bupas = ($row['jenis_sasaran'] == 'ibu_menyusui') ? '1' : '-';
            $is_baduta = ($row['jenis_sasaran'] == 'baduta') ? '1' : '-';
            $is_balita = ($row['jenis_sasaran'] == 'balita') ? '1' : '-';

            $penapisan_asfr = ($row['jenis_sasaran'] == 'catin' && ($row['usia_catin_wanita'] < 21 || $row['usia_catin_pria'] < 25)) ? '✓' : '-';
            $penapisan_anemia = ($row['status_anemia'] == 'Anemia') ? '✓' : '-';
            $penapisan_kek = ($row['lila'] > 0 && $row['lila'] < 23.5) ? '✓' : '-';
            $penapisan_jamban = (strpos($row['sanitasi_jamban'], 'Tidak Layak') !== false || strpos($row['sanitasi_jamban'], 'Tidak Ada') !== false) ? '✓' : '-';
            $penapisan_air = (strpos($row['sumber_air'], 'Tidak Layak') !== false) ? '✓' : '-';

            $inv = $row['intervensi'];
            $src_jamban = $row['sumber_intervensi_jamban'];
            $src_air = $row['sumber_intervensi_air'];
        ?>
        <tr>
            <td><?= $row['tanggal_input']; ?></td>
            <td><?= $row['kecamatan'] . ' / ' . $row['desa']; ?></td>
            <td><?= $is_catin; ?></td><td><?= $is_bumil; ?></td><td><?= $is_bupas; ?></td><td><?= $is_baduta; ?></td><td><?= $is_balita; ?></td>
            <td><?= $penapisan_asfr; ?></td><td><?= $penapisan_anemia; ?></td><td><?= $penapisan_kek; ?></td><td><?= $penapisan_jamban; ?></td><td><?= $penapisan_air; ?></td>
            <td><?= (strpos($inv, 'KIE') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'Genting') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'Rujukan') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'Bimbingan Perkawinan') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'Bansos') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'EPPGBM') !== false) ? '✓' : '-'; ?></td>
            <td><?= (strpos($inv, 'PMT') !== false) ? '✓' : '-'; ?></td>
            <td><?= ($src_jamban == 'Dana Desa' || $src_air == 'Dana Desa') ? '✓' : '-'; ?></td>
            <td><?= ($src_jamban == 'APBD Kabupaten' || $src_air == 'APBD Kabupaten') ? '✓' : '-'; ?></td>
            <td><?= ($src_jamban == 'APBN / Kementerian' || $src_air == 'APBN / Kementerian') ? '✓' : '-'; ?></td>
            <td><?= ($src_jamban == 'Swadaya / Mandiri' || $src_air == 'Swadaya / Mandiri') ? '✓' : '-'; ?></td>
            <td><?= ($src_jamban == 'CSR / Perusahaan' || $src_air == 'CSR / Perusahaan') ? '✓' : '-'; ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>