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
            'skin_color' => 'required|string|max:255',
            'hair_color' => 'required|string|max:255',
            'eyes_color' => 'required|string|max:255',
            'slanting_eyes' => 'required|string|max:255',
        ]);
 
        $prompt = $this->generatePrompt($validated['firstName'], $validated['lastName'], $validated['address'], $validated['city'], $validated['postal_code'], $validated['country'], $validated['skin_color'],$validated['hair_color'],$validated['eyes_color'],$validated['slanting_eyes']);
        $valer = $this->serviceOpenAI->generateCanva($prompt);
        // dd($valer);
        // preg_match_all('/([A-Za-zéèàùçêîôâëïü -]+)\s*:\s*(\d+(?:[.,]\d+)?)\s*%/', $valer, $matches, PREG_SET_ORDER);
        preg_match_all('/([A-Z]{2})\s+(\d+(?:\.\d+)?)/', $valer, $matches, PREG_SET_ORDER);

        $countries = json_decode(file_get_contents(resource_path('json/countries.json')), true);
        // dd($matches,$valer);
        $result = [];
        // $countrise = [];

        foreach ($matches as $match) {
            
            $code = trim($match[1]);
            $percent = $match[2];
            $result[$code] =  $percent;
            // $country = $this->getCountryName($code);
            // if ($country) {
            //     $countrise[$code] = $country;
            // }
        }
        
        if ($result) {
            $this->generateMap($result);
        }
        
        // $this->generatePdf($validated['firstName'], $validated['lastName']);
        session([
            'firstName' => $validated['firstName'],
            'lastName' => $validated['lastName'],
        ]);
    }

    private function generatePrompt($firstName, $lastName, $address, $city, $postal_code, $country, $skin_color, $hair_color, $eyes_color, $slanting_eyes)
    {
        
        $prompt = "";
        $prompt .= "In your opinion, what is the geographical origin of a person named \"$firstName $lastName\" ";
        $prompt .= "living at $address, $city ($postal_code), $country, with the following features: ";
        $prompt .= "skin-color: $skin_color, hair-color: $hair_color, eyes-color: $eyes_color, slanting_eyes: $slanting_eyes.\n\n";
        $prompt .= "Assign a percentage for each country using the ISO country code. ";
        $prompt .= "You must never mention continents, only countries with their ISO codes. ";
        $prompt .= "Each percentage must have exactly two decimal places and must not be rounded, must not be a whole number, and must never end in .00. ";
        $prompt .= "For example: 82.64 is correct, but 82.00 or 83.00 are not. The total must equal 100%. ";
        $prompt .= "The result you provide must be exactly 3 to 6 lines. ";
        $prompt .= "Each line must be formatted like this: FR 82.64 — that is, ISO code followed by the percentage. ";
        $prompt .= "Do not add any explanation, just list the countries and their percentages.";

        return $prompt;
    }

    public function generateMap($data)
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
            $countryName = $this->getCountryName($countryCode);

           if ($countryName) {
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
                    str_replace('.', ',', $percentage)
                );

                $legendY += $legendSpacing;
           }
        }

        $legendElements .= '</g>';

        $svgContent = preg_replace('/<\/svg>/', $legendElements . '</svg>', $svgContent);

        $outputPath = public_path('maps/generated_world.svg');
        file_put_contents($outputPath, $svgContent);

        $url = asset('maps/generated_world.svg');

        return response()->json(['map_url' => $url]);
    }


    // private function getCountryName($countryCode)
    // {
    //     $countries = [
    //         'FR' => 'France',
    //         'DZ' => 'Algérie',
    //         'IT' => 'Italie',
    //         'CA' => 'Canada',
    //     ];

    //     return $countries[$countryCode] ?? $countryCode;
    // }
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

    public function getCountryName($code) 
    {
        $code = strtoupper($code);

        switch (strtoupper($code)) {
            case 'AF': return 'Afghanistan';
            case 'AL': return 'Albania';
            case 'DZ': return 'Algeria';
            case 'AD': return 'Andorra';
            case 'AO': return 'Angola';
            case 'AG': return 'Antigua and Barbuda';
            case 'AR': return 'Argentina';
            case 'AM': return 'Armenia';
            case 'AU': return 'Australia';
            case 'AT': return 'Austria';
            case 'AZ': return 'Azerbaijan';
            case 'BS': return 'Bahamas';
            case 'BH': return 'Bahrain';
            case 'BD': return 'Bangladesh';
            case 'BB': return 'Barbados';
            case 'BY': return 'Belarus';
            case 'BE': return 'Belgium';
            case 'BZ': return 'Belize';
            case 'BJ': return 'Benin';
            case 'BT': return 'Bhutan';
            case 'BO': return 'Bolivia';
            case 'BA': return 'Bosnia and Herzegovina';
            case 'BW': return 'Botswana';
            case 'BR': return 'Brazil';
            case 'BN': return 'Brunei';
            case 'BG': return 'Bulgaria';
            case 'BF': return 'Burkina Faso';
            case 'BI': return 'Burundi';
            case 'CV': return 'Cabo Verde';
            case 'KH': return 'Cambodia';
            case 'CM': return 'Cameroon';
            case 'CA': return 'Canada';
            case 'CF': return 'Central African Republic';
            case 'TD': return 'Chad';
            case 'CL': return 'Chile';
            case 'CN': return 'China';
            case 'CO': return 'Colombia';
            case 'KM': return 'Comoros';
            case 'CG': return 'Congo';
            case 'CD': return 'Congo (Democratic Republic)';
            case 'CR': return 'Costa Rica';
            case 'HR': return 'Croatia';
            case 'CU': return 'Cuba';
            case 'CY': return 'Cyprus';
            case 'CZ': return 'Czechia';
            case 'DK': return 'Denmark';
            case 'DJ': return 'Djibouti';
            case 'DM': return 'Dominica';
            case 'DO': return 'Dominican Republic';
            case 'EC': return 'Ecuador';
            case 'EG': return 'Egypt';
            case 'SV': return 'El Salvador';
            case 'GQ': return 'Equatorial Guinea';
            case 'ER': return 'Eritrea';
            case 'EE': return 'Estonia';
            case 'SZ': return 'Eswatini';
            case 'ET': return 'Ethiopia';
            case 'FJ': return 'Fiji';
            case 'FI': return 'Finland';
            case 'FR': return 'France';
            case 'GA': return 'Gabon';
            case 'GM': return 'Gambia';
            case 'GE': return 'Georgia';
            case 'DE': return 'Germany';
            case 'GH': return 'Ghana';
            case 'GR': return 'Greece';
            case 'GD': return 'Grenada';
            case 'GT': return 'Guatemala';
            case 'GN': return 'Guinea';
            case 'GW': return 'Guinea-Bissau';
            case 'GY': return 'Guyana';
            case 'HT': return 'Haiti';
            case 'HN': return 'Honduras';
            case 'HU': return 'Hungary';
            case 'IS': return 'Iceland';
            case 'IN': return 'India';
            case 'ID': return 'Indonesia';
            case 'IR': return 'Iran';
            case 'IQ': return 'Iraq';
            case 'IE': return 'Ireland';
            case 'IL': return 'Israel';
            case 'IT': return 'Italy';
            case 'JM': return 'Jamaica';
            case 'JP': return 'Japan';
            case 'JO': return 'Jordan';
            case 'KZ': return 'Kazakhstan';
            case 'KE': return 'Kenya';
            case 'KI': return 'Kiribati';
            case 'KP': return 'Korea, North';
            case 'KR': return 'Korea, South';
            case 'KW': return 'Kuwait';
            case 'KG': return 'Kyrgyzstan';
            case 'LA': return 'Laos';
            case 'LV': return 'Latvia';
            case 'LB': return 'Lebanon';
            case 'LS': return 'Lesotho';
            case 'LR': return 'Liberia';
            case 'LY': return 'Libya';
            case 'LI': return 'Liechtenstein';
            case 'LT': return 'Lithuania';
            case 'LU': return 'Luxembourg';
            case 'MG': return 'Madagascar';
            case 'MW': return 'Malawi';
            case 'MY': return 'Malaysia';
            case 'MV': return 'Maldives';
            case 'ML': return 'Mali';
            case 'MT': return 'Malta';
            case 'MH': return 'Marshall Islands';
            case 'MR': return 'Mauritania';
            case 'MU': return 'Mauritius';
            case 'MX': return 'Mexico';
            case 'FM': return 'Micronesia';
            case 'MD': return 'Moldova';
            case 'MC': return 'Monaco';
            case 'MN': return 'Mongolia';
            case 'ME': return 'Montenegro';
            case 'MA': return 'Morocco';
            case 'MZ': return 'Mozambique';
            case 'MM': return 'Myanmar';
            case 'NA': return 'Namibia';
            case 'NR': return 'Nauru';
            case 'NP': return 'Nepal';
            case 'NL': return 'Netherlands';
            case 'NZ': return 'New Zealand';
            case 'NI': return 'Nicaragua';
            case 'NE': return 'Niger';
            case 'NG': return 'Nigeria';
            case 'MK': return 'North Macedonia';
            case 'NO': return 'Norway';
            case 'OM': return 'Oman';
            case 'PK': return 'Pakistan';
            case 'PW': return 'Palau';
            case 'PA': return 'Panama';
            case 'PG': return 'Papua New Guinea';
            case 'PY': return 'Paraguay';
            case 'PE': return 'Peru';
            case 'PH': return 'Philippines';
            case 'PL': return 'Poland';
            case 'PT': return 'Portugal';
            case 'QA': return 'Qatar';
            case 'RO': return 'Romania';
            case 'RU': return 'Russia';
            case 'RW': return 'Rwanda';
            case 'KN': return 'Saint Kitts and Nevis';
            case 'LC': return 'Saint Lucia';
            case 'VC': return 'Saint Vincent and the Grenadines';
            case 'WS': return 'Samoa';
            case 'SM': return 'San Marino';
            case 'ST': return 'Sao Tome and Principe';
            case 'SA': return 'Saudi Arabia';
            case 'SN': return 'Senegal';
            case 'RS': return 'Serbia';
            case 'SC': return 'Seychelles';
            case 'SL': return 'Sierra Leone';
            case 'SG': return 'Singapore';
            case 'SK': return 'Slovakia';
            case 'SI': return 'Slovenia';
            case 'SB': return 'Solomon Islands';
            case 'SO': return 'Somalia';
            case 'ZA': return 'South Africa';
            case 'SS': return 'South Sudan';
            case 'ES': return 'Spain';
            case 'LK': return 'Sri Lanka';
            case 'SD': return 'Sudan';
            case 'SR': return 'Suriname';
            case 'SE': return 'Sweden';
            case 'CH': return 'Switzerland';
            case 'SY': return 'Syria';
            case 'TW': return 'Taiwan';
            case 'TJ': return 'Tajikistan';
            case 'TZ': return 'Tanzania';
            case 'TH': return 'Thailand';
            case 'TL': return 'Timor-Leste';
            case 'TG': return 'Togo';
            case 'TO': return 'Tonga';
            case 'TT': return 'Trinidad and Tobago';
            case 'TN': return 'Tunisia';
            case 'TR': return 'Turkey';
            case 'TM': return 'Turkmenistan';
            case 'TV': return 'Tuvalu';
            case 'UG': return 'Uganda';
            case 'UA': return 'Ukraine';
            case 'AE': return 'United Arab Emirates';
            case 'GB': return 'United Kingdom';
            case 'US': return 'United States';
            case 'UY': return 'Uruguay';
            case 'UZ': return 'Uzbekistan';
            case 'VU': return 'Vanuatu';
            case 'VA': return 'Vatican City';
            case 'VE': return 'Venezuela';
            case 'VN': return 'Vietnam';
            case 'YE': return 'Yemen';
            case 'ZM': return 'Zambia';
            case 'ZW': return 'Zimbabwe';
            case 'XK': return 'Kosovo';
            default: return null;
        }
    }

}
