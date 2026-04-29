<?php $page = 'categories'; ?>
@extends('layout.mainlayout')
@section('content')

<div class="page-wrapper">
    <div class="content">

        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">Categories</h3>
                <p class="text-muted small mb-0">Categories are managed by Super Admin.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible border-0 mb-4">
                {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table mb-0 border">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>No of Items</th>
                                <th>Created On</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories ?? [] as $category)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm avatar-rounded flex-shrink-0 bg-light d-flex align-items-center justify-content-center">
                                            @if($category->image)
                                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}" class="img-fluid">
                                            @else
                                                <i class="icon-layers text-muted"></i>
                                            @endif
                                        </div>
                                        <span class="fw-medium">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $category->items_count ?? 0 }}</td>
                                <td>{{ $category->created_at?->format('d M Y') }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-soft-success">Active</span>
                                    @else
                                        <span class="badge badge-soft-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    No categories assigned yet. Contact Super Admin to add categories.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
