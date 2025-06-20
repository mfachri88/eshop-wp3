@extends('v_layouts.app')

@section('content')
{{-- CSS untuk frame foto, diletakkan langsung di sini untuk kepastian --}}
<style>
    .image-preview-container {
        width: 100%;
        height: 320px;
        border: 1px solid #ddd;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .image-preview-container .foto-preview {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Ini adalah kunci agar foto mengikuti frame */
        object-position: center;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="billing-details">
            <div class="section-title">
                <h3 class="title">{{$judul}}</h3>
            </div>

            @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>{{ session('success') }}</strong>
                </div>
            @endif
            @if(session()->has('msgError'))
                <div class="alert alert-danger alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <strong>{{ session('msgError') }}</strong>
                </div>
            @endif

            <form action="{{ route('customer.updateakun', $edit->user->id) }}" method="post" enctype="multipart/form-data">
                @method('put')
                @csrf

                <div class="row">
                    {{-- Kolom Foto --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Foto</label>
                            <div class="image-preview-container mb-2">
                                {{-- Gunakan ID unik untuk gambar pratinjau --}}
                                <img id="imagePreview" src="{{ $edit->user && $edit->user->foto ? asset('storage/img-customer/' . $edit->user->foto) : asset('storage/img-user/img-default.jpg') }}" class="foto-preview">
                            </div>
                            {{-- Gunakan ID unik untuk input file --}}
                            <input type="file" name="foto" id="fotoInput" class="form-control @error('foto') is-invalid @enderror">
                            @error('foto')
                                <div class="invalid-feedback alert-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Kolom Form Input --}}
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" value="{{ old('nama', $edit->user->nama) }}" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan Nama">
                            @error('nama')
                                <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="text" name="email" value="{{ old('email', $edit->user->email) }}" class="form-control @error('email') is-invalid @enderror" placeholder="Masukkan Email">
                            @error('email')
                                <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>HP</label>
                            <input type="text" onkeypress="return hanyaAngka(event)" name="hp" value="{{ old('hp', $edit->user->hp) }}" class="form-control @error('hp') is-invalid @enderror" placeholder="Masukkan Nomor HP">
                            @error('hp')
                                <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Alamat</label><br>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat', $edit->alamat) }}</textarea>
                            @error('alamat')
                                <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Kode Pos</label>
                            <input type="text" name="pos" value="{{ old('pos', $edit->pos) }}" class="form-control @error('pos') is-invalid @enderror" placeholder="Masukkan Kode Pos">
                            @error('pos')
                                <span class="invalid-feedback alert-danger" role="alert">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12" style="margin-top: 20px;">
                        <div class="pull-left">
                            <button type="submit" class="primary-btn">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- JavaScript untuk pratinjau gambar, diletakkan langsung di sini --}}
<script>
    // Pastikan skrip berjalan setelah halaman dimuat sepenuhnya
    document.addEventListener('DOMContentLoaded', function() {
        const fotoInput = document.getElementById('fotoInput');
        const imagePreview = document.getElementById('imagePreview');

        // Tambahkan event listener ke input file
        fotoInput.addEventListener('change', function() {
            // Cek jika ada file yang dipilih
            if (this.files && this.files[0]) {
                const reader = new FileReader();

                // Saat file selesai dibaca, ubah src dari gambar pratinjau
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };

                // Baca file sebagai URL
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    function hanyaAngka(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;
        return true;
    }
</script>
@endsection