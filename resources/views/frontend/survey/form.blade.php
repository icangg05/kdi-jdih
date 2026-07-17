<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survei Kepuasan Pengguna - JDIH Kendari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .survey-container {
            max-width: 800px;
            margin: 50px auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .survey-header {
            text-align: center;
            margin-bottom: 40px;
            border-bottom: 3px solid #6f42c1;
            padding-bottom: 20px;
        }
        .survey-header h1 {
            color: #6f42c1;
            font-weight: 700;
        }
        .survey-header p {
            color: #6c757d;
            font-size: 1.1rem;
        }
        .rating-stars {
            font-size: 28px;
            color: #ffc107;
            cursor: pointer;
            margin: 10px 0;
        }
        .rating-stars i {
            margin: 0 2px;
            transition: all 0.2s;
        }
        .rating-stars i:hover {
            transform: scale(1.2);
        }
        .btn-submit {
            background: linear-gradient(135deg, #6f42c1, #9c27b0);
            color: white;
            padding: 12px 40px;
            font-size: 18px;
            font-weight: 600;
            border: none;
            border-radius: 10px;
            transition: all 0.3s;
            width: 100%;
        }
        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(111, 66, 193, 0.3);
        }
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px 15px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.25rem rgba(111, 66, 193, 0.25);
        }
        .required {
            color: #dc3545;
        }
        .section-title {
            color: #6f42c1;
            font-weight: 600;
            margin: 30px 0 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f1f1;
        }
        .back-home {
            display: inline-block;
            margin-top: 20px;
            color: #6f42c1;
            text-decoration: none;
            font-weight: 500;
        }
        .back-home:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
   
    
    <div class="survey-container">
        <div class="text-center mb-0">
        <img src="{{ asset('assets/img/logo-new-jdih.png') }}" 
             alt="Logo JDIH Kota Kendari" 
             class="img-fluid" 
             style="max-height: 100px;">
    </div>
        <div class="survey-header">
            <h1><i class="fas fa-clipboard-check me-2"></i>Survei Kepuasan Pengguna</h1>
            <p>Bantu kami meningkatkan layanan JDIH Kota Kendari dengan mengisi survei ini</p>
            <small class="text-muted">Waktu pengisian: ±5 menit</small>
        </div>

        <form action="{{ route('survey.store') }}" method="POST">
            @csrf
            
            <!-- Informasi Pengguna -->
            <div class="mb-4">
                <h3 class="section-title"><i class="fas fa-user me-2"></i>Informasi Pengguna</h3>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="instansi" class="form-label">Instansi/Perusahaan</label>
                        <input type="text" class="form-control" id="instansi" name="instansi">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="jenis_pengguna" class="form-label">Jenis Pengguna <span class="required">*</span></label>
                        <select class="form-select" id="jenis_pengguna" name="jenis_pengguna" required>
                            <option value="">Pilih Jenis Pengguna</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Akademisi">Akademisi</option>
                            <option value="Praktisi Hukum">Praktisi Hukum</option>
                            <option value="Masyarakat Umum">Masyarakat Umum</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Penilaian -->
            <div class="mb-4">
                <h3 class="section-title"><i class="fas fa-star me-2"></i>Penilaian Layanan</h3>
                <p class="text-muted">Berikan penilaian pada skala 1-5 (1 = Sangat Tidak Puas, 5 = Sangat Puas)</p>
                
                @foreach([
                    ['id' => 'kemudahan_akses', 'label' => '1. Kemudahan Akses', 'desc' => 'Kemudahan dalam mengakses informasi dan dokumen hukum'],
                    ['id' => 'kelengkapan_informasi', 'label' => '2. Kelengkapan Informasi', 'desc' => 'Kelengkapan dokumen dan informasi yang disediakan'],
                    ['id' => 'kecepatan_loading', 'label' => '3. Kecepatan Loading', 'desc' => 'Kecepatan akses dan loading halaman website'],
                    ['id' => 'tampilan_antarmuka', 'label' => '4. Tampilan Antarmuka', 'desc' => 'Tampilan dan user interface yang user-friendly'],
                    ['id' => 'relevansi_pencarian', 'label' => '5. Relevansi Pencarian', 'desc' => 'Relevansi hasil pencarian dengan kata kunci yang dicari']
                ] as $item)
                <div class="mb-4">
                    <label class="form-label">{{ $item['label'] }} <span class="required">*</span></label>
                    <p class="text-muted small mb-2">{{ $item['desc'] }}</p>
                    
                    <div class="rating-input d-flex justify-content-between mb-2">
                        @for($i = 1; $i <= 5; $i++)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="{{ $item['id'] }}" id="{{ $item['id'] }}_{{ $i }}" value="{{ $i }}" required>
                            <label class="form-check-label" for="{{ $item['id'] }}_{{ $i }}">
                                <div class="text-center">
                                    <div style="font-size: 24px; color: #ffc107;">{{ $i }} ⭐</div>
                                    <small class="text-muted">{{ $i == 1 ? 'Sangat Tidak Puas' : ($i == 5 ? 'Sangat Puas' : '') }}</small>
                                </div>
                            </label>
                        </div>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Saran dan Harapan -->
            <div class="mb-4">
                <h3 class="section-title"><i class="fas fa-comment-dots me-2"></i>Saran dan Harapan</h3>
                
                <div class="mb-3">
                    <label for="saran_perbaikan" class="form-label">Saran Perbaikan</label>
                    <textarea class="form-control" id="saran_perbaikan" name="saran_perbaikan" rows="3" placeholder="Bagaimana kami dapat meningkatkan layanan kami?"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="fitur_harapan" class="form-label">Fitur yang Diharapkan</label>
                    <textarea class="form-control" id="fitur_harapan" name="fitur_harapan" rows="3" placeholder="Fitur apa yang Anda harapkan untuk ditambahkan?"></textarea>
                </div>
            </div>

            <!-- Kontak Tambahan -->
            <div class="mb-4">
                <h3 class="section-title"><i class="fas fa-address-card me-2"></i>Kontak Tambahan (Opsional)</h3>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="bersedia_dihubungi" name="bersedia_dihubungi" value="1">
                    <label class="form-check-label" for="bersedia_dihubungi">
                        Saya bersedia dihubungi untuk informasi lebih lanjut
                    </label>
                </div>
                
                <div class="mb-3">
                    <label for="kontak" class="form-label">Kontak (WhatsApp/Telepon)</label>
                    <input type="text" class="form-control" id="kontak" name="kontak" placeholder="Contoh: 0812-3456-7890">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Survei
                </button>
            </div>
        </form>

        <div class="text-center mt-4">
            <a href="{{ url('/') }}" class="back-home">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Cek rating
            const ratingInputs = document.querySelectorAll('input[type="radio"][required]');
            ratingInputs.forEach(function(group) {
                const name = group.name;
                const checked = document.querySelectorAll(`input[name="${name}"]:checked`).length;
                if (checked === 0) {
                    isValid = false;
                    const label = document.querySelector(`label[for="${group.id.split('_')[0]}"]`);
                    alert(`Silakan beri rating untuk: ${label ? label.textContent.trim() : name}`);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });

        // Rating hover effect
        document.querySelectorAll('.rating-input input').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const stars = this.parentElement.querySelectorAll('i');
                stars.forEach(function(star, index) {
                    if (index < parseInt(this.value)) {
                        star.classList.add('fas');
                        star.classList.remove('far');
                    } else {
                        star.classList.add('far');
                        star.classList.remove('fas');
                    }
                }.bind(this));
            });
        });
    </script>
</body>
</html>