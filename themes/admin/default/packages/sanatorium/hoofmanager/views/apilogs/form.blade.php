@extends('layouts/default')

{{-- Page title --}}
@section('title')
@parent
{{{ trans("action.{$mode}") }}} {{ trans('sanatorium/hoofmanager::apilogs/common.title') }}
@stop

{{-- Queue assets --}}
{{ Asset::queue('validate', 'platform/js/validate.js', 'jquery') }}

{{-- Inline scripts --}}
@section('scripts')
@parent
@stop

{{-- Inline styles --}}
@section('styles')
@parent
@stop

{{-- Page content --}}
@section('page')

<section class="panel panel-default panel-tabs">

	{{-- Form --}}
	<form id="hoofmanager-form" action="{{ request()->fullUrl() }}" role="form" method="post" data-parsley-validate>

		{{-- Form: CSRF Token --}}
		<input type="hidden" name="_token" value="{{ csrf_token() }}">

		<header class="panel-heading">

			<nav class="navbar navbar-default navbar-actions">

				<div class="container-fluid">

					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#actions">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>

						<a class="btn btn-navbar-cancel navbar-btn pull-left tip" href="{{ route('admin.sanatorium.hoofmanager.apilogs.all') }}" data-toggle="tooltip" data-original-title="{{{ trans('action.cancel') }}}">
							<i class="fa fa-reply"></i> <span class="visible-xs-inline">{{{ trans('action.cancel') }}}</span>
						</a>

						<span class="navbar-brand">{{{ trans("action.{$mode}") }}} <small>{{{ $apilog->exists ? $apilog->id : null }}}</small></span>
					</div>

					{{-- Form: Actions --}}
					<div class="collapse navbar-collapse" id="actions">

						<ul class="nav navbar-nav navbar-right">

							@if ($apilog->exists)
							<li>
								<a href="{{ route('admin.sanatorium.hoofmanager.apilogs.delete', $apilog->id) }}" class="tip" data-action-delete data-toggle="tooltip" data-original-title="{{{ trans('action.delete') }}}" type="delete">
									<i class="fa fa-trash-o"></i> <span class="visible-xs-inline">{{{ trans('action.delete') }}}</span>
								</a>
							</li>
							@endif

							<li>
								<button class="btn btn-primary navbar-btn" data-toggle="tooltip" data-original-title="{{{ trans('action.save') }}}">
									<i class="fa fa-save"></i> <span class="visible-xs-inline">{{{ trans('action.save') }}}</span>
								</button>
							</li>

						</ul>

					</div>

				</div>

			</nav>

		</header>

		<div class="panel-body">

			<div role="tabpanel">

				{{-- Form: Tabs --}}
				<ul class="nav nav-tabs" role="tablist">
					<li class="active" role="presentation"><a href="#general-tab" aria-controls="general-tab" role="tab" data-toggle="tab">{{{ trans('sanatorium/hoofmanager::apilogs/common.tabs.general') }}}</a></li>
					<li role="presentation"><a href="#attributes" aria-controls="attributes" role="tab" data-toggle="tab">{{{ trans('sanatorium/hoofmanager::apilogs/common.tabs.attributes') }}}</a></li>
				</ul>

				<div class="tab-content">

					{{-- Tab: General --}}
					<div role="tabpanel" class="tab-pane fade in active" id="general-tab">

						<fieldset>

							<div class="row">

								<div class="form-group{{ Alert::onForm('call', ' has-error') }}">

									<label for="call" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.call_help') }}}"></i>
										{{{ trans('sanatorium/hoofmanager::apilogs/model.general.call') }}}
									</label>

									<textarea class="form-control" name="call" id="call" placeholder="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.call') }}}">{{{ input()->old('call', $apilog->call) }}}</textarea>

									<span class="help-block">{{{ Alert::onForm('call') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('data', ' has-error') }}">

									<label for="data" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.data_help') }}}"></i>
										{{{ trans('sanatorium/hoofmanager::apilogs/model.general.data') }}}
									</label>

									<textarea class="form-control" name="data" id="data" placeholder="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.data') }}}">{{{ input()->old('data', $apilog->data) }}}</textarea>

									<span class="help-block">{{{ Alert::onForm('data') }}}</span>

								</div>

								<div class="form-group{{ Alert::onForm('source', ' has-error') }}">

									<label for="source" class="control-label">
										<i class="fa fa-info-circle" data-toggle="popover" data-content="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.source_help') }}}"></i>
										{{{ trans('sanatorium/hoofmanager::apilogs/model.general.source') }}}
									</label>

									<textarea class="form-control" name="source" id="source" placeholder="{{{ trans('sanatorium/hoofmanager::apilogs/model.general.source') }}}">{{{ input()->old('source', $apilog->source) }}}</textarea>

									<span class="help-block">{{{ Alert::onForm('source') }}}</span>

								</div>


							</div>

						</fieldset>

					</div>

					{{-- Tab: Attributes --}}
					<div role="tabpanel" class="tab-pane fade" id="attributes">
						@attributes($apilog)
					</div>

				</div>

			</div>

		</div>

	</form>

</section>
@stop
