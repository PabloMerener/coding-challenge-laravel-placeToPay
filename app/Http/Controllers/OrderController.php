<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\PlaceToPayService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * The PlacetoPay service instance.
     *
     * @var App\Services\PlacetoPayService;
     */
    protected $placeToPay;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Repositories\UserRepository  $users
     * @return void
     */
    public function __construct(PlaceToPayService $placeToPayService)
    {
        $this->placeToPay = $placeToPayService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::paginate(6);

        return view('orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = Product::firstOrFail();

        $order = Order::create([
            'user_id' => auth()->user()->id,
            'customer_name' => $request->name,
            'customer_email' => $request->email,
            'customer_mobile' => $request->phone,
            'status' => Order::CREATED,
            'product_id' => $product->id,
            'quantity' => 1,
            'amount' => $product->unit_price,
        ]);

        $response = $this->placeToPay->sendRequestPayment($order)->object();

        if (
            isset($response->status->status) &&
            $response->status->status === $this->placeToPay::CREATE_SESSION_OK
        ) {
            $order->fill(['requestId' => $response->requestId])->save();

            return redirect($response->processUrl);
        } else {
            return $response->message;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {
        if ($order->status === Order::CREATED || $order->status === Order::REJECTED) {
            $request = $this->placeToPay->getRequestInformation($order->requestId)->object();

            if ($request->status->status === Order::APPROVED ?? null) {
                $order->fill(['status' => Order::PAYED])->save();
            } else {
                $order->fill(['status' => Order::REJECTED])->save();
            }
        }

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        $response = $this->placeToPay->sendRequestPayment($order)->object();

        if (
            isset($response->status->status) &&
            $response->status->status === $this->placeToPay::CREATE_SESSION_OK
        ) {
            $order->fill(['requestId' => $response->requestId])->save();

            return redirect($response->processUrl);
        } else {
            return $response->message;
        }

        $order->refresh();

        return view('orders.show', compact('order'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
}
