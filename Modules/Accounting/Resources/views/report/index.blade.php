@extends('accounting::layouts.app')
@section('title')
    {{ trans_choice('accounting::general.accounting', 1) }} {{ trans_choice('accounting::lang.report', 2) }}
@endsection

@section('content')

    @include('accounting::layouts.nav')
    <!-- Content Header (Page header) -->
    @component('accounting::components.section_header')
        @slot('title')
            {{ trans_choice('accounting::general.accounting', 1) }} {{ trans_choice('accounting::lang.report', 2) }}
        @endslot
    @endcomponent

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">

            @foreach ($reports as $report)
                @component('accounting::components.box')
                    @slot('title')
                        {{ $report->section_title }}
                    @endslot

                    @slot('body')
                        <div class="row">
                            @foreach ($report->items as $item)
                                <div class="col-md-6" style="margin-bottom: 10px">
                                    <h4>
                                        <a href="{{ $item->url }}">{{ $item->title }}</a>
                                    </h4>
                                    <div>{{ $item->description }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endslot
                @endcomponent
            @endforeach

        </div>
    </section>

@stop
@section('javascript')
@endsection
