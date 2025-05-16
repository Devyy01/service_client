<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ancestry Test Results</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .page {
            width: 100%;
            height: 100vh;
            background-image: url("{{ $svgBase64 }}");
            background-size: cover;
            background-position: center;
            text-align: center;
            position: relative;
        }

        .title {
            font-size: 32px;
            font-weight: bold;
            margin-top: 100px;
            color: #269989;
        }

        .name {
            font-size: 24px;
            margin-top: 50px;
            text-decoration: underline;
            color: #269989;
        }

        .footer {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 12px;
            color: #269989;
        }
    </style>
</head>
<body>
    <div class="page" style="position: relative; text-align: center; font-family: Arial, sans-serif; color: #269989;">
        <!-- Image SVG en base64 -->
        <img src="{{ $svgBase64 }}" alt="Background SVG" style="width: 100%; height: auto; position: absolute; top: 0; left: 0; z-index: -1; opacity: 0.3;">
        
        <div style="position: relative; z-index: 1; padding-top: 100px;">
            <h1 style="color: #269989; font-size: 36px; margin: 0;">QUICK DNA</h1>
            <div class="title" style="font-size: 32px; font-weight: bold; margin-top: 50px;">
                ANCESTRY TEST RESULTS
            </div>
            <div class="name" style="font-size: 24px; margin-top: 50px; text-decoration: underline;">
                Louise Montézin
            </div>
        </div>

        <div class="footer" style="position: absolute; bottom: 20px; right: 20px; font-size: 12px; color: #269989; z-index: 1;">
            © 2020, Quick DNA LLC.
        </div>
    </div>
</body>

</html>
