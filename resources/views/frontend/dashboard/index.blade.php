@extends('frontend.dashboard.user_dashboard')
@section('userdashboard')

@php
    $id = Auth::user()->id;
    $profileData = App\Models\User::find($id);
@endphp

<div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between mb-5">
    <div class="media media-card align-items-center">
        <div class="media-img media--img media-img-md rounded-full">
            <img class="rounded-full" src="{{ (!empty($profileData->photo)) ? url('upload/user_images/'.$profileData->photo) : url('upload/no_image.jpg')}}" alt="Student thumbnail image">
        </div>
        <div class="media-body">
            <h2 class="section__title fs-30">Hello, {{ $profileData->name }}</h2>
            <div class="rating-wrap d-flex align-items-center pt-2">
                <div class="review-stars">
                    <span class="rating-number">4.4</span>
                    <span class="la la-star"></span>
                    <span class="la la-star"></span>
                    <span class="la la-star"></span>
                    <span class="la la-star"></span>
                    <span class="la la-star-o"></span>
                </div>
                <span class="rating-total pl-1">(20,230)</span>
            </div><!-- end rating-wrap -->
        </div><!-- end media-body -->
    </div><!-- end media -->
   
</div><!-- end breadcrumb-content -->
<div class="section-block mb-5"></div>
<div class="dashboard-heading mb-5">
    <h3 class="fs-22 font-weight-semi-bold">Dashboard</h3>
</div>


@endsection