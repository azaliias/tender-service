<?php

namespace App\Http\Controllers;

use App\Models\Tender;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenderController extends Controller
{
    /**
     * Список тендеров с фильтрацией
     */
    public function index(Request $request)
    {
        $query = Tender::query();

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('date')) {
            $date = Carbon::createFromFormat('Y-m-d', $request->date)
                ->startOfDay()
                ->toDateTimeString();
            $query->where('created_at', '>=', $date)
                ->where('created_at', '<', Carbon::parse($date)->addDay());
        }

        $tenders = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $tenders->items(),
            'pagination' => [
                'total' => $tenders->total(),
                'per_page' => $tenders->perPage(),
                'current_page' => $tenders->currentPage(),
                'last_page' => $tenders->lastPage(),
                'from' => $tenders->firstItem(),
                'to' => $tenders->lastItem(),
            ]
        ]);
    }

    /**
     * Создание нового тендера
     */
    public function store(Request $request)
    {
        $validStatuses = ['Открыто', 'Закрыто', 'Отменено'];

        $validator = Validator::make($request->all(), [
            'external_code' => 'required|string|max:50',
            'number' => 'required|string|max:50',
            'status' => ['nullable', Rule::in($validStatuses)],
            'name' => 'required|string|max:255',
        ], [
            'status.in' => 'The selected status is invalid. Valid values: ' . implode(', ', $validStatuses) . ' or null.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 404);
        }

        $tender = Tender::create($request->all());

        return response()->json([
            'message' => 'Tender created successfully',
            'data' => $tender
        ]);
    }

    /**
     * Получение тендера с участием идентификатора (external_code - Внешний код)
     */
    public function show($external_code)
    {
        $tender = Tender::where('external_code', $external_code)->first();

        if (!$tender) {
            return response()->json([
                'message' => 'Tender not found'
            ], 404);
        }

        return response()->json($tender);
    }
}
