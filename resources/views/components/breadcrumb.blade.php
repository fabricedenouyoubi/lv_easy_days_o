<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
        @if($showHome)
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">Accueil</a>
            </li>
        @endif
        
        @foreach($items as $item)
            @if($loop->last)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $item['label'] }}
                </li>
            @else
                <li class="breadcrumb-item">
                    @if(isset($item['url']))
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        {{ $item['label'] }}
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>