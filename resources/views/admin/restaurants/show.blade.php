<?php $page = 'admin-restaurants'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <div class="flex-grow-1">
                <h3 class="mb-0">{{ $restaurant->name }}</h3>
            </div>
            <a href="{{ route('admin.restaurants.edit', $restaurant) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.restaurants.index') }}" class="btn btn-white">Back to list</a>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Details</h5>
                        <p class="mb-1"><strong>Slug:</strong> {{ $restaurant->slug }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $restaurant->email ?? '–' }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $restaurant->phone ?? '–' }}</p>
                        <p class="mb-0"><strong>Address:</strong> {{ $restaurant->address ?? '–' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Stats</h5>
                        <p class="mb-1">Tables: {{ $restaurant->tables_count }}</p>
                        <p class="mb-0">Orders: {{ $restaurant->orders_count }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
