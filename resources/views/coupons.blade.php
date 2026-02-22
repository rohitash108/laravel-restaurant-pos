<?php $page = 'coupons'; ?>
@extends('layout.mainlayout')
@section('content')

    <!-- ========================
        Start Page Content
    ========================= -->

    <div class="page-wrapper">

        <!-- Start Content -->
        <div class="content">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Page Header -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
                <div class="flex-grow-1">
                    <h3 class="mb-0">Coupons<a href="#" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="icon-upload me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as PDF</a>
                            </li>
                            <li>
                                <a href="#" class="dropdown-item rounded">Export as Excel</a>
                            </li>
                        </ul>
                    </div>
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_coupon"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            <!-- End Page Header -->

            @if(($coupons ?? collect())->isEmpty())
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="icon-badge-percent fs-48 text-muted mb-3"></i>
                    <h5 class="mb-2">No coupons yet</h5>
                    <p class="text-muted mb-0">Coupons will appear here when the feature is enabled.</p>
                </div>
            </div>
            @else
            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">

                        <div class="search-input">
                            <span class="btn-searchset"><i class="icon-search fs-14"></i></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <!-- filter -->
                            <a href="#" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#filter-offcanvas" aria-controls="filter-offcanvas">
                                <i class="icon-funnel me-2"></i>Filter
                            </a>

                            <!-- column -->
                            <div class="dropdown">
                                <a href="#" class="btn btn-icon btn-white" data-bs-toggle="dropdown" data-bs-auto-close="outside"><i class="icon-columns-3"></i></a>
                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-3 pb-0">
                                    <h5 class="mb-3">Column</h5>
                                    <div>
                                        <div id="drag-container">
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                                    Coupon Code
                                                </label>
                                            </div>
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                                    Valid Category
                                                </label>
                                            </div>
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox">
                                                    Discount Type
                                                </label>
                                            </div>
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                                    Discount Amount
                                                </label>
                                            </div>
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                                    Duration
                                                </label>
                                            </div>
                                            <div class="mb-3 drag-item" draggable="true">
                                                <label class="d-flex align-items-center">
                                                    <i class="icon-grip-vertical me-2"></i>
                                                    <input class="form-check-input m-0 me-2" type="checkbox" checked>
                                                    Actions
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- sort by -->
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                    Sort by : Newest
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end p-3">
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Newest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Oldest</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Ascending</a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item">Descending</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- table start -->
                    <div class="table-responsive table-nowrap">
                        <table class="table mb-0 border datatable">
                            <thead>
                                <tr>
                                    <th>Coupon Code</th>
                                    <th>Valid Category</th>
                                    <th>Discount Type</th>
                                    <th>Discount Amount</th>
                                    <th>Duration</th>
                                        <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($coupons as $coupon)
                                <tr>
                                    <td>{{ $coupon->code ?? '–' }}</td>
                                    <td>{{ $coupon->category?->name ?? 'All' }}</td>
                                    <td>{{ ucfirst($coupon->discount_type ?? 'percentage') }}</td>
                                    <td>{{ $coupon->discount_type === 'percentage' ? $coupon->discount_amount . '%' : '$' . number_format($coupon->discount_amount ?? 0, 2) }}</td>
                                    <td>{{ $coupon->valid_from && $coupon->valid_to ? $coupon->valid_from->format('d M Y') . ' - ' . $coupon->valid_to->format('d M Y') : ($coupon->valid_from ? $coupon->valid_from->format('d M Y') . ' - …' : '–') }}</td>
                                    <td>
                                        <span class="badge badge-soft-{{ ($coupon->is_active ?? true) ? 'success' : 'danger' }}">{{ ($coupon->is_active ?? true) ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle me-2" data-bs-toggle="modal" data-bs-target="#edit_coupon_{{ $coupon->id }}"><i class="icon-pencil-line"></i></a>
                                        <a href="#" class="btn btn-icon btn-sm btn-white rounded-circle" data-bs-toggle="modal" data-bs-target="#delete_coupon_{{ $coupon->id }}"><i class="icon-trash-2"></i></a>
                                    </td>
                                </tr>

                                <!-- Edit Coupon Modal -->
                                <div class="modal fade" id="edit_coupon_{{ $coupon->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('coupons.update', $coupon) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Coupon</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Coupon Code</label>
                                                        <input type="text" class="form-control" name="code" value="{{ $coupon->code }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Category</label>
                                                        <select class="form-select" name="category_id">
                                                            <option value="">All Categories</option>
                                                            @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $coupon->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Discount Type</label>
                                                            <select class="form-select" name="discount_type" required>
                                                                <option value="percentage" {{ $coupon->discount_type === 'percentage' ? 'selected' : '' }}>Percentage</option>
                                                                <option value="fixed" {{ $coupon->discount_type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Discount Amount</label>
                                                            <input type="number" step="0.01" class="form-control" name="discount_amount" value="{{ $coupon->discount_amount }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Valid From</label>
                                                            <input type="date" class="form-control" name="valid_from" value="{{ $coupon->valid_from?->format('Y-m-d') }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label">Valid To</label>
                                                            <input type="date" class="form-control" name="valid_to" value="{{ $coupon->valid_to?->format('Y-m-d') }}">
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $coupon->is_active ? 'checked' : '' }}>
                                                        <label class="form-check-label">Active</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Coupon Modal -->
                                <div class="modal fade" id="delete_coupon_{{ $coupon->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <form action="{{ route('coupons.destroy', $coupon) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Coupon</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete coupon <strong>{{ $coupon->code }}</strong>?</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                @empty
                                <tr><td colspan="7" class="text-center py-4 text-muted">No coupons yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                </div>

            </div>
            <!-- card end -->
            @endif

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection

