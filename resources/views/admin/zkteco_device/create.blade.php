@extends('layouts.master')

@section('title', 'إضافة جهاز بصمة')

@section('button')
    <a href="{{ route('admin.zkteco-devices.index') }}">
        <button class="btn btn-sm btn-secondary"><i class="link-icon" data-feather="arrow-left"></i> رجوع</button>
    </a>
@endsection

@section('main-content')
    <section class="content">
        @include('admin.section.flash_message')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">إضافة جهاز جديد</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.zkteco-devices.store') }}" method="POST">
                    @csrf
                    @include('admin.zkteco_device.form')
                </form>
            </div>
        </div>
    </section>
@endsection