<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
        }

        .header {
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .success-box {
            background: #eef2ff;
            border: 1px solid #e0e7ff;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
        }

        .total {
            font-size: 1.2rem;
            font-weight: bold;
            color: #4f46e5;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.8em;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2 style="color: #4f46e5;">Order Confirmation</h2>
        </div>

        <div class="success-box">
            <p>Thank you for your order, {{ $order->user->name }}!</p>
            <p>Your order <strong>#{{ $order->id }}</strong> has been placed successfully.</p>
        </div>

        <h3>Order Summary</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>${{ number_format($item->price_at_purchase, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <p style="text-align: right; margin-top: 20px;">
            <span class="label">Total Amount:</span>
            <span class="total">${{ number_format($order->total_amount, 2) }}</span>
        </p>

        <div class="footer">
            If you have any questions, please reply to this email.
        </div>
    </div>
</body>

</html>