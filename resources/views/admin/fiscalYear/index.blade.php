@extends('layouts.master')
@section('title',__('index.fiscal_years'))
@section('action',__('index.lists'))
@section('button')
    @can('create_award_type')
        <a href="{{ route('admin.fiscal_year.create')}}">
            <button class="btn btn-primary">
                <i class="link-icon" data-feather="plus"></i>@lang('index.add_fiscal_year')
            </button>
        </a>
    @endcan
@endsection

@section('main-content')

    <section class="content">
        @include('admin.section.flash_message')
        @include('admin.fiscalYear.common.breadcrumb')
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="dataTableExample" class="table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('index.title')</th>
                            <th>@lang('index.start_date')</th>
                            <th>@lang('index.end_date')</th>
{{--                            @canany(['update_award_type','delete_award_type'])--}}
                                <th class="text-center">@lang('index.action')</th>
{{--                            @endcanany--}}
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                        @forelse($fiscalYears as $value)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ucfirst($value->year)}}</td>
                                <td>
                                    {{\App\Helpers\AppHelper::formatDateForView($value->start_date)}}
                                </td>
                                <td>
                                    {{\App\Helpers\AppHelper::formatDateForView($value->end_date)}}
                                </td>

                                <td class="text-center">
                                    <ul class="d-flex list-unstyled mb-0 justify-content-center">
                                        @can('update_award_type')
                                            <li class="me-2">
                                                <a href="{{route('admin.fiscal_year.edit',$value->id)}}" title="@lang('index.edit')">
                                                    <i class="link-icon" data-feather="edit"></i>
                                                </a>
                                            </li>
                                        @endcan

                                        @can('delete_award_type')
                                            <li>
                                                <a class="delete"
                                                   data-href="{{route('admin.fiscal_year.delete',$value->id)}}" title="@lang('index.delete')">
                                                    <i class="link-icon"  data-feather="delete"></i>
                                                </a>
                                            </li>
                                        @endcan
                                    </ul>
                                </td>

                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="text-center"><b>@lang('index.no_records_found')</b></p>
                                </td>
                            </tr>
                        @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    @include('admin.fiscalYear.common.scripts')
@endsection






