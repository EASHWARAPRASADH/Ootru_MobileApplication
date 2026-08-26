<x-master-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title ">{{ $pageTitle  ?? ''}}</h4>
                        </div>
                        <div class="card-header-toolbar">
                            @if(isset($button))
                            {!! $button !!}
                            @endif
                        </div>                        
                    </div>
                    
                    <div class="card-body">
                        
                        @include('global.user-datatable')
                        
                        <div class="card-header-toolbar">
                            @if(isset($multi_checkbox_delete))
                               {!! $multi_checkbox_delete !!}
                            @endif
                        </div>
                        {{ $dataTable->table(['class' => 'table  w-100'],false) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('bottom_script')
       {{ $dataTable->scripts() }}
    @endsection
</x-master-layout>
