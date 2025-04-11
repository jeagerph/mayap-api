<?php

namespace App\Http\Controllers\Api\MyCompany;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use App\Models\BeneficiaryRelative;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Repositories\Base\OpenAiRepository;

class OpenAIController extends Controller
{
    protected $openAiRepository;

    public function __construct(OpenAiRepository $openAiRepository)
    {
        $this->openAiRepository = $openAiRepository;
    }

    public function askOpenAI(Request $request)
    {
        $userQuery = $request->input('ask');


        $filters = [];

        // Handle "How many beneficiaries do we have now?" query
        if (stripos($userQuery, 'how many beneficiaries') !== false) {

            $totalBeneficiaries = Beneficiary::where('company_id', Auth::user()->company()->id)->count();


            return response()->json([
                'response' => "You currently have a total of $totalBeneficiaries beneficiaries."
            ]);
        }


        if (stripos($userQuery, 'senior citizen') !== false || stripos($userQuery, 'senior') !== false) {

            $filters['age'] = "60,100";
        }

        if (stripos($userQuery, 'priority') !== false) {

            $filters['isPriority'] = true;
        }

        if (stripos($userQuery, 'household') !== false) {

            $filters['isHousehold'] = true;
        }

        if (stripos($userQuery, 'verified voter') !== false) {

            $filters['isGreen'] = true;
        }

        if (stripos($userQuery, 'cross matched voter') !== false) {

            $filters['isOrange'] = true;
        }

        $model = new Beneficiary;

        $beneficiaries = $model->filtered($model, $request->merge(['filter' => $filters]))
        ->get([
            'first_name',
            'middle_name',
            'last_name',
            'date_of_birth',
            'date_registered',
            'code'
        ]);

        if ($beneficiaries->isEmpty()) {
            return response()->json(['response' => 'No beneficiaries found based on your criteria.']);
        }


        $totalBeneficiaries = $beneficiaries->count();


        $seniorCitizens = $beneficiaries->filter(function ($b) {
            return \Carbon\Carbon::parse($b->date_of_birth)->age >= 60; // Filter out seniors (age >= 60)
        });

        $totalSeniors = $seniorCitizens->count();


        $averageAge = $beneficiaries->avg(function ($b) {
            return \Carbon\Carbon::parse($b->date_of_birth)->age;
        });


        $formatted = "Here are the filtered beneficiaries:\n";

        // foreach ($beneficiaries as $b) {
        //     $relativeFilters = [
        //         'beneficiaryCode' => $b->code
        //     ];

        //     $relativeSorts = [
        //         'orderNo' => 'asc'
        //     ];

        //     $request->merge([
        //         'beneficiary-relatives-related' => true,
        //         'filter' => $this->handleQueries('filter', $request, $relativeFilters),
        //         'sort' => $this->handleQueries('sort', $request, $relativeSorts),
        //         'all' => true
        //     ]);

        //     $beneficiaryRelativemodel = new BeneficiaryRelative;

        //     $relative = $beneficiaryRelativemodel->build();

        //     if (empty($relative)) {
        //         continue; // Skip if no relatives found
        //     } else {
        //         $formatted .= "Heare are the relatives of beneficiary {$b->first_name} {$b->middle_name} {$b->last_name}:\n";

        //         foreach ($relative as $r) {
        //             // $formatted .= "- {$r->relative->first_name} {$r->relative->middle_name} {$r->relative->last_name}, Relationship: {$r->relationship}\n";
        //             $formatted .= "- $r\n";
        //         }
        //     }

        // }

        $formatted .= "\n\nSummary:\n";
        $formatted .= "- Total Beneficiaries: $totalBeneficiaries\n";
        $formatted .= "- Total Senior Citizens: $totalSeniors\n";
        $formatted .= "- Average Age: " . round($averageAge, 2) . "\n";

        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant that summarizes filtered beneficiary data.'],
            ['role' => 'user', 'content' => $formatted == null || $formatted == '' ? $userQuery : $formatted . "\n\nNow answer this question: " . $userQuery],
        ];


        $response = $this->openAiRepository->chat($messages);

        return response()->json([
            'response' => $response,
            'total_beneficiaries' => $totalBeneficiaries,
            'total_senior_citizens' => $totalSeniors,
            'average_age' => round($averageAge, 2),
        ]);
    }
}
