<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1"
                    aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('accounting/dashboard') }}">
                    <i class="fas fa-book"></i>
                    {{ __('accounting::lang.accounting') }}
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'chart_of_account') class="active" @endif>
                        <a href="{{ url('accounting/chart_of_account') }}">
                            @lang('accounting::lang.view_charts_of_accounts')
                        </a>
                    </li>



                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'journal_entry') class="active" @endif>
                        <a href="{{ url('accounting/journal_entry') }}">
                            @lang('accounting::lang.journal_of_entries')
                        </a>
                    </li>



                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'transfers') class="active" @endif>
                        <a href="{{ url('accounting/transfers') }}">
                            {{ trans_choice('accounting::lang.transfer', 2) }}
                        </a>
                    </li>


                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'transactions')) class="active" @endif>
                        <a href="{{ url('accounting/transactions/sales?type=payment') }}">@lang('accounting::lang.transactions')</a>
                    </li>


                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'reconcile') class="active" @endif>
                        <a href="{{ url('accounting/reconcile') }}">@lang('accounting::lang.reconcile')</a>
                    </li>



                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'budget') class="active" @endif>
                        <a
                            href="{{ url('accounting/budget?view=monthly&year=' . get_financial_year()) }}">{{ trans('accounting::general.budgeting') }}</a>
                    </li>


                    @if (auth()->user()->can('brand.view'))
                        <li @if (request()->segment(1) == 'report') class="active" @endif>
                            <a href="{{ url('report/accounting') }}">@lang('accounting::lang.reports')</a>
                        </li>
                    @endif


                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'settings') class="active" @endif>
                        <a href="{{ url('accounting/settings/account_subtypes') }}">@lang('accounting::lang.settings')</a>
                    </li>

                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>
