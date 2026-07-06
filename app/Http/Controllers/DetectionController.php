<?php

namespace App\Http\Controllers;

use App\Services\AbrService;
use App\Services\TbrService;
use App\Services\CbrService;
use App\Services\DetectionFusionService;
use App\Services\DetectionResultService;
use Illuminate\Http\Request;

class DetectionController extends Controller
{
    public function __construct(
        private AbrService             $abrService,
        private TbrService             $tbrService,
        private CbrService             $cbrService,
        private DetectionFusionService $fusionService,
        private DetectionResultService $resultService,
    ) {}

    public function showForm()
    {
        return view('detection.form');
    }

    public function analyze(Request $request)
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'ic_number'      => 'required|string|max:20',
            'honorific'      => 'nullable|string|max:20',
            'photo'          => 'nullable|image|max:2048',
            'cbr_gender'     => 'nullable|in:Male,Female',
            'cbr_confidence' => 'nullable|integer|min:0|max:100',
        ]);

        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('uploads', 'public');
        }

        $abr = $this->abrService->execute($request->input('ic_number'));
        $tbr = $this->tbrService->execute(
            $request->input('full_name'),
            $request->input('honorific', '')
        );
        $cbr = $this->cbrService->execute(
            $request->input('cbr_gender', 'Female'),
            (int) $request->input('cbr_confidence', 50)
        );

        $fusion = $this->fusionService->execute(
            $abr['prediction'],
            $tbr['prediction'],
            $cbr['prediction']
        );

        $this->resultService->execute([
            'matric_no'  => $request->input('ic_number'),
            'full_name'  => $request->input('full_name'),
            'ic_number'  => $request->input('ic_number'),
            'image_path' => $imagePath,
            'abr'        => $abr,
            'tbr'        => $tbr,
            'cbr'        => $cbr,
        ], $fusion);

        session()->flash('detection_result', [
            'full_name'  => $request->input('full_name'),
            'image_path' => $imagePath,
            'abr'        => $abr,
            'tbr'        => $tbr,
            'cbr'        => $cbr,
            'fusion'     => $fusion,
        ]);

        return redirect()->route('detection.result');
    }

    public function showResult()
    {
        $result = session('detection_result');

        if (!$result) {
            return redirect()->route('detection.form')
                             ->with('error', 'No detection result found. Please submit the form.');
        }

        return view('detection.result', compact('result'));
    }
}
