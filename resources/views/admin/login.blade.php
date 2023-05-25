<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ثبت نام</title>
    <link rel="stylesheet" href="{{ url('/resources/assets/css/mainAdmin.css')}}">
    <meta name="viewport" content="width =device-width, initial-scale=1.0" />
    <meta name="theme-color" content="#FFE1C4">
    <script src="{{url('/resources/assets/js/sweetalert.min.js')}}"></script>
    <link rel="icon" type="image/png" href="{{ url('/resources/assets/images/part.png')}}">
    <style>
        .downloadAppimg {
                list-style-type: none;
                height:28px;
            }
        .app{
            padding:10px 15px;
            background-color:black;
            margin:5px;
            border-radius:10px;
        }
    </style>
</head>
<body style="background-color:#bbcbda;">
    <section class="account-box">
        <div class="register login" style="background: linear-gradient(#85baef, #116bc7, #2659a9); margin-top:200px;">

            <div class="headline" style="color:rgb(0, 0, 0);text-align:center;">ورود به CRM</div>
                <div class="content">
                    <form action="{{('/loginUser')}}" method="post">
                        @csrf
                        <label for="mobtel" style="color:white">ایمیل یا شماره موبایل</label>
                        <input name="userName" type="text" placeholder="نام کاربری" required>
                        <label for="pwd" style="color:white">کلمه عبور</label>
                        <input name="password" type="password"  placeholder="کلمه عبور" required>
                        <!-- <input name="token" type="text" id="tokenInput" placeholder=" token " required> -->
                        <button type="submit" style="background-color:rgb(0, 0, 0)"><i class="fa fa-unlock"></i> ورود به CRM </button>
                        <!-- <button type="button" onclick="chekcToken()" style="background-color:rgb(0, 0, 0)"><i class="fa fa-unlock"></i> نوتفیکیشن </button> -->
                        @if(isset($loginError))
                            @if($loginError=="نام کاربری و یا رمز ورود اشتباه است")
                                <script>
                                    swal({
                                        title: "خطا!",
                                        text: "نام کاربری و یا رمز ورود اشتباه است",
                                        icon: "warning",
                                        button: "تایید!",
                                    });
                                </script>
                            @else
                                @php
                                    unset($loginError);
                                @endphp
                            @endif
                        @endif
                    </form>
                    <form action="{{url('/downloadApk')}}" method="get" id='myform'>
                        <div class="app d-flex justify-content-center" role="group" aria-label="Basic mixed styles example">
                            <a href="javascript:;" onclick="document.getElementById('myform').submit()" class="ms-1 btn btn-success">
                            <img class="downloadAppimg" src="{{ url('/resources/assets/images/Gplay.png')}}"> دانلود اندرويد</a>
                        </div>
                   </form>
                </div>
           </div>
    </section>
    <script>
        function chekcToken() {
            document.getElementById('tokenInput').value=localStorage.getItem('token');
                // alert(localStorage.getItem('token'));
        }    
    </script>
</body>
</html>
