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
            border-bottom: 2px solid #ef4444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .alert-box {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
        }

        .value {
            color: #ef4444;
            font-weight: bold;
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
            <h2 style="color: #ef4444;">Inventory Alert</h2>
        </div>

        <div class="alert-box">
            <p>The following product requires attention:</p>
            <p>
                <span class="label">Product:</span> {{ $product->name }}<br>
                <span class="label">Status:</span> <span class="value">{{ strtoupper($status) }}</span><br>
                <span class="label">Current Stock:</span> {{ $product->stock_quantity }} units
            </p>
        </div>

        <p>Please check the inventory management dashboard to restock this item.</p>

        <div class="footer">
            Automated system alert from Marketplace Operations.
        </div>
    </div>
</body>

</html>