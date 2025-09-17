<table style="width:100%; color: #000000 !important;">
	<thead>
		<tr>
			<td>
				<p class="text-right">
					<small class="text-muted-imp">
						@if(!empty($receipt_details->invoice_no_prefix))
							{!! $receipt_details->invoice_no_prefix !!}
						@endif
						{{$receipt_details->invoice_no}}
					</small>
				</p>
			</td>
		</tr>
	</thead>

	<tbody>
		<tr>
			<td class="text-center" style="line-height: 15px !important; padding-bottom: 10px !important">
				@if(!empty($receipt_details->invoice_heading))
					<h2 style="font-weight: bold; font-size: 35px !important; margin-top: 10px;">{!! $receipt_details->invoice_heading !!}</h2>
				@endif
			</td>
		</tr>

		<tr>
			<td>

				<!-- business information here -->
				<div class="row invoice-info">
					<div class="col-md-6 invoice-col width-50">
						<div class="text-right font-23">
							{{$receipt_details->invoice_no}}
						</div>

						<!-- Date-->
						@if(!empty($receipt_details->date_label))
							<div class="text-right font-23 ">
								<span class="pull-left">
									{{$receipt_details->date_label}}
								</span>
								{{$receipt_details->invoice_date}}
							</div>
						@endif

						<div class="word-wrap">
							@if(!empty($receipt_details->customer_label))
								<b>{{ $receipt_details->customer_label }}</b><br/>
							@endif

							<!-- customer info -->
							@if(!empty($receipt_details->customer_info))
								{!! $receipt_details->customer_info !!}
							@endif
							@if(!empty($receipt_details->client_id_label))
								<br/>
								<strong>{{ $receipt_details->client_id_label }}</strong> {{ $receipt_details->client_id }}
							@endif
						</div>
					</div>

					<div class="col-md-6 invoice-col width-50 ">
						@if(empty($receipt_details->letter_head))
						<!-- Shop & Location Name  -->
							<span>
								@if(!empty($receipt_details->display_name))
									{{$receipt_details->display_name}}
									<br/>
								@endif

								@if(!empty($receipt_details->address))
									{!! $receipt_details->address !!}
								@endif

								@if(!empty($receipt_details->contact))
									<br/>{!! $receipt_details->contact !!}
								@endif

								@if(!empty($receipt_details->website))
									<br/>{{ $receipt_details->website }}
								@endif

								@if(!empty($receipt_details->location_custom_fields))
									<br/>{{ $receipt_details->location_custom_fields }}
								@endif
							</span>
						@endif
					</div>
				</div>
				<div class="row  mt-5">
					<div class="col-xs-12">
						<table class="table table-bordered table-no-top-cell-border table-slim mb-12">
							<thead>
								<tr style="background-color: #357ca5 !important; color: white !important; font-size: 20px !important" class="table-no-side-cell-border table-no-top-cell-border text-center">
									<td style="background-color: #357ca5 !important; color: white !important; width: 5% !important">#</td>
									<td style="background-color: #357ca5 !important; color: white !important; width: 80% !important">
										{{$receipt_details->table_product_label}}
									</td>
									<td style="background-color: #357ca5 !important; color: white !important; width: 15% !important;">
										{{$receipt_details->table_qty_label}}
									</td>
								</tr>
							</thead>
							<tbody>
								@foreach($receipt_details->lines as $line)
									<tr>
										<td class="text-center">
											{{$loop->iteration}}
										</td>
										<td>
											@if(!empty($line['image']))
												<img src="{{$line['image']}}" alt="Image" width="50" style="float: left; margin-right: 8px;">
											@endif
											{{$line['name']}} {{$line['product_variation']}} {{$line['variation']}} 
											@if(!empty($line['sub_sku'])), {{$line['sub_sku']}} @endif @if(!empty($line['brand'])), {{$line['brand']}} @endif
											@if(!empty($line['product_custom_fields'])), {{$line['product_custom_fields']}} @endif
											@if(!empty($line['product_description']))
												<small>
													{!!$line['product_description']!!}
												</small>
											@endif
											@if(!empty($line['lot_number']))<br> {{$line['lot_number_label']}}:  {{$line['lot_number']}} @endif 
											@if(!empty($line['product_expiry'])), {{$line['product_expiry_label']}}:  {{$line['product_expiry']}} @endif 

											@if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif @if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}} </small>@endif
											@if(!empty($line['warranty_description'])) <small> {{$line['warranty_description'] ?? ''}}</small>@endif

											@if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
											<br><small>
												1 {{$line['units']}} = {{$line['base_unit_multiplier']}} {{$line['base_unit_name']}} <br>
											</small>
											@endif
										</td>

										<td class="text-right">
											{{$line['quantity']}} {{$line['units']}}

											@if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
											<br><small>
												{{$line['quantity']}} x {{$line['base_unit_multiplier']}} = {{$line['orig_quantity']}} {{$line['base_unit_name']}}
											</small>
											@endif
										</td>
									</tr>
									@if(!empty($line['modifiers']))
										@foreach($line['modifiers'] as $modifier)
											<tr>
												<td class="text-center">
													&nbsp;
												</td>
												<td>
													{{$modifier['name']}} {{$modifier['variation']}} 
													@if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif 
													@if(!empty($modifier['sell_line_note']))({!!$modifier['sell_line_note']!!}) @endif 
												</td>
												<td class="text-right">
													{{$modifier['quantity']}} {{$modifier['units']}}
												</td>
											</tr>
										@endforeach
									@endif
								@endforeach

								@php
									$lines = count($receipt_details->lines);
								@endphp

								@for ($i = $lines; $i < 7; $i++)
									<tr>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
										<td>&nbsp;</td>
									</tr>
								@endfor
							</tbody>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>