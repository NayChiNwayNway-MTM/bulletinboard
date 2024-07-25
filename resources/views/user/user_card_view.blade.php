@extends('layouts.nav')
@section('content')
<section class="background">
<header><h3 class="px-5 margin">User List</h3></header>
<div class="container ">

      <form action="" method="get" class="" id="search_form">
        @csrf 
        @if(session('success'))
            <div class="alert alert-success" id="alert">
                {{ session('success') }}
            </div>
        @endif
          <div class="alert" role="alert" id="response">
          </div>

          <div class="row g-3 align-items-center">
            <div class="col-auto">
                <label for="search_name" class="col-form-label text-end">Name:</label>
            </div>
            <div class="col">
                <input type="text" name="name" class="form-control" id="search_name" value="{{ request('name') }}">
            </div>
            <div class="col-auto">
                <label for="search_email" class="col-form-label text-end">Email:</label>
            </div>
            <div class="col">
                <input type="email" name="email" class="form-control" id="search_email" value="{{ request('email') }}">
            </div>
            <div class="col-auto">
                <label for="start_date" class="col-form-label text-end">From:</label>
            </div>
            <div class="col">
                <input type="date" name="from" class="form-control" id="start_date" value="{{ request('from') }}">
            </div>
            <div class="col-auto">
                <label for="end_date" class="col-form-label text-end">To:</label>
            </div>
            <div class="col">
                <input type="date" name="to" class="form-control" id="end_date" value="{{ request('to') }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary" id="search_user">Search</button>
            </div>
            <div class="col-auto">
                <a href="{{route('user')}}" class="btn btn-primary" id="designView">Design</a>
            </div>
          </div>
        <div class="row mt-3">
          <div class="col-md-1">
            <label for="page_size">Page size:</label>
          </div>
          <div class="col-md-1">
            <select name="page_size" id="page_size" onchange="this.form.submit()" class="form-select">
                <option value="9" {{ request('page_size') == 9 ? 'selected' : '' }}>9</option>
                <option value="15" {{ request('page_size') == 15? 'selected' : '' }}>15</option>
                <option value="21" {{ request('page_size') == 21 ? 'selected' : '' }}>21</option>
            </select>
          </div>
        </div>
      </form> 
      <div class="row mt-3" id="body">
         
        @if($users->isEmpty())
          <h5 class="text-center mt-3">User Not Found.</h5>
        @else       
        <div class="container" id="postCard">
                  <div class="row row-cols-1 row-cols-md-3 g-4" id="card">
                    @foreach($users as $user)
                      <div class="col mb-3" id="{{$user->id}}">
                          <div class="card h-100 rounded-3 shadow-sm custom-card"> 
                              <div class="card-body  d-flex flex-column"> 
                                  <div class="d-flex justify-content-center align-items-center mb-3">
                                      <div class="text-center">
                                        <img src="{{$user->profile}}" alt="profile" class="rounded-circle img-thumbnail custom-img-thumbnail" style="width:80px; height: 80px;">
                                      </div>
                                    </div>     
                                        <div class="dropdown custom-dropdown ms-auto">
                                          <button class="btn btn-link p-0 text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                              <i class="fa fa-ellipsis-h" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="delete"></i>
                                          </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                              <li>
                                                <button class="userdelete dropdown-item" data-id="{{ $user->id }}"><img src="{{asset('uploads/trash_card.png')}}" alt="delete" class="delete_icon me-2">Delete</button>
                                              </li>
                                            </ul>
                                        </div>

                                  <h5 class="text-center">{{$user->name}}</h5>
                                  <div class="mb-3">
                                      <div class="d-flex align-items-center">
                                          <!--<i class="fa fa-envelope me-2"></i>-->
                                          <img src="{{asset('uploads/email.png')}}" alt="email" class="common">
                                          <!--&nbsp;&nbsp;<div>{{ $user->email }}</div>-->
                                          &nbsp;&nbsp;<a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                      </div>
                                      <div class="d-flex align-items-center mt-2">
                                          <!--<i class="fa fa-phone me-2"></i>-->
                                          <img src="{{asset('uploads/phone.png')}}" alt="phone" class="phone">
                                          <!--&nbsp;&nbsp;<div>{{ $user->phone }}</div>-->
                                          &nbsp;&nbsp;<a href="tel:{{ $user->phone }}">{{ $user->phone }}</a>
                                      </div>
                                      <div class="d-flex align-items-center mt-3">
                                            <!--<i class="fa fa-birthday-cake me-2"></i>-->
                                            <img src="{{asset('uploads/birthday.png')}}" alt="birthday" class="common">
                                            &nbsp;&nbsp;<div>{{ $user->dob }}</div>
                                      </div>
                                      <div class="d-flex align-items-center mt-3">
                                            <!--<i class="fa fa-address-card-o me-2"></i>-->
                                            <img src="{{asset('uploads/address.png')}}" alt="address" class="common">
                                            &nbsp;&nbsp;<div>{{$user->address}}</div>
                                      </div>

                                  </div>
                                    <div class="ms-3"></div>
                              </div>
                              <div class="card-footer mt-auto text-end"> 
                                  <button class=" user_detail_card view" data-bs-toggle="tooltip" 
                                  data-bs-placement="bottom" data-bs-title="view">
                                  <img src="{{asset('uploads/eye.png')}}" alt="delete" class="eye_icon"></button>
                                  
                              </div>
                          </div>
                      </div>                    
                    @endforeach
                  </div>
                </div>
          @endif
          <!-- Pagination Links -->
          <div class="">
        
              {!! $users->appends(request()->except('page'))->links() !!}
              
          </div>
        </div>

      </div>   
  <!-- start delete modal-->
  <div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteModalLabel">Delete Confirm</h5>
                    <div class="col-md-6"></div>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Are you sure you want to delete this User?</strong></p>
                        <div class="row mb-3">
                          <div class="col-5"><strong>ID:</strong></div>
                          <div class="col-7"> <span id="userid"></span></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-5"><strong>Name:</strong></div>
                          <div class="col-7"><span id="username"></span></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-5"><strong>Type:</strong></div>
                          <div class="col-7"><span id="usertype"></span></div>
                        </div>
                        <div class="row mb-3" >
                          <div class="col-5"><strong>Email:</strong></div>
                          <div class="col-7"><span id="useremail"></span></div>
                        </div>
                        <div class="row mb-3">
                         <div class="col-5"><strong>Phone:</strong></div>
                         <div class="col-7"><span id="userphone"></span></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-5"><strong>Date Of Birth:</strong></div>
                          <div class="col-7"><span id="userdob"></span></div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-5"><strong>Address:</strong></div>
                          <div class="col-7"><span id="useraddress"></span></div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
  </div>
   <!--end delete modal-->
  <!--start detail modal-->
      <div class="modal fade" id="userdetailModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary" id="userdetailModalLabel">User Detail</h5>
                        <div class="col-md-7"></div>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                          <div class="col-4"><img src="" alt="Profile" id="user_profile" style="width: 150px; height: 150px;"></div>
                          <div class="col-8">
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Name:</strong>
                              </div>
                              <div class="col-7"><span id="name"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Type:</strong> 
                              </div>
                              <div class="col-7"><span id="type"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Email:</strong> 
                              </div>
                              <div class="col-7"><span id="email"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Phone:</strong> 
                              </div>
                              <div class="col-7"><span id="phone"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Date Of Birth:</strong> 
                              </div>
                              <div class="col-7"><span id="dob"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Address:</strong> 
                              </div>
                              <div class="col-7"><span id="address"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Created Date:</strong> 
                              </div>
                              <div class="col-7"><span id="created_date"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Created User:</strong> 
                              </div>
                              <div class="col-7"><span id="created_user"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Updated Date:</strong> 
                              </div>
                              <div class="col-7"><span id="updated_date"></span></div>
                            </div>
                            <div class="row  mb-3">
                              <div class="col-5">
                                <strong>Updated User:</strong> 
                              </div>
                              <div class="col-7"><span id="updated_user"></span></div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    
                    </div>
                </div>
            </div>
      </div>
  <!--end detail modal-->
  <img src="{{asset('uploads/page_top.png')}}" alt="pagetop" class="pagetop" id="scrolltop" onclick="scrollToTop()">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
          $(document).ready(function(){
            var form=$('#search_form');
      
            $('#search_user').on('click',function(){
              form.attr('action','{{url("/search_card")}}');
            })
          });
        // start delete user for card
        $(document).ready(function(){
         
          $(document).on('click','.userdelete',function(e){
           let parentDiv=$(this).closest('.col.mb-3')
           let id=parentDiv.attr('id');
           console.log(id)
            e.preventDefault();
            $.ajax({
              method:`post`,
              url:`user/userdelete/${id}`,
              success:function(response){
                var user=response.userinfo;
                if(response.success){
                  $('#postModal').modal('show');
                  $('#userid').text(user.id);
                  $('#username').text(user.name);
                  if(user.type==1){
                    $('#usertype').text("User");
                  }
                  else{
                    $('#usertype').text("Admin");
                  }
                  $('#useremail').text(user.email);
                  $('#userphone').text(user.phone);
                  $('#userdob').text(user.dob);
                  $('#useraddress').text(user.address);
                  $('#confirmDelete').on('click',function(){
                    $.ajax({
                      method:`post`,
                      url:`user/deleteduser/${id}`,
                      success:function(response){
                       console.log(response.success);
                       $('#response').text(response.success);
                       location.reload();
                      }
                     
                    });
                  });
                }
              }
            })   
          })
           
                    
          
        });
        //end delete user for card
        //start user details for card
          $(document).ready(function(){
            $(document).on('click','.user_detail_card',function(){
              var parentDiv=$(this).closest('.col.mb-3')
              var id=parentDiv.attr('id')
              console.log(id);
              $.ajax({
                method:`post`,
                url:`/user/detail/${id}`,
                success:function(response){
                  console.log(response)
                  var user=response.detail;
                  console.log(user.name)
                  if(response.detail){
                    $('#user_profile').attr('src', user.profile);
                    $('#userdetailModal').modal('show');
                    $('#name').text(user.name);
                    if(user.type==1){
                      $('#type').text("User");
                    }
                    else{
                      $('#type').text("Admin");
                    }
                    $('#email').text(user.email);
                    $('#phone').text(user.phone);
                    $('#dob').text(user.dob);
                    $('#address').text(user.address);
                    var created_at=new Date(user.created_at);
                    created_at.setMinutes(created_at.getMinutes() - created_at.getTimezoneOffset());
                    var dateFormat=created_at.toISOString().split('T')[0];
                    $('#created_date').text(dateFormat);
                    var updated_at=new Date(user.updated_at);
                    updated_at.setMinutes(updated_at.getMinutes() - updated_at.getTimezoneOffset());
                    var Updated_format = updated_at.toISOString().split('T')[0];
                    $('#updated_date').text(Updated_format);
                    var createdUser = (response.created_user && response.created_user.length > 0) ? response.created_user : 'unknown';
                    $('#updated_user').text(createdUser);
                    $('#created_user').text(createdUser);
                  }
               
                }
              })
            })
          });
       //end user detail for card
       //start toggle design 
       $(document).ready(function(){
        var viewBtn=document.getElementById('designView');
        let currentRoute="{{Route::currentRouteName() }}";
        if(currentRoute === 'user'){
          viewBtn.textContent= 'View Card';

        }
        else if(currentRoute === 'search_user'){
          viewBtn.textContent ='View Card';
        }
        else if(currentRoute === 'user_card'){
          viewBtn.textContent = 'View Table';
        }
        else if(currentRoute === 'search_card'){
          viewBtn.textContent = 'View Table'
        }
       })
       //end toggle design
       //start page top
        function scrollToTop(){
          window.scrollTo({
            top:0,
            behavior:'smooth'
          })
        }
        window.onscroll = function(){
          if(document.body.scrollTop > 20 || document.documentElement.scrollTop > 20 ){
            document.getElementById('scrolltop').style.display = 'block'
          }else{
            document.getElementById('scrolltop').style.display = 'none'
          }
        }
       //end page top
    </script>
</section>
@endsection