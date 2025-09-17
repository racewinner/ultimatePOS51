@extends('accounting::layouts.app')
@section('title')
    {{ trans_choice('accounting::lang.edit', 1) }} {{ trans_choice('accounting::lang.account', 1) }}
@endsection

@section('content')

    @include('accounting::layouts.nav')
    <!-- Content Header (Page header) -->
    @component('accounting::components.section_header')
        @slot('title')
            {{ trans_choice('accounting::general.accounting', 1) }}
        @endslot
        @slot('subtitle')
            {{ trans_choice('accounting::lang.edit', 1) }} {{ trans_choice('accounting::lang.account', 1) }}
        @endslot
    @endcomponent

    <!-- Main content -->
    <section class="content no-print" id="vue-app">
        @can('product.view')
            <div class="row">

                @component('accounting::components.box')
                    @slot('body')
                        <form method="post" action="{{ url('accounting/chart_of_account/' . $chart_of_account->id . '/update') }}" class="">
                            {{ csrf_field() }}
                            <div class="box-body">
                                @if (count($errors) > 0)
                                    <div class="form-group has-feedback">
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label class="control-label" for="name">{{ trans_choice('accounting::lang.name', 1) }}</label>
                                    <input type="text" name="name" class="form-control" placeholder="" value="{{ $chart_of_account->name }}"
                                        id="name" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="account_type">{{ trans_choice('accounting::lang.account', 1) }}
                                        {{ trans_choice('accounting::lang.type', 1) }}</label>
                                    <select class="form-control" name="account_type" id="account_type" required>
                                        <option value="asset" @if ($chart_of_account->account_type == 'asset') selected @endif>
                                            {{ trans_choice('accounting::general.asset', 1) }}</option>
                                        <option value="expense" @if ($chart_of_account->account_type == 'expense') selected @endif>
                                            {{ trans_choice('accounting::general.expense', 1) }}</option>
                                        <option value="income" @if ($chart_of_account->account_type == 'income') selected @endif>
                                            {{ trans_choice('accounting::general.income', 1) }}</option>
                                        <option value="equity" @if ($chart_of_account->account_type == 'equity') selected @endif>
                                            {{ trans_choice('accounting::general.equity', 1) }}</option>
                                        <option value="liability" @if ($chart_of_account->account_type == 'liability') selected @endif>
                                            {{ trans_choice('accounting::general.liability', 1) }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="parent_id">{{ trans_choice('accounting::lang.parent', 1) }}</label>
                                    <select class="form-control select2" name="parent_id" id="parent_id">
                                        <option value="" disabled>{{ trans_choice('accounting::lang.select', 1) }}</option>
                                        @foreach ($chart_of_accounts as $key)
                                            <option value="{{ $key->id }}" @if ($chart_of_account->parent_id == $key->id) selected @endif>{{ $key->name }}
                                            </option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="gl_code"> {{ trans_choice('accounting::general.gl_code', 1) }}</label>
                                    <input type="text" name="gl_code" class="form-control" placeholder="" value="{{ $chart_of_account->gl_code }}"
                                        id="gl_code" required>
                                </div>
                                <div class="form-group">
                                    <label class="control-label"
                                        for="allow_manual">{{ trans_choice('accounting::general.manual_entries_allowed', 1) }}</label>
                                    <select class="form-control" name="allow_manual" id="allow_manual" required>
                                        <option value="1" @if ($chart_of_account->allow_manual == 1) selected @endif>
                                            {{ trans_choice('accounting::lang.yes', 1) }}
                                        </option>
                                        <option value="0" @if ($chart_of_account->allow_manual == 0) selected @endif>
                                            {{ trans_choice('accounting::lang.no', 1) }}
                                        </option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="active">{{ trans_choice('accounting::lang.active', 1) }}</label>
                                    <select class="form-control" name="active" id="active" required>
                                        <option value="1" @if ($chart_of_account->active == 1) selected @endif>
                                            {{ trans_choice('accounting::lang.yes', 1) }}</option>
                                        <option value="0" @if ($chart_of_account->active == 0) selected @endif>
                                            {{ trans_choice('accounting::lang.no', 1) }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="control-label" for="notes">{{ trans_choice('accounting::lang.note', 2) }}</label>
                                    <textarea name="notes" class="form-control" id="notes">{{ $chart_of_account->notes }}</textarea>
                                </div>
                            </div>
                            <!-- /.box-body -->
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary pull-right">{{ trans_choice('accounting::lang.save', 1) }}</button>
                            </div>
                        </form>
                    @endslot
                @endcomponent

            </div>
        @endcan
    </section>

@stop
@section('javascript')
@endsection
