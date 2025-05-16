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
      background-image: url('public/gradient.png');
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }

    body {
      font-family: DejaVu Sans, sans-serif;
      background: url('public/gradient.png');
      margin: 1in; /* Marges internes pour ne pas toucher les bords */
    }

    h1 {
      font-size: 30pt;
      text-align: center;
      color: #1caa93;
    }

    h2 {
      font-size: 22pt;
      color: #1caa93;
      margin-top: 30px;
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

    .footer {
      text-align: right;
      font-size: 12pt;
      margin-top: 50px;
    }

    .footer.absolute {
      position: absolute;
      right: 30px;
      bottom: 30px;
    }

    .footer p {
      margin: 0;
    }
  </style>
</head>
<body>

  <!-- PAGE 1 -->
  <div class="section head">
    <h1>ANCESTRY TEST RESULTS</h1>
    <p style="font-size: 20pt; text-decoration: underline;">louise Montézin</p>
    <div class="footer absolute">
      <p>&copy;  DNA Result.</p>
    </div>
  </div>

  <!-- PAGE 2 -->
  <div class="section">
    <h2>ETHNICITY TEST RESULTS</h2>
     <img src="{{ $svgBase64 }}" alt="Background SVG" style="width: 100%; height: auto; position: absolute; top: 90; left: 0; z-index: 1; opacity: 0.3;">
  </div>

</body>
</html>
