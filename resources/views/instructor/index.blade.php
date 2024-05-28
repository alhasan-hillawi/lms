@extends('instructor.instructor_dashboard')
@section('instructor')

@php
  $id = Auth::user()->id;
  $instructorId = App\Models\User::find($id);
  $status = $instructorId->status;
@endphp

<div class="page-content">
  
  @if ($status === '1')
  <h4>Instructor Account Is <span class="text-success">Active</span> </h4>
  @else   
  <h4>Instructor Account Is <span class="text-danger">InActive</span> </h4> 
 <p class="text-danger"><b> Plz wait admin will check and approve your account</b> </p>
  @endif



@endsection