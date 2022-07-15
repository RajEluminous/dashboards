<html>
	<head>
		<title></title>
	</head>
	<body data-gramm="false">
		<div style="font-family:verdana; font-size:16px; line-height:22px; max-width:433px">
			<p>Hi Marion,</p>
			<p>Here are the names of the customers who purchased The Amazing You last month. Sending you the names for prayers:</p>
            <h3><u>Customer Name</u></h3>
            <table style="font-size:16px;">
                <tbody>
                    @php $i = 0; @endphp
                    @foreach ($user as $account)
                        @php $i++; @endphp
                        <tr>
                            <td width="40px;" style="text-align:left">{{$i}}.</td>
                            <td style="text-align:left">{{$account}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </body>
</html>

