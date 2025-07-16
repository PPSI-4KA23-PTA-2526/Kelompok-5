@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
</div>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <h3>{{ $stats['total_products'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Users</h5>
                <h3>{{ $stats['total_users'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h5 class="card-title">Total Orders</h5>
                <h3>{{ $stats['total_orders'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Pending Orders</h5>
                <h3>{{ $stats['pending_orders'] }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Recent Orders</h5>
            </div>
            <div class="card-body">
                @foreach($recent_orders as $order)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $order->order_number }}</strong><br>
                            <small>{{ $order->user->name }}</small>
                        </div>
                        <span class="badge bg-secondary">{{ $order->status }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection