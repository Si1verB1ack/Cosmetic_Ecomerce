<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItems;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //crate function index
    public function index(Request $request){
        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftJoin('users','users.id','orders.user_id');

        if(!empty($request->get('keyword'))){
            $orders = $orders->where('users.name','like','%'. $request->keyword.'%');
            $orders = $orders->orWhere('users.email','like','%'. $request->keyword.'%');
            $orders = $orders->orWhere('orders.id','like','%'. $request->keyword.'%');
        }

        $orders = $orders->paginate(10);

        return view('admin.orders.list',[
            'orders' => $orders,
        ]);
    }

    //crate function detail
    public function detail($orderId){
        $order = Order::select('orders.*','countries.name as countryName')
                        ->where('orders.id', $orderId)
                        ->leftJoin('countries','countries.id','orders.country_id')
                        ->first();
        $orderItems = OrderItems::where('order_id',$orderId)->get();

        return view('admin.orders.detail',[
            'order' => $order,
            'orderItems' => $orderItems,
        ]);
    }
    public function changeOrderStatusForm(Request $request, $orderId){
        $order = Order::find($orderId);
        $order->status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();

        //flash message create-success and return status and message
        session()->flash('create-success','Order status updated successfully');

        return response()->json([
           'status' => true,
           'message'=> 'Order status updated successfully'
        ]);

        // $order = Order::select('orders.*','countries.name as countryName')
        //                 ->where('orders.id', $orderId)
        //                 ->leftJoin('countries','countries.id','orders.country_id')
        //                 ->first();
        // $orderItems = OrderItems::where('order_id',$orderId)->get();

        // return view('admin.orders.detail',[
        //     'order' => $order,
        //     'orderItems' => $orderItems,
        // ]);
    }

    public function sendInvoiceEmail($orderId,Request $request){
        orderEmail($orderId,$request->userType);

        session()->flash('create-success','Order email sent successfully');

        return response()->json([
           'status' => true,
           'message'=> 'Order email sent successfully'
        ]);
    }
}
