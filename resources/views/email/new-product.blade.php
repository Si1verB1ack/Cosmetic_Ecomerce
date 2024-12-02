<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Product Launch</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <table role="presentation" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-collapse: collapse;">
        <tr>
            <td style="padding: 40px 20px; text-align: center; background-color: #1a1a1a;">
                <h1 style="color: #ffffff; margin: 0; font-size: 28px;">🎉 New Arrival Alert!</h1>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 20px;">
                <img src="cid:large-product-image" alt="Product Image" style="width: 100%; max-width: 500px; height: auto; display: block; margin: 0 auto; border-radius: 8px;">
            </td>
        </tr>

        <tr>
            <td style="padding: 0 20px 30px;">
                <div style="text-align: center;">
                    <h2 style="color: #333333; margin: 0 0 15px; font-size: 24px;">{{ $mailData['name'] }}</h2>
                    <p style="color: #666666; margin: 0 0 25px; font-size: 16px;">We're excited to introduce our latest product to our collection!</p>
                    <p style="color: #333333; font-weight: bold; font-size: 22px; margin: 0 0 30px;">{{ $mailData['price'] }}</p>
                    <a href="#" style="display: inline-block; padding: 15px 30px; background-color: #2c2c2c; color: #ffffff; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px;">Shop Now</a>
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding: 30px 20px; background-color: #f8f8f8; text-align: center;">
                <p style="color: #666666; margin: 0 0 10px; font-size: 14px;">Follow us on social media for more updates:</p>
                <div style="margin-bottom: 20px;">
                    <a href="#" style="text-decoration: none; margin: 0 10px; color: #333333;">Instagram</a>
                    <a href="#" style="text-decoration: none; margin: 0 10px; color: #333333;">Facebook</a>
                    <a href="#" style="text-decoration: none; margin: 0 10px; color: #333333;">Twitter</a>
                </div>
                <p style="color: #999999; margin: 0; font-size: 12px;">© 2024 Ecommerce Final. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>
