<?php
    $auth_user= authSession();
?>
 @if($action_type == 'bank_details')
    @if($row->status == 'requested')
        @if($auth_user->can('withdrawrequest-show'))
        <a class="mr-2 loadRemoteModel" href="{{ route('withdrawrequest.show',$user) }}"><i class="fas fa-eye text-secondary"></i></a>
        @endif
    @else
        {{'-'}}
    @endif
@endif
@if($action_type == 'action')
    <div class="form-group">
        @if($row->status == 'requested')
            <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('approvedWithdrawRequest',  ['id' => $id]) }}', 'approve')"
                title="{{ __('message.approve') }}">
                <span class="badge badge-success mr-1"><i class="fas fa-check"></i> </span>
            </a>
            <a class="mr-2" href="javascript:void(0)" onclick="confirmAction('{{ route('declineWithdrawRequest', ['id' => $id]) }}', 'reject')"
                title="{{ __('message.decline') }}">
                <span class="badge badge-danger mr-1"> <i class="fas fa-ban"></i></span>
            </a>
            @php
                 $withdrawDetailExists = \App\Models\WithdrawDetail::where('withdrawrequest_id', $id)->first();
            @endphp

            @if($withdrawDetailExists)
                <a class="mr-2 loadRemoteModel" href="{{ route('withdraw-history-edit', $id) }}" title="{{ __('message.add_details') }}">
                    <i class="fa-solid fa-circle-plus"></i>
                </a>
            @else
                
            @endif
        @else
            {{'-'}}
        @endif
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

