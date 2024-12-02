<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order Invoice</title>
</head>

<body
    style="font-family: 'Segoe UI', Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #2d3748; margin: 0; padding: 0; background-color: #f7fafc;">
    <table role="presentation"
        style="width: 100%; max-width: 800px; margin: 20px auto; background-color: #ffffff; border-collapse: collapse; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <!-- Header -->
        <tr>
            <td
                style="padding: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px 8px 0 0;">
                <table style="width: 100%;">
                    <tr>
                        <td style="padding-bottom: 30px;">
                            <!-- Company Logo Placeholder -->
                            <div
                                style="width: 150px; height: 50px; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px;">
                            </div>
                        </td>
                        <td style="text-align: right; color: #ffffff;">
                            <div style="font-size: 24px; font-weight: bold;">INVOICE</div>
                            <div style="font-size: 14px; margin-top: 5px;">#{{ $mailData['order']->id }}</div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="color: #ffffff;">
                            @if ($mailData['userType'] == 'customer')
                                <h1 style="margin: 0; font-size: 28px; font-weight: 600;">Thanks for your order! 🎉</h1>
                                <p style="margin: 10px 0 0; opacity: 0.9;">We're preparing your items with care.</p>
                            @else
                                <h1 style="margin: 0; font-size: 28px; font-weight: 600;">New Order Received</h1>
                                <p style="margin: 10px 0 0; opacity: 0.9;">Order requires processing</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Order Info & Shipping -->
        <tr>
            <td style="padding: 40px;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 50%; vertical-align: top;">
                            <div style="background: #f8fafc; padding: 25px; border-radius: 8px;">
                                <h3
                                    style="margin: 0 0 15px; color: #4a5568; font-size: 16px; text-transform: uppercase; letter-spacing: 1px;">
                                    Shipping Address</h3>
                                <div style="font-size: 15px; line-height: 1.6;">
                                    <strong
                                        style="color: #2d3748; font-size: 17px;">{{ $mailData['order']->first_name . ' ' . $mailData['order']->last_name }}</strong><br>
                                    {{ $mailData['order']->address }}<br>
                                    {{ $mailData['order']->city }}, {{ $mailData['order']->zip }}<br>
                                    {{ getCountryInfo($mailData['order']->country_id)->name }}<br>
                                    <span style="color: #718096;">📱 {{ $mailData['order']->mobile }}</span><br>
                                    <span style="color: #718096;">✉️ {{ $mailData['order']->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                            <div style="background: #f8fafc; padding: 25px; border-radius: 8px;">
                                <h3
                                    style="margin: 0 0 15px; color: #4a5568; font-size: 16px; text-transform: uppercase; letter-spacing: 1px;">
                                    Order Details</h3>
                                <table style="width: 100%; font-size: 15px;">
                                    <tr>
                                        <td style="color: #718096; padding: 5px 0;">Order Date:</td>
                                        <td style="text-align: right;">{{ date('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #718096; padding: 5px 0;">Order Status:</td>
                                        <td style="text-align: right;">
                                            <span style="
    @switch($mailData['order']->status)
        @case('pending')
            background: rgba(255, 159, 67, 0.1); color: #ff9f43;
            @break
        @case('shipped')
            background: rgba(45, 149, 250, 0.1); color: #2d95fa;
            @break
        @case('delivered')
            background: rgba(39, 194, 76, 0.1); color: #27c24c;
            @break
        @case('cancelled')
            background: rgba(234, 32, 39, 0.1); color: #ea202b;
            @break
        @default
            background: rgba(102, 126, 234, 0.1); color: #667eea;
    @endswitch
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid currentColor;
    opacity: 0.9;
">
    {{ $mailData['order']->status }}
</span>
                                            </td>
                                    </tr>
                                </table>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Order Items -->
        <tr>
            <td style="padding: 0 40px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th
                                style="background: #f8fafc; padding: 15px; text-align: left; border-radius: 8px 0 0 8px; color: #4a5568; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                                Product</th>
                            <th
                                style="background: #f8fafc; padding: 15px; text-align: right; color: #4a5568; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                                Price</th>
                            <th
                                style="background: #f8fafc; padding: 15px; text-align: right; color: #4a5568; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                                Qty</th>
                            <th
                                style="background: #f8fafc; padding: 15px; text-align: right; border-radius: 0 8px 8px 0; color: #4a5568; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($mailData['order']->items as $item)
                            <tr>
                                <td style="padding: 20px 15px; border-bottom: 1px solid #edf2f7;">
                                    <div style="font-weight: 500;">{{ $item->name }}</div>
                                </td>
                                <td
                                    style="padding: 20px 15px; text-align: right; border-bottom: 1px solid #edf2f7; color: #718096;">
                                    ${{ number_format($item->price, 2) }}</td>
                                <td
                                    style="padding: 20px 15px; text-align: right; border-bottom: 1px solid #edf2f7; color: #718096;">
                                    {{ $item->qty }}</td>
                                <td
                                    style="padding: 20px 15px; text-align: right; border-bottom: 1px solid #edf2f7; font-weight: 500;">
                                    ${{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>

        <!-- Order Summary -->
        <tr>
            <td style="padding: 40px;">
                <table style="width: 100%; max-width: 400px; margin-left: auto;">
                    <tr>
                        <td style="padding: 10px 0; color: #718096;">Subtotal:</td>
                        <td style="padding: 10px 0; text-align: right;">
                            ${{ number_format($mailData['order']->subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #718096;">
                            Discount:
                            {{ !empty($mailData['order']->coupon_code) ? '(' . $mailData['order']->coupon_code . ')' : '' }}
                        </td>
                        <td style="padding: 10px 0; text-align: right; color: #e53e3e;">
                            -${{ number_format($mailData['order']->discount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #718096;">Shipping:</td>
                        <td style="padding: 10px 0; text-align: right;">
                            ${{ number_format($mailData['order']->shipping, 2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <hr style="border: none; border-top: 2px dashed #edf2f7; margin: 15px 0;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; font-weight: bold; font-size: 18px;">Grand Total:</td>
                        <td
                            style="padding: 10px 0; text-align: right; font-weight: bold; font-size: 18px; color: #667eea;">
                            ${{ number_format($mailData['order']->grand_total, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>

        <!-- Footer -->
        <tr>
            <td style="padding: 40px; background-color: #f8fafc; border-radius: 0 0 8px 8px; text-align: center;">
                <div style="max-width: 600px; margin: 0 auto;">
                    <p style="margin: 0 0 15px; color: #4a5568; font-size: 16px;">Thank you for shopping with us!</p>
                    <p style="margin: 0; color: #718096; font-size: 14px;">If you have any questions about your order,
                        please contact our support team.</p>

                    <div style="margin-top: 30px;">
                        <a href="#"
                            style="display: inline-block; padding: 12px 24px; background-color: #667eea; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 500; margin: 0 10px;">Track
                            Order</a>
                        <a href="#"
                            style="display: inline-block; padding: 12px 24px; background-color: #ffffff; color: #667eea; text-decoration: none; border-radius: 6px; font-weight: 500; border: 1px solid #667eea; margin: 0 10px;">Contact
                            Support</a>
                    </div>

                    <div style="margin-top: 30px;">
                        <a href="#" style="color: #718096; text-decoration: none; margin: 0 10px;">Website</a>
                        <a href="#" style="color: #718096; text-decoration: none; margin: 0 10px;">Terms</a>
                        <a href="#" style="color: #718096; text-decoration: none; margin: 0 10px;">Privacy
                            Policy</a>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>

</html>
