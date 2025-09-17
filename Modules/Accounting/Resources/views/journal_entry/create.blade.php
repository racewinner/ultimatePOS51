@extends('accounting::layouts.app')
@section('title')
    {{ trans_choice('accounting::lang.add', 1) }} {{ trans_choice('accounting::general.journal', 1) }}
    {{ trans_choice('accounting::lang.entry', 1) }}
@endsection

@section('content')

    @include('accounting::layouts.nav')
    <!-- Content Header (Page header) -->
    @component('accounting::components.section_header')
        @slot('title')
            {{ trans_choice('accounting::general.accounting', 1) }}
        @endslot
        @slot('subtitle')
            {{ trans_choice('accounting::lang.add', 1) }} {{ trans_choice('accounting::general.journal', 1) }}
            {{ trans_choice('accounting::lang.entry', 1) }}
        @endslot
    @endcomponent

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('accounting::components.box')
                    @slot('body')
                        <section class="content">
                            <form method="post" action="{{ url('accounting/journal_entry/store') }}">
                                {{ csrf_field() }}
                                <div class="card card-bordered card-preview">
                                    <div class="card-body">
                                        <div class="row gy-4">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="location_id"
                                                        class="control-label">{{ trans_choice('accounting::lang.business_location', 1) }}</label>
                                                    <select class="form-control @error('location_id') is-invalid @enderror" name="location_id"
                                                        id="location_id" v-model="location_id" required>
                                                        <option value="" disabled selected>{{ trans_choice('accounting::lang.select', 1) }}
                                                        </option>
                                                        @foreach ($business_locations as $key)
                                                            <option value="{{ $key->id }}">{{ $key->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('location_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="currency_id"
                                                        class="control-label">{{ trans_choice('accounting::lang.currency', 1) }}</label>
                                                    <v-select label="currency" :options="currencies" :reduce="currency => currency.id"
                                                        v-model="currency_id">
                                                        <template #search="{attributes, events}">
                                                            <input autocomplete="off" class="vs__search @error('currency_id') is-invalid @enderror"
                                                                :required="!currency_id" v-bind="attributes" v-on="events" />
                                                        </template>
                                                    </v-select>
                                                    <input type="hidden" name="currency_id" v-model="currency_id">
                                                    @error('currency_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="date" class="control-label">{{ trans_choice('accounting::lang.date', 1) }}</label>
                                                    <input type="text" v-model="date" class="form-control datepicker @error('date') is-invalid @enderror"
                                                        name="date" required>
                                                    @error('date')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="payment_type_id" class="control-label">{{ trans_choice('accounting::lang.payment', 1) }}
                                                        {{ trans_choice('accounting::lang.type', 1) }}</label>
                                                    <select class="form-control" name="payment_type_id" id="payment_type_id">
                                                        @foreach ($payment_types as $payment_type)
                                                            <option value="{{ $payment_type->id }}">{{ $payment_type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('payment_type_id')
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                                    @enderror
                                                </div>
                                            </div>

                                            {{-- Dynamic table start --}}

                                            <div class="col-md-12">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <th>{{ trans('accounting::lang.debit') }}</th>
                                                            <th>{{ trans('accounting::lang.credit') }}</th>
                                                            <th>{{ trans('accounting::lang.amount') }}</th>
                                                            <th>{{ trans('accounting::lang.description') }}</th>
                                                            <th>{{ trans_choice('accounting::lang.action', 1) }}</th>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(data, index) in journal_entry_data" :id="index">
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label for="debit"
                                                                            class="control-label">{{ trans_choice('accounting::general.chart_of_account', 1) }}</label>
                                                                        <v-select label="name_with_subtype" :options="chart_of_accounts"
                                                                            :reduce="account => account.id" v-model="journal_entry_data[index].debit">
                                                                            <template #search="{attributes, events}">
                                                                                <input autocomplete="off"
                                                                                    class="vs__search @error('debit') is-invalid @enderror"
                                                                                    v-bind="attributes" v-bind:required="!journal_entry_data[index].debit"
                                                                                    v-on="events" />
                                                                            </template>
                                                                        </v-select>
                                                                        <input type="hidden" :name="`journal_entry_data[${index}][debit]`"
                                                                            v-model="journal_entry_data[index].debit">
                                                                        @error('debit')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label for="credit"
                                                                            class="control-label">{{ trans_choice('accounting::general.chart_of_account', 1) }}</label>
                                                                        <v-select label="name_with_subtype" :options="chart_of_accounts"
                                                                            :reduce="account => account.id" v-model="journal_entry_data[index].credit">
                                                                            <template #search="{attributes, events}">
                                                                                <input autocomplete="off"
                                                                                    class="vs__search @error('credit') is-invalid @enderror"
                                                                                    v-bind="attributes" v-bind:required="!journal_entry_data[index].credit"
                                                                                    v-on="events" />
                                                                            </template>
                                                                        </v-select>
                                                                        <input type="hidden" :name="`journal_entry_data[${index}][credit]`"
                                                                            v-model="journal_entry_data[index].credit">
                                                                        @error('credit')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label for="amount"
                                                                            class="control-label">{{ trans_choice('accounting::lang.amount', 1) }}</label>
                                                                        <input type="number" :name="`journal_entry_data[${index}][amount]`"
                                                                            v-model="journal_entry_data[index].amount" id="amount"
                                                                            class="form-control numeric @error('amount') is-invalid @enderror" required>
                                                                    </div>
                                                                    @error('amount')
                                                                        <span class="invalid-feedback" role="alert">
                                                                            <strong>{{ $message }}</strong>
                                                                        </span>
                                                                    @enderror
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <label for="notes"
                                                                            class="control-label">{{ trans_choice('accounting::lang.note', 2) }}</label>
                                                                        <textarea type="text" :name="`journal_entry_data[${index}][notes]`" v-model="journal_entry_data[index].notes"
                                                                            class="form-control @error('notes') is-invalid @enderror">                                                                                                         </textarea>
                                                                        @error('notes')
                                                                            <span class="invalid-feedback" role="alert">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="btn-group">
                                                                        <div v-if="journal_entry_data.length > 1" class="btn btn-xs btn-danger"
                                                                            @click="remove_row(index)">
                                                                            <i class="fa fa-times"></i>
                                                                        </div>
                                                                        <div v-if="index == journal_entry_data.length - 1" class="btn btn-xs btn-primary"
                                                                            @click="add_row">
                                                                            <i class="fa fa-plus"></i>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr class="bg-grand-total">
                                                                <td colspan="2">
                                                                    {{ trans('sale.total') }}
                                                                </td>
                                                                <td>@{{ number_format(amount_total) }}</td>
                                                                <td colspan="2"></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>

                                            {{-- Dynamic table end --}}
                                        </div>
                                    </div>

                                    <div class="card-footer btn-group float-right">
                                        <a href="{{ url('accounting/journal_entry') }}"
                                            class="btn btn-danger confirm">{{ trans_choice('accounting::lang.cancel', 1) }}</a>
                                        <button type="submit" class="btn btn-primary">{{ trans_choice('accounting::lang.save', 1) }}</button>
                                    </div>
                                </div><!-- .card-preview -->
                            </form>
                        </section>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
    <script>
        var app = new Vue({
            el: "#vue-app",
            data: {
                location_id: "{{ old('location_id') }}",
                currency_id: parseInt("{{ old('currency_id') }}"),
                reference: "{{ old('reference') }}",
                date: "{{ old('date', date('Y-m-d')) }}",
                payment_type_id: "{{ old('payment_type_id') }}",
                receipt: "{{ old('receipt') }}",
                notes: "{{ old('notes') }}",
                currencies: {!! json_encode($currencies) !!},
                chart_of_accounts: {!! json_encode($chart_of_accounts) !!},
                journal_entry_data: {!! json_encode(old('journal_entry_data', []), JSON_NUMERIC_CHECK) !!}
            },

            mounted() {
                this.add_default_row();
            },

            computed: {
                amount_total() {
                    let sum = 0;
                    for (const el of this.journal_entry_data) {
                        const amount = el.amount == '' || isNaN(el.amount) ? 0 : parseInt(el.amount);
                        sum += amount;
                    }
                    return sum;
                }
            },

            methods: {
                add_default_row() {
                    if (this.journal_entry_data.length == 0) {
                        this.add_row();
                    }
                },

                add_row() {
                    this.journal_entry_data.push({
                        debit: '',
                        credit: '',
                        amount: '',
                        notes: '',
                    });
                },

                remove_row(index) {
                    this.journal_entry_data.splice(index, 1);
                },

                number_format(number) {
                    return new Intl.NumberFormat().format(number);
                }
            }
        })
    </script>
@endsection
