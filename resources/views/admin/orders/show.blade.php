// resources/views/admin/orders/show.blade.php
@extends('layouts.admin')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Order Details - {{ $order->order_number }}</h1>
    <div>
        @if($order->midtrans_order_id)
            <a href="{{ route('admin.orders.syncPayment', $order) }}" class="btn btn-warning me-2">
                <i class="fas fa-sync"></i> Sync Payment
            </a>
        @endif
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Order Items Card -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Order Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total Amount:</th>
                                <th>${{ number_format($order->total_amount, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Customer Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Customer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $order->user->name }}</p>
                <p><strong>Email:</strong> {{ $order->user->email }}</p>
                <p><strong>Phone:</strong> {{ $order->user->phone ?? '-' }}</p>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Payment Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Payment Status:</strong> 
                    <span class="badge {{ $order->getPaymentStatusBadgeClass() }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
                @if($order->payment_type)
                    <p><strong>Payment Type:</strong> {{ ucfirst($order->payment_type) }}</p>
                @endif
                @if($order->midtrans_transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $order->midtrans_transaction_id }}</p>
                @endif
                @if($order->paid_at)
                    <p><strong>Paid At:</strong> {{ $order->paid_at->format('Y-m-d H:i:s') }}</p>
                @endif
            </div>
        </div>

        <!-- Shipping Information -->
        <div class="card mb-3">
            <div class="card-header">
                <h5>Shipping Information</h5>
            </div>
            <div class="card-body">
                <p>{{ $order->shipping_address }}</p>
            </div>
        </div>

        <!-- Order Status -->
        <div class="card">
            <div class="card-header">
                <h5>Order Status</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection