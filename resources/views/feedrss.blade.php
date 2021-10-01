<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <atom:link href="{{ url($source['home_url']) }}" rel="self" type="application/rss+xml" />
        <title><![CDATA[{{ $source['name'] }}]]></title>
        <link><![CDATA[{{ url($source['home_url']) }}]]></link>
        <description><![CDATA[{{ $source['name'] }}]]></description>
        <pubDate>{{ $lastUpdated }}</pubDate>
        @foreach($items as $item)
            <item>
                <title><![CDATA[{{ $item['title'] }}]]></title>
                <link>{!! $item['url'] !!}</link>
                <description><![CDATA[{!! $item['description'] !!}]]></description>
                @if(! empty($item['guid']))
                <guid>{!! $item['guid'] !!}</guid>
                @else
                <guid>{!! $item['url'] !!}</guid>
                @endif
                <pubDate>{{ $item['pubDate']->format('Y-m-d H:i:s O') }}</pubDate>
            </item>
        @endforeach
    </channel>
</rss>
