@php
    $title = 'Data Pembentukan PUU';
@endphp

<x-layouts.backend :title="$title" :listNav="[['label' => 'Pembentukan PUU']]">

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.pembentukan-puu.create') }}" class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Tambah Data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="box-body">
                <!-- FORM PENCARIAN -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('backend.pembentukan-puu.index') }}" class="form-inline">
                            <div class="form-group">
                                <label for="search" class="sr-only">Cari Data</label>
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           id="search" 
                                           class="form-control" 
                                           placeholder="Cari berdasarkan judul, nomor, atau lembaga..." 
                                           value="{{ request('search') }}"
                                           style="width: 300px;">
                                    <span class="input-group-btn">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-search"></i> Cari
                                        </button>
                                        @if(request('search') || request('jenis_dokumen') || request('status_publikasi') || request('hak_akses') || request('tahun'))
                                        <a href="{{ route('backend.pembentukan-puu.index') }}" class="btn btn-default">
                                            <i class="fa fa-times"></i> Reset
                                        </a>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Filter Jenis Dokumen -->
                            <div class="form-group" style="margin-left: 10px;">
                                <select name="jenis_dokumen" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Jenis</option>
                                    @foreach($jenisDokumenList as $key => $label)
                                        <option value="{{ $key }}" {{ request('jenis_dokumen') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Filter Status -->
                            <div class="form-group" style="margin-left: 10px;">
                                <select name="status_publikasi" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="draft" {{ request('status_publikasi') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ request('status_publikasi') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="archived" {{ request('status_publikasi') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            
                            <!-- Filter Hak Akses -->
                            <div class="form-group" style="margin-left: 10px;">
                                <select name="hak_akses" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Akses</option>
                                    <option value="public" {{ request('hak_akses') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ request('hak_akses') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            
                            <!-- Tahun Filter -->
                            <div class="form-group" style="margin-left: 10px;">
                                <input type="number" 
                                       name="tahun" 
                                       class="form-control" 
                                       placeholder="Tahun" 
                                       value="{{ request('tahun') }}"
                                       min="1900"
                                       max="{{ date('Y') }}"
                                       style="width: 100px;"
                                       onchange="this.form.submit()">
                            </div>
                        </form>
                        
                        @if(request()->has('search') || request()->has('jenis_dokumen') || request()->has('status_publikasi') || request()->has('hak_akses') || request()->has('tahun'))
                        <div class="alert alert-info" style="margin-top: 10px; margin-bottom: 0;">
                            <i class="fa fa-filter"></i> Menampilkan hasil pencarian/filter
                            @if(request('search'))
                            - Kata kunci: "<strong>{{ request('search') }}</strong>"
                            @endif
                            @if(request('jenis_dokumen') && isset($jenisDokumenList[request('jenis_dokumen')]))
                            - Jenis: <strong>{{ $jenisDokumenList[request('jenis_dokumen')] }}</strong>
                            @endif
                            @if(request('status_publikasi'))
                            - Status: <strong>{{ ucfirst(request('status_publikasi')) }}</strong>
                            @endif
                            @if(request('hak_akses'))
                            - Akses: <strong>{{ ucfirst(request('hak_akses')) }}</strong>
                            @endif
                            @if(request('tahun'))
                            - Tahun: <strong>{{ request('tahun') }}</strong>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                <!-- END FORM PENCARIAN -->
                
                @if($puu->count() > 0)
                <div class="pull-right" style="margin-bottom: 10px;">
                    Ditampilkan {{ $puu->firstItem() }} - {{ $puu->lastItem() }} dari {{ $puu->total() }} Data
                </div>
                @endif
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Jenis Dokumen</th>
                            <th width="35%">Judul</th>
                            <th width="10%">Nomor</th>
                            <th width="8%" class="text-center">Tahun</th>
                            <th width="15%">Lembaga Pemrakarsa</th>
                            <th width="10%" class="text-center">Status Publikasi</th>
                            <th width="8%" class="text-center">Akses</th>
                            <th width="12%" class="text-center">Tanggal Unggah</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($puu as $index => $item)
                        <tr>
                            <td class="text-center">{{ ($puu->currentPage() - 1) * $puu->perPage() + $index + 1 }}</td>
                            <td>
                                @php
                                    $jenisLabels = [
                                        'naskah_akademik' => 'Naskah Akademik',
                                        'rancangan_puu' => 'Rancangan PUU',
                                        'penelitian_hukum' => 'Penelitian Hukum',
                                        'pengkajian_hukum' => 'Pengkajian Hukum',
                                        'pengkajian_konstitusi' => 'Pengkajian Konstitusi',
                                        'analisis_evaluasi' => 'Analisis Evaluasi',
                                    ];
                                @endphp
                                <span class="label label-primary">
                                    {{ $jenisLabels[$item->jenis_dokumen] ?? $item->jenis_dokumen }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('backend.pembentukan-puu.show', $item->id) }}">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 70) }}
                                </a>
                                @if($item->dokumen_utama)
                                <br>
                                <small>
                                    <a href="{{ route('backend.pembentukan-puu.download', ['id' => $item->id, 'type' => 'dokumen']) }}" 
                                       class="text-success" title="Download">
                                        <i class="fa fa-download"></i> Download
                                    </a>
                                </small>
                                @endif
                            </td>
                            <td>{{ $item->nomor_dokumen ?? '-' }}</td>
                            <td class="text-center">{{ $item->tahun }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->lembaga_pemrakarsa, 25) }}</td>
                            <td class="text-center">
                                @if($item->status_publikasi == 'published')
                                    <span class="label label-success">Published</span>
                                @elseif($item->status_publikasi == 'draft')
                                    <span class="label label-warning">Draft</span>
                                @elseif($item->status_publikasi == 'archived')
                                    <span class="label label-info">Arsip</span>
                                @else
                                    <span class="label label-default">{{ $item->status_publikasi }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->hak_akses == 'public')
                                    <span class="label label-primary">Public</span>
                                @elseif($item->hak_akses == 'private')
                                    <span class="label label-default">Private</span>
                                @else
                                    <span class="label label-default">{{ $item->hak_akses }}</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($item->tanggal_unggah)
                                    {{ \Carbon\Carbon::parse($item->tanggal_unggah)->format('d/m/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('backend.pembentukan-puu.show', $item->id) }}" 
                                       class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('backend.pembentukan-puu.edit', $item->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('backend.pembentukan-puu.destroy', $item->id) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i> 
                                    @if(request()->has('search') || request()->has('jenis_dokumen') || request()->has('status_publikasi') || request()->has('hak_akses') || request()->has('tahun'))
                                        Data tidak ditemukan dengan filter yang dipilih.
                                    @else
                                        Belum ada data Pembentukan PUU.
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($puu->hasPages())
            <div class="box-footer">
                <div class="pull-right">
                    {{ $puu->appends(request()->query())->links() }}
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>