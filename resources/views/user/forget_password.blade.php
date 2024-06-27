@extends('layouts.master')
@section('content')
<div class="container">
    <div class="row">
      <div class="col-md-6 m-auto">
      <form action="{{route('forget.password.post',)}}" method="post" class=" mt-5">
        @csrf 
       
          <div class="card text-center" style="width: 500px;">
            <div class="card-header h5 text-white bg-primary">Password Reset</div>
              <div class="card-body px-5">
                  <p class="card-text py-2">
                      Enter your email address and we'll send you an email with instructions to reset your password.
                  </p>
                  @if(session('error'))
                    <div class="alert alert-danger" id="alert">
                      {{session('error')}}
                    </div>
                  @endif
                  <div data-mdb-input-init class="form-outline mt-3">
                      <label class="form-label" for="typeEmail">Email input</label>
                      <input type="email" id="typeEmail" name="email" class="form-control my-3" />
                      <span class="text-danger ">
                        @error('email')
                          {{$message}}
                        @enderror
                      </span>
                  </div>
                  <button  class="btn btn-primary w-100 mt-3">Reset password</button>
                  
              </div>
            </div>

    </form>
      </div>
    </div>
   
</div>
@endsection