<?php
    $auth_user = authSession();
?>

@if($auth_user->can('order-show'))
    <a class="mr-2" href="{{ route('order.show',$id) }}"><i class="fas fa-eye text-secondary"></i></a>
@endif


