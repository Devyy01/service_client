<?php

namespace App\Http\Controllers;

use App\Services\ApiChatgpt;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PersonalInfoController extends Controller
{

    public $serviceOpenAI;


    public function __construct(ApiChatgpt $serviceOpenAI)
    {
        $this->serviceOpenAI = $serviceOpenAI;
    }

    public function showForm()
    {
        return view('subscription');
    }

    public function submitForm(Request $request)
    {

        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'characteristic' => 'required|string|max:255',
        ]);

        $prompt = $this->generatePrompt($validated['firstName'], $validated['lastName'], $validated['address'], $validated['city'], $validated['postal_code'], $validated['country'], $validated['characteristic']);
        $valer = $this->serviceOpenAI->generateCanva($prompt);
        preg_match_all('/([A-Za-zéèàùçêîôâëïü -]+)\s*:\s*(\d+(?:[.,]\d+)?)\s*%/', $valer, $matches, PREG_SET_ORDER);

        $countries = json_decode(file_get_contents(resource_path('json/countries.json')), true);

        $result = [];
        $countrise_code = [];

        foreach ($matches as $match) {
            
            $country = trim($match[1]);
            $percent = $match[2];
            $country_code=$this->getCountryCode($country);
            if ($country_code) {
                $result[$country_code] =  $percent;
                $countrise_code[$country_code] = $country;
            }
        }
        
        if ($result && $countrise_code) {
            $this->generateMap($result, $countrise_code);
        }
        
        // $this->generatePdf($validated['firstName'], $validated['lastName']);
        session([
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
        ]);
    }

    private function generatePrompt($firstName, $lastName, $address, $city, $postal_code, $country, $characteristic)
    {
        $prompt = "You are an expert in demographic and onomastic analysis. Here are the instructions:\n\n";
        $prompt .= "You must estimate the most likely geographical origin of a person based on their first name, last name, address, and physical description.\n\n";
        $prompt .= "You must follow these rules:\n";
        $prompt .= "1. You must assign **percentages by country only**, no continents.\n";
        $prompt .= "2. Each percentage must be a precise number, not rounded (example: 55.2%, 10.7%, not 50% or 10%).\n";
        $prompt .= "3. The total percentage must add up to exactly 100%.\n";
        $prompt .= "4. The response must be in this format:\nFrance: xx.x%\nMorocco: xx.x%\n...\n\n";
        $prompt .= "Here is the information to analyze:\n\n";
        $prompt .= "First name: $firstName\nLast name: $lastName\nAddress: $address, $city $postal_code, $country\nPhysical description: $characteristic\n\n";
        $prompt .= "Please assign percentages by country and follow all the rules stated above.";


        return $prompt;
    }

    public function generateMap($data, $data_code)
    {

        $defaultColor = '#1CAB94';

        $svgPath = public_path('maps/world.svg');
        if (!file_exists($svgPath)) {
            return response()->json(['error' => 'SVG map not found'], 404);
        }

        $svgContent = file_get_contents($svgPath);

        $patternAllCountries = '/(<path[^>]*id="[^"]+"[^>]*)(style="[^"]*")?/i';

        $svgContent = preg_replace_callback($patternAllCountries, function ($matches) use ($defaultColor) {
            $before = $matches[1];
            $styleAttribute = $matches[2] ?? null;

            if ($styleAttribute) {
                if (preg_match('/fill\s*:\s*#[0-9a-fA-F]{3,6}/i', $styleAttribute)) {
                    $newStyle = preg_replace('/fill\s*:\s*#[0-9a-fA-F]{3,6}/i', 'fill:' . $defaultColor, $styleAttribute);
                } else {
                    $newStyle = preg_replace('/style="([^"]*)"/i', 'style="$1;fill:' . $defaultColor . '"', $styleAttribute);
                }
                return $before . $newStyle;
            } else {
                if (strpos($before, 'style=') !== false) {
                    $before = preg_replace('/style="[^"]*"/i', 'style="fill:' . $defaultColor . '"', $before);
                    return $before;
                } else {
                    return $before . ' style="fill:' . $defaultColor . '"';
                }
            }
        }, $svgContent);


        foreach ($data as $countryCode => $percentage) {
            $color = $this->getColorFromPercentage($percentage);

            $patternCountry = '/(<path[^>]*id="' . preg_quote($countryCode, '/') . '"[^>]*)(style="[^"]*")?/i';

            $svgContent = preg_replace_callback($patternCountry, function ($matches) use ($color) {
                $before = $matches[1];
                $styleAttribute = $matches[2] ?? null;

                if ($styleAttribute) {
                    if (preg_match('/fill\s*:\s*#[0-9a-fA-F]{3,6}/i', $styleAttribute)) {
                        $newStyle = preg_replace('/fill\s*:\s*#[0-9a-fA-F]{3,6}/i', 'fill:' . $color, $styleAttribute);
                    } else {
                        $newStyle = preg_replace('/style="([^"]*)"/i', 'style="$1;fill:' . $color . '"', $styleAttribute);
                    }
                    return $before . $newStyle;
                } else {
                    if (strpos($before, 'style=') !== false) {
                        $before = preg_replace('/style="[^"]*"/i', 'style="fill:' . $color . '"', $before);
                        return $before;
                    } else {
                        return $before . ' style="fill:' . $color . '"';
                    }
                }
            }, $svgContent);
        }

        $legendX = 30;
        $legendY = 250;
        $legendSpacing = 25;
        $rectSize = 15; 

        $legendElements = '<g id="legend">';

        $legendElements .= sprintf(
            '<text x="%d" y="%d" font-size="14" fill="#000" font-weight="bold">Countries</text>',
            $legendX,
            $legendY - 20 
        );

        foreach ($data as $countryCode => $percentage) {
            $color = $this->getColorFromPercentage($percentage);
            $countryName = $data_code[$countryCode];

            $legendElements .= sprintf(
                '<rect x="%d" y="%d" width="%d" height="%d" style="fill:%s;stroke:black;stroke-width:1" />',
                $legendX,
                $legendY,
                $rectSize,
                $rectSize,
                $color
            );

            $legendElements .= sprintf(
                '<text x="%d" y="%d" font-size="12" fill="#000">%s (%s%%)</text>',
                $legendX + $rectSize + 10,
                $legendY + $rectSize - 2,
                htmlspecialchars($countryName),
                $percentage
            );

            $legendY += $legendSpacing;
        }

        $legendElements .= '</g>';

        $svgContent = preg_replace('/<\/svg>/', $legendElements . '</svg>', $svgContent);

        $outputPath = public_path('maps/generated_world.svg');
        file_put_contents($outputPath, $svgContent);

        $url = asset('maps/generated_world.svg');

        return response()->json(['map_url' => $url]);
    }


    private function getCountryName($countryCode)
    {
        $countries = [
            'FR' => 'France',
            'DZ' => 'Algérie',
            'IT' => 'Italie',
            'CA' => 'Canada',
        ];

        return $countries[$countryCode] ?? $countryCode;
    }
    private function getColorFromPercentage($percentage)
    {
        
        $hue = 120 - ($percentage * 1.2);  
        $saturation = 100; 
        $lightness = 50; 

        return $this->hslToHex($hue, $saturation, $lightness);
    }

    private function hslToHex($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        $r = $g = $b = 0;

        if ($s == 0) {
            $r = $g = $b = $l;
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = $this->hueToRgb($p, $q, $h + 1 / 3);
            $g = $this->hueToRgb($p, $q, $h);
            $b = $this->hueToRgb($p, $q, $h - 1 / 3);
        }

        $r = intval($r * 255);
        $g = intval($g * 255);
        $b = intval($b * 255);

        return sprintf("#%02X%02X%02X", $r, $g, $b);
    }

    private function hueToRgb($p, $q, $t)
    {
        if ($t < 0) $t += 1;
        if ($t > 1) $t -= 1;
        if ($t < 1 / 6) return $p + ($q - $p) * 6 * $t;
        if ($t < 1 / 2) return $q;
        if ($t < 2 / 3) return $p + ($q - $p) * (2 / 3 - $t) * 6;
        return $p;
    }

     public function generatePdf(){
       
        $firstName = session('firstName');
        $lastName = session('lastName');
    $svgPath = public_path('maps/generated_world.svg');
    $svgContent = file_get_contents($svgPath);
    $svgBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
    $fullName = $firstName . ' ' . $lastName;
    $nampdf = $firstName . '_' . $lastName;
   
    $pdf = PDF::loadView('ethniquepdfi', [
        'svgBase64' => $svgBase64,
        'name' => $fullName,
    ]);

    $fileName = $nampdf . date('Y_m_d_H_i_s') . '.pdf';

    // Chemin pour enregistrer dans storage/app/public/pdfs/
    $filePath = storage_path('app/public/pdfs/' . $fileName);

    if (!file_exists(dirname($filePath))) {
        mkdir(dirname($filePath), 0755, true);
    }

    // Enregistrement du PDF
    $pdf->save($filePath);

    return response()->download($filePath);
    }

    public function getCountryCode($country)
    {
        switch ($country) {
            case 'Afghanistan': return 'AF';
            case 'Albania': return 'AL';
            case 'Algeria': return 'DZ';
            case 'Andorra': return 'AD';
            case 'Angola': return 'AO';
            case 'Antigua and Barbuda': return 'AG';
            case 'Argentina': return 'AR';
            case 'Armenia': return 'AM';
            case 'Australia': return 'AU';
            case 'Austria': return 'AT';
            case 'Azerbaijan': return 'AZ';
            case 'Bahamas': return 'BS';
            case 'Bahrain': return 'BH';
            case 'Bangladesh': return 'BD';
            case 'Barbados': return 'BB';
            case 'Belarus': return 'BY';
            case 'Belgium': return 'BE';
            case 'Belize': return 'BZ';
            case 'Benin': return 'BJ';
            case 'Bhutan': return 'BT';
            case 'Bolivia': return 'BO';
            case 'Bosnia and Herzegovina': return 'BA';
            case 'Botswana': return 'BW';
            case 'Brazil': return 'BR';
            case 'Brunei': return 'BN';
            case 'Bulgaria': return 'BG';
            case 'Burkina Faso': return 'BF';
            case 'Burundi': return 'BI';
            case 'Cabo Verde': return 'CV';
            case 'Cambodia': return 'KH';
            case 'Cameroon': return 'CM';
            case 'Canada': return 'CA';
            case 'Central African Republic': return 'CF';
            case 'Chad': return 'TD';
            case 'Chile': return 'CL';
            case 'China': return 'CN';
            case 'Colombia': return 'CO';
            case 'Comoros': return 'KM';
            case 'Congo': return 'CG';
            case 'Congo (Democratic Republic)': return 'CD';
            case 'Costa Rica': return 'CR';
            case 'Croatia': return 'HR';
            case 'Cuba': return 'CU';
            case 'Cyprus': return 'CY';
            case 'Czechia': return 'CZ';
            case 'Denmark': return 'DK';
            case 'Djibouti': return 'DJ';
            case 'Dominica': return 'DM';
            case 'Dominican Republic': return 'DO';
            case 'Ecuador': return 'EC';
            case 'Egypt': return 'EG';
            case 'El Salvador': return 'SV';
            case 'Equatorial Guinea': return 'GQ';
            case 'Eritrea': return 'ER';
            case 'Estonia': return 'EE';
            case 'Eswatini': return 'SZ';
            case 'Ethiopia': return 'ET';
            case 'Fiji': return 'FJ';
            case 'Finland': return 'FI';
            case 'France': return 'FR';
            case 'Gabon': return 'GA';
            case 'Gambia': return 'GM';
            case 'Georgia': return 'GE';
            case 'Germany': return 'DE';
            case 'Ghana': return 'GH';
            case 'Greece': return 'GR';
            case 'Grenada': return 'GD';
            case 'Guatemala': return 'GT';
            case 'Guinea': return 'GN';
            case 'Guinea-Bissau': return 'GW';
            case 'Guyana': return 'GY';
            case 'Haiti': return 'HT';
            case 'Honduras': return 'HN';
            case 'Hungary': return 'HU';
            case 'Iceland': return 'IS';
            case 'India': return 'IN';
            case 'Indonesia': return 'ID';
            case 'Iran': return 'IR';
            case 'Iraq': return 'IQ';
            case 'Ireland': return 'IE';
            case 'Israel': return 'IL';
            case 'Italy': return 'IT';
            case 'Jamaica': return 'JM';
            case 'Japan': return 'JP';
            case 'Jordan': return 'JO';
            case 'Kazakhstan': return 'KZ';
            case 'Kenya': return 'KE';
            case 'Kiribati': return 'KI';
            case 'Korea, North': return 'KP';
            case 'Korea, South': return 'KR';
            case 'Kuwait': return 'KW';
            case 'Kyrgyzstan': return 'KG';
            case 'Laos': return 'LA';
            case 'Latvia': return 'LV';
            case 'Lebanon': return 'LB';
            case 'Lesotho': return 'LS';
            case 'Liberia': return 'LR';
            case 'Libya': return 'LY';
            case 'Liechtenstein': return 'LI';
            case 'Lithuania': return 'LT';
            case 'Luxembourg': return 'LU';
            case 'Madagascar': return 'MG';
            case 'Malawi': return 'MW';
            case 'Malaysia': return 'MY';
            case 'Maldives': return 'MV';
            case 'Mali': return 'ML';
            case 'Malta': return 'MT';
            case 'Marshall Islands': return 'MH';
            case 'Mauritania': return 'MR';
            case 'Mauritius': return 'MU';
            case 'Mexico': return 'MX';
            case 'Micronesia': return 'FM';
            case 'Moldova': return 'MD';
            case 'Monaco': return 'MC';
            case 'Mongolia': return 'MN';
            case 'Montenegro': return 'ME';
            case 'Morocco': return 'MA';
            case 'Mozambique': return 'MZ';
            case 'Myanmar': return 'MM';
            case 'Namibia': return 'NA';
            case 'Nauru': return 'NR';
            case 'Nepal': return 'NP';
            case 'Netherlands': return 'NL';
            case 'New Zealand': return 'NZ';
            case 'Nicaragua': return 'NI';
            case 'Niger': return 'NE';
            case 'Nigeria': return 'NG';
            case 'North Macedonia': return 'MK';
            case 'Norway': return 'NO';
            case 'Oman': return 'OM';
            case 'Pakistan': return 'PK';
            case 'Palau': return 'PW';
            case 'Panama': return 'PA';
            case 'Papua New Guinea': return 'PG';
            case 'Paraguay': return 'PY';
            case 'Peru': return 'PE';
            case 'Philippines': return 'PH';
            case 'Poland': return 'PL';
            case 'Portugal': return 'PT';
            case 'Qatar': return 'QA';
            case 'Romania': return 'RO';
            case 'Russia': return 'RU';
            case 'Rwanda': return 'RW';
            case 'Saint Kitts and Nevis': return 'KN';
            case 'Saint Lucia': return 'LC';
            case 'Saint Vincent and the Grenadines': return 'VC';
            case 'Samoa': return 'WS';
            case 'San Marino': return 'SM';
            case 'Sao Tome and Principe': return 'ST';
            case 'Saudi Arabia': return 'SA';
            case 'Senegal': return 'SN';
            case 'Serbia': return 'RS';
            case 'Seychelles': return 'SC';
            case 'Sierra Leone': return 'SL';
            case 'Singapore': return 'SG';
            case 'Slovakia': return 'SK';
            case 'Slovenia': return 'SI';
            case 'Solomon Islands': return 'SB';
            case 'Somalia': return 'SO';
            case 'South Africa': return 'ZA';
            case 'South Sudan': return 'SS';
            case 'Spain': return 'ES';
            case 'Sri Lanka': return 'LK';
            case 'Sudan': return 'SD';
            case 'Suriname': return 'SR';
            case 'Sweden': return 'SE';
            case 'Switzerland': return 'CH';
            case 'Syria': return 'SY';
            case 'Taiwan': return 'TW';
            case 'Tajikistan': return 'TJ';
            case 'Tanzania': return 'TZ';
            case 'Thailand': return 'TH';
            case 'Timor-Leste': return 'TL';
            case 'Togo': return 'TG';
            case 'Tonga': return 'TO';
            case 'Trinidad and Tobago': return 'TT';
            case 'Tunisia': return 'TN';
            case 'Turkey': return 'TR';
            case 'Turkmenistan': return 'TM';
            case 'Tuvalu': return 'TV';
            case 'Uganda': return 'UG';
            case 'Ukraine': return 'UA';
            case 'United Arab Emirates': return 'AE';
            case 'United Kingdom': return 'GB';
            case 'United States': return 'US';
            case 'Uruguay': return 'UY';
            case 'Uzbekistan': return 'UZ';
            case 'Vanuatu': return 'VU';
            case 'Vatican City': return 'VA';
            case 'Venezuela': return 'VE';
            case 'Vietnam': return 'VN';
            case 'Yemen': return 'YE';
            case 'Zambia': return 'ZM';
            case 'Zimbabwe': return 'ZW';
            case 'Kosovo': return 'XK';
            default: return null;
        }
    }
}
