<?php
    $auth_user= authSession();
?>

    <div class="d-flex justify-content-end align-items-center">
        @if($auth_user->can('emergency-map'))
            <a class="mr-2" href="{{ route('emergency.show',$id) }}"><i class="fa fa-location-arrow"></i></a>
        @endif
        @if($auth_user->can('emergency-show'))
            <a class="mr-2" href="{{ route('emergency.edit',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
        @endif
    </div>
