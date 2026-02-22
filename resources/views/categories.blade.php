<?php $page = 'categories'; ?>
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
                    <h3 class="mb-0">Categories <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
                <div class="gap-2 d-flex align-items-center flex-wrap">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="icon-upload me-1"></i>Export
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-3">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded">Export as PDF</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded">Export as Excel</a>
                            </li>
                        </ul>
                    </div>
                    <a href="#" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_category"><i class="icon-circle-plus me-1"></i>Add New</a>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible border-0 mb-4" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible border-0 mb-4" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- End Page Header -->

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <div class="d-flex align-items-center flex-wrap gap-3 justify-content-between mb-4">

                        <div class="search-input">
                            <span class="btn-searchset"><i class="icon-search fs-14"></i></span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">

                            <!-- filter -->
                            <a href="javascript:void(0);" class="btn btn-white d-inline-flex align-items-center" data-bs-toggle="offcanvas" data-bs-target="#filter-offcanvas" aria-controls="filter-offcanvas">
                                <i class="icon-funnel me-2"></i>Filter
                            </a>

                            <!-- column -->
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="btn btn-icon btn-white" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                    <i class="icon-columns-3"></i>
                                </a>

                                <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-3 pb-0">
                                    <h5 class="mb-3">Column</h5>
                                    <div id="drag-container">
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Category
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                No of Items
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Created On
                                            </label>
                                        </div>
                                        <div class="mb-3 drag-item" draggable="true">
                                            <label class="d-flex align-items-center">
                                                <i class="icon-grip-vertical me-2"></i>
                                                <input class="form-check-input m-0 me-2" type="checkbox">
                                                Status
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- sort by -->
                            <div class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
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
                                    <th>Category</th>
                                    <th>No of Items</th>
                                    <th>Created On</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories ?? [] as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm avatar-rounded flex-shrink-0 me-2 bg-light d-flex align-items-center justify-content-center">
                                                @if($category->image)
                                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid">
                                                @else
                                                    <i class="icon-layers text-muted"></i>
                                                @endif
                                            </div>
                                            <h6 class="fs-14 fw-normal mb-0">{{ $category->name }}</h6>
                                        </div>
                                    </td>
                                    <td>{{ $category->items_count ?? 0 }}</td>
                                    <td>{{ $category->created_at?->format('F j, Y') }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-soft-success">Active</span>
                                        @else
                                            <span class="badge badge-soft-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="btn btn-icon btn-sm btn-white rounded-circle me-2 edit-category-btn" data-bs-toggle="modal" data-bs-target="#edit_category" data-id="{{ $category->id }}" data-name="{{ $category->name }}"><i class="icon-pencil-line"></i></a>
                                        <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-sm btn-white rounded-circle"><i class="icon-trash-2 text-danger"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">No categories yet. Add one using the button above.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <!-- table end -->
                </div>

            </div>
            <!-- card start -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
