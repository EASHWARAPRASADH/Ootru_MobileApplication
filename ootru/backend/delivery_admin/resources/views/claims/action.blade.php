<?php
    $auth_user= authSession();
?>
    @if($action_type == 'resolve')
        <div class="d-flex justify-content-end align-items-center">
        @if($row->status == 'approved')
            @if($auth_user->can('claims-show'))
                <a class="mr-2 loadRemoteModel" href="{{ route('claims-model',$id) }}"> <i class="fa-solid fa-circle-plus"></i></a>
            @endif
        @endif
        @if(($row->status == 'pending' || $row->status == 'approved') && $auth_user->can('claims-show'))
            <a class="mr-2" href="{{ route('claims.show', $id) }}">
                <i class="fas fa-eye text-secondary"></i>
            </a>
        @endif
        </div>
    @endif      
    @if($action_type == 'action')
        <div class="form-group">
            @if($row->status == 'pending')
            <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('approvedstatus', ['id' => $row->id, 'status' => 1]) }}', 'approve')" title="{{ __('message.approve') }}">
                <span class="badge badge-success mr-1"><i class="fas fa-check"></i></span>
            </a>
            <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('approvedstatus', ['id' => $row->id, 'status' => 0]) }}', 'reject')" title="{{ __('message.reject') }}">
                <span class="badge badge-danger mr-1"><i class="fas fa-ban"></i></span>
            </a>  
            @endif
            @if(($row->status == 'pending' || $row->status == 'approved') && $auth_user->can('claims-show'))
                <a class="mr-2" href="{{ route('claims.show', $data) }}">
                    <i class="fas fa-eye text-secondary"></i>
                </a>
            @endif
            @if($row->status == 'approved' && $auth_user->can('claims-show'))
                <a class="mr-2 loadRemoteModel" href="{{ route('claims-model',$data) }}"> <i class="fa-solid fa-circle-plus"></i></a>
            @endif
        </div>
    @endif
    @if($action_type == 'close')
    <div class="form-group">
        {{-- <a class="mr-2 loadRemoteModel" href="{{ route('claims-model',$id) }}">  <i class="fas fa-eye text-secondary"></i> --}}
            <a class="mr-2" href="{{ route('close-view', $id) }}" title="{{ __('message.update_form_title',['form' => __('message.claims') ]) }}"><i class="fas fa-eye text-secondary"></i></a>
    </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmAction(url, action) {
            const title = action === 'approve' ? 'Are you sure you want to approve this?' : 'Are you sure you want to reject this?';
            const confirmButtonText = action === 'approve' ? 'Yes, Approve' : 'Yes, Reject';
            const cancelButtonText = 'Cancel';

            Swal.fire({
                title: title,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'approve' ? '#28a745' : '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: confirmButtonText,
                cancelButtonText: cancelButtonText,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the URL if confirmed
                    window.location.href = url;
                }
            });
        }
    </script>
    
    
