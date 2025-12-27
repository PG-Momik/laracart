<x-mail::message>
    # Daily Sales Summary
    Date: {{ now()->format('M d, Y') }}

    <x-mail::panel>
        ### Key Performance Indicators
        - **Total Revenue:** ${{ number_format($stats['total_revenue'], 2) }}
        - **Orders Processed:** {{ $stats['order_count'] }}
        - **Items Sold:** {{ $stats['items_count'] }}
    </x-mail::panel>

    ### Top Selling Categories
    @foreach($stats['top_categories'] as $category => $count)
        - **{{ $category }}:** {{ $count }} units
    @endforeach

    <x-mail::button :url="config('app.url') . '/dashboard'">
        View Full Dashboard
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>