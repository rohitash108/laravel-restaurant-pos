<?php $page = 'integrations-settings'; ?>
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
                    <h3 class="mb-0">Integrations / API <a href="{{ route('integrations-settings') }}" class="btn btn-icon btn-sm btn-white rounded-circle ms-2"><i class="icon-refresh-ccw"></i></a></h3>
                </div>
            </div>
            <!-- End Page Header -->

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- card start -->
            <div class="card mb-0">
                <div class="card-body">
                    <form action="{{ route('integrations-settings.update') }}" method="POST">
                        @csrf

                        <!-- start row -->
                        <div class="row">

                            @php
                                $integrations = [
                                    ['key' => 'gmail', 'label' => 'Gmail', 'icon' => 'mail-icon.svg', 'desc' => 'RESTful API you can use to send, receive, search, label, archive emails, <br> manage settings in Gmail mailboxes.'],
                                    ['key' => 'gupshup', 'label' => 'Gupshup', 'icon' => 'gupshup.svg', 'desc' => 'Messaging platform (SMS, WhatsApp, RCS) with presence'],
                                    ['key' => 'printnode', 'label' => 'PrintNode', 'icon' => 'print-node.svg', 'desc' => 'Middleware agents for cloud-to-local printing.'],
                                ];
                            @endphp

                            @foreach($integrations as $integration)
                            <div class="col-md-12">
                                <div class="card payment-type flex-fill{{ $loop->last ? ' mb-0' : '' }}">
                                    <div class="card-body">
                                        <div class="w-100 d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-lg bg-light rounded-circle border p-2 me-2 flex-shrink-0">
                                                    <img src="{{URL::asset('build/img/icons/' . $integration['icon'])}}" alt="Img">
                                                </div>
                                                <div>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ $integration['label'] }}</h6>
                                                    <p class="mb-0">{!! $integration['desc'] !!}</p>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="form-check form-switch mb-0">
                                                    <label class="form-check-label" for="int_{{ $integration['key'] }}"></label>
                                                    <input class="form-check-input" type="checkbox" role="switch" id="int_{{ $integration['key'] }}" name="{{ $integration['key'] }}" {{ ($settings[$integration['key']] ?? ($integration['key'] !== 'gupshup' ? '1' : '0')) === '1' ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end col -->
                            @endforeach

                        </div>
                        <!-- end row -->

                        <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-2 border-top mt-4 pt-4">
                            <button type="button" class="btn btn-light me-2" onclick="window.location.reload()">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>

                    </form>

                </div> <!-- end card body -->

            </div>
            <!-- card start -->

        </div>
        <!-- End Content -->

    </div>

    <!-- ========================
        End Page Content
    ========================= -->

@endsection
