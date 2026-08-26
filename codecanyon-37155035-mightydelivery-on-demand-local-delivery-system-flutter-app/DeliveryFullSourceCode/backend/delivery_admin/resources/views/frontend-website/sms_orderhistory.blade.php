<x-frontand-layout :assets="$assets ?? []">

    <!-- START ORDER HISTORY TIMELINE -->
    <div class="container timeline-main-section">
        <div class="card order-history-card">
            <div class="d-flex align-items-center history-name">
                <span class="text-white ms-3 fw-bold">
                   {{'#'.__('message.order')}}  {{ $data->id }} {{__('message.history')}}
                </span>
            </div>
            <section class="py-4 mt-2 timeline-section">
                @if (count($data->orderhistory) > 0)
                    <ul class="timeline-with-icons">
                        @foreach ($data->orderHistoryasc as $history)
                            <li class="timeline-item mb-2">
                                <span class="timeline-icon">
                                    @php
                                        $iconClass = '';
                                        $icon = '';
                                        switch ($history->history_type) {
                                            case 'create':
                                                $iconClass = 'fa-solid fa-file-pen';
                                                break;
                                            case 'courier_assigned':
                                                $iconClass = 'fa-solid fa-file-signature';
                                                break;
                                            case 'courier_arrived':
                                                $icon = 'fa-solid fa-file-arrow-down';
                                                break;
                                            case 'courier_picked_up':
                                                $icon = 'fa-solid fa-truck-fast';
                                                break;
                                            case 'courier_departed':
                                                $iconClass = 'fa fa-plane-departure';
                                                break;
                                            case 'payment_status_message':
                                                $iconClass = 'fa fa-credit-card';
                                                break;
                                            case 'courier_transfer':
                                                $iconClass = 'fas fa-box';
                                                break;
                                            case 'completed':
                                                $icon = 'fa fa-square-check';
                                                break;
                                            case 'return':
                                                $iconClass = "fa fa-rotate";
                                                break;
                                            default:
                                                $iconClass = 'fa-solid fa-question-circle';
                                        }
                                    @endphp
                                    <i class="fa-xl {{ $iconClass }} text-white ms-2 ml-1"></i>
                                    <i class="fa-xl {{ $icon }} text-white me-1"></i>
                                </span>
                                <div class="timeline-content">
                                    <h5 class="fw-bold">{{ ucfirst(str_replace('_', ' ', $history->history_type)) }}
                                    </h5>
                                    <p class="text-muted mb-2">{{ $history->history_message }}</p>
                                    <p class="text-muted fw-bold">
                                        {{dateAgoFormate($history->datetime)}}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
    <!-- END ORDER HISTORY TIMELINE -->

</x-frontand-layout>
