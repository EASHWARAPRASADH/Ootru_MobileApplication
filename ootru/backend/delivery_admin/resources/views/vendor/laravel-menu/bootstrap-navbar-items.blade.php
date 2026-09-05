@foreach($items as $item)
    <?php
    $hasChildren = $item->hasChildren();
    $active = '';
    $targetId = '';

    if ($hasChildren) {
        if ($item->children()->where('isActive', true)->first() !== null) {
            $active = 'active';
        }
        $cleanUrl = str_replace('#', '', $item->url());
        $targetId = !empty($cleanUrl) ? $cleanUrl : (!empty($item->nickname) ? $item->nickname : 'item-' . $item->id);
    } else {
        $active = $item->isActive ? 'active' : '';
    }
    ?>
    <li @lm_attrs($item) class="{{ $item->attributes['class'] ?? '' }} {{ $active }}" @lm_endattrs>
        @if($item->link)
            @if($hasChildren)
                <a @lm_attrs($item->link) data-toggle="collapse" data-target="#{{ $targetId }}" role="button" aria-expanded="{{ $active != '' ? 'true' : 'false' }}" aria-controls="{{ $targetId }}" class="{{ $active != '' ? '' : 'collapsed' }}" @lm_endattrs href="#{{ $targetId }}">
                    {!! $item->title !!}
                    <i class="fas fa-angle-right mm-arrow-right arrow-active"></i>
                    <i class="fas fa-angle-down mm-arrow-right arrow-hover"></i>
                </a>
            @else
                <a @lm_attrs($item->link) class="nav-link" @lm_endattrs href="{!! $item->url() !!}">
                    {!! $item->title !!}
                </a>
            @endif
        @else
            <span class="navbar-text">{!! $item->title !!}</span>
        @endif
        @if($hasChildren)
            <ul class="submenu collapse {{ $active != '' ? 'show' : '' }}" id="{{ $targetId }}" data-parent="#mm-sidebar-toggle">
                @include(config('laravel-menu.views.bootstrap-items'), ['items' => $item->children()])
            </ul>
        @endif
    </li>
    @if($item->divider)
        <li{!! Lavary\Menu\Builder::attributes($item->divider) !!}></li>
    @endif
@endforeach
