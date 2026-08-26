<x-master-layout>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12 mb-3">
                <div class="d-flex align-items-center justify-content-between welcome-content">
                    <div class="navbar-breadcrumb">
                        <!-- <h4 class="mb-0 font-weight-700">Welcome To Dashboard</h4> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-2 col-md-4">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body custom-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mm-cart-text">
                                    <p class="mb-0">{{ __('message.total_order') }}</p>
                                    <br>
                                    <h5 class="font-weight-700">{{ $dashboard_data['total_order'] }}</h5>
                                </div>
                                <div class="mm-cart-image text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="42" fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                        <path d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2zm3.564 1.426L5.596 5 8 5.961 14.154 3.5zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body custom-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mm-cart-text">
                                    <p class="mb-0">{{ __('message.delivered_order') }}</p>
                                    <br>
                                    <h5 class="font-weight-700">{{ $dashboard_data['total_completed_order'] }}</h5>
                                </div>
                                <div class="mm-cart-image text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="42" fill="currentColor" class="bi bi-cart-check" viewBox="0 0 16 16">
                                        <path d="M11.354 6.354a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146a.5.5 0 1 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0z"/>
                                        <path d="M.5 1a.5.5 0 0 0 0 1h1.11l.401 1.607 1.498 7.985A.5.5 0 0 0 4 12h1a2 2 0 1 0 0 4 2 2 0 0 0 0-4h7a2 2 0 1 0 0 4 2 2 0 0 0 0-4h1a.5.5 0 0 0 .491-.408l1.5-8A.5.5 0 0 0 14.5 3H2.89l-.405-1.621A.5.5 0 0 0 2 1zm3.915 10L3.102 4h10.796l-1.313 7zM6 14a1 1 0 1 1-2 0 1 1 0 0 1 2 0m7 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body custom-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mm-cart-text">
                                    <p class="mb-0">{{ __('message.assigned_order') }}</p>
                                    <br>
                                    {{-- <h5 class="font-weight-700">{{ $data['dashboard']['total_assigned_order'] }}</h5> --}}
                                </div>
                                <div class="mm-cart-image text-primary">
                                    <svg class="svg-icon svg-danger" width="50" height="52" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body custom-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mm-cart-text">
                                    <p class="mb-0">{{ __('message.accepted_order') }}</p>
                                    <br>
                                    {{-- <h5 class="font-weight-700">{{ $data['dashboard']['total_accepetd_order'] }}</h5> --}}
                                </div>
                                <div class="mm-cart-image text-primary">
                                    <svg class="svg-icon svg-danger" width="50" height="52" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body custom-hover">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="mm-cart-text">
                                    <p class="mb-0">{{ __('message.arrived_order') }}</p>
                                    <br>
                                    {{-- <h5 class="font-weight-700">{{ $data['dashboard']['total_arrived_order'] }}</h5> --}}
                                </div>
                                <div class="mm-cart-image text-primary">
                                    <svg class="svg-icon svg-danger" width="50" height="52" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Page end  -->
    </div>
    @section('bottom_script')
    @endsection
</x-master-layout>
