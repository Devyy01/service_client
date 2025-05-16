<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>DNA test Result</title>
  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
    }

    @page {
      margin: 0;
      size: A4 landscape;
      background-image: url("{{ public_path('gradient.png') }}");
      background-repeat: no-repeat;
      background-size: cover;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      background: url("{{ public_path('gradient.png') }}");
      background-size: cover;
    }

    h1 {
      font-size: 30pt;
      text-align: center;
      color: #1caa93;
    }

    h2 {
      font-size: 30pt;
      color: #1caa93;
      margin-top: 10px;
      margin-left: 16px;
      position: absolute;
      top: 1px; 
      left: 10px; 
    }

    p {
      font-size: 14pt;
      color: #1caa93;
      line-height: 1.6;
    }

    .section {
      min-height: 100vh;
      width: 100%;
      page-break-after: always;
    }

    .no-break {
      page-break-after: avoid;
    }

    .section:last-of-type {
      page-break-after: auto;
    }

    .section.head {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
    }
    .logo{
      margin-top: 70px;
      margin-bottom: 20px;  
    }

    .footer {
      text-align: right;
      font-size: 12pt;
      margin-top: 50px;
    }

    .footer.absolute {
      position: absolute;
      right: 50px;
      bottom: 70px;
    }

    .footer p {
      margin: 0;
    }
    .litlelogo {
      position: absolute;
      top: 15px;
      right : 20px;;
    }
  </style>
</head>
<body>

  <!-- PAGE 1 -->
  <div class="section head">
    <div class="logo">
      <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 210px; height: auto;">
    </div>
    <h1>ANCESTRY TEST RESULTS</h1>
    <p style="font-size: 20pt; text-decoration: underline;">{{$name}}</p>
    <div class="footer absolute">
      <p>&copy; {{ date('Y') }} DNA Result.</p>
    </div>
  </div>

  <!-- PAGE 2 -->
  <div class="section">
    <div class="litlelogo">
      <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 120px;">
    </div>
    <h2 style="margin-top:25px">ETHNICITY TEST RESULTS</h2>
    <div style="margin-top:280px">
        <img src="{{ $svgBase64 }}" alt="Background SVG" style="width: 75%; margin-left:13%">
    </div>
  </div>
</body>
</html>
     
    
