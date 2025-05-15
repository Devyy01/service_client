<?php

namespace App\Http\Controllers;

use App\Services\ApiChatgpt;
use Illuminate\Http\Request;

class PersonalInfoController extends Controller
{

    public $serviceOpenAI;


    public function __construct(ApiChatgpt $serviceOpenAI)
    {
        $this->serviceOpenAI = $serviceOpenAI;
    }

    public function showForm()
    {
        return view('subscription'); // Charger la vue contenant ton formulaire
    }

    public function submitForm(Request $request)
    {

        // Valider les données
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'characteristic' => 'required|string|max:255',
        ]);

        // dd($validated); // Affiche les données validées
        $prompt = $this->generatePrompt($validated['firstName'], $validated['lastName'], $validated['address'], $validated['city'], $validated['postal_code'], $validated['country'], $validated['characteristic']);
        // dd($prompt);
        $valer = $this->serviceOpenAI->generateCanva($prompt);
        // dd($valer);
        preg_match_all('/([A-Za-zéèàùçêîôâëïü -]+)\s*:\s*(\d+(?:[.,]\d+)?)\s*%/', $valer, $matches, PREG_SET_ORDER);

        $countries = json_decode(file_get_contents(resource_path('json/countries.json')), true);

        $result = [];
        $countrise_code = [];
        //   dd($valer);
        foreach ($matches as $match) {
            $country = trim($match[1]);
            $percent = $match[2];
            $country_client = collect($countries)->firstWhere('label', $country);
            if ($country_client) {
                $country_code = $country_client['code'];
                $result[$country_code] =  $percent;
                $countrise_code[$country_code] = $country;
            }
        }
        // dd($result,$countrise_code);
        if ($result && $countrise_code) {
            $this->generateMap($result, $countrise_code);
        }
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

        $legendX = 30;  // Position X de la légende
        $legendY = 250; // Position Y de départ
        $legendSpacing = 25; // Espace entre lignes
        $rectSize = 15; // Taille des carrés colorés

        $legendElements = '<g id="legend">';

        $legendElements .= sprintf(
            '<text x="%d" y="%d" font-size="14" fill="#000" font-weight="bold">Countries</text>',
            $legendX,
            $legendY - 20 // Positionner au-dessus de la légende
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

            // Texte à droite du carré
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

        // Ajouter la légende à la fin du SVG (avant </svg>)
        $svgContent = preg_replace('/<\/svg>/', $legendElements . '</svg>', $svgContent);

        // Sauvegarder le SVG modifié
        $outputPath = public_path('maps/generated_world.svg');
        file_put_contents($outputPath, $svgContent);

        // Retourner l'URL du fichier généré
        $url = asset('maps/generated_world.svg');

        return response()->json(['map_url' => $url]);
    }

    // private function getColorFromPercentage($percentage)
    // {
    //     // Du vert (0%) vers rouge (100%)
    //     $red   = min(255, intval($percentage * 2.55));
    //     $green = min(255, 255 - intval($percentage * 2.55));
    //     $blue  = 0;

    //     return sprintf("#%02X%02X%02X", $red, $green, $blue);
    // }

    private function getCountryName($countryCode)
    {
        $countries = [
            'FR' => 'France',
            'DZ' => 'Algérie',
            'IT' => 'Italie',
            'CA' => 'Canada',
            // Tu peux en ajouter d'autres ici
        ];

        return $countries[$countryCode] ?? $countryCode;
    }
    private function getColorFromPercentage($percentage)
    {
        // Hue de 120° (vert) à 0° (rouge)
        $hue = 120 - ($percentage * 1.2);  // 100% → 0° rouge | 0% → 120° vert
        $saturation = 100; // 100% saturation
        $lightness = 50;   // 50% lumière

        return $this->hslToHex($hue, $saturation, $lightness);
    }

    private function hslToHex($h, $s, $l)
    {
        $h /= 360;
        $s /= 100;
        $l /= 100;

        $r = $g = $b = 0;

        if ($s == 0) {
            $r = $g = $b = $l; // Gris
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
}
