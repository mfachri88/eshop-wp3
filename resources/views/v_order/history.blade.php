@extends('v_layouts.app')

@section('content')
<div class="container my-4">
    <h2>HISTORY PESANAN</h2>
    <hr>

    {{-- Session Messages --}}
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($orders->count() > 0)
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Pesanan</th>
                    <th>Tanggal</th>
                    <th>Total Bayar</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        <td>Rp. {{ number_format($order->total_harga + $order->biaya_ongkir, 0, ',', '.') }}</td>
                        <td>
                            @if ($order->status == 'Paid')
                                Proses
                            @else
                                {{ $order->status }}
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Button to trigger the detail modal --}}
                            <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#orderDetailModal-{{ $order->id }}">
                                Lihat Detail
                            </button>
                            {{-- Link to Invoice (if applicable) --}}
                            {{-- <a href="{{ route('order.invoice', $order->id) }}" class="btn btn-secondary btn-sm ms-2">Invoice</a> --}}
                        </td>
                    </tr>

                    <div class="modal fade" id="orderDetailModal-{{ $order->id }}" tabindex="-1" aria-labelledby="orderDetailModalLabel-{{ $order->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="orderDetailModalLabel-{{ $order->id }}">Detail Pesanan #{{ $order->id }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Tanggal Pesanan:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                                    <p><strong>Status:</strong>
                                        @if ($order->status == 'Paid')
                                            Proses
                                        @else
                                            {{ $order->status }}
                                        @endif
                                    </p>
                                    <p><strong>Alamat Pengiriman:</strong> {{ $order->alamat }} (Kode Pos: {{ $order->pos }})</p>
                                    <p><strong>Kurir:</strong> {{ $order->kurir ? strtoupper($order->kurir) . ' - ' . $order->layanan_ongkir : '-' }}</p>
                                    <p><strong>Biaya Ongkir:</strong> Rp. {{ number_format($order->biaya_ongkir, 0, ',', '.') }} (Estimasi: {{ $order->estimasi_ongkir ?? '-' }} Hari)</p>
                                    <p><strong>Total Berat:</strong> {{ $order->total_berat ?? 0 }} Gram</p>

                                    <h6 class="mt-3">Produk dalam Pesanan:</h6>
                                    @if($order->orderItems->count() > 0)
                                        <table class="table table-sm table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th class="text-center">Jumlah</th>
                                                    <th class="text-end">Harga Satuan</th>
                                                    <th class="text-end">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($order->orderItems as $item)
                                                    <tr>
                                                        <td>{{ $item->produk->nama_produk }}</td>
                                                        <td class="text-center">{{ $item->quantity }}</td>
                                                        <td class="text-end">Rp. {{ number_format($item->harga, 0, ',', '.') }}</td>
                                                        <td class="text-end">Rp. {{ number_format($item->harga * $item->quantity, 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-end">Subtotal Produk:</th>
                                                    <th class="text-end">Rp. {{ number_format($order->total_harga, 0, ',', '.') }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" class="text-end">Total Bayar (termasuk ongkir):</th>
                                                    <th class="text-end">Rp. {{ number_format($order->total_harga + $order->biaya_ongkir, 0, ',', '.') }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    @else
                                        <p>Tidak ada produk dalam pesanan ini.</p>
                                    @endif
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info" role="alert">
            Anda belum memiliki riwayat pesanan.
        </div>
        <a href="{{ route('produk.index') }}" class="btn btn-primary">Mulai Belanja Sekarang</a>
    @endif
</div>
@endsection