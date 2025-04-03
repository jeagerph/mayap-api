<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title> {{ env('APP_NAME')}} API Gateway</title>

        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <style>
            body {
                height: 75vh;
                width: 99%;
                /* 2 */
                display: flex;
                align-items: center;
                justify-content: center;

                background: #f4f4f4;
            }

            .container {
                text-align: center;
            }

            .image {
                text-align: center;
            }

            .text {
                font-family: 'Nunito';
                font-size: 24px;
                letter-spacing: 1px;
            }

            .bold {
                font-weight: bold;
            }

            .mtop {
                margin-top: 50px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            {{-- <div class="image">
                <img src="{{ asset('assets/images/kapitan-ph/logo-with-title.png') }}" width="900" height="500" alt="KAPITAN PH Logo">
            </div> --}}

            <p class="text">DEVELOPED AND MAINTAINED BY <span class="bold">VELCRO TECH PHILIPPINES, INC.</span></p>
            <p class="text">© {{now()->format('Y')}} ALL RIGHTS RESERVED</p>

            <div class="mtop">
                <p class="text">
                    <a href="mailto:admin@velcrotech.ph">admin@velcrotech.ph</a>
                </p>
            </div>
        </div>
    </body>
</html>
