<?php $page = 'table'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-3 mb-4">
            <h3 class="mb-0">Tables – Select restaurant</h3>
        </div>
        <p class="text-muted mb-4">Choose a restaurant to manage its tables.</p>
        <div class="row">
            @foreach($restaurants as $r)
                <div class="col-md-4 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2">{{ $r->name }}</h6>
                            <a href="{{ route('table', ['restaurant_id' => $r->id]) }}" class="btn btn-primary btn-sm">Manage tables</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
