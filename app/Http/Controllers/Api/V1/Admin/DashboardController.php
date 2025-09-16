<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
    public function index(Request $request){
        {
            //%b get month like as (Jan, Feb, Mar).
            // Get last 3 Order sorted by creation date (latest first)
            $Order = Order::orderBy('created_at', 'desc')->take(3)->get();
            
            // Dashboard summary: total amounts and counts by status
            //suumary for money debend on status and numbers of oredrs debend on ststus
            $dashboardData = Order::selectRaw("
                SUM(total) AS TotalAmount,
                SUM(IF(status='ordered', total, 0)) AS TotalOrderAmount,
                SUM(IF(status='delivered', total, 0)) AS TotalDeliveredAmount,
                SUM(IF(status='canceled', total, 0)) AS TotalCanceledAmount,
                COUNT(*) AS TotalOrder,
                SUM(IF(status='ordered', 1, 0)) AS OrderCount,
                SUM(IF(status='delivered', 1, 0)) AS DeliveredCount,
                SUM(IF(status='canceled', 1, 0)) AS CanceledCount
            ")->first();
            
            // Monthly data: get stats grouped by month (for charts)
            //make monthly Statistics from tables oredrs +  month_names
            $monthlyDatas = DB::select("
                SELECT 
                    M.id AS MonthNo,
                    M.name AS MonthName,
                    IFNULL(D.TotalAmount, 0) AS TotalAmount,
                    IFNULL(D.TotalOrderAmount, 0) AS TotalOrderAmount,
                    IFNULL(D.TotalDeliveredAmount, 0) AS TotalDeliveredAmount,
                    IFNULL(D.TotalCanceledAmount, 0) AS TotalCanceledAmount
                FROM month_names M
        
                LEFT JOIN (
                    SELECT 
                        MONTH(created_at) AS MonthNo,
                        DATE_FORMAT(created_at, '%b') AS MonthName,
                        SUM(total) AS TotalAmount,
                        SUM(IF(status = 'ordered', total, 0)) AS TotalOrderAmount,
                        SUM(IF(status = 'delivered', total, 0)) AS TotalDeliveredAmount,
                        SUM(IF(status = 'canceled', total, 0)) AS TotalCanceledAmount
        
                    FROM Order
                    WHERE YEAR(created_at) = YEAR(NOW())
                    GROUP BY YEAR(created_at), MONTH(created_at), DATE_FORMAT(created_at, '%b')
                    ORDER BY MONTH(created_at)
                ) D ON D.MonthNo = M.id
            ");
        
            // Convert (Array) monthly amounts to (string) comma-separated strings for chart usage
            $AmountM = implode(',', collect($monthlyDatas)->pluck('TotalAmount')->toArray());
            $orderedAmountM = implode(',', collect($monthlyDatas)->pluck('TotalOrderAmount')->toArray());
            $deliveredAmountM = implode(',', collect($monthlyDatas)->pluck('TotalDeliveredAmount')->toArray());
            $canceledAmountM = implode(',', collect($monthlyDatas)->pluck('TotalCanceledAmount')->toArray());
        
            // Calculate total sums for each type of amount across the year
            $totalAmount = collect($monthlyDatas)->sum('TotalAmount');
            $totalDeliveredAmount = collect($monthlyDatas)->sum('TotalDeliveredAmount');
            $totalOrderedAmount = collect($monthlyDatas)->sum('TotalOrderAmount');
            $totalCanceledAmount = collect($monthlyDatas)->sum('TotalCanceledAmount');
        
            return response()->json([
                'Order' => $Order,
                'dashboardData' => $dashboardData,
                'monthlyData' => $monthlyDatas,
                'chart' => [
                    'AmountM' => $AmountM,
                    'orderedAmountM' => $orderedAmountM,
                    'deliveredAmountM' => $deliveredAmountM,
                    'canceledAmountM' => $canceledAmountM,
                ],
                'totals' => [
                    'totalAmount' => $totalAmount,
                    'totalDeliveredAmount' => $totalDeliveredAmount,
                    'totalOrderedAmount' => $totalOrderedAmount,
                    'totalCanceledAmount' => $totalCanceledAmount,
                ]
            ],200);
        }
    }
}