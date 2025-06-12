@extends('adminlte::page')

@section('title', 'Profile')

@section('content_header')
    <h1 class="m-0 text-dark">Profile</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            {{-- Update Profile Information Card --}}
            <div class="card">
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Update Password Card --}}
            <div class="card">
                <div class="card-body">
                     @include('profile.partials.update-password-form')
                </div>
            </div>
            
            {{-- Delete Account Card --}}
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Delete Account</h3>
                </div>
                <div class="card-body">
                     @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@stop
