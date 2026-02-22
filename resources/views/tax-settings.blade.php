<?php $page = 'tax-settings'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Tax Settings <a href="{{ route('tax-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_tax"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">

                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tax Name</th>
                                    <th>Rate</th>
                                    <th>Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($taxes as $index => $tax)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $tax->name }}</td>
                                    <td>{{ rtrim(rtrim(number_format($tax->rate, 4), '0'), '.') }}%</td>
                                    <td>{{ ucfirst($tax->type) }}</td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2" data-bs-toggle="modal" data-bs-target="#edit_tax_{{ $tax->id }}"><i class="icon-pencil-line"></i></a>
                                        <form action="{{ route('tax-settings.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this tax?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-sm btn-white rounded-circle text-danger"><i class="icon-trash-2"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No taxes configured yet. Click "Add New" to create one.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                </div>

            </div>
            <!-- card end -->

        </div>
        <!-- End Content -->

    </div>

    <!-- Add Tax Modal -->
    <div class="modal fade" id="add_tax" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Tax</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('tax-settings.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tax Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rate (%)<span class="text-danger ms-1">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="rate" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="exclusive">Exclusive</option>
                                <option value="inclusive">Inclusive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Tax Modals (one per tax) -->
    @foreach($taxes as $tax)
    <div class="modal fade" id="edit_tax_{{ $tax->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tax</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('tax-settings.update', $tax) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tax Name<span class="text-danger ms-1">*</span></label>
                            <input type="text" class="form-control" name="name" value="{{ $tax->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Rate (%)<span class="text-danger ms-1">*</span></label>
                            <input type="number" step="0.01" class="form-control" name="rate" value="{{ $tax->rate }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type<span class="text-danger ms-1">*</span></label>
                            <select class="form-select" name="type" required>
                                <option value="exclusive" {{ $tax->type === 'exclusive' ? 'selected' : '' }}>Exclusive</option>
                                <option value="inclusive" {{ $tax->type === 'inclusive' ? 'selected' : '' }}>Inclusive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
