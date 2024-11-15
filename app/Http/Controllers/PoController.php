<?php

namespace App\Http\Controllers;

use App\Models\PreOrder;
use App\Models\PreOrderItem;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Sum;

class PoController extends Controller
{
    public function view()
    {
        $order = PreOrderItem::where('pre_order_id', null)->get();
        return view('Admin.PO.PreOrder', compact('order'));
    }

    public function AddOrder(Request $request)
    {
        $qty = $request->qty;
        $price = $request->price;
        $grandtotal = $qty * $price;

        $item = new PreOrderItem();
        $item->product = $request->product;
        $item->unit = $request->unit;
        $item->qty = $qty;
        $item->price = $price;
        $item->grandtotal = $grandtotal;
        $item->keterangan = $request->keterangan;
        $item->save();

        return back();
    }

    public function Delete($id)
    {
        $item = PreOrderItem::find($id);
        if ($item) {
            $item->delete();
        }

        return back();
    }

    public function ProccessOrder(Request $request)
    {
        $ids = $request->input('ids');
        $order = PreOrderItem::whereIn('id', $ids)->whereNull('pre_order_id')->get();

        if ($order->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending orders found for this user.'
            ]);
        }

        $grandtotal = $order->sum(function ($item) {
            return $item->price * $item->qty;
        });

        $mainPo = new PreOrder();
        $mainPo->customer = $request->customer;
        $mainPo->customer_contact = $request->customer_contact;
        $mainPo->keterangan = $request->keterangan;
        $mainPo->payment = $request->payment;
        $mainPo->cash = $request->cash;
        $mainPo->total_price = $grandtotal; // Assign the calculated grand total

        if ($request->hasFile('transfer_img')) {
            $file = $request->file('transfer_img');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('uploads/transfer_images/po', $fileName, 'public'); // Store the file
            $mainPo->transfer_img = $filePath; // Save the file path to the database
        }

        $mainPo->save();

        $id = $mainPo->id;
        foreach ($order as $item) {
            $item->pre_order_id = $id; // Assign the main PO ID to each item
            $item->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Order processed successfully.'
        ]);
    }


    public function PoList()
    {
        $orders = PreOrder::where('progress', '!=', 'done')
            ->orderBy('created_at', 'desc') // Change 'created_at' to your desired field
            ->get();
        return view('Admin.PO.List', compact('orders'));
    }

    public function DeletePO($id)
    {
        $PO = PreOrder::find($id);

        if ($PO) {
            $PO->delete();
        }

        return back();
    }

    public  function EditPo($id)
    {
        $PoCustomer = PreOrder::find($id);
        $PoDetail = PreOrderItem::where('pre_order_id', $id)->get();

        return view('Admin.PO.editPo', compact('PoCustomer', 'PoDetail'));
    }

    public function updatePo(Request $request, $id)
    {
        // Update the main PreOrder details
        $PreOrder = PreOrder::findOrFail($id);
        $PreOrder->customer = $request->customer;
        $PreOrder->customer_contact = $request->customer_contact;
        $PreOrder->keterangan = $request->keterangan;
        $PreOrder->progress = $request->progress;
        $PreOrder->save();

        // Loop through each PreOrderItem and update individual details if they exist in the request
        $PreOrderItems = PreOrderItem::where("pre_order_id", $id)->get();
        foreach ($PreOrderItems as $pi) {
            // Check if the request contains an individual keterangan for this item
            if (isset($request->PoDetail[$pi->id]['keteranganOrder'])) {
                $pi->keterangan = $request->PoDetail[$pi->id]['keteranganOrder'];
            }
            $pi->save();
        }

        return back()->with('success', 'Pre Order updated successfully!');
    }
}
