<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Baru - ProdukTeh</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            background-color: #f5f5f5;
            padding: 0;
        }

        .email-container {
            background-color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4b6f32, #2d8f2d);
            padding: 30px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
        }

        @keyframes float {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .logo {
            position: relative;
            z-index: 1;
        }

        .logo h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        .logo p {
            margin: 5px 0 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 400;
        }

        .content {
            padding: 40px 30px;
        }

        .greeting {
            background: linear-gradient(135deg, #f8fff8, #e8f5e8);
            border-left: 4px solid #1a5d1a;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 0 8px 8px 0;
        }

        .greeting h2 {
            margin: 0 0 10px 0;
            color: #1a5d1a;
            font-size: 18px;
            font-weight: 600;
        }

        .greeting p {
            margin: 0;
            color: #8aad52;
            font-size: 14px;
        }

        .customer-details {
            background-color: #fafafa;
            border: 1px solid #e8e8e8;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
        }

        .detail-row {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-start;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .label {
            font-weight: 600;
            width: 90px;
            color: #d4a22c;
            font-size: 14px;
            flex-shrink: 0;
        }

        .value {
            flex: 1;
            font-size: 14px;
            color: #333;
        }

        .message-box {
            background-color: #ffffff;
            border: 2px solid #e8f5e8;
            border-radius: 8px;
            padding: 18px;
            margin-top: 8px;
            font-style: italic;
            color: #1a5d1a;
            line-height: 1.6;
        }

        .cta-section {
            background: linear-gradient(135deg, #4b6f32, #1a5d1a);
            color: white;
            padding: 25px;
            text-align: center;
            margin: 30px -30px -40px -30px;
        }

        .cta-button {
            display: inline-block;
            background-color: white;
            color: #4b6f32;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .cta-button:hover {
            background-color: #f0f0f0;
            transform: translateY(-2px);
        }

        .footer {
            background-color: #1a1a1a;
            color: #888;
            padding: 20px;
            text-align: center;
            font-size: 12px;
        }

        .footer p {
            margin: 0;
            line-height: 1.4;
        }

        a {
            color: #1a5d1a;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }

        .timestamp {
            font-size: 12px;
            color: #888;
            text-align: right;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        @media (max-width: 600px) {
            .content {
                padding: 25px 20px;
            }

            .detail-row {
                flex-direction: column;
            }

            .label {
                width: auto;
                margin-bottom: 5px;
            }

            .cta-section {
                margin: 25px -20px -25px -20px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">
                <h1>Teh Tarhadi</h1>
                <p>Produk Teh Tubruk</p>
            </div>
        </div>

        <div class="content">
            <div class="greeting">
                <h2>Pesan Baru Diterima!</h2>
                <p>Anda mendapat <strong>pesan baru</strong> dari pelanggan melalui kontak Teh Tarhadi</p>
            </div>

            <div class="customer-details">
                <div class="detail-row">
                    <div class="label">Nama:</div>
                    <div class="value"><strong>{{ $data['nama'] }}</strong></div>
                </div>

                <div class="detail-row">
                    <div class="label">Email:</div>
                    <div class="value"><a href="mailto:{{ $data['email'] }}">{{ $data['email'] }}</a></div>
                </div>

                <div class="detail-row">
                    <div class="label">Telepon:</div>
                    <div class="value"><a href="tel:{{ $data['telepon'] }}">{{ $data['telepon'] }}</a></div>
                </div>

                <div class="detail-row">
                    <div class="label">Pesan:</div>
                    <div class="value">
                        "{{ $data['pesan'] }}"
                    </div>
                </div>
            </div>

        </div>

        <div class="cta-section">
            <h3 style="margin: 0 0 10px 0; font-size: 16px;">Segera Hubungi Pelanggan!</h3>
            <p style="margin: 0 0 15px 0; opacity: 0.9; font-size: 14px;">Berikan layanan terbaik untuk meningkatkan
                kepuasan pelanggan</p>
            <a href="mailto:{{ $data['email'] }}" class="cta-button">Balas Email</a>
        </div>

        <div class="footer">
            <p><strong>Teh Tarhadi</strong> - Email Otomatis Sistem</p>
            <p>Notifikasi ini dikirim secara otomatis, mohon membalas email ini</p>
        </div>
    </div>
</body>

</html>