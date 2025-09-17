@extends('accounting::layouts.app')
@section('title')
    {{ ucfirst(Request::get('view')) }}
    {{ trans_choice('accounting::general.budget', 1) }} - {{ $financial_year }}
@endsection

@section('content')

    @include('accounting::layouts.nav')
    <!-- Content Header (Page header) -->
    @component('accounting::components.section_header')
        @slot('title')
            {{ ucfirst(Request::get('view')) }}
            {{ trans_choice('accounting::general.budget', 1) }} - {{ $financial_year }}
        @endslot
    @endcomponent

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        <div class="row">
            @component('accounting::components.box')
                @slot('header')
                    <div class="box-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#settingsModal">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>

                    <div class="col-md-4">
                        <form action="{{ Request::url() }}" id="financial_year_form">
                            <input type="hidden" name="view" value="{{ Request::get('view') }}">
                            <div class="form-group">
                                <label for="year">{{ trans_choice('accounting::general.financial_year', 1) }}</label>
                                <input type="text" class="form-control year-datepicker" name="year" id="financial_year" v-model="financial_year">
                            </div>
                        </form>
                    </div>
                @endslot

                @slot('body')
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="@if (Request::get('view') == 'monthly') active @endif">
                                <a href="{{ request()->view != 'monthly' ? $url->monthly : '#' }}" aria-expanded="true">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    {{ trans_choice('accounting::lang.monthly', 1) }}
                                </a>
                            </li>
                            <li class="@if (Request::get('view') == 'quarterly') active @endif">
                                <a href="{{ request()->view != 'quarterly' ? $url->quarterly : '#' }}" aria-expanded="true">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    {{ trans_choice('accounting::lang.quarterly', 1) }}
                                </a>
                            </li>
                            <li class="@if (Request::get('view') == 'yearly') active @endif">
                                <a href="{{ request()->view != 'yearly' ? $url->yearly : '#' }}" aria-expanded="true">
                                    <i class="fas fa-calendar" aria-hidden="true"></i>
                                    {{ trans_choice('accounting::lang.yearly', 2) }}
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active">

                                {{-- Conditional views --}}

                                @if (!empty($chart_of_accounts) && count($chart_of_accounts) > 0)
                                    @switch(Request::get('view'))
                                        @case('monthly')
                                            @include('accounting::budget.partials.monthly_view')
                                        @break

                                        @case('quarterly')
                                            @include('accounting::budget.partials.quarterly_view')
                                        @break

                                        @case('yearly')
                                            @include('accounting::budget.partials.yearly_view')
                                        @break

                                        @default
                                            <div class="alert alert-info text-center">
                                                {{ trans('accounting::lang.an_error_occurred') }}
                                            </div>
                                    @endswitch
                                @else
                                    <div class="alert alert-info text-center">
                                        {{ trans('accounting::lang.chart_of_account_needed_to_budget') }}
                                        {{ trans('accounting::lang.add') }}
                                        <a href="{{ url('accounting/chart_of_account/create') }}">
                                            {{ trans('accounting::lang.here') }}
                                        </a>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>

                    @include('accounting::budget.modals.budget_settings_modal')
                @endslot
            @endcomponent
        </div>
    </section>
@stop

@section('javascript')
    <script>
        $(function() {
            $('.apply_for_all').on('click', function() {
                const id = $(this).attr('input_id')
                const value = $('#' + id).val();
                const view = "{{ Request::get('view') }}";

                switch (view) {
                    case 'monthly':
                        $("[id^=month_]").val(value);
                        break;

                    case 'quarterly':
                        $("[id^=quarter_]").val(value);
                        break;

                    default:
                        break;
                }

            });

            $('#financial_year').on('change', function() {
                document.getElementById('financial_year_form').submit();
            })
        })

        new Vue({
            el: '#vue-app',
            data() {
                return {
                    financial_year_start: "{{ $financial_year_start }}",
                    financial_year: "{{ $financial_year }}",
                    business_id: "{{ session('business.id') }}",
                    view: "{{ Request::get('view') }}",
                    chart_of_account_id: '',
                    chart_of_accounts: {!! json_encode($chart_of_accounts) !!},
                    months: [],
                    quarters: [],
                    yearly_budget: 0,
                    eliminate_decimals: true
                }
            },

            computed: {
                chart_of_account() {
                    const chart_of_account = this.chart_of_accounts.find(account => account.id == this.chart_of_account_id);
                    const default_chart_of_account = {
                        name: ''
                    };

                    return chart_of_account ?
                        chart_of_account :
                        default_chart_of_account;
                }
            },

            methods: {
                onClickEditBudget(e) {
                    this.chart_of_account_id = e.target.getAttribute('chart_of_account_id');

                    switch (this.view) {
                        case 'monthly':
                            !this.chart_of_account || this.chart_of_account.budget == null ?
                                this.setMonthlyBudget({}) :
                                this.setMonthlyBudget(this.chart_of_account.budget);
                            break;

                        case 'quarterly':
                            !this.chart_of_account || this.chart_of_account.budget == null ?
                                this.setQuarterlyBudget({}) :
                                this.setQuarterlyBudget(this.chart_of_account.budget);
                            break;

                        case 'yearly':
                            this.yearly_budget = !this.chart_of_account || this.chart_of_account.budget == null ?
                                0 :
                                this.chart_of_account.budget.yearly;
                            break;

                        default:
                            throw 'No view type found'
                            break;
                    }
                },

                setMonthlyBudget(budget) {
                    if (!Object.keys(budget).length > 0) {
                        for (let i = 1; i <= 12; i++) {
                            this.months[i] = 0;
                        }

                    } else {
                        for (let i = 1; i <= 12; i++) {
                            const month = `month_${i}`;
                            this.months[i] = budget[month];
                        }
                    }
                },

                setQuarterlyBudget(budget) {
                    if (!Object.keys(budget).length > 0) {
                        for (let i = 1; i <= 4; i++) {
                            this.quarters[i] = 0;
                        }

                    } else {
                        for (let i = 1; i <= 4; i++) {
                            this.quarters[i] = budget.quarterly[i];
                        }
                    }
                },
            },
        });
    </script>
@endsection
