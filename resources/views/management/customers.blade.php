@extends('layouts.app')

@section('content')
<main class="container py-4">
    @include('layouts.goBack')

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Customer Management</h1>
            <p class="text-muted small mb-0">Data member dan loyalty point (10% dari setiap pembelian)</p>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="bi bi-person-plus"></i> Tambah Member
        </button>
    </div>

    @if(session('lastAct'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('lastAct') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Telepon</th>
                        <th scope="col" class="text-end">Loyalty Point</th>
                        <th scope="col">Terdaftar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $index => $customer)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-semibold">{{ $customer->name }}</td>
                            <td>{{ \App\Support\CustomerPhone::display($customer->phone) }}</td>
                            <td class="text-end">
                                <span class="badge rounded-pill text-bg-primary">{{ number_format($customer->loyalty_points, 0, ',', '.') }} poin</span>
                            </td>
                            <td class="text-muted small">{{ $customer->created_at?->timezone('Asia/Jakarta')->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                Belum ada member terdaftar. Tambahkan member dari kasir atau tombol di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content pos-modal__content">
            <div class="modal-header pos-modal__header">
                <div>
                    <h5 class="modal-title fw-bold mb-0" id="addMemberModalLabel">Tambah Member Baru</h5>
                    <p class="small text-muted mb-0">Hanya nama dan nomor telepon</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-body pos-modal__body">
                    <div class="mb-3">
                        <label for="memberName" class="form-label fw-semibold">Nama</label>
                        <input type="text" name="name" id="memberName" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required maxlength="120" autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label for="memberPhoneAdmin" class="form-label fw-semibold">Nomor telepon</label>
                        <input type="tel" name="phone" id="memberPhoneAdmin" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required maxlength="20" placeholder="08xxxxxxxxxx" autocomplete="tel">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer pos-modal__footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@if($errors->any())
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addMemberModal')).show();
            });
        </script>
    @endpush
@endif
