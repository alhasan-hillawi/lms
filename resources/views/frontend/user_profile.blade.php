@extends('frontend.user_dashboard')
@section('userdashboard')


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>



<div class="container-fluid">
            <div class="breadcrumb-content d-flex flex-wrap align-items-center justify-content-between mb-5">
                <div class="media media-card align-items-center">
                    <div class="media-img media--img media-img-md rounded-full">
                    <img src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}" class="user-img" alt="user avatar">
                    </div>
                    <div class="media-body">
                        <h2 class="section__title fs-30">{{$profileData->name}}</h2>
                        <h6>{{$profileData->email}}</h6>                       

                        
                    </div><!-- end media-body -->
                </div><!-- end media -->
            
            </div><!-- end breadcrumb-content -->
            <div class="section-block mb-5"></div>
         

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                    <div class="setting-body">
                        <h3 class="fs-17 font-weight-semi-bold pb-4">Edit Profile</h3>
                        
                        
                    


                        <form method="POST" action="{{ route('user.profile.store') }}"  enctype="multipart/form-data">
                        @csrf
                            <div class="input-box col-lg-6">
                                <label class="label-text">Name</label>
                                <div class="form-group">
                                    <input class="form-control form--control"  type="text" name="name" value="{{ $profileData->name }}" >
                                    <span class="la la-user input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                            <div class="input-box col-lg-6">
                                <label class="label-text">Phone</label>
                                <div class="form-group">
                                    <input class="form-control form--control" type="text" name="phone" value="{{ $profileData->phone }}">
                                    <span class="la la-phone input-icon"></span>
                                </div>
                            </div><!-- end input-box -->
                          
                            <div class="input-box col-lg-6">
                                <label class="label-text">Email Address</label>
                                <div class="form-group">
                                    <input class="form-control form--control" type="email" name="email" value="{{ $profileData->email }}" readonly>
                                    <span class="la la-envelope input-icon"></span>
                                </div>
                            </div><!-- end input-box -->


                            <div class="input-box col-lg-6">
                                    <h6 class="mb-0">Photo</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <img id="showImage" src="{{ (!empty($profileData->photo)) ? url('upload/admin_images/'.$profileData->photo) : url('upload/no_image.jpg') }}"  alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                                </div>
                                </div>

                          
                                <div class="input-box col-lg-6">
                                    <input class="form-control"  name="photo" type="file" id="image">
                                </div>

                            </div>

                            <div class="input-box col-lg-6">
                                <label class="label-text">Address </label>
                                <div class="form-group">
                                    <input class="form-control form--control"  type="text" name="address" value="{{ $profileData->address }}">
                                    <span class="la la-home input-icon"></span>
                                </div>
                            </div><!-- end input-box -->

                          
                            

                            <div class="input-box col-lg-12 py-2">
                                <button type="submit" class="btn theme-btn">Save Changes</button>
                            </div><!-- end input-box -->
                        </form>



                    </div><!-- end setting-body -->
                </div><!-- end tab-pane -->



                </div><!-- end setting-body -->
                </div><!-- end tab-pane -->


                <div class="section-block mb-5"></div>

               
                
             
        </div><!-- end container-fluid -->



        </div><!-- end container-fluid -->




        <script type="text/javascript">
	$(document).ready(function(){
		$('#image').change(function(e){
			var reader = new FileReader();
			reader.onload = function(e){
				$('#showImage').attr('src',e.target.result);
			}
			reader.readAsDataURL(e.target.files['0']);
		});
	});


</script>

        @endsection