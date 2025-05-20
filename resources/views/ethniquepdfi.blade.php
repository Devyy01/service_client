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
      marging: 10cm;
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
      margin-top: 5px;
      margin-left: 40px;
      position: absolute;
      top: 2px; 
      left: 10px; 
    }
    h3 {
      font-size: 25pt;
      color: #1caa93;
      margin-top: 10px;
      margin-left: 40px;
    }

    p {
      font-size: 16pt;
      color: #1caa93;
      line-height: 1.3;
      margin-left: 40px;
    }

    .section {
      min-height: 100vh;
      width: 100%;
      page-break-after: always;
    }

    .section.content {
      padding: 30px;
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
      font-size: 16pt;
      margin-top: 10px;
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
      top: 20px;
      right : 20px;;
    }
    ul {
      list-style: none;
      padding-bottom: 15px;
    }
    li {
      display: flex;
      line-height: 1cm;
      position: relative;
      align-items: center;
    }
    li p {
      padding-left: 16px;
      font-size: 16pt;
      color: #1caa93;
      display: inline;
      font-weight: bold;
    }
    li img {
      position: absolute;
      left :0;
      top: 19px;
    }
    .nobold {
      font-size: 16pt;
      color: #1caa93;
      font-weight: normal;
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

      <!-- PAGE 3 -->
    <div class="section content">
      <div class="litlelogo">
        <img src="{{ public_path('logo.svg') }}" alt="Logo" style="width: 120px; height: auto;">
      </div>
      <h3>GENETIC DISEASE SCREENING</h3>
      <div>
        <ul>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Cystic Fibrosis</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Sickle Cell Disease</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Thalassemia (Alpha & Beta Thalassemia)</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Tay-Sachs Disease</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Huntington's Disease</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>Fragile X Syndrome</p> </li>
          <li><img src="{{ public_path('cross.svg') }}" width="25" height="25" alt="Icône"><p>BRCA1 / BRCA2 Gene Mutations</p> </li>
        </ul>
      </div>
      <div>
        <h3 style ="font-size:30px;">CONCLUSION</h3>
        <p> 
          No pathogenic genetic mutations associated with the conditions listed above
          <br>
          were detected in your DNA analysis. This indicates no known inherited risk for
          <br>
          these specific disorders based on current testing methods.
        </p>
      </div>
    </div>

      <!-- PAGE 4 -->
    <div class="section content">
      <div class="litlelogo">
        <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 120px; height: auto;">
      </div>
      <h3>DRUG TOLERANCE TEST</h3>
          <div>
            <ul>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Metformin</p> <span class="nobold">– Treats type 2 diabetes</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Levothyroxine</p><span class="nobold">– Treats hypothyroidism</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Atorvastatin</p><span class="nobold">– Treats high cholesterol</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Enalapril</p><span class="nobold">– Treats high blood pressure (hypertension)</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Amlodipine</p><span class="nobold">– Treats hypertension and angina</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Hydroxychloroquine</p><span class="nobold">– Treats lupus and rheumatoid arthritis</span></li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"> <p>Insulin (analogs)</p><span class="nobold">– Treats type 1 and type 2 diabetes</span></li>
            </ul>
          </div>
        <div style="margin-top: 20px;">
          <h3 style ="font-size:30px;">CONCLUSION</h3>
          <p> 
            According to your pharmacogenetic analysis, the following medications are
            <br>
            associated with a high likelihood of tolerance based on your genetic makeup.
          </p>
      </div>
    </div>

        <!-- PAGE 5 -->
    <div class="section content">
      <div class="litlelogo">
        <img src="{{ public_path('logo.svg') }}" alt="Logo" style="width: 120px; height: auto;">
      </div>
      <h3>DRUG TOLERANCE TEST</h3>
          <div>
            <ul>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Caffeine Sensitivity</p> </li>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Lactose Tolerance</p> </li>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Preference for Sugar</p> </li>
              <li><img src="{{ public_path('check.svg') }}" width="25" height="25" alt="Icône"><p>Endurance Muscle Composition</p> </li>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Alcohol Sensitivity</p> </li>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Long Sleeper</p> </li>
              <li><img src="{{ public_path('warning.svg') }}" width="25" height="25" alt="Icône"><p>Pain Sensitivity</p> </li>
            </ul>
          </div>
        <div style="margin-top: 20px;">
          <h3 style ="font-size:30px;">CONCLUSION</h3>
          <p> 
            These insights represent the primary traits and predispositions identified through
            <br>
            the analysis of your DNA. They reflect how your genetic makeup may influence
            <br>
            various aspects of your physiology, behavior, and overall health profile.
          </p>
      </div>
    </div>

    <!-- PAGE 6 -->
  <div class="section content">
    <div class="litlelogo">
      <img src="{{ public_path('logo.png') }}" alt="Logo" style="width: 120px; height: auto;">
    </div>
    <h3>CLOSE RELATIVE MATCHES</h3>
    <p>
      No close relatives have been identified in our database at this time.
      <br>
      If any are detected in the future, you will be notified by email.
    </p>
  </div>
</body>
</html>
