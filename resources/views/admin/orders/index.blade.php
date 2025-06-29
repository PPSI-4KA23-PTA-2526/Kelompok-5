// resources/views/admin/orders/index.blade.php
@extends('layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Orders</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order Number</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Order Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->order_number }}</td>
                <td>{{ $order->user->name }}</td>
                <td>${{ number_format($order->total_amount, 2) }}</td>
                <td>
                    <span class="badge {{ $order->getPaymentStatusBadgeClass() }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </td>
                <td>
                    <span class="badge 
                        @switch($order->status)
                            @case('pending') bg-warning @break
                            @case('processing') bg-info @break
                            @case('shipped') bg-primary @break
                            @case('delivered') bg-success @break
                            @case('cancelled') bg-danger @break
                        @endswitch
                    ">
                        {{ ucFirst($order->status) }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-info">View</a>
                    @if($order->midtrans_order_id)
                        <a href="{{ route('admin.orders.syncPayment', $order) }}" 
                           class="btn btn-sm btn-warning" 
                           title="Sync Payment Status">
                            <i class="fas fa-sync"></i>
                        </a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{ $orders->links() }}
@endsection