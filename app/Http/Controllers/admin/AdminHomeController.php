<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TempImage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AdminHomeController extends Controller
{
    public function index(){
        Carbon::setLocale('Asia/Phnom_Penh');
        // session()->forget('logged_in');
        $totalOrders = Order::where('status', '!=', 'cancelled')->count();
        $totalRevenues = Order::where('status', '!=', 'cancelled')->sum('grand_total');
        $totalProducts = Product::count();

        $totalCustomers = User::where('role',1)->count();

        $startOfMonth = Carbon::now('Asia/Phnom_Penh')->startOfMonth()->format('Y-m-d');
        $currentDate = Carbon::now('Asia/Phnom_Penh')->format('Y-m-d');

        $revenuesThisMonth = Order::where('status', '!=', 'cancelled')
                                    ->whereDate('created_at','>=',$startOfMonth)
                                    ->whereDate('created_at','<=',$currentDate)
                                    ->sum('grand_total');

        $LastMonthStartDate = Carbon::now('Asia/Phnom_Penh')->subMonth()->startOfMonth()->format('Y-m-d');
        $LastMonthEndDate = Carbon::now('Asia/Phnom_Penh')->subMonth()->endOfMonth()->format('Y-m-d');
        $LastMonthName = Carbon::now('Asia/Phnom_Penh')->subMonth()->startOfMonth()->format('M');

        $revenuesLastMonth = Order::where('status', '!=', 'cancelled')
                                    ->whereDate('created_at','>=',$LastMonthStartDate)
                                    ->whereDate('created_at','<=',$LastMonthEndDate)
                                    ->sum('grand_total');

        $Last30day = Carbon::now('Asia/Phnom_Penh')->subDays(30)->format('Y-m-d');

        $revenuesLast30day = Order::where('status', '!=', 'cancelled')
                                    ->whereDate('created_at','>=',$Last30day)
                                    ->whereDate('created_at','<=',$currentDate)
                                    ->sum('grand_total');

        $dayBeforeToday = Carbon::now('Asia/Phnom_Penh')->subDays(1)->format('Y-m-d H:i:s');

        $tempImage = TempImage::where('created_at','<=',$dayBeforeToday)->get();

        foreach($tempImage as $temp){

            $path = public_path('/temp/'.$temp->name);
            $thumbPath = public_path('/temp/thumb/'.$temp->name);

            if(File::exists($path)){
                File::delete($path);
            }

            if(File::exists($thumbPath)){
                File::delete($thumbPath);
            }

            TempImage::where('id',$temp->id)->delete();

        }

        return view('admin.dashboard',[
            'totalOrders'=>$totalOrders,
            'totalProducts'=>$totalProducts,
            'totalCustomers'=>$totalCustomers,
            'totalRevenues'=>$totalRevenues,
            'revenuesThisMonth'=>$revenuesThisMonth,
            'revenuesLastMonth'=>$revenuesLastMonth,
            'LastMonthName'=>$LastMonthName,
            'revenuesLast30day'=>$revenuesLast30day,
        ]);
    }

    public function logout(Request $request){
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}

