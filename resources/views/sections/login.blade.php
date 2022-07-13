<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>{{ __('auth.title_login') }}</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="css/AdminLTE.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="css/blue.css">
  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>

  .login-box
  {
    box-shadow: 0px 3px 10px 5px rgba(0,0,0, 0.3);
    margin: 0px;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50% , -50%);
  }
  .login-box-body
  {
    background: rgba(255,255,255, 1); 
   
  }
    .invalid-feedback{
      color: #CC0000;
    }

    .login-logo
    {
      width: 125px;
      height: 125px;
      margin: 2rem auto;
      border-radius: 100%;
      background-color: #ffffff; box-shadow: 0px 3px 10px 5px rgba(0,0,0, 0.1);
     padding: 5px;
    }
    .login-logo img
    {
     
      border-radius: 100%;
      margin: auto;
    }
    .login-box-msg
    {
      font-size: 15px;
      font-weight: 600;
    }
    .remember-me .checkbox
    {
      margin-top: 0px;
    }
    .btn-red ,  .btn-red:hover , .btn-red:focus ,  .btn-red:active
    {
      background-color: #c20302 !important;
      border: 0px !important;
      border-radius: 0px !important;
    }

    .login-box .form-control:focus
    {
      border: 1px solid #d73925;
    }
    .forget-password 
    {
      color: #999999;
      text-decoration: underline;
    }
    
  </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">

  <!-- /.login-logo -->
  <div class="login-box-body"> 
  <div class="login-logo">
    <!-- <a href="/">{{__('layout.app_name')}}</a> -->
    <a href="/"><img src="images/logo.jpg" class="img-responsive" /></a>
  </div>
    <p class="login-box-msg">{{__('auth.upper_title_login')}}</p>

    <form action="{{ route('login') }}" method="post">
      @csrf
      <div class="form-group has-feedback">
      <div>
        <input type="email" class="form-control" placeholder="{{__('auth.email')}}" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      @error('email')
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
      @enderror
      </div>
      <div class="form-group has-feedback">
        <div>
          <input type="password" class="form-control" placeholder="{{__('auth.password')}}" name="password" required autocomplete="current-password">
          <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
      </div>
      <div class="row">
        <div class="col-xs-6 remember-me" style="padding-left: 0px;">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox"  name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> {{__('auth.remember')}}
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-6 text-right" style="padding-right: 0px;">
        <button type="submit" class="btn btn-primary btn-block btn-flat btn-red">{{__('auth.sign_in')}}</button>
          
        </div>
        <!-- /.col -->
      </div>

      
    </form>

    <!-- <div class="social-auth-links text-center">
      <p>- OR -</p>
      <a href="/google/login" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> {{__('auth.google_sign')}}</a>
    </div> -->
    <!-- /.social-auth-links -->
    @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="forget-password">{{__('auth.forget')}}</a><br>
          @endif
    

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="js/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="js/bootstrap.min.js"></script>
<!-- iCheck -->
<script src="js/icheck.min.js"></script>
<script>
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
