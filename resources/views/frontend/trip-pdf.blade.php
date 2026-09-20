<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $trip->title }}</title>
    <style>
        @page {
            margin: 120px 40px 90px 40px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #1e293b;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 80px;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        header table {
            width: 100%;
        }

        header .company {
            font-size: 9px;
            color: #64748b;
        }

        header .trip-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
        }

        footer {
            position: fixed;
            bottom: -70px;
            left: 0;
            right: 0;
            height: 60px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            font-size: 9px;
            color: #64748b;
            text-align: center;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 4px;
            color: #0f172a;
        }

        h2 {
            font-size: 15px;
            margin: 22px 0 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
        }

        p {
            line-height: 1.5;
        }

        ul {
            margin: 0 0 0 16px;
            padding: 0;
        }

        li {
            margin-bottom: 4px;
        }

        table.facts td {
            padding: 3px 24px 3px 0;
            font-size: 11px;
        }

        table.facts .label {
            display: block;
            font-size: 8px;
            text-transform: uppercase;
            color: #94a3b8;
        }

        table.itinerary {
            width: 100%;
            border-collapse: collapse;
        }

        table.itinerary th,
        table.itinerary td {
            border: 1px solid #e2e8f0;
            padding: 6px 8px;
            font-size: 10px;
            text-align: left;
            vertical-align: top;
        }

        table.itinerary th {
            background: #f8fafc;
        }

        table.two-col {
            width: 100%;
        }

        table.two-col td {
            width: 50%;
            vertical-align: top;
        }

        table.two-col td.right {
            padding-left: 16px;
        }

        .faq-question {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .faq-answer {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <table>
        <tr>
            <td style="width: 70px;">
                <img src="{{ public_path('images/logo.webp') }}" style="height: 40px;">
            </td>
            <td style="text-align: right;">
                <div class="trip-name">{{ $trip->title }}</div>
                <div class="company">{{ config('app.name') }}</div>
            </td>
        </tr>
    </table>
</header>

<footer>
    {{ config('app.name') }}
    @if ($generalSettings->phone)
        &middot; Tel: {{ $generalSettings->phone }}
    @endif
    @if ($generalSettings->whatsapp_number)
        &middot; WhatsApp: {{ $generalSettings->whatsapp_number }}
    @endif
</footer>

<h1>{{ $trip->title }}</h1>

@if ($blurb = $trip->short_description ?: $trip->defaultMetaDescription())
    <p>{{ $blurb }}</p>
@endif

<table class="facts">
    <tr>
        @if ($trip->duration)
            <td><span class="label">Duration</span>{{ $trip->duration }}</td>
        @endif
        @if ($trip->difficulty)
            <td><span class="label">Difficulty</span>{{ $trip->difficulty->getLabel() }}</td>
        @endif
        @if ($trip->altitude)
            <td><span class="label">Max altitude</span>{{ $trip->altitude }}</td>
        @endif
        @if ($trip->price)
            <td><span class="label">From</span>${{ number_format($trip->price, 0) }} / person</td>
        @endif
    </tr>
</table>

@if ($trip->highlights)
    <h2>Highlights</h2>
    <ul>
        @foreach ($trip->highlights as $highlight)
            <li>{{ $highlight }}</li>
        @endforeach
    </ul>
@endif

@if ($trip->detailed_itinerary)
    <h2>Itinerary</h2>
    <table class="itinerary">
        <thead>
        <tr>
            <th>Day</th>
            <th>Activity</th>
            <th>Walking Hr</th>
            <th>Max Altitude</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($trip->detailed_itinerary as $day)
            <tr>
                <td>{{ $day['day'] ?? '' }}</td>
                <td>{{ $day['title'] ?? '' }}</td>
                <td>{{ $day['duration'] ?? '—' }}</td>
                <td>{{ $day['altitude'] ?? '—' }}</td>
            </tr>
            @if ($day['description'] ?? null)
                <tr>
                    <td></td>
                    <td colspan="3">{{ $day['description'] }}</td>
                </tr>
            @endif
        @endforeach
        </tbody>
    </table>
@endif

@if ($trip->overview)
    <h2>Trip details</h2>
    {!! $trip->overview !!}
@endif

@if ($trip->includes || $trip->excludes)
    <h2>Includes &amp; Excludes</h2>
    <table class="two-col">
        <tr>
            <td>
                @if ($trip->includes)
                    <strong>Includes</strong>
                    <ul>
                        @foreach ($trip->includes as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
            <td class="right">
                @if ($trip->excludes)
                    <strong>Excludes</strong>
                    <ul>
                        @foreach ($trip->excludes as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
        </tr>
    </table>
@endif

@if ($trip->checklist || $trip->know_before_you_go || $trip->do_not_do)
    <h2>Practical Info</h2>

    @if ($trip->know_before_you_go)
        {!! $trip->know_before_you_go !!}
    @endif

    <table class="two-col">
        <tr>
            <td>
                @if ($trip->checklist)
                    <strong>Packing Checklist</strong>
                    <ul>
                        @foreach ($trip->checklist as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
            <td class="right">
                @if ($trip->do_not_do)
                    <strong>Do Not Do</strong>
                    <ul>
                        @foreach ($trip->do_not_do as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @endif
            </td>
        </tr>
    </table>
@endif

@if ($trip->faq)
    <h2>Frequently Asked Questions</h2>
    @foreach ($trip->faq as $entry)
        <p class="faq-question">{{ $entry['question'] ?? '' }}</p>
        <p class="faq-answer">{{ $entry['answer'] ?? '' }}</p>
    @endforeach
@endif

</body>
</html>
