<?php
$invoice_content = "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='utf-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1' />

    <title>Invoice</title>


    <!-- Invoice styling -->
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0;
            padding-bottom: 0;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;

            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        
        .invoice-box table tr td:nth-child(3) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(3) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        .legal {
            text-align: left;
            margin-top: 50px;
            font-size: small
        }
    </style>
</head>

<body>


<div class='invoice-box'>
    <table>
        <tr class='top'>
            <td colspan='2'>
                <table>
                    <tr>
                        <!--<td class='title'>
                            <img src='./images/logo.png' alt='Company logo' style='width: 100%; max-width: 300px' />
                        </td>-->
                        <td class='title'>Marketplace</td>

                        <td>
                            Invoice #: %r_order_number_%r<br />
                            Order date: %r_order_date_%r<br />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class='information'>
            <td colspan='2'>
                <table>
                    <tr>
                        <td>
                            %r_address_customer_%r
                        </td>

                        <td>
                            %r_address_seller_%r
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class='heading'>
            <td>Item</td>

            <td>Amount</td>

            <td>Price</td>
        </tr>


        %r_ordered_articles_%r
    </table>

    <p class='legal'>Legal: Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. </p>
    <table>
        <td>More Legal</td>
        <td>Even More Legal</td>
    </table>
</div>
</body>
</html>";


