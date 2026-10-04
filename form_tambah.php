<?php
session_start();
include "koneksi.php";

if (isset($_POST['submit'])) {
    $kecamatan = $_POST['kecamatan'];
    $desa = $_POST['desa'];
    $jenis_sasaran = $_POST['jenis_sasaran'];
    $no_tim_tpk = $_POST['no_tim_tpk'];
    $nama_kader = $_POST['nama_kader'];
    $nama_kk = $_POST['nama_kk'];
    $nama_sasaran = $_POST['nama_sasaran'];
    $nik = $_POST['nik'];
    $alamat = $_POST['alamat'];
    $no_hp = $_POST['no_hp'];
    $kadar_hb = $_POST['kadar_hb'];
    $tinggi_badan = $_POST['tinggi_badan'];
    $berat_badan = $_POST['berat_badan'];
    $lila = $_POST['lila'];
    $status_anemia = $_POST['status_anemia'];
    $tfu = $_POST['tfu'];
    $menerima_mbg = $_POST['menerima_mbg'];
    $pus_risiko_4t = $_POST['pus_risiko_4t'];
    $jenis_alat_kontrasepsi = $_POST['jenis_alat_kontrasepsi'];
    
    // Checkbox array ditampung dengan implode
    $intervensi = isset($_POST['intervensi']) ? implode(", ", $_POST['intervensi']) : '';
    $sanitasi_jamban = $_POST['sanitasi_jamban'];
    $sumber_intervensi_jamban = $_POST['sumber_intervensi_jamban'];
    $sumber_air = isset($_POST['sumber_air']) ? implode(", ", $_POST['sumber_air']) : '';
    $sumber_intervensi_air = $_POST['sumber_intervensi_air'];
    
    $lat = $_POST['latitude'];
    $long = $_POST['longitude'];
    $tanggal_input = date('Y-m-d');

    // Upload Foto
    $foto = $_FILES['foto_kegiatan']['name'];
    $tmp = $_FILES['foto_kegiatan']['tmp_name'];
    $path = "uploads/" . basename($foto);
    if(!is_dir('uploads')) { mkdir('uploads', 0777, true); }
    move_uploaded_file($tmp, $path);

    $query = "INSERT INTO keluarga_sasaran (kecamatan, desa, jenis_sasaran, no_tim_tpk, nama_kader, nama_kk, nama_sasaran, nik, alamat, no_hp, kadar_hb, tinggi_badan, berat_badan, lila, status_anemia, tfu, menerima_mbg, pus_risiko_4t, jenis_alat_kontrasepsi, intervensi, sanitasi_jamban, sumber_intervensi_jamban, sumber_air, sumber_intervensi_air, foto_kegiatan, latitude, longitude, tanggal_input) 
              VALUES ('$kecamatan', '$desa', '$jenis_sasaran', '$no_tim_tpk', '$nama_kader', '$nama_kk', '$nama_sasaran', '$nik', '$alamat', '$no_hp', '$kadar_hb', '$tinggi_badan', '$berat_badan', '$lila', '$status_anemia', '$tfu', '$menerima_mbg', '$pus_risiko_4t', '$jenis_alat_kontrasepsi', '$intervensi', '$sanitasi_jamban', '$sumber_intervensi_jamban', '$sumber_air', '$sumber_intervensi_air', '$foto', '$lat', '$long', '$tanggal_input')";
    
    if(mysqli_query($conn, $query)){
        echo "<script>alert('Data berhasil dikirim ke Admin & Database!'); window.location='kader_dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan data: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Input Pendampingan TPK - SILING</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('lat').value = position.coords.latitude;
                    document.getElementById('long').value = position.coords.longitude;
                    document.getElementById('gps-status').innerHTML = "Lokasi GPS & Waktu: Terdeteksi ✓ (" + position.coords.latitude.toFixed(4) + ", " + position.coords.longitude.toFixed(4) + ")";
                    document.getElementById('gps-status').classList.remove('text-danger');
                    document.getElementById('gps-status').classList.add('text-success');
                });
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        }
        window.onload = getLocation;
    </script>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="card p-4 shadow-sm border-0 rounded-4">
            <h4 class="text-dark fw-bold">📝 Formulir Input Pendampingan TPK</h4>
            <p class="text-muted" style="font-size: 13px;">Isi data lapangan dengan benar dan upload foto kegiatan pendampingan</p>
            <hr>
            <form method="POST" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Kecamatan</label>
                        <select name="kecamatan" class="form-select" required>
                            <option value="">-- Pilih Kecamatan --</option>
                            <option value="Kronjo">Kronjo</option>
                            <option value="Mauk">Mauk</option>
                            <option value="Balaraja">Balaraja</option>
                            <option value="Rajeg">Rajeg</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Desa / Kelurahan</label>
                        <input type="text" name="desa" class="form-control" placeholder="Nama Desa" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Jenis Sasaran</label>
                        <select name="jenis_sasaran" class="form-select" required>
                            <option value="">-- Pilih Jenis Sasaran --</option>
                            <option value="catin">Calon Pengantin (Catin)</option>
                            <option value="ibu_hamil">Ibu Hamil</option>
                            <option value="ibu_menyusui">Ibu Menyusui</option>
                            <option value="baduta">Baduta</option>
                            <option value="balita">Balita</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">No Tim TPK</label>
                        <input type="text" name="no_tim_tpk" class="form-control" placeholder="Contoh: TPK-01" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Nama Kader TPK</label>
                        <input type="text" name="nama_kader" class="form-control" placeholder="Nama Lengkap Kader" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Nama Kepala Keluarga (KK)</label>
                        <input type="text" name="nama_kk" class="form-control" placeholder="Nama KK" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Nama Sasaran</label>
                        <input type="text" name="nama_sasaran" class="form-control" placeholder="Nama Sasaran" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">No NIK Sasaran</label>
                        <input type="text" name="nik" class="form-control" maxlength="16" placeholder="16 digit NIK" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Alamat Sasaran</label>
                        <input type="text" name="alamat" class="form-control" placeholder="Kampung/RT/RW" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">No. Handphone (WA)</label>
                        <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Kadar HB (g/dL)</label>
                        <input type="number" step="0.1" name="kadar_hb" class="form-control" placeholder="Contoh: 11.5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Tinggi Badan (TB cm)</label>
                        <input type="number" step="0.1" name="tinggi_badan" class="form-control" placeholder="Contoh: 155">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Berat Badan (BB kg)</label>
                        <input type="number" step="0.1" name="berat_badan" class="form-control" placeholder="Contoh: 50">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">LiLA (Lingkar Lengan Atas cm)</label>
                        <input type="number" step="0.1" name="lila" class="form-control" placeholder="Contoh: 23.5">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Status Anemia</label>
                        <select name="status_anemia" class="form-select">
                            <option value="Tidak Anemia">Tidak Anemia</option>
                            <option value="Anemia">Anemia</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">TFU (Tinggi Fundus Uteri cm)</label>
                        <input type="number" step="0.1" name="tfu" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Menerima MBG 3B</label>
                        <select name="menerima_mbg" class="form-select">
                            <option value="Tidak">Tidak</option>
                            <option value="Ya">Ya</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">PUS Risiko 4T</label>
                        <select name="pus_risiko_4t" class="form-select">
                            <option value="Bukan PUS 4T">Bukan PUS 4T</option>
                            <option value="Terlalu Muda (< 20 Thn)">Terlalu Muda (< 20 Thn)</option>
                            <option value="Terlalu Tua (> 35 Thn)">Terlalu Tua (> 35 Thn)</option>
                            <option value="Terlalu Dekat Jarak Kehamilan">Terlalu Dekat Jarak Kehamilan</option>
                            <option value="Terlalu Banyak Anak (> 4)">Terlalu Banyak Anak (> 4)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" style="font-size: 13px;">Jenis Alat Kontrasepsi</label>
                        <select name="jenis_alat_kontrasepsi" class="form-select">
                            <option value="Tidak BerKB">Tidak BerKB</option>
                            <option value="IUD">IUD</option>
                            <option value="Implan">Implan</option>
                            <option value="Suntik">Suntik</option>
                            <option value="Pil">Pil</option>
                            <option value="Kondom">Kondom</option>
                            <option value="MOW/MOP">MOW / MOP</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold" style="font-size: 13px;">Intervensi Yang Diterima (Bisa Pilih > 1)</label>
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="Tidak menerima"><label class="form-check-label">Tidak menerima</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="Bimbingan Perkawinan (Catin)"><label class="form-check-label">Bimbingan Perkawinan (Catin)</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="Rujukan"><label class="form-check-label">Rujukan</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="Bansos"><label class="form-check-label">Bansos</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="KIE"><label class="form-check-label">KIE</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="EPPGBM"><label class="form-check-label">EPPGBM</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="PMT"><label class="form-check-label">PMT</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="intervensi[]" value="Genting"><label class="form-check-label">Genting</label></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">Sanitasi Jamban</label>
                        <select name="sanitasi_jamban" class="form-select">
                            <option value="Jamban Layak (Milik Sendiri)">Jamban Layak (Milik Sendiri)</option>
                            <option value="Jamban Layak (Bersama)">Jamban Layak (Bersama)</option>
                            <option value="Jamban Tidak Layak">Jamban Tidak Layak</option>
                            <option value="Tidak Ada Jamban">Tidak Ada Jamban</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">Sumber Intervensi Jamban</label>
                        <select name="sumber_intervensi_jamban" class="form-select">
                            <option value="">-- Pilih Sumber Intervensi Jamban --</option>
                            <option value="Dana Desa">Dana Desa</option>
                            <option value="APBD Kabupaten">APBD Kabupaten</option>
                            <option value="APBN / Kementerian">APBN / Kementerian</option>
                            <option value="Swadaya / Mandiri">Swadaya / Mandiri</option>
                            <option value="CSR / Perusahaan">CSR / Perusahaan</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">Sumber Air Minum Layak (Bisa Pilih > 1)</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="PDAM"><label class="form-check-label">PDAM</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="Air Kemasan"><label class="form-check-label">Air Kemasan/Ulang</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="Ledeng"><label class="form-check-label">Ledeng</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="Sumur Bor"><label class="form-check-label">Sumur Bor</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="Sumur Terlindung"><label class="form-check-label">Sumur Terlindung</label></div>
                            <div class="form-check"><input class="form-check-input" type="checkbox" name="sumber_air[]" value="Tidak Layak"><label class="form-check-label text-danger">Tidak Layak</label></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold" style="font-size: 13px;">Sumber Intervensi Air Minum</label>
                        <select name="sumber_intervensi_air" class="form-select">
                            <option value="">-- Pilih Sumber Intervensi Air Minum --</option>
                            <option value="Dana Desa">Dana Desa</option>
                            <option value="APBD Kabupaten">APBD Kabupaten</option>
                            <option value="APBN / Kementerian">APBN / Kementerian</option>
                            <option value="Swadaya / Mandiri">Swadaya / Mandiri</option>
                            <option value="CSR / Perusahaan">CSR / Perusahaan</option>
                        </select>
                    </div>

                    <!-- Kotak Unggah Foto & GPS -->
                    <div class="col-12 mt-4">
                        <div class="p-3 border rounded bg-warning bg-opacity-10">
                            <label class="fw-bold mb-2">📸 Unggah Foto Kegiatan Pendampingan (Wajib Ada Foto)</label>
                            <input type="file" name="foto_kegiatan" class="form-control mb-2" accept="image/*" required>
                            <small id="gps-status" class="text-danger fw-bold">Lokasi GPS & Waktu: Belum terdeteksi (Akan otomatis saat halaman dimuat)</small>
                            <input type="hidden" name="latitude" id="lat">
                            <input type="hidden" name="longitude" id="long">
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" name="submit" class="btn btn-success w-100 py-2 fw-bold">🚀 Kirim Data ke Admin & Spreadsheet</button>
                        <a href="kader_dashboard.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>