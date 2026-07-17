<x-layouts.backend title="Data Disabilitas" :listNav="[['label' => 'Disabilitas']]">

<div class="row">
    <div class="col-xs-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Data Disabilitas</h3>
                <div class="pull-right">
                    <a href="{{ route('backend.disabilitas.create') }}" class="btn btn-success">
                        <i class="fa fa-plus-circle"></i> Tambah Data
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="box-body">
                <!-- FORM PENCARIAN -->
                <div class="row" style="margin-bottom: 20px;">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('backend.disabilitas.index') }}" class="form-inline">
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
                                        @if(request('search'))
                                        <a href="{{ route('backend.disabilitas.index') }}" class="btn btn-default">
                                            <i class="fa fa-times"></i> Reset
                                        </a>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Filter Tambahan (Opsional) -->
                            <div class="form-group" style="margin-left: 10px;">
                                <select name="status" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Status</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Arsip</option>
                                </select>
                            </div>
                            
                            <div class="form-group" style="margin-left: 10px;">
                                <select name="hak_akses" class="form-control" onchange="this.form.submit()">
                                    <option value="">Semua Akses</option>
                                    <option value="public" {{ request('hak_akses') == 'public' ? 'selected' : '' }}>Public</option>
                                    <option value="private" {{ request('hak_akses') == 'private' ? 'selected' : '' }}>Private</option>
                                </select>
                            </div>
                            
                            <!-- Tahun Filter (Opsional) -->
                            <div class="form-group" style="margin-left: 10px;">
                                <input type="number" 
                                       name="tahun" 
                                       class="form-control" 
                                       placeholder="Tahun" 
                                       value="{{ request('tahun') }}"
                                       min="1900"
                                       max="{{ date('Y') }}"
                                       style="width: 100px;">
                            </div>
                        </form>
                        
                        @if(request()->has('search') || request()->has('status') || request()->has('hak_akses') || request()->has('tahun'))
                        <div class="alert alert-info" style="margin-top: 10px; margin-bottom: 0;">
                            <i class="fa fa-filter"></i> Menampilkan hasil pencarian/filter
                            @if(request('search'))
                            - Kata kunci: "<strong>{{ request('search') }}</strong>"
                            @endif
                            @if(request('status'))
                            - Status: <strong>{{ ucfirst(request('status')) }}</strong>
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
                
                @if($disabilitas->count() > 0)
                <div class="pull-right" style="margin-bottom: 10px;">
                    Ditampilkan {{ $disabilitas->firstItem() }} - {{ $disabilitas->lastItem() }} dari {{ $disabilitas->total() }} Data
                </div>
                @endif
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Jenis Dokumen</th>
                            <th width="35%">Judul</th>
                            <th width="12%">Nomor</th>
                            <th width="8%" class="text-center">Tahun</th>
                            <th width="15%">Lembaga Penetap</th>
                            <th width="10%" class="text-center">Status</th>
                            <th width="8%" class="text-center">Akses</th>
                            <th width="12%" class="text-center">Tanggal Unggah</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($disabilitas as $index => $item)
                        <tr>
                            <td class="text-center">{{ ($disabilitas->currentPage() - 1) * $disabilitas->perPage() + $index + 1 }}</td>
                            <td>{{ $item->jenis_dokumen }}</td>
                            <td>
                                <a href="{{ route('backend.disabilitas.show', $item->id) }}">
                                    {{ \Illuminate\Support\Str::limit($item->judul, 60) }}
                                </a>
                            </td>
                            <td>{{ $item->nomor_dokumen }}</td>
                            <td class="text-center">{{ $item->tahun }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($item->lembaga_penetap, 30) }}</td>
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
                                    {{ \Carbon\Carbon::parse($item->tanggal_unggah)->format('j/n/Y') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('backend.disabilitas.show', $item->id) }}" 
                                       class="btn btn-info btn-sm" title="Lihat Detail">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('backend.disabilitas.edit', $item->id) }}" 
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <form action="{{ route('backend.disabilitas.destroy', $item->id) }}" 
                                          method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" 
                                                onclick="return confirm('Yakin ingin menghapus dokumen ini?')"
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
                                    @if(request()->has('search') || request()->has('status') || request()->has('hak_akses') || request()->has('tahun'))
                                        Data tidak ditemukan dengan filter yang dipilih.
                                    @else
                                        Belum ada data disabilitas.
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($disabilitas->hasPages())
            <div class="box-footer">
                <div class="pull-right">
                    {{ $disabilitas->appends(request()->query())->links() }}
                </div>
                <div class="clearfix"></div>
            </div>
            @endif
        </div>
    </div>
</div>

</x-layouts.backend>