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
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background: #7c3aed;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.8em;
            color: #999;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Product Restocked</h2>
        </div>

        <p>The product <strong>{{ $product->name }}</strong> is now back in inventory.</p>

        <p>
            Added Quantity: {{ $addedQuantity }} units<br>
            Current Availability: {{ $product->stock_quantity }} units
        </p>

        <p>The product is available for purchase in the marketplace.</p>

        <a href="{{ config('app.url') }}/products/{{ $product->id }}" class="btn">View Product</a>

        <div class="footer">
            Thank you from the Marketplace team.
        </div>
    </div>
</body>

</html>