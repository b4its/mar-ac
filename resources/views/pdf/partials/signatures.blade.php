<div class="ttd-grid">
    @foreach ($blocks as $block)
        <div class="ttd">
            <div>{!! $block['label'] !!}</div>
            <div class="nama">{{ $block['name'] }}</div>
            @if (! empty($block['nip']))
                <div class="nip">NIP. {{ $block['nip'] }}</div>
            @endif
        </div>
    @endforeach
</div>
